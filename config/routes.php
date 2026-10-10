<?php
/////////////////////////
///////ZOTEC FRAMEWORK
//////admin@zotecsoft.com
////////////////////////
require_once("./config/main.php");
//PUBLIC Pages
get('/', 'controller/website/WebsiteController.php');
get('/privacy-policy', 'controller/website/WebsiteController.php');
get('/terms-conditions', 'controller/website/WebsiteController.php');
get('/admin/login', 'controller/public/AuthController.php');
get('/login', 'controller/public/AuthController.php');
post('/login', 'controller/public/AuthController.php');
get('/signup', 'controller/public/AuthController.php');
post('/signup', 'controller/public/AuthController.php');

get('/forget-password', 'controller/public/AuthController.php');
post('/forget-password', 'controller/public/AuthController.php');

//cronJob
get('/cronJob/currencies', 'controller/cronJob/currencies.php');

if(@$_SESSION['reset'] != ""):
    get('/reset/login', 'controller/public/AuthController.php');
    post('/reset/login', 'controller/public/AuthController.php');
endif;

//USER DASHBOARD
if(@$_SESSION['AccountVerify'] != ""):
    get('/email-verification', 'controller/public/AuthController.php');
    post('/email-verification', 'controller/public/AuthController.php');
endif;
if(@$_SESSION['users']['type'] == 'admin'):

    get('/admin/dashboard', 'controller/admin/adminDashboardController.php');
    get('/admin/currencies', 'controller/admin/CurrenciesController.php');
    get('/admin/terms&condition', 'controller/admin/Terms&ConditionController.php');
    post('/admin/terms&condition', 'controller/admin/Terms&ConditionController.php');
    get('/admin/privacyPolicy', 'controller/admin/Terms&ConditionController.php');
    post('/admin/privacyPolicy', 'controller/admin/Terms&ConditionController.php');
    get('/admin/appVersion', 'controller/admin/Terms&ConditionController.php');
    post('/admin/appVersion', 'controller/admin/Terms&ConditionController.php');
    get('/admin/about-us', 'controller/admin/AboutController.php');
    post('/admin/about-us', 'controller/admin/AboutController.php');

    get('/admin/faqs', 'controller/admin/FaqController.php');
    post('/admin/add/faqs', 'controller/admin/FaqController.php');
    get('/admin/api/faqs', 'controller/admin/FaqController.php');
    post('/admin/view/edit/faq', 'controller/admin/FaqController.php');
    post('/admin/update/faqs', 'controller/admin/FaqController.php');

    get('/admin/transactions', 'controller/admin/TransactionController.php');
    post('/admin/getTransactionDetails', 'controller/admin/TransactionController.php');


//All Users
    get('/admin/api', 'controller/admin/Api.php');
    post('/admin/user/statusChange/$id', 'controller/admin/Api.php');
    delete('/admin/user/delete/$id', 'controller/admin/Api.php');
    get('/admin/all_user', 'controller/admin/UsersController.php');
    post('/admin/get_user', 'controller/admin/UsersController.php');
    get('/admin/delete/user', 'controller/admin/UsersController.php');
    post('/admin/user_edit', 'controller/admin/UsersController.php');
    get('/admin/site_settings', 'controller/admin/SiteSettingsController.php');
//Active USers
    get('/admin/active/users', 'controller/admin/UsersController.php');
    get('/admin/active/users/api', 'controller/admin/Api.php');


    get('/admin/block/users', 'controller/admin/UsersController.php');
    get('/admin/block/users/api', 'controller/admin/Api.php');

    post('/admin/site_settings', 'controller/admin/SiteSettingsController.php');

    get('/admin/profile', 'controller/admin/ProfileController.php');
    post('/admin/fetch_profile', 'controller/admin/ProfileController.php');
    post('/admin/fetch_social_key_data', 'controller/admin/ProfileController.php');
    post('/admin/profile', 'controller/admin/ProfileController.php');
    get('/admin/profile/security', 'controller/admin/ProfileController.php');
    post('/admin/profile/password_change', 'controller/admin/ProfileController.php');

