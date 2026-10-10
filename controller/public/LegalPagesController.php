<?php
require("config/env.php");
if($route == '/dashboard/privacy'){
    $seo = array(
        'title' => 'Privacy Policy',
        'description' => 'Privacy Policy',
        'keywords' => 'Privacy Policy'
    );
    echo $twig->render('public/legal_pages/privacy.twig', ['seo'=>$seo]);
}

if($route == '/dashboard/terms'){
    $seo = array(
        'title' => 'Terms & Conditions',
        'description' => 'Terms & Conditions',
        'keywords' => 'Terms & Conditions'
    );
    echo $twig->render('public/legal_pages/terms.twig', ['seo'=>$seo]);
}