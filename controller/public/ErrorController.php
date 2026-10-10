<?php
require("config/env.php");
$seo = array(
    'title' => '404 Page Not Found',
    'description' => 'Security App',
    'keywords' => 'security app',
    'url' => '/',
);
echo $twig->render('404.twig', ['seo'=>$seo]);