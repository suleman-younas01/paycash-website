<?php
require("config/env.php");


if ($route == '/admin/dashboard') {
    $seo = array(
        'title' => 'Dashboard | Paycash',
        'description' => '"Access your PayCash account. Sign in to manage your payments, transfer money securely, track your transactions, and enjoy seamless financial services at your fingertips.',
        'keywords' => 'login, sign in, PayCash, mobile wallet, money transfer, secure payments, digital wallet, account login, online payments, financial services'
    );

    echo $twig->render('admin/dashboard.twig', [
        'seo' => $seo,

        'csrf' => set_csrf(),
    ]);
}
