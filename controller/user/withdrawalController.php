<?php
include "config/env.php";
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Soneso\StellarSDK\StellarSDK;
use Soneso\StellarSDK\Crypto\KeyPair;
use Soneso\StellarSDK\Asset;
use Soneso\StellarSDK\Network;

$client = new Client();
$coinbaseWallet = $h->table('coinbase_wallet')->select()->fetchAll();
$stellarKeys = $h->table('stellar_account')->select()->fetchAll();
$public_key = $stellarKeys[0]['public_key'];
$secret_key = $stellarKeys[0]['secret_key'];

if ($route == '/user/withdraw') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $amount = $_POST['amount'] ?? '';
        $currency = $_POST['currency'] ?? '';
        $to_wallet_address = $_POST['to_wallet_address'] ?? '';
        $wallet_id = $coinbaseWallet[0]['wallet_id'];

        // Validate Input
        if (empty($amount) || empty($currency) || empty($to_wallet_address)) {
            echo json_encode([
                "status" => "error",
                "message" => "All fields are required."
            ]);
            exit;
        }

        // Step 1: Burn Stellar Token
        try {
            $assetCode = strtoupper($currency);
            $issuerPublic = $stellarKeys[0]['public_key'];
            $custodialSecret = $stellarKeys[0]['secret_key'];
            if($stellarKeys[0]['status'] == 'testnet') {
                $server = StellarSDK::getTestNetInstance(); // Use Testnet
            }else{
                // Using Public Network for Stellar
                $server = StellarSDK::getPublicNetInstance();
            }

            $sourceKeyPair = KeyPair::fromSeed($custodialSecret);
            try {
                $account = $server->accounts()->account($sourceKeyPair->getAccountId());
            } catch (Exception $e) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to load Stellar account: " . $e->getMessage()
                ]);
                exit;
            }

            try {
                $asset = Asset::createNonNativeAsset($assetCode, $issuerPublic);

                // Validate Destination Address Format
                try {
                    $destination = \Soneso\StellarSDK\MuxedAccount::fromAccountId($issuerPublic);
                } catch (Exception $e) {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Invalid Stellar address format. Please check the destination address."
                    ]);
                    exit;
                }

                // Payment Operation to Burn Tokens
                $paymentOp = new \Soneso\StellarSDK\PaymentOperation(
                    $destination,
                    $asset,
                    strval($amount)
                );

                $txBuilder = new \Soneso\StellarSDK\TransactionBuilder($account);
                $txBuilder->addOperation($paymentOp);
                $transaction = $txBuilder->build();

                // Sign with KeyPair and Network (Fixed Here)
                if($stellarKeys[0]['status'] == 'testnet') {
                    $transaction->sign($sourceKeyPair, Network::testnet());
                }else{
                    $transaction->sign($sourceKeyPair, Network::publicnet());
                }

                $response = $server->submitTransaction($transaction);
                $stellarTxnHash = $response->getHash();
            } catch (Exception $e) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Stellar token burning failed: " . $e->getMessage()
                ]);
                exit;
            }
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Stellar SDK Error: " . $e->getMessage()
            ]);
            exit;
        }

        // Step 2: Transfer Actual Funds via Python Coinbase API
        try {
            $postData = [
                "from_wallet_id" => $wallet_id,
                "to_address_id" => $to_wallet_address,
                "amount" => $amount,
                "currency" => $currency,
                "network_id" => "base-sepolia"
            ];

            $apiUrl = "https://coinbase.zotecsoft.com/wallet/transfer";
            $headers = ['Content-Type: application/json'];

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            $apiResponse = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($apiResponse === false) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Coinbase API request failed. Please check your network connection."
                ]);
                exit;
            }

            $responseData = json_decode($apiResponse, true);
            if ($httpCode !== 200 || empty($responseData['transfer_id'])) {
                echo json_encode([
                    "status" => "error",
                    "message" => $responseData['message'] ?? "Coinbase transfer failed. Please try again."
                ]);
                exit;
            }

            $transaction_id = $responseData['transfer_id'];
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Coinbase API Error: " . $e->getMessage()
            ]);
            exit;
        }

        // Step 3: Save Transaction in Database
        try {
            $systemTxnId = "WDL-" . date("Ymd") . "-" . rand(10000, 99999);
            $h->insert('transactions')->values([
                'user_id' => $loginUserId,
                'wallet_to' => $to_wallet_address,
                'amount' => $amount,
                'amount_in_usd' => $amount,
                'currency' => $currency,
                'transaction_id' => $transaction_id,
                'transaction_type' => 'withdraw',
                'status' => 'requested',
                'stellar_txn_id' => $stellarTxnHash,
                'system_transaction_id' => $systemTxnId
            ])->run();

            echo json_encode([
                "status" => "success",
                "message" => "Withdrawal initiated successfully",
                "system_transaction_id" => $systemTxnId
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
            exit;
        }
    }
}
?>
