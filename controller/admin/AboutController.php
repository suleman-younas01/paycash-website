<?php
require("config/env.php");


if ($route == '/admin/about-us'):

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//LOGIN
        if (isset($_POST['about_us'])):
            $about_us = $_POST['about_us'];
            $update = $h->update('about_us')->values(['about_us' => $about_us,])->where('id', '=', '1')->run();
            if ($update) {
                echo "1";
                exit();
            }

        endif;

    } else {
        $seo = array(
            'title' => 'About Us | Paycash',
            'description' => 'Change Your Site Logo, Site Name and Favicon.',
            'keywords' => 'Site Settings, Settings'
        );
        $about = $h->table('about_us')->select()->where('id', '=', '1')->fetchAll();

        echo $twig->render('admin/about_us/about_us.twig', ['seo' => $seo, 'about' => $about, 'csrf' => set_csrf()]);
    }
endif;
