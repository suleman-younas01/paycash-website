<?php
require("config/env.php");

// Fetch transaction details
if ($route == '/transaction/detail/$transactionId') {

    if (!$transactionId) {
        die(json_encode(["error" => "Transaction ID is required."]));
    }

    // Fetch transaction details
    $transaction = $h->table('transactions')->select()->where('id', '=', $transactionId)->fetchAll();

    if (!$transaction) {
        die(json_encode(["error" => "Transaction not found."]));
    }

    // Fetch sender & recipient names separately
    if (!empty($transaction[0]['user_id'])) {
        $sender = $h->table('users')
            ->select('fname','lname')
            ->where('id', '=', $transaction[0]['user_id'])
            ->fetchAll();
        $transaction[0]['sender_name'] = $sender ? $sender[0]['fname'].' '.$sender[0]['lname'] : "Unknown";
    } else {
        $transaction[0]['sender_name'] = "N/A";
    }

    if (!empty($transaction[0]['recipient_id'])) {
        $recipient = $h->table('users')
            ->select('fname','lname')
            ->where('id', '=', $transaction[0]['recipient_id'])
            ->fetchAll();
        $transaction[0]['recipient_name'] = $recipient ? $recipient[0]['fname'].' '.$recipient[0]['lname'] : "Unknown";
    } else {
        $transaction[0]['recipient_name'] = "N/A";
    }

    echo $twig->render('public/pages/transaction_detail.twig', [
        'transaction' => $transaction[0],
        'loginUser' => $loginUser[0],
    ]);
}


if ($route == '/transactions') {
    global $h;

    // Step 1: Fetch transactions (without JOIN)
    $transactions = $h->table('transactions')
        ->select("*")
        ->where(function ($query) use ($loginUserId) {
            $query->where('user_id', '=', $loginUserId)
                ->orWhere('recipient_id', '=', $loginUserId);
        })
        ->orderBy("id", "desc")
        ->fetchAll();

    // Step 2: Fetch sender & recipient names separately
    foreach ($transactions as &$transaction) {
        // Fetch sender name
        if (!empty($transaction['user_id'])) {
            $sender = $h->table('users')
                ->select("fname")
                ->where("id", "=", $transaction['user_id'])
                ->fetchAll();

            $transaction['sender_name'] = $sender ? $sender[0]['fname'] : "Unknown";
        } else {
            $transaction['sender_name'] = "N/A";
        }

        // Fetch recipient name (only for transfers)
        if (!empty($transaction['recipient_id'])) {
            $recipient = $h->table('users')
                ->select("fname")
                ->where("id", "=", $transaction['recipient_id'])
                ->fetchAll();

            $transaction['recipient_name'] = $recipient ? $recipient[0]['fname'] : "Unknown";
        } else {
            $transaction['recipient_name'] = "N/A";
        }
    }

    $seo = [
        'title' => 'Transaction History',
        'description' => '',
        'keywords' => 'security app, transactions, money transfer'
    ];

    echo $twig->render('public/pages/transactions.twig', [
        'seo' => $seo,
        'loginUser' => $loginUser[0],
        'transaction_history' => $transactions,
        'csrf' => set_csrf(),
    ]);
}


