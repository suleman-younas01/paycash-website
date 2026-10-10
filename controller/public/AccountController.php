<?php
require("config/env.php");


if($route == '/account'){
    $seo = array(
        'title' => 'Account',
        'description' => '',
        'keywords' => 'security app'
    );

    echo $twig->render('public/pages/accounts.twig', [
        'seo' => $seo,
        'csrf' => set_csrf(),
    ]);
}

if($route == '/profile'){
    $seo = array(
        'title' => 'Account',
        'description' => '',
        'keywords' => 'security app'
    );
    $UserInfo=$h->table('users')->select()->where('id', '=', $loginUserId)->fetchAll();
    echo $twig->render('public/pages/accounts/profile.twig', [
        'seo' => $seo,
        'UserInfo' => $UserInfo,
        'csrf' => set_csrf(),
    ]);
}
if($route == '/privacy'){
    $privacy=$h->table('privacy_policy')->select()->where('id', '=', 1)->fetchAll();
    $seo = array(
        'title' => 'Account',
        'description' => '',
        'keywords' => 'security app'
    );

    echo $twig->render('public/pages/accounts/privacy.twig', [
        'seo' => $seo,
        'privacy' => $privacy,
        'csrf' => set_csrf(),
    ]);
}

if($route == '/upload-bankDetails'){
    $seo = array(
        'title' => 'Account',
        'description' => '',
        'keywords' => 'security app'
    );

    echo $twig->render('public/pages/accounts/upload-BankDetails.twig', [
        'seo' => $seo,
        'csrf' => set_csrf(),
    ]);
}
if($route == '/termsCondition'){
    $terms=$h->table('term&conditions')->select()->where('id', '=', 1)->fetchAll();
    $seo = array(
        'title' => 'Account',
        'description' => '',
        'keywords' => 'security app'
    );

    echo $twig->render('public/pages/accounts/termsCondition.twig', [
        'seo' => $seo,
        'terms' => $terms,
        'csrf' => set_csrf(),
    ]);
}
if($route == '/kyc'){
    $seo = array(
        'title' => 'Account',
        'description' => '',
        'keywords' => 'security app'
    );

    echo $twig->render('public/pages/accounts/kyc.twig', [
        'seo' => $seo,
        'csrf' => set_csrf(),
    ]);
}
if ($route == '/faq') {
    $faqs = $h->table('faqs')->select()->fetchAll();
    $seo = [
        'title' => 'Account | Faq',
        'description' => '',
        'keywords' => 'security app',
    ];

    echo $twig->render('public/pages/accounts/faq.twig', [
        'seo' => $seo,
        'csrf' => set_csrf(),
        'faqs' => $faqs,
    ]);
}

if($route == '/changePassword'){
    $seo = array(
        'title' => 'Account',
        'description' => '',
        'keywords' => 'security app'
    );

    echo $twig->render('public/pages/accounts/changePassword.twig', [
        'seo' => $seo,
        'csrf' => set_csrf(),
    ]);
}

if($route == '/bankDetails'){
    $seo = array(
        'title' => 'Account',
        'description' => '',
        'keywords' => 'security app'
    );

    echo $twig->render('public/pages/accounts/bankDetails.twig', [
        'seo' => $seo,
        'csrf' => set_csrf(),
    ]);
}
if($route == '/appVersion'){
    $version=$h->table('app_version')->select()->where('id', '=', 1)->fetchAll();
    $seo = array(
        'title' => 'Account',
        'description' => '',
        'keywords' => 'security app'
    );

    echo $twig->render('public/pages/accounts/appVersion.twig', [
        'seo' => $seo,
        'version' => $version,
        'csrf' => set_csrf(),
    ]);
}
if($route == '/about'){
    $about=$h->table('about_us')->select()->where('id', '=', 1)->fetchAll();
    $seo = array(
        'title' => 'Account',
        'description' => '',
        'keywords' => 'security app'
    );

    echo $twig->render('public/pages/accounts/about.twig', [
        'seo' => $seo,
        'about' => $about,
        'csrf' => set_csrf(),
    ]);
}
