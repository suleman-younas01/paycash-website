<?php
require("config/env.php");

if ($route == '/login' || $route == '/'):

    // Check if already logged in
    if (isset($_SESSION['users'])):
        if ($_SESSION['users']['type'] == 'admin'):
            header("Location: /admin/dashboard");
        else:
            header("Location: /dashboard");
        endif;
    endif;

    // Handle POST request for login
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['email']) && isset($_POST['password'])):
            echo $response = userLogin($_POST['email'], $_POST['password'], 'users');
        endif;
        exit();
    } else {
        // Render the login page with SEO metadata and CSRF token
        $seo = array(
            'title' => 'Login',
            'description' => 'Access your PayCash account. Sign in to manage your payments, transfer money securely, track your transactions, and enjoy seamless financial services at your fingertips.',
            'keywords' => 'login, sign in, PayCash, mobile wallet, money transfer, secure payments, digital wallet, account login, online payments, financial services'
        );
        echo $twig->render('public/auth/login.twig', ['seo' => $seo, 'csrf' => set_csrf()]);
    }

endif;

if ($route == '/admin/login'):

    // Check if already logged in
//    if (isset($_SESSION['users'])):
//        if ($_SESSION['users']['type'] == 'admin'):
//            header("Location: /admin/dashboard");
//        else:
//            header("Location: /dashboard");
//        endif;
//    endif;

    // Handle POST request for login
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['email']) && isset($_POST['password'])):
            echo $response = userLogin($_POST['email'], $_POST['password'], 'users');
        endif;
        exit();
    } else {
        // Render the login page with SEO metadata and CSRF token
        $seo = array(
            'title' => 'Login',
            'description' => 'Access your PayCash account. Sign in to manage your payments, transfer money securely, track your transactions, and enjoy seamless financial services at your fingertips.',
            'keywords' => 'login, sign in, PayCash, mobile wallet, money transfer, secure payments, digital wallet, account login, online payments, financial services'
        );
        echo $twig->render('admin/login.twig', ['seo' => $seo, 'csrf' => set_csrf()]);
    }

endif;

if($route == '/signup'):
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //SIgnUP
            echo $SIGNUP_response=userRegister($_POST['first_name'], $_POST['last_name'],
                $_POST['email'], $_POST['phone'],$_POST['password'],'users');

        exit();
    }else {
        $seo = array(
            'title' => 'Register Your Account',
            'description' => 'Create your PayCash account to access secure payments, instant money transfers, and track your transactions with ease. Sign up today and simplify your finances.',

            'keywords' => 'sign up, register, create account, Paycash, new user'
        );
        echo $twig->render('public/auth/signUp.twig', ['seo' => $seo]);
    }
endif;
if($route == '/email-verification'):
    if(isset($_SESSION['users'])):
        header("Location: /dashboard");
        exit();
    endif;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(!empty($_POST['verify_code'])) {
            $email = $_POST['email'];
            $verify_code = $_POST['verify_code'];
            $check = $h->table('users')->select()->where('email', '=', $email)->where('verify_code', '=', $verify_code)->fetchAll();
            if (!empty($check)) {
                $verify_code = rand(0, 1000);
                $res = $h->table('users')->update(['verify_code' => $verify_code, 'status' => 'active'])->where('email', '=', $email)->run();
                if ($res){
                    echo '1';
                    exit();
                }else{
                    echo '0';
                    exit();
                }
            }else{
                echo '3';
                exit();
            }
        }else{
            echo '2';
            exit();
        }
    }else{
        $seo = array(
            'title' => 'Account Verification',
            'description' => 'Verify your PayCash account to enable secure transactions, protect your information, and access all features. Complete your account verification quickly and easily.',
            'keywords' => 'account verification, verify PayCash account, secure transactions, account security, identity verification, PayCash features, secure access',

        );
        echo $twig->render('public/auth/account_verification.twig', ['seo'=>$seo,'AccountVerify' => @$_SESSION['AccountVerify'], 'csrf'=>set_csrf()]);
    }
endif;
if($route == '/forget-password'):
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //LOGIN
        if(isset($_POST['email'])):
            echo $response=forgetPassword($_POST['email'], 'users');
        endif;
        exit();

    }else {
        $seo = array(
            'title' => 'Forgot Your Password? Reset It Here',
            'description' => 'Forgot your PayCash password? Reset it here quickly and securely to regain access to your account and continue managing your finances seamlessly.',
            'keywords' => 'forgot password, reset password, password recovery, PayCash password reset, secure account access, account recovery, regain access',
        );
        echo $twig->render('public/auth/forget_password.twig', ['seo' => $seo]);
    }
endif;

if(@$_SESSION['reset'] != ''):
    if($route == '/reset/login'):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //LOGIN
            if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['code'])):
            
                echo $response=setPassword($_POST['email'], $_POST['password'], $_POST['code'], 'users');
            endif;
            exit();

        }else {
            $seo = array(
                'title' => 'Reset Your Password',
                'description' => 'Reset your PayCash account password securely. Follow the steps to regain access and protect your account for a seamless experience.',
                'keywords' => 'reset password, forgot password, account recovery, PayCash password reset, secure account, regain access, password assistance',
            );

            echo $twig->render('public/auth/password-reset.twig', ['seo' => $seo, 'csrf'=>set_csrf(),'Reset' => @$_SESSION['reset']]);
        }
    endif;
endif;