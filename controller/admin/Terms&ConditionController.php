<?php
require("config/env.php");

if($route == '/admin/terms&condition'):

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //LOGIN
        if(isset($_POST['content'])):
            $content = $_POST['content'];
                $update = $h->update('term&conditions')->values(['content' => $content,])->where('id','=','1')->run();
                if ($update){
                    echo "1";
                    exit();
                }

        endif;

    }else{
        $seo = array(
            'title' => 'Terms & Condition | Paycash',
            'description' => 'Change Your Site Logo, Site Name and Favicon.',
            'keywords' => 'Site Settings, Settings'
        );
        $TermsCondition=$h->table('term&conditions')->select()->where('id', '=', '1')->fetchAll();
        $terms = array(
            'content' => $TermsCondition[0]['content'],
        );
        echo $twig->render('admin/app_policies/terms_condition.twig', ['seo'=>$seo,'terms' => $terms, 'csrf'=>set_csrf()]);
    }
endif;


if($route == '/admin/privacyPolicy'):

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //LOGIN
        if(isset($_POST['content'])):
            $content = $_POST['content'];
            $update = $h->update('privacy_policy')->values(['content' => $content,])->where('id','=','1')->run();
            if ($update){
                echo "1";
                exit();
            }

        endif;

    }else{
        $seo = array(
            'title' => 'Privacy Policy | Paycash',
            'description' => 'Change Your Site Logo, Site Name and Favicon.',
            'keywords' => 'Site Settings, Settings'
        );
        $privacy=$h->table('privacy_policy')->select()->where('id', '=', '1')->fetchAll();

        echo $twig->render('admin/app_policies/privacy_policy.twig', ['seo'=>$seo,'privacy' => $privacy, 'csrf'=>set_csrf()]);
    }
endif;

if($route == '/admin/appVersion'):

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //LOGIN
        if(isset($_POST['version'])):
            $version = $_POST['version'];
            $update = $h->update('app_version')->values(['version' => $version,])->where('id','=','1')->run();
            if ($update){
                echo "1";
                exit();
            }

        endif;

    }else{
        $seo = array(
            'title' => 'App Version | Paycash',
            'description' => 'Change Your Site Logo, Site Name and Favicon.',
            'keywords' => 'Site Settings, Settings'
        );
        $version=$h->table('app_version')->select()->where('id', '=', '1')->fetchAll();

        echo $twig->render('admin/app_policies/app_version.twig', ['seo'=>$seo,'version' => $version, 'csrf'=>set_csrf()]);
    }
endif;