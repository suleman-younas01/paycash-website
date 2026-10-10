<?php
require("config/env.php");

if ($route == '/dashboard') {
    $totalBalance=getUserBalance($loginUserId);
    $seo = array(
        'title' => 'Dashboard | PayCash Wallet',
        'description' => 'PayCash Wallet is your ultimate platform for managing finances effortlessly. Access your dashboard to send money, track transactions, and manage your account securely. Enjoy fast and secure digital payments with PayCash.',
        'keywords' => 'PayCash dashboard, digital wallet, money transfer, secure payments, transaction tracking, financial management, online payments'
    );
    echo $twig->render('public/home.twig', [
        'seo' => $seo,
        'Balance' => $totalBalance,
        'cryptocurrencies' => ['eth','usdc','weth'],
        'csrf' => set_csrf()
    ]);
}


