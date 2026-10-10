<?php
require("config/env.php");

if ($route == '/') {
    $seo = array(
        'title' => 'Dashboard | PayCash Wallet',
        'description' => 'PayCash Wallet is your ultimate platform for managing finances effortlessly. Access your dashboard to send money, track transactions, and manage your account securely. Enjoy fast and secure digital payments with PayCash.',
        'keywords' => 'PayCash dashboard, digital wallet, money transfer, secure payments, transaction tracking, financial management, online payments'
    );
    echo $twig->render('website/website.twig', [
        'seo' => $seo,
        'csrf' => set_csrf()
    ]);
}
if ($route == '/privacy-policy') {
    $seo = array(
        'title' => 'Privacy Policy | PayCash Wallet',
        'description' => 'PayCash Wallet is your ultimate platform for managing finances effortlessly. Access your dashboard to send money, track transactions, and manage your account securely. Enjoy fast and secure digital payments with PayCash.',
        'keywords' => 'PayCash dashboard, digital wallet, money transfer, secure payments, transaction tracking, financial management, online payments'
    );
    echo $twig->render('website/privacy-policy.twig', [
        'seo' => $seo,
        'csrf' => set_csrf()
    ]);
}
if ($route == '/terms-conditions') {
    $seo = array(
        'title' => 'Privacy Policy | PayCash Wallet',
        'description' => 'PayCash Wallet is your ultimate platform for managing finances effortlessly. Access your dashboard to send money, track transactions, and manage your account securely. Enjoy fast and secure digital payments with PayCash.',
        'keywords' => 'PayCash dashboard, digital wallet, money transfer, secure payments, transaction tracking, financial management, online payments'
    );
    echo $twig->render('website/terms.twig', [
        'seo' => $seo,
        'csrf' => set_csrf()
    ]);
}

