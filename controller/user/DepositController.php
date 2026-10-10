<?php
require("config/env.php");

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
require 'vendor/autoload.php';
use Soneso\StellarSDK\Asset;
$client = new Client();
$coinbaseWallet = $h->table('coinbase_wallet')->select()->fetchAll();

// Set API Key and Base URL
$apiKey = $coinbaseWallet[0]['coinbase_apiKey'];
$baseUrl = $coinbaseWallet[0]['base_url'];
$wallet_id=$coinbaseWallet[0]['wallet_id'];
$callback_url=$coinbaseWallet[0]['callback_url'];


//Create Webhook
if ($route == '/user/create-webhook') {
    createCoinBaseWebhook($baseUrl, $apiKey, $client, $wallet_id, $callback_url);
}
//Create Webhook
if ($route == '/user/create-stellarAccount') {
    $stellarAccount = generateStellarAccount();
    $res= $h->table('stellar_account')->update([
        'public_key' => $stellarAccount['public_key'],
        'secret_key' => $stellarAccount['secret_key']])->where('id','=','1')->run();
}

if ($route == '/callback') {
      // Capture raw POST data
    $rawData = file_get_contents("php://input");

    // Decode JSON data if the payload is JSON
    $postData = json_decode($rawData, true);

   $insert = $h->insert('webhook_logs')->values(['response_code' => $rawData])->run();

     // Extract necessary fields
        $walletTo = $postData['data']['to']['id'] ?? null;
        $walletFrom = $postData['data']['from']['id'] ?? null;
        $date = $postData['created_at'] ?? null;
        $amount = $postData['data']['amount']['amount'] ?? null;
        $native_amount = $postData['data']['native_amount']['amount'] ?? null;
        $native_currency = $postData['data']['native_amount']['currency'] ?? null;
        $currency = $postData['data']['amount']['currency'] ?? null;
        $transactionId = $postData['data']['id'] ?? null;
        $status = $postData['data']['status'] ?? null;

    if ($date) {
            $dateObject = new DateTime($date);
            $date = $dateObject->format('Y-m-d H:i:s'); // Convert to 'YYYY-MM-DD HH:MM:SS'
        }

    $transactionsDetail = $h->table('transactions')->select()->where('transaction_id', '=', $transactionId)->fetchAll();
    $stellarKeys = $h->table('stellar_account')->select()->fetchAll();
    $coinbaseWalletHA = $h->table('coinbase_wallet')->select()->fetchAll();
    $public_key=$stellarKeys[0]['public_key'];
    $secret_key=$stellarKeys[0]['secret_key'];
    $walletAddress=$coinbaseWalletHA[0]['wallet_id'];
    $amountInUSD=$transactionsDetail[0]['amount_in_usd'];
    if (!checkStellarAccountExists($public_key)) {
        echo json_encode(["status" => "error", "message" => "Stellar account does not exist."]);
        exit;
    }
    $stellar_txn_id=issuePCTTokens($amountInUSD,$public_key,$public_key,$secret_key);
    
    if($stellar_txn_id != null) {
        $res = $h->table('transactions')->update([
            'wallet_to' => $walletTo,
            'wallet_from' => $walletFrom,
            'date' => $date,
            'amount' => $amount,
            'native_amount' => $native_amount,
            'native_currency' => $native_currency,
            'transaction_id' => $transactionId,
            'stellar_txn_id' => $stellar_txn_id,
            'status' => $status,
            'currency' => $currency])->where('transaction_id', '=', $transactionId)->run();
    }

}


// Initialize Guzzle Client
$client = new Client([
    'base_uri' => $baseUrl,
    'headers' => [
        'x-api-key' => $apiKey,
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]
]);


// Function to Create a Address for Deposit amount
if ($route == '/user/deposit') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//        header('Content-Type: application/json'); // Set response type to JSON
        $asset = $_POST['asset'] ?? '';
        $amount = $_POST['amount'] ?? '';
        $USDPrice=getCryptoPrice($asset);
        if($USDPrice != null){
        $TotalAmountInUSD=$amount * $USDPrice;
        $mode = $_POST['mode'] ?? '';
        // Validate required fields
        if (empty($asset) || empty($amount) || empty($mode) || empty($wallet_id)) {
            echo json_encode([
                "status" => "error",
                "message" => "All fields are required. Please fill in all fields.",
            ]);
            exit;
        }

        // Validate that amount is a positive number
        if (!is_numeric($amount) || floatval($amount) <= 0) {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid deposit amount. Please enter a valid number.",
            ]);
            exit;
        }

        // Call deposit function
        $result = walletDeposit($client,$amount,$asset,$wallet_id);

        if (isset($result['error'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Deposit failed: " . $result['error'],
            ]);
            exit;
        }
        // Convert BTC to USD (1 BTC = 28000 USD example rate)
        $usdRate = getCryptoPrice($asset);
        $usdAmount = $usdRate * $amount; // 1 USD = 1 PCT
        // Insert transaction into database
            $currentBalance=getUserBalance($loginUserId);
            $totalBalance=$currentBalance + $usdAmount;


        $insert = $h->insert('transactions')->values([
            'user_id' => $loginUserId,
            'wallet_to' => $coinbaseWallet[0]['wallet_id'],
            'transaction_id' => $result['address_id'],
            'transaction_type' => 'deposit',
            'amount' => $amount,
            'amount_in_usd' => $usdAmount,
            'currency' => $result['currency'],
            'sender_balance' => $totalBalance,
            'status' => 'requested'
        ])->run();
        if ($insert) {
            $system_transaction_id=generateSystemTransactionId($insert);
            $res= $h->table('transactions')->update([
                'system_transaction_id' =>$system_transaction_id])->where('id','=',$insert)->run();
            echo json_encode([
                "status" => "success",
                "transaction_id" => $insert,
                "message" => "Deposit request submitted successfully!",
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: Unable to process the transaction.",
            ]);
        }
    }
    }
}


if ($route == '/user/deposit/result/$transactionID') {
    $transactionDetail = $h->table('transactions')->select()->where('id','=',$transactionID)->fetchAll();
    $seo = array(
        'title' => 'Dashboard | PayCash Wallet',
        'description' => 'PayCash Wallet is your ultimate platform for managing finances effortlessly. Access your dashboard to send money, track transactions, and manage your account securely. Enjoy fast and secure digital payments with PayCash.',
        'keywords' => 'PayCash dashboard, digital wallet, money transfer, secure payments, transaction tracking, financial management, online payments'
    );
    echo $twig->render('public/transactions/results.twig', [
        'seo' => $seo,
        'transactionDetail' => $transactionDetail[0],
        'csrf' => set_csrf()
    ]);

}

if ($route == '/checkResult') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['check_result'])){
            $transID=$_POST['transID'];
        $transactionDetail = $h->table('transactions')->select()->where('id', '=', $transID)->fetchAll();
       if($transactionDetail[0]['status'] == 'completed'){
           echo "1";
           exit();
       }else{
           echo "0";
           exit();
       }
    }
    }

}

if ($route == '/user/transfer') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//        header('Content-Type: application/json'); // Set response type to JSON
        $recipientEmail= $_POST['email'] ?? '';
        $amount = $_POST['amount'] ?? '';
        if($amount > 0){
            $data=transferFunds($loginUserId, $recipientEmail, $amount);
            echo json_encode([
                "status" => $data['status'],
                "message" => $data['message'],
            ]);
            exit();
        }
    }
}
?>