endif;
 post('/callback', 'controller/user/DepositController.php');
if(@$_SESSION['users']['type'] == 'user'):
//    Paycash Route


  post('/user/withdraw', 'controller/user/withdrawalController.php');
  get('/callback', 'controller/user/DepositController.php');
  get('/transactions', 'controller/user/TransactionController.php');
  get('/transaction/detail/$transactionId', 'controller/user/TransactionController.php');
    get('/user/deposit/result/$transactionID', 'controller/user/DepositController.php');
    get('/user/callback', 'controller/user/DepositController.php');
    get('/user/create-webhook', 'controller/user/DepositController.php');
    get('/user/create-stellarAccount', 'controller/user/DepositController.php');
    get('/dashboard', 'controller/user/HomeController.php');
    get('/user/deposit', 'controller/user/DepositController.php');
    post('/user/transfer', 'controller/user/DepositController.php');
    post('/user/deposit', 'controller/user/DepositController.php');
    post('/checkResult', 'controller/user/DepositController.php');
    get('/wallet', 'controller/public/walletController.php');
//    get('/transactions', 'controller/public/TransactionController.php');
    get('/account', 'controller/public/AccountController.php');
    get('/setting', 'controller/public/SettingController.php');

    get('/profile', 'controller/public/AccountController.php');
    get('/about', 'controller/public/AccountController.php');
    get('/appVersion', 'controller/public/AccountController.php');
    get('/bankDetails', 'controller/public/AccountController.php');
    get('/changePassword', 'controller/public/AccountController.php');
    post('/changePassword/post', 'controller/user/ProfileSettingsController.php');
    get('/faq', 'controller/public/AccountController.php');
    get('/kyc', 'controller/public/AccountController.php');
    get('/privacy', 'controller/public/AccountController.php');
    get('/termsCondition', 'controller/public/AccountController.php');
    get('/upload-bankDetails', 'controller/public/AccountController.php');

    get('/profile', 'controller/public/ProfileController.php');
    post('/submit/profile', 'controller/public/ProfileController.php');


//    OtherRoutes
    get('/payment-gateway/$formName', 'controller/public/payment_gateways/PaymentGatewayController.php');
    get('/payment-gateway/$formName/$lastInsertId', 'controller/public/payment_gateways/PaymentGatewayController.php');
    get('/payment-gateway/$lastInsertId/$event/$category', 'controller/public/payment_gateways/PaymentGatewayController.php');
    get('/paypal/$formName', 'controller/public/payment_gateways/PaymentGatewayController.php');
    get('/now-payment/$formName', 'controller/public/payment_gateways/PaymentGatewayController.php');
    post('/otherBank', 'controller/public/payment_gateways/PaymentGatewayController.php');
    post('/get_bank_data', 'controller/public/payment_gateways/PaymentGatewayController.php');
    post('/paypalController', 'controller/public/payment_gateways/PaymentGatewayController.php');
    post('/stripeController', 'controller/public/payment_gateways/PaymentGatewayController.php');
    post('/NowPayment', 'controller/public/payment_gateways/PaymentGatewayController.php');
    post('/CreateTransactionNowPayment', 'controller/public/payment_gateways/PaymentGatewayController.php');
    post('/ipn', 'controller/public/payment_gateways/PaymentGatewayController.php');

    get('/kyc', 'controller/public/KycController.php');
    get('/kyc/submit', 'controller/public/KycController.php');
    get('/success', 'controller/public/kyc/Success.php');
    get('/failed', 'controller/public/kyc/Failed.php');
//events
    get('/events', 'controller/public/EventController.php');
    get('/events/purchased', 'controller/public/EventController.php');
    get('/event/details/$id', 'controller/public/EventController.php');
//event Form
    get('/event/form/$slug', 'controller/public/EventController.php');
    get('/update/event/form/$id', 'controller/public/EventController.php');
    // event form Post
    post('/submit/event/form', 'controller/public/EventController.php');
    post('/submit/update/event/form', 'controller/public/EventController.php');

