<?php
require("config/env.php");
if($route == '/kyc'){
$seo = array(
    'title' => 'KYC | Biafra Security App',
    'description' => 'KYC',
    'keywords' => 'KYC'
);
echo $twig->render('public/kyc/index.twig', ['seo'=>$seo,'csrf'=>set_csrf()]);
}


if($route == '/kyc/submit'){
    $cardType=$_SESSION['userData']['cardType'];
    $token = GenerateToken();
    $API_response = AccontCreation($token, $cardType);

    unset($_SESSION['KycData']);
    $_SESSION['KycData'] = [
        'accessToken' => $token,
        'accountCreation' => $API_response
    ];
    $webUrl = $API_response->web->href;
    header("Location: $webUrl");

    $seo = array(
        'title' => 'Jumio | Biafra Security App',
        'description' => 'KYC',
        'keywords' => 'KYC'
    );
    //echo $twig->render('public/kyc/jumio.twig', ['seo'=>$seo,'csrf'=>set_csrf()]);
}
