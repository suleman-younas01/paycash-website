<?php
require("config/env.php");

if($route == '/profile'){
    $seo = array(
        'title' => 'Profile',
        'description' => 'Biafra',
        'keywords' => 'Biafra security app'
    );

    $users= $h->table('users')->select()->where('id', '=', $loginUserId)->fetchAll();

    echo $twig->render('public/profile.twig', ['seo' => $seo, 'users' => $users, 'csrf' => set_csrf()]);
}
if($route == '/submit/profile'){
    @$fname= $_POST['fname'];
    @$lname= $_POST['lname'];
    @$password= $_POST['password'];
     @$confirm_password = $_POST['confirm_password'];

    if(!empty($password)){
        if(!empty($confirm_password)){
            if($password == $confirm_password){
                $password = password_hash($password, PASSWORD_DEFAULT);
                $res= $h->table('users')->update(['fname' => $fname, 'lname' => $lname, 'password' => $password])->where('id','=',$loginUserId)->run();
                echo "1";
                exit();
            }else{
                echo "3";
                exit();
            }
        }else{
            echo "2";
            exit();
        }
    }
    if(empty($password)){
        $res= $h->table('users')->update(['fname' => $fname, 'lname' => $lname])->where('id','=',$loginUserId)->run();
        echo "1";
        exit();
    }
}