get('/applications', 'controller/public/HomeController.php');
get('/application/$tableName', 'controller/public/Application_detail.php');
get('/applications/accepted', 'controller/public/Application_detail.php');

get('/apply-terms', 'controller/public/HomeController.php');
get('/forms', 'controller/public/HomeController.php');
get('/json/personal-info', 'json/personal-info.json');

//Partial Forms Submission Routes
post('/submit/personal-info', 'controller/partial_forms/Personal_info.php');
post('/submit/contact-info', 'controller/partial_forms/Contact_info.php');
post('/submit/identification-details', 'controller/partial_forms/Identification_details.php');
post('/submit/parents-info', 'controller/partial_forms/Parents_info.php');
post('/submit/homeland-info', 'controller/partial_forms/Homeland_info.php');
post('/submit/social-info', 'controller/partial_forms/Social_info.php');
post('/submit/languages-info', 'controller/partial_forms/Languages_info.php');
post('/submit/photo-signature-info', 'controller/partial_forms/Photo_signature_info.php');

//Partial Forms Submission Routes
post('/d-submit/personal-info', 'controller/partial_forms_D/Personal_info.php');
post('/d-submit/contact-info', 'controller/partial_forms_D/Contact_info.php');
post('/d-submit/identification-details', 'controller/partial_forms_D/Identification_details.php');
post('/d-submit/guardian-1', 'controller/partial_forms_D/Guardian-1.php');
post('/d-submit/guardian-2', 'controller/partial_forms_D/Guardian-2.php');
post('/d-submit/parents-info', 'controller/partial_forms_D/Parents_info.php');
post('/d-submit/homeland-info', 'controller/partial_forms_D/Homeland_info.php');
post('/d-submit/social-info', 'controller/partial_forms_D/Social_info.php');
post('/d-submit/languages-info', 'controller/partial_forms_D/Languages_info.php');
post('/d-submit/photo-signature-info', 'controller/partial_forms_D/Photo_signature_info.php');

//type-A
get('/forms/type-a/step-1', 'controller/public/TypeAController.php');
get('/forms/type-a/step-2', 'controller/public/TypeAController.php');
get('/forms/type-a/step-3', 'controller/public/TypeAController.php');
get('/forms/type-a/step-4', 'controller/public/TypeAController.php');
get('/forms/type-a/step-5', 'controller/public/TypeAController.php');
get('/forms/type-a/step-6', 'controller/public/TypeAController.php');
get('/forms/type-a/step-7', 'controller/public/TypeAController.php');
get('/forms/type-a/step-8', 'controller/public/TypeAController.php');
get('/forms/type-a/step-9', 'controller/public/TypeAController.php');

//type-B
get('/forms/type-b/step-1', 'controller/public/TypeBController.php');
get('/forms/type-b/step-2', 'controller/public/TypeBController.php');
get('/forms/type-b/step-3', 'controller/public/TypeBController.php');
get('/forms/type-b/step-4', 'controller/public/TypeBController.php');
get('/forms/type-b/step-5', 'controller/public/TypeBController.php');
get('/forms/type-b/step-6', 'controller/public/TypeBController.php');
get('/forms/type-b/step-7', 'controller/public/TypeBController.php');
get('/forms/type-b/step-8', 'controller/public/TypeBController.php');
get('/forms/type-b/step-9', 'controller/public/TypeBController.php');
//type-C
get('/forms/type-c/step-1', 'controller/public/TypeCController.php');
get('/forms/type-c/step-2', 'controller/public/TypeCController.php');
get('/forms/type-c/step-3', 'controller/public/TypeCController.php');
get('/forms/type-c/step-4', 'controller/public/TypeCController.php');
get('/forms/type-c/step-5', 'controller/public/TypeCController.php');
get('/forms/type-c/step-6', 'controller/public/TypeCController.php');
get('/forms/type-c/step-7', 'controller/public/TypeCController.php');
get('/forms/type-c/step-8', 'controller/public/TypeCController.php');
get('/forms/type-c/step-9', 'controller/public/TypeCController.php');
//type-D1
get('/forms/type-d1/step-1', 'controller/public/TypeDController.php');
get('/forms/type-d1/step-2', 'controller/public/TypeDController.php');
get('/forms/type-d1/step-3', 'controller/public/TypeDController.php');
get('/forms/type-d1/step-4', 'controller/public/TypeDController.php');
get('/forms/type-d1/step-5', 'controller/public/TypeDController.php');
get('/forms/type-d1/step-6', 'controller/public/TypeDController.php');
get('/forms/type-d1/step-7', 'controller/public/TypeDController.php');
get('/forms/type-d1/step-8', 'controller/public/TypeDController.php');

