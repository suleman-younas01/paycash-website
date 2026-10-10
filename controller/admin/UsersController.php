<?php
require("config/env.php");

if($route == '/admin/all_user'):
    $seo = array(
        'title' => 'Users',
        'description' => '',
        'keywords' => 'login'
    );
    echo $twig->render('admin/users/allUsers.twig', ['seo'=>$seo, 'csrf'=>set_csrf()]);
endif;

//    EDIT MODEL
if ($route == '/user/get_user'):
    if(isset($_POST['edit']) && !empty($_POST['edit'])){
        $id= $_POST['id'];
        $users = $h->table('users')->select()->where('id', '=', $id)->fetchAll();
        echo json_encode($users);
        exit();
    }
endif;

if($route == '/admin/active/users'):
    $seo = array(
        'title' => 'Active Users',
        'description' => '',
        'keywords' => 'login'
    );
    echo $twig->render('admin/users/active-Users.twig', ['seo'=>$seo, 'csrf'=>set_csrf()]);
endif;

if($route == '/admin/block/users'):
    $seo = array(
        'title' => 'Block Users',
        'description' => '',
        'keywords' => 'login'
    );
    echo $twig->render('admin/users/block-Users.twig', ['seo'=>$seo, 'csrf'=>set_csrf()]);
endif;