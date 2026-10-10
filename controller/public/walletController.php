<?php
require("config/env.php");


if($route == '/wallet'){
    $seo = array(
        'title' => 'Wallet',
        'description' => '',
        'keywords' => 'security app'
    );

    echo $twig->render('public/pages/wallet.twig', [
        'seo' => $seo,
        'csrf' => set_csrf(),
    ]);
}