//type-D2
get('/forms/type-d2/step-1', 'controller/public/TypeD2Controller.php');
get('/forms/type-d2/step-2', 'controller/public/TypeD2Controller.php');
get('/forms/type-d2/step-3', 'controller/public/TypeD2Controller.php');
get('/forms/type-d2/step-4', 'controller/public/TypeD2Controller.php');
get('/forms/type-d2/step-5', 'controller/public/TypeD2Controller.php');
get('/forms/type-d2/step-6', 'controller/public/TypeD2Controller.php');
get('/forms/type-d2/step-7', 'controller/public/TypeD2Controller.php');
get('/forms/type-d2/step-8', 'controller/public/TypeD2Controller.php');
//type-D3
get('/forms/type-d3/step-1', 'controller/public/TypeD3Controller.php');
get('/forms/type-d3/step-2', 'controller/public/TypeD3Controller.php');
get('/forms/type-d3/step-3', 'controller/public/TypeD3Controller.php');
get('/forms/type-d3/step-4', 'controller/public/TypeD3Controller.php');
get('/forms/type-d3/step-5', 'controller/public/TypeD3Controller.php');
get('/forms/type-d3/step-6', 'controller/public/TypeD3Controller.php');
get('/forms/type-d3/step-7', 'controller/public/TypeD3Controller.php');
get('/forms/type-d3/step-8', 'controller/public/TypeD3Controller.php');
get('/forms/type-d3/step-9', 'controller/public/TypeD3Controller.php');


//type-d4
get('/forms/type-d4/step-1', 'controller/public/TypeD4Controller.php');
get('/forms/type-d4/step-2', 'controller/public/TypeD4Controller.php');
get('/forms/type-d4/step-3', 'controller/public/TypeD4Controller.php');
get('/forms/type-d4/step-4', 'controller/public/TypeD4Controller.php');
get('/forms/type-d4/step-5', 'controller/public/TypeD4Controller.php');
get('/forms/type-d4/step-6', 'controller/public/TypeD4Controller.php');
get('/forms/type-d4/step-7', 'controller/public/TypeD4Controller.php');
get('/forms/type-d4/step-8', 'controller/public/TypeD4Controller.php');
get('/forms/type-d4/step-9', 'controller/public/TypeD4Controller.php');

//type-d5
get('/forms/type-d5/step-1', 'controller/public/TypeD5Controller.php');
get('/forms/type-d5/step-2', 'controller/public/TypeD5Controller.php');
get('/forms/type-d5/step-3', 'controller/public/TypeD5Controller.php');
get('/forms/type-d5/step-4', 'controller/public/TypeD5Controller.php');
get('/forms/type-d5/step-5', 'controller/public/TypeD5Controller.php');
get('/forms/type-d5/step-6', 'controller/public/TypeD5Controller.php');
get('/forms/type-d5/step-7', 'controller/public/TypeD5Controller.php');
get('/forms/type-d5/step-8', 'controller/public/TypeD5Controller.php');
get('/forms/type-d5/step-9', 'controller/public/TypeD5Controller.php');


get('/dashboard/privacy', 'controller/public/LegalPagesController.php');
get('/dashboard/terms', 'controller/public/LegalPagesController.php');


//END OF USER DASHBOARD


endif;


//END OF ADMIN API

get('/logout', 'controller/LogoutController.php');
//404 PAGE
any('/404','controller/public/ErrorController.php');
