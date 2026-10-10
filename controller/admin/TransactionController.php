<?php
require("config/env.php");
if ($route == '/admin/transactions') {
    $totalBalance=getUserBalance($loginUserId);
    $seo = array(
        'title' => 'Dashboard | PayCash Wallet',
        'description' => 'PayCash Wallet is your ultimate platform for managing finances effortlessly. Access your dashboard to send money, track transactions, and manage your account securely. Enjoy fast and secure digital payments with PayCash.',
        'keywords' => 'PayCash dashboard, digital wallet, money transfer, secure payments, transaction tracking, financial management, online payments'
    );
    echo $twig->render('admin/transactions/index.twig', [
        'seo' => $seo,
        'csrf' => set_csrf()
    ]);
}
if ($route == "/admin/getTransactionDetails") {
    $transactionId = $_POST['id'];

    // Fetch transaction details
    $transaction = $h->table('transactions')->select()->where('id', '=', $transactionId)->fetchAll();

    if ($transaction) {
        $transaction = $transaction[0]; // Extract first result

        // Fetch sender and receiver names
        $senderName = "N/A";
        $recipientName = "N/A";

        if (!empty($transaction['user_id'])) {
            $sender = $h->table('users')->select('fname')->where('id', '=', $transaction['user_id'])->fetchAll();
            $senderName = $sender ? $sender[0]['fname'] : "Unknown";
        }

        if (!empty($transaction['recipient_id'])) {
            $recipient = $h->table('users')->select('fname')->where('id', '=', $transaction['recipient_id'])->fetchAll();
            $recipientName = $recipient ? $recipient[0]['fname'] : "Unknown";
        }

        // Manage fields based on transaction type
        $transactionType = $transaction['transaction_type'];
        $response = [
            "transaction_id" => $transaction['transaction_id'], // Always show transaction ID
            "transaction_type" => $transactionType,
            "status" => ucfirst($transaction['status']),
            "created_at" => $transaction['created_at'],
            "amount" => number_format((float) $transaction['amount'], 2),
            "amount_in_usd" => number_format((float) $transaction['amount_in_usd'], 2),
            "currency" => strtoupper($transaction['currency']),
            "sender_name" => $senderName,
            "sender_balance" => number_format((float) $transaction['sender_balance'], 2), // Fix null issue
        ];

        // Only include recipient details for transfers
        if ($transactionType === "transfer") {
            $response["recipient_name"] = $recipientName;
            $response["recipient_balance"] = number_format((float) $transaction['recipient_balance'], 2);
        }

        // Only show system transaction ID for deposit & withdraw
        if ($transactionType == "deposit" || $transactionType == "withdraw") {
            $response["system_transaction_id"] = $transaction['system_transaction_id'];
        }

        echo json_encode($response);
    } else {
        echo json_encode(["error" => "Transaction not found"]);
    }
}


