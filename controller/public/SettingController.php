<?php
require("config/env.php");


if($route == '/setting'){
    $seo = array(
        'title' => 'Setting',
        'description' => '',
        'keywords' => 'security app'
    );

    echo $twig->render('public/pages/setting.twig', [
        'seo' => $seo,
        'csrf' => set_csrf(),
    ]);
}
