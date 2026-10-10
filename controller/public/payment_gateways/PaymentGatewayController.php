<?php
require("config/env.php");
if($route == '/payment-gateway/$formName' || $route == '/payment-gateway/$lastInsertId/$event/$category'){
    $seo = array(
        'title' => 'Payment Method',
        'description' => 'Biafra',
        'keywords' => 'Biafra security app'
    );
    $banks = $h->table('bank')->select()->where('status', '=', 'active')->fetchAll();
unset($_SESSION['nowPaymentData']);
if(@$category){
    $event = $h->table('event')->select()->where('slug', '=',$event)->fetchAll();
    $decision='1';
    $eventCategories= explode(',',$event[0]['event_ticket_category']);
    $eventPrice= explode(',',$event[0]['event_price']);
    foreach ($eventCategories as $index => $cat){
        $category = str_replace("%20", " ", $category);
        if($cat == $category){
            $price=$eventPrice[$index];
            $price = str_replace("$", "", $price);
        }
    }
    $table_name = 'event_form';
    $path = '/events/purchased';
}else{
    $permission = $h->table('permissions')->select()->where('path', '=',"/".$formName)->fetchAll();
    $tableName=$permission[0]['table_name'];

    $checkRecord=$h->table($tableName)->select()->where('user_id','=',$loginUserId)->fetchAll();
    if(empty($checkRecord)){
        $newInsert= $h->insert($tableName)->values(['user_id' => $loginUserId,'payment' => 'no','status' => 'save'])->run();
        $FormDetails=$h->table('permissions')->select()->where('table_name','=',$tableName)->fetchAll();

        $lastInsertId = $newInsert;
    }else{
        $lastInsertId=$checkRecord[0]['id'];
        if($checkRecord[0]['payment'] =='yes'){
            header("Location: /forms/type-".strtolower($permission[0]['type'])."/step-1");
            $path="/forms/type-".strtolower($permission[0]['type'])."/step-1";
        }
    }
    $decision='0';
    $price=$permission[0]['price'];
    $table_name =$permission[0]['table_name'];
    $path ="/forms/type-".strtolower($permission[0]['type'])."/step-1";
}
    echo $twig->render('public/payment_gateways/paymentGateway.twig', ['seo' => $seo,'path' => $path,'lastInsertId' => @$lastInsertId,'formName' => @$formName,'table_name' => @$table_name,'decision' => $decision,'price' => @$price,'banks' => $banks, 'csrf' => set_csrf()]);

}
if($route == '/get_bank_data'){
if (isset($_POST['bank_id']) && !empty($_POST['bank_id'])) {
    $id = $_POST['bank_id'];
    $GetBank= $h->table('bank')->select()->where('id', '=', $id)->fetchAll();
    echo json_encode($GetBank[0]);
    exit();
}
}
if($route == '/otherBank'){
    $bank_name = $_POST['bank_name'];
    $transaction_id= $_POST['transaction_id'];
    $des= base64_encode($_POST['des']);
    $price= $_POST['price'];
    $path= $_POST['path'];
    $tableName = $_POST["tableName"];
    $decision= $_POST['decision'];
    $LastInsertId= $_POST['LastInsertId'];
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] == UPLOAD_ERR_OK) {
        $proof=UploadIMGBYAPI('proof','uploads/other_bank/');
        @$proofArr = json_decode($proof);
        @$proof = $proofArr->imageName;
    }else{
        @$proof='null';
    }
    if(!empty($bank_name) && !empty($price) && !empty($path) && !empty($tableName) &&  !empty($LastInsertId) &&  !empty($transaction_id)){
        if($proof !='null'){
            $year=date('y');
            if($decision != '0'){
                $res= $h->update('event_form')->values(['transaction_id' => $transaction_id,'payment_type' => $bank_name,'total_price' => $price,'proof' => $proof,'payment_des' => $des, 'payment' => 'yes'])->where('id', '=', $LastInsertId)->run();
                $EventFormDetails=$h->table('event_form')->select()->where('id','=',$LastInsertId)->fetchAll();

                $EventDetails=$h->table('event')->select()->where('id','=',$EventFormDetails['event_id'])->fetchAll();

              //  $activity= $h->table('activity_log')->insert(['user_id' => $loginUserId, 'username' => $loginUserName,'ip_address' => $ip_address,'country' => $get_country,'user_type' => $loginUserType,'activity' => $loginUserName. ' Submit the Payment of '. $EventDetails['event_name'].' Where id is '.$LastInsertId])->execute();

                $ticketNo="#".sprintf("%'.04d", $LastInsertId)."-".$year;
                $emailTitle="Ticket No:". $ticketNo;
                $finalPrice=$price;
//                include "views/email-template/purchase.php";
//                mailSender(ADMIN_EMAIL,$loginUser['email'],'Thank you for purchase - '.SITE_NAME,$message,$mail);
                $path='/user/event/tickets';
            }else {
                $sql= $h->update($tableName)->values(['transaction_id' => $transaction_id,'payment_type' => $bank_name,'total_price' => $price,'proof' => $proof,'payment_des' => $des,'payment' => 'yes','status' => 'save'])->where('id', '=', $LastInsertId)->run();
                $FormDetails=$h->table('permissions')->select()->where('table_name','=',$tableName)->fetchAll();
             //   $activity= $h->table('activity_log')->insert(['user_id' => $loginUserId, 'username' => $loginUserName,'ip_address' => $ip_address,'country' => $get_country,'user_type' => $loginUserType,'activity' => $loginUserName. ' Submit the Payment of '. $FormDetails['form_name'].' Where form id is BSA-'.sprintf("%'.04d", $LastInsertId).'-'.date('y')])->execute();

                $ticketNo="BSA-".sprintf("%'.04d", $LastInsertId)."-".$year;

                if($tableName !='citizen_registration'){
                    $emailTitle="Form No:". $ticketNo;
                }else{
                    $emailTitle="";
                }
                $finalPrice=$price;
//                include "views/email-template/purchase.php";
//                mailSender(ADMIN_EMAIL,$loginUser['email'],'Thank you for purchase- '.SITE_NAME,$message,$mail);
            }
            http_response_code(200);
            echo json_encode(array("statusCode" => 200, "path"=>$path));
            exit();
        }
    }
}
if($route == '/paypalController'){
    if(isset($_POST['purchase_paypal'])) {
        $finalPrice = $_POST['finalPrice'];
        $balance_transaction= $_POST['payment_id'];
        $path= $_POST['path'];
        $status= $_POST['status'];
        $tableName= $_POST['tableName'];
        $decision= $_POST['decision'];
        $LastInsertId= $_POST['LastInsertId'];
        if (!empty($finalPrice)) {
            if($status == 'COMPLETED'){
                $res=$h->insert('transactions')->values(['user_id' => $loginUserId,'payment_type' => 'Paypal','transaction_id'=>$balance_transaction,'total_price' => $finalPrice, 'status'=>'COMPLETED'])->run();
                if($res){
//                $res=$h->table($tableName)->insert(['user_id' => $loginUserId,'transaction_id'=>$balance_transaction,'status'=>'pending'])->execute();
                    $year=date('y');
                    if($decision != '0'){
                        $res= $h->update('event_form')->values(['transaction_id' => $balance_transaction,'payment_type' => 'Paypal','total_price' => $finalPrice,'payment' => 'yes'])->where('id', '=', $LastInsertId)->run();
                        $EventFormDetails=$h->table('event_form')->select()->where('id','=',$LastInsertId)->one();
                        $EventDetails=$h->table('event')->select()->where('id','=',$EventFormDetails['event_id'])->fetchAll();
//                        $activity= $h->table('activity_log')->insert(['user_id' => $loginUserId, 'username' => $loginUserName,'ip_address' => $ip_address,'country' => $get_country,'user_type' => $loginUserType,'activity' => $loginUserName. ' Submit the Payment of '. $EventDetails['event_name'].' Where id is '.$LastInsertId])->execute();

                        $ticketNo="#".sprintf("%'.04d", $LastInsertId)."-".$year;
                        $emailTitle="Ticket No:". $ticketNo;
                        include "views/email-template/purchase.php";
                        mailSender($env['ADMIN_EMAIL'],$loginUser['email'],'Thank you for purchase - '.$env['SITE_NAME'],$message,$mail);

                        $path='/events/purchased';
                    }else{
                        $sql= $h->update($tableName)->values(['transaction_id' => $balance_transaction,'payment_type' => 'Paypal','total_price' => $finalPrice,'payment' => 'yes','status' => 'save'])->where('id', '=', $LastInsertId)->run();
                        $FormDetails=$h->table('permissions')->select()->where('table_name','=',$tableName)->fetchAll();
//                        $activity= $h->table('activity_log')->insert(['user_id' => $loginUserId, 'username' => $loginUserName,'ip_address' => $ip_address,'country' => $get_country,'user_type' => $loginUserType,'activity' => $loginUserName. ' Submit the Payment of '. $FormDetails['form_name'].' Where form id is BSA-'.sprintf("%'.04d", $LastInsertId).'-'.date('y')])->execute();

//                   $sql = "INSERT INTO $tableName (user_id, transaction_id, payment_type, status) VALUES (?,?,?,?)";
//                   $CONN->prepare($sql)->execute([$loginUserId, $balance_transaction,'Paypal','pending']);
//                   $id = $CONN->lastInsertId();
                        $ticketNo="BSA-".sprintf("%'.04d", $LastInsertId)."-".$year;
                        if($tableName !='citizen_registration'){
                            $emailTitle="Form No:". $ticketNo;
                        }else{
                            $emailTitle="";
                        }
//                   include "views/email-template/purchase.php";
//                   mailSender(ADMIN_EMAIL,$loginUser['email'],'Thank you for purchase- '.SITE_NAME,$message,$mail);

//                   $path="/update/".$path."/".$LastInsertId;
                    }
                    http_response_code(200);
                    echo json_encode(array("statusCode" => 200, "path"=>$path));
                    exit();
                }else{
                    http_response_code(202);
                    echo json_encode(array("statusCode" => 202, "message"=>"Something went wrong!"));
                    exit();
                }
            }else{
                http_response_code(202);
                echo json_encode(array("statusCode" => 202, "message"=>"Something went wrong!"));
                exit();
            }
        }
    }
}
if($route == '/stripeController'){
    require 'vendor/autoload.php';
    $stripeSecretKey = $env['STRIPE_SECRET_KEY'] ?? '';
    \Stripe\Stripe::setApiKey($stripeSecretKey);

    $token = $_POST['stripeToken'];
    $price= $_POST['price'];
    $path= $_POST['path'];
    $tableName = $_POST["tableName"];
    $decision= $_POST['decision'];
    $LastInsertId= $_POST['LastInsertId'];
    $name=$loginUser[0]['fname'].' '.$loginUser[0]['lname'];
    $year=date('y');
    if($decision != '0'){
        $paymentNo="#".sprintf("%'.04d", $LastInsertId)."-".$year;
    }else{
        $paymentNo="BSA-".sprintf("%'.04d", $LastInsertId)."-".$year;
    }

    if(!empty($token)){
        $finalCent="0";
        $finalCent+= $price * 100;
        try {
            $stripe = new \Stripe\StripeClient($stripeSecretKey);
            $charge = $stripe->charges->create([
                'amount' => $finalCent,
                'currency' => 'usd',
                'description' => $paymentNo,
                'source' => $token,
            ]);
            $id = $charge['id'];
            $amount = $charge['amount'];
            $balance_transaction = $charge['balance_transaction'];
            $currency = $charge['currency'];
            $status = $charge['status'];

            if($status == 'succeeded'){
                $res=$h->insert('transactions')->values(['user_id' => $loginUserId,'payment_type' => 'Stripe','transaction_id'=>$balance_transaction,'total_price' => $price, 'status'=>'COMPLETED'])->run();

                if($decision != '0'){
                    $res= $h->update('event_form')->values(['transaction_id' => $balance_transaction,'payment_type' => 'Stripe','total_price' => $price,'payment' => 'yes'])->where('id', '=', $LastInsertId)->run();
                    $EventFormDetails=$h->table('event_form')->select()->where('id','=',$LastInsertId)->fetchAll();
                    $EventDetails=$h->table('event')->select()->where('id','=',$EventFormDetails[0]['event_id'])->fetchAll();
//                    $activity= $h->table('activity_log')->insert(['user_id' => $loginUserId, 'username' => $loginUserName,'ip_address' => $ip_address,'country' => $get_country,'user_type' => $loginUserType,'activity' => $loginUserName. ' Submit the Payment of '. $EventDetails['event_name'].' Where id is '.$LastInsertId])->execute();

                    $ticketNo="#".sprintf("%'.04d", $LastInsertId)."-".$year;
                    $emailTitle="Ticket No:". $ticketNo;
                    $finalPrice=$price;
                    include "views/email-template/purchase.php";
                    mailSender($env['ADMIN_EMAIL'],$loginUser[0]['email'],'Thank you for purchase - '.$env['SITE_NAME'],$message,$mail);

                    $path='/events/purchased';
                }else {
                    $sql= $h->update($tableName)->values(['transaction_id' => $balance_transaction,'payment_type' => 'Stripe','payment' => 'yes','total_price' => $price,'status' => 'save'])->where('id', '=', $LastInsertId)->run();
                    $FormDetails=$h->table('permissions')->select()->where('table_name','=',$tableName)->fetchAll();
//                    $activity= $h->insert('activity_log')->values(['user_id' => $loginUserId, 'username' => $loginUserName,'ip_address' => $ip_address,'country' => $get_country,'user_type' => $loginUserType,'activity' => $loginUserName. ' Submit the Payment of '. $FormDetails['form_name'].' Where form id is BSA-'.sprintf("%'.04d", $LastInsertId).'-'.date('y')])->run();

//                $sql = "INSERT INTO $tableName (user_id, transaction_id,payment_type, status) VALUES (?,?,?,?)";
//                $CONN->prepare($sql)->execute([$loginUserId, $balance_transaction,'Stripe','completed']);
//                $id = $CONN->lastInsertId();
                    $ticketNo="BSA-".sprintf("%'.04d", $LastInsertId)."-".$year;
                    if($tableName !='citizen_registration'){
                        $emailTitle="Form No:". $ticketNo;
                    }else{
                        $emailTitle="";
                    }
                    $finalPrice=$price;
                    include "views/email-template/purchase.php";
                    mailSender($env['ADMIN_EMAIL'],$loginUser[0]['email'],'Thank you for purchase- '.$env['SITE_NAME'],$message,$mail);
                }
                http_response_code(200);
                echo json_encode(array("statusCode" => 200, "path"=>$path));
                exit();

            }else{
                http_response_code(202);
                echo json_encode(array("statusCode" => 202, "message"=>"Something went wrong!"));
                exit();
            }

            echo json_encode($response);
        } catch (\Stripe\Exception\CardException $e) {
            http_response_code(202);
            echo json_encode(array("statusCode" => 202, "message"=>$e->getMessage()));
        } catch (Exception $e) {
            http_response_code(202);
            echo json_encode(array("statusCode" => 202, "message"=>$e->getMessage()));
        }
    }
}
if($route == '/now-payment/$formName'){
    $seo = array(
        'title' => 'Now Payment',
        'description' => 'Biafra',
        'keywords' => 'Biafra security app'
    );

$cryptoCurrencies = [
    "XMR" => [
        "name" => "XMR",
        "value" => "XMR",
        "image" => "https://cryptologos.cc/logos/monero-xmr-logo.svg?v=023"
    ],
    "USDC" => [
        "name" => "USDC",
        "value" => "USDC",
        "image" => "https://cryptologos.cc/logos/usd-coin-usdc-logo.svg?v=023"
    ],
    "USDE" => [
        "name" => "USDE",
        "value" => "USDE",
        "image" => "https://s2.coinmarketcap.com/static/img/coins/200x200/111.png"
    ],
    "ZIL" => [
        "name" => "ZIL",
        "value" => "ZIL",
        "image" => "https://cryptologos.cc/logos/zilliqa-zil-logo.svg?v=023"
    ],
    "BRISEMAINNET" => [
        "name" => "BRISEMAINNET",
        "value" => "BRISEMAINNET",
        "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTuVihDzVa_wqWut2H_ypaxOORohuAPP_93g0yQD_OhOHmTBsN-woQGXzdSAP20bS5Itvc&usqp=CAU"
    ],
    "THETA" => [
        "name" => "THETA",
        "value" => "THETA",
        "image" => "https://cryptologos.cc/logos/theta-fuel-tfuel-logo.svg?v=034"
    ],
    "USDTBSC" => [
        "name" => "USDTBSC",
        "value" => "USDTBSC",
        "image" => "https://cryptologos.cc/logos/tether-usdt-logo.svg?v=023"
    ],
    "XRP" => [
        "name" => "XRP",
        "value" => "XRP",
        "image" => "https://cryptologos.cc/logos/xrp-xrp-logo.svg?v=023"
    ],
    "EURT" => [
        "name" => "EURT",
        "value" => "EURT",
        "image" => "https://s2.coinmarketcap.com/static/img/coins/200x200/10789.png"
    ],
    "XTZ" => [
        "name" => "XTZ",
        "value" => "XTZ",
        "image" => "https://cryptologos.cc/logos/tezos-xtz-logo.svg?v=023"
    ],
    "AITECH" => [
        "name" => "AITECH",
        "value" => "AITECH",
        "image" => "https://s2.coinmarketcap.com/static/img/coins/64x64/19055.png"
    ],
    "FLOKI" => [
        "name" => "FLOKI",
        "value" => "FLOKI",
        "image" => "https://cryptologos.cc/logos/floki-inu-floki-logo.svg?v=034"
    ],
    "PIT" => [
        "name" => "PIT",
        "value" => "PIT",
        "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTTCVWt1dgiG-0IrpPRQmNldB8GMRhoswJdwg&s"
    ],
    "UNI" => [
        "name" => "UNI",
        "value" => "UNI",
        "image" => "https://cryptologos.cc/logos/uniswap-uni-logo.svg?v=023"
    ],
    "BCH" => [
        "name" => "BCH",
        "value" => "BCH",
        "image" => "https://cryptologos.cc/logos/bitcoin-cash-bch-logo.svg?v=023"
    ],
    "SHIB" => [
        "name" => "SHIB",
        "value" => "SHIB",
        "image" => "https://cryptologos.cc/logos/shiba-inu-shib-logo.svg?v=023"
    ],
    "DOT" => [
        "name" => "DOT",
        "value" => "DOT",
        "image" => "https://cryptologos.cc/logos/polkadot-new-dot-logo.svg?v=023"
    ],
    "SAND" => [
        "name" => "SAND",
        "value" => "SAND",
        "image" => "https://cryptologos.cc/logos/the-sandbox-sand-logo.svg?v=034"
    ],
    "CHZ" => [
        "name" => "CHZ",
        "value" => "CHZ",
        "image" => "https://cryptologos.cc/logos/chiliz-chz-logo.svg?v=023"
    ],
    "USDTERC20" => [
        "name" => "USDTERC20",
        "value" => "USDTERC20",
        "image" => "https://cryptologos.cc/logos/tether-usdt-logo.svg?v=023"
    ],
    "XAUT" => [
        "name" => "XAUT",
        "value" => "XAUT",
        "image" => "https://cryptologos.cc/logos/tether-gold-xaut-logo.svg?v=023"
    ],
    "TUSDTRC20" => [
        "name" => "TUSDTRC20",
        "value" => "TUSDTRC20",
        "image" => "https://cryptologos.cc/logos/trueusd-tusd-logo.svg?v=023"
    ],
    "DASH" => [
        "name" => "DASH",
        "value" => "DASH",
        "image" => "https://cryptologos.cc/logos/dash-dash-logo.svg?v=023"
    ],
    "AVAXC" => [
        "name" => "AVAXC",
        "value" => "AVAXC",
        "image" => "https://cryptologos.cc/logos/avalanche-avax-logo.svg?v=023"
    ],
    "LUNC" => [
        "name" => "LUNC",
        "value" => "LUNC",
        "image" => "https://cryptologos.cc/logos/terra-luna-luna-logo.svg?v=023"
    ],
    "APT" => [
        "name" => "APT",
        "value" => "APT",
        "image" => "https://cryptologos.cc/logos/aptos-apt-logo.svg?v=023"
    ],
    "PYUSD" => [
        "name" => "PYUSD",
        "value" => "PYUSD",
        "image" => "https://cryptologos.cc/logos/paypal-usd-pyusd-logo.svg?v=034"
    ],
    "USDTSOL" => [
        "name" => "USDTSOL",
        "value" => "USDTSOL",
        "image" => "https://cryptologos.cc/logos/tether-usdt-logo.svg?v=023"
    ],
    "USDDTRC20" => [
        "name" => "USDDTRC20",
        "value" => "USDDTRC20",
        "image" => "https://cryptologos.cc/logos/tether-usdt-logo.svg?v=034"
    ],
    "SOL" => [
        "name" => "SOL",
        "value" => "SOL",
        "image" => "https://cryptologos.cc/logos/solana-sol-logo.svg?v=023"
    ],
    "BUSDBSC" => [
        "name" => "BUSDBSC",
        "value" => "BUSDBSC",
        "image" => "https://cryptologos.cc/logos/binance-usd-busd-logo.svg?v=023"
    ],
    "QTUM" => [
        "name" => "QTUM",
        "value" => "QTUM",
        "image" => "https://cryptologos.cc/logos/qtum-qtum-logo.svg?v=023"
    ],
    "USDTTON" => [
        "name" => "USDTTON",
        "value" => "USDTTON",
        "image" => "https://cryptologos.cc/logos/tether-usdt-logo.svg?v=023"
    ],
    "BRISE" => [
        "name" => "BRISE",
        "value" => "BRISE",
        "image" => "https://altcoinsbox.com/wp-content/uploads/2023/04/bitgert-logo.png"
    ],
    "RACA" => [
        "name" => "RACA",
        "value" => "RACA",
        "image" => "https://s2.coinmarketcap.com/static/img/coins/200x200/11346.png"
    ],
    "BNBBSC" => [
        "name" => "BNBBSC",
        "value" => "BNBBSC",
        "image" => "https://cryptologos.cc/logos/binance-coin-bnb-logo.svg?v=023"
    ],
    "OKB" => [
        "name" => "OKB",
        "value" => "OKB",
        "image" => "https://cryptologos.cc/logos/okb-okb-logo.svg?v=023"
    ],
    "ETC" => [
        "name" => "ETC",
        "value" => "ETC",
        "image" => "https://cryptologos.cc/logos/ethereum-classic-etc-logo.svg?v=023"
    ],
    "BNBMAINNET" => [
        "name" => "BNBMAINNET",
        "value" => "BNBMAINNET",
        "image" => "https://cryptologos.cc/logos/binance-coin-bnb-logo.svg?v=023"
    ],
    "BTG" => [
        "name" => "BTG",
        "value" => "BTG",
        "image" => "https://cryptologos.cc/logos/bitcoin-gold-btg-logo.svg?v=023"
    ],
    "MATIC" => [
        "name" => "MATIC",
        "value" => "MATIC",
        "image" => "https://cryptologos.cc/logos/polygon-matic-logo.svg?v=023"
    ],
    "FIL" => [
        "name" => "FIL",
        "value" => "FIL",
        "image" => "https://cryptologos.cc/logos/filecoin-fil-logo.svg?v=023"
    ],
    "LINK" => [
        "name" => "LINK",
        "value" => "LINK",
        "image" => "https://cryptologos.cc/logos/chainlink-link-logo.svg?v=023"
    ],
    "PEPE" => [
        "name" => "PEPE",
        "value" => "PEPE",
        "image" => "https://cdn.coinranking.com/yGtacWSk9/PEIPEI.PNG?size=34x34"
    ],
    "XLM" => [
        "name" => "XLM",
        "value" => "XLM",
        "image" => "https://cryptologos.cc/logos/stellar-xlm-logo.svg?v=023"
    ],
    "USDCBSC" => [
        "name" => "USDCBSC",
        "value" => "USDCBSC",
        "image" => "https://cryptologos.cc/logos/usd-coin-usdc-logo.svg?v=023"
    ],
    "DOGE" => [
        "name" => "DOGE",
        "value" => "DOGE",
        "image" => "https://cryptologos.cc/logos/dogecoin-doge-logo.svg?v=023"
    ],
    "TUSD" => [
        "name" => "TUSD",
        "value" => "TUSD",
        "image" => "https://cryptologos.cc/logos/trueusd-tusd-logo.svg?v=023"
    ],
    "WAVES" => [
        "name" => "WAVES",
        "value" => "WAVES",
        "image" => "https://cryptologos.cc/logos/waves-waves-logo.svg?v=023"
    ],
    "AAVE" => [
        "name" => "AAVE",
        "value" => "AAVE",
        "image" => "https://cryptologos.cc/logos/aave-aave-logo.svg?v=023"
    ],
    "ARB" => [
        "name" => "ARB",
        "value" => "ARB",
        "image" => "https://cryptologos.cc/logos/arbitrum-arb-logo.svg?v=023"
    ],
    "CSPR" => [
        "name" => "CSPR",
        "value" => "CSPR",
        "image" => "https://cryptologos.cc/logos/casper-cspr-logo.svg?v=023"
    ],
    "PEIPEI" => [
        "name" => "PEIPEI",
        "value" => "PEIPEI",
        "image" => "https://cdn.coinranking.com/yGtacWSk9/PEIPEI.PNG?size=34x34"
    ],
    "EGLDBSC" => [
        "name" => "EGLDBSC",
        "value" => "EGLDBSC",
        "image" => "https://cryptologos.cc/logos/multiversx-egld-egld-logo.svg?v=034"
    ],
    "ETH" => [
        "name" => "ETH",
        "value" => "ETH",
        "image" => "https://cryptologos.cc/logos/ethereum-eth-logo.svg?v=023"
    ],
    "XDC" => [
        "name" => "XDC",
        "value" => "XDC",
        "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTdFt6duZDmK7xjzf88FjouynHo0RYEF-kWQA&s"
    ],
    "FTM" => [
        "name" => "FTM",
        "value" => "FTM",
        "image" => "https://cryptologos.cc/logos/fantom-ftm-logo.svg?v=023"
    ],
    "XEM" => [
        "name" => "XEM",
        "value" => "XEM",
        "image" => "https://cryptologos.cc/logos/nem-xem-logo.svg?v=023"
    ],
    "DOGECOIN" => [
        "name" => "DOGECOIN",
        "value" => "DOGECOIN",
        "image" => "https://cryptologos.cc/logos/dogecoin-doge-logo.svg?v=023"
    ],
    "APE" => [
        "name" => "APE",
        "value" => "APE",
        "image" => "https://cryptologos.cc/logos/apecoin-ape-ape-logo.svg?v=034"
    ],
    "XVG" => [
        "name" => "XVG",
        "value" => "XVG",
        "image" => "https://cryptologos.cc/logos/verge-xvg-logo.svg?v=023"
    ],
    "LUNA" => [
        "name" => "LUNA",
        "value" => "LUNA",
        "image" => "https://cryptologos.cc/logos/terra-luna-luna-logo.svg?v=023"
    ],
    "JASMY" => [
        "name" => "JASMY",
        "value" => "JASMY",
        "image" => "https://cryptologos.cc/logos/jasmy-jasmy-logo.svg?v=034"
    ],
    "1INCH" => [
        "name" => "1INCH",
        "value" => "1INCH",
        "image" => "https://cryptologos.cc/logos/1inch-1inch-logo.svg?v=023"
    ],
    "ATOM" => [
        "name" => "ATOM",
        "value" => "ATOM",
        "image" => "https://cryptologos.cc/logos/cosmos-atom-logo.svg?v=023"
    ],
    "MATICMAINNET" => [
        "name" => "MATICMAINNET",
        "value" => "MATICMAINNET",
        "image" => "https://cryptologos.cc/logos/polygon-matic-logo.svg?v=023"
    ],
    "HBAR" => [
        "name" => "HBAR",
        "value" => "HBAR",
        "image" => "https://cryptologos.cc/logos/hedera-hashgraph-hbar-logo.svg?v=023"
    ],
    "XEC" => [
        "name" => "XEC",
        "value" => "XEC",
        "image" => "https://cryptologos.cc/logos/ecash-xec-logo.svg?v=034"
    ],
    "SHIBBSC" => [
        "name" => "SHIBBSC",
        "value" => "SHIBBSC",
        "image" => "https://cryptologos.cc/logos/shiba-inu-shib-logo.svg?v=023"
    ],
    "TRX" => [
        "name" => "TRX",
        "value" => "TRX",
        "image" => "https://cryptologos.cc/logos/tron-trx-logo.svg?v=023"
    ],
    "VET" => [
        "name" => "VET",
        "value" => "VET",
        "image" => "https://cryptologos.cc/logos/vechain-vet-logo.svg?v=023"
    ],
    "ADA" => [
        "name" => "ADA",
        "value" => "ADA",
        "image" => "https://cryptologos.cc/logos/cardano-ada-logo.svg?v=023"
    ],
    "BSV" => [
        "name" => "BSV",
        "value" => "BSV",
        "image" => "https://cryptologos.cc/logos/bitcoin-sv-bsv-logo.svg?v=023"
    ],
    "TON" => [
        "name" => "TON",
        "value" => "TON",
        "image" => "https://cryptologos.cc/logos/toncoin-ton-logo.svg?v=034"
    ],
    "FLOKIBSC" => [
        "name" => "FLOKIBSC",
        "value" => "FLOKIBSC",
        "image" => "https://cryptologos.cc/logos/floki-inu-floki-logo.svg?v=034"
    ],
    "BTC" => [
        "name" => "BTC",
        "value" => "BTC",
        "image" => "https://cryptologos.cc/logos/bitcoin-btc-logo.svg?v=023"
    ],
    "USDCMATIC" => [
        "name" => "USDCMATIC",
        "value" => "USDCMATIC",
        "image" => "https://cryptologos.cc/logos/usd-coin-usdc-logo.svg?v=023"
    ],
    "NANO" => [
        "name" => "NANO",
        "value" => "NANO",
        "image" => "https://cryptologos.cc/logos/nano-nano-logo.svg?v=023"
    ],
    "BUSD" => [
        "name" => "BUSD",
        "value" => "BUSD",
        "image" => "https://cryptologos.cc/logos/binance-usd-busd-logo.svg?v=023"
    ],
    "DAI" => [
        "name" => "DAI",
        "value" => "DAI",
        "image" => "https://cryptologos.cc/logos/multi-collateral-dai-dai-logo.svg?v=023"
    ],
    "LTC" => [
        "name" => "LTC",
        "value" => "LTC",
        "image" => "https://cryptologos.cc/logos/litecoin-ltc-logo.svg?v=023"
    ],
    "BABYDOGE" => [
        "name" => "BABYDOGE",
        "value" => "BABYDOGE",
        "image" => "https://cryptologos.cc/logos/baby-doge-coin-babydoge-logo.svg?v=023"
    ],
    "ID" => [
        "name" => "ID",
        "value" => "ID",
        "image" => "https://cryptologos.cc/logos/space-id-id-logo.svg?v=034"
    ],
    "USDCSOL" => [
        "name" => "USDCSOL",
        "value" => "USDCSOL",
        "image" => "https://cryptologos.cc/logos/usd-coin-usdc-logo.svg?v=023"
    ],
    "ARK" => [
        "name" => "ARK",
        "value" => "ARK",
        "image" => "https://cryptologos.cc/logos/ark-ark-logo.svg?v=023"
    ],
    "NEAR" => [
        "name" => "NEAR",
        "value" => "NEAR",
        "image" => "https://cryptologos.cc/logos/near-protocol-near-logo.svg?v=023"
    ],
    "ALGO" => [
        "name" => "ALGO",
        "value" => "ALGO",
        "image" => "https://cryptologos.cc/logos/algorand-algo-logo.svg?v=023"
    ],
    "YFI" => [
        "name" => "YFI",
        "value" => "YFI",
        "image" => "https://cryptologos.cc/logos/yearn-finance-yfi-logo.svg?v=023"
    ],
    "ZEC" => [
        "name" => "ZEC",
        "value" => "ZEC",
        "image" => "https://cryptologos.cc/logos/zcash-zec-logo.svg?v=023"
    ],
    "SEI" => [
        "name" => "SEI",
        "value" => "SEI",
        "image" => "https://cryptologos.cc/logos/sei-sei-logo.svg?v=034"
    ]
];
$apiKey = $env['NOWPAYMENTS_API_KEY'] ?? '';
$currencyApiUrl = 'https://api.nowpayments.io/v1/currencies';
$ch = curl_init($currencyApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'x-api-key: ' . $apiKey,
    'Content-Type: application/json'
]);
$currencyResponse = curl_exec($ch);
curl_close($ch);
$currencies = json_decode($currencyResponse, true);
if (@$category) {
    $event = $h->table('event')->select()->where('slug', '=',$event)->fetchAll();

} else {
    $permission = $h->table('permissions')->select()->where('path', '=', "/" . $formName)->fetchAll();
    $tableName = $permission[0]['table_name'];
    $checkRecord = $h->table($tableName)->select()->where('user_id', '=', $loginUserId)->fetchAll();
    if (empty($checkRecord)) {
        $newInsert = $h->insert($tableName)->values(['user_id' => $loginUserId, 'payment' => 'no', 'status' => 'save'])->run();
        $lastInsertId = $newInsert;
    } else {
        $lastInsertId = $checkRecord[0]['id'];
        if ($checkRecord[0]['payment'] == 'yes') {
            header("Location: /" . $formName);
            $path = "/" . $formName;
        }
    }
}
if (@$category) {
    $decision = '1';
    $eventCategories = explode(',', $event[0]['event_ticket_category']);
    $eventPrice = explode(',', $event[0]['event_price']);
    foreach ($eventCategories as $index => $cat) {
        $category = str_replace("%20", " ", $category);
        if ($cat == $category) {
            $price = $eventPrice[$index];
            $price = str_replace("$", "", $price);
        }
    }
    $table_name = 'event_form';
    $path = '/events/purchased';
} else {
    $decision = '0';
    $price = $permission[0]['price'];
    $table_name = $permission[0]['table_name'];
    $path = $formName;

}
    echo $twig->render('public/payment_gateways/nowPayment.twig', ['seo' => $seo,'lastInsertId' => $lastInsertId,'decision' => $decision,'price' => $price,'table_name' => $table_name,'path' => $path,'nowPaymentData' => @$_SESSION['nowPaymentData'],'cryptoCurrencies' => $cryptoCurrencies, 'csrf' => set_csrf()]);
}
if($route == '/NowPayment'){
    $bank_name = 'payWithCrypto';
    $price = $_POST['price'];
    $path = $_POST['path'];
    $tableName = $_POST["tableName"];
    $decision = $_POST['decision'];
    $LastInsertId = $_POST['LastInsertId'];
    $currency = $_POST['currency'];
    if (!empty($price) && !empty($path) && !empty($tableName) && !empty($LastInsertId) && !empty($currency)) {

        $apiKey = $env['NOWPAYMENTS_API_KEY'] ?? '';
        $paymentData = [
            'price_amount' => $price,
            'price_currency' => 'usd',
            'pay_currency' => $currency,
            'ipn_callback_url' => 'https://www.biafrasecurityadministration.org/ipn',
            'order_id' => $LastInsertId,
            'order_description' => 'Payment for order ' . $LastInsertId,
            'is_fixed_rate' => true,
        ];

        $ch = curl_init('https://api.nowpayments.io/v1/payment');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));

        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if (isset($responseData['payment_id'])) {
            $transaction_id = $responseData['payment_id'];
            $proof = $responseData['pay_address'];
            if ($decision != '0') {
                $res = $h->update('event_form')->values([
                    'transaction_id' => $transaction_id,
                    'payment_type' => $bank_name,
                    'total_price' => $price,
                    'proof' => "Currency: ".$currency."<br> Payment Address: ".$proof,
                    'payment_des' => $responseData['order_description'],
                    'payment' => 'no'
                ])->where('id', '=', $LastInsertId)->run();
                $EventFormDetails=$h->table('event_form')->select()->where('id','=',$LastInsertId)->fetchAll();
                $EventDetails=$h->table('event')->select()->where('id','=',$EventFormDetails['event_id'])->fetchAll();
//                $activity= $h->insert('activity_log')->values(['user_id' => $loginUserId, 'username' => $loginUserName,'ip_address' => $ip_address,'country' => $get_country,'user_type' => $loginUserType,'activity' => $loginUserName. ' Submit the Payment of '. $EventDetails['event_name'].' Where id is '.$LastInsertId])->run();
                $ticketNo = "#" . sprintf("%'.04d", $LastInsertId) . "-" . date('y');
                $emailTitle = "Ticket No:" . $ticketNo;
                $finalPrice = $price;
                $path = '/events/purchased';

                // Example for sending an email (uncomment and configure as needed)
                // include "views/email-template/purchase.php";
                // mailSender(ADMIN_EMAIL, $loginUser['email'], 'Thank you for purchase - ' . SITE_NAME, $message, $mail);
            } else {
                $sql = $h->update($tableName)->values([
                    'transaction_id' => $transaction_id,
                    'payment_type' => $bank_name,
                    'total_price' => $price,
                    'proof' => "Currency: ".$currency."<br> Payment Address: ".$proof,
                    'payment_des' => $responseData['order_description'],
                    'payment' => 'no',
                    'status' => 'save'
                ])->where('id', '=', $LastInsertId)->run();
                $FormDetails=$h->table('permissions')->select()->where('table_name','=',$tableName)->fetchAll();
//                $activity= $h->insert('activity_log')->values(['user_id' => $loginUserId, 'username' => $loginUserName,'ip_address' => $ip_address,'country' => $get_country,'user_type' => $loginUserType,'activity' => $loginUserName. ' Submit the Payment of '. $FormDetails['form_name'].' Where form id is BSA-'.sprintf("%'.04d", $LastInsertId).'-'.date('y')])->run();
                $ticketNo = "BSA-" . sprintf("%'.04d", $LastInsertId) . "-" . date('y');
                if ($tableName != 'citizen_registration') {
                    $emailTitle = "Form No:" . $ticketNo;

                } else {
                    $emailTitle = "";
                }

                $finalPrice = $price;

                // Example for sending an email (uncomment and configure as needed)
                // include "views/email-template/purchase.php";
                // mailSender(ADMIN_EMAIL, $loginUser['email'], 'Thank you for purchase - ' . SITE_NAME, $message, $mail);
            }
            $payAddress = $responseData['pay_address'];
            $payAmount = $responseData['pay_amount'];
            $payCurrency = $responseData['pay_currency'];
            http_response_code(200);
//        $qrCodeData = '&data='.$payCurrency . ':' . $payAddress . '&amount=' . $payAmount;
            $qrCodeData = '&data='.$payAddress;
            $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250'.$qrCodeData;
            $payTag = isset($responseData['payin_extra_id']) ? $responseData['payin_extra_id'] : null;
            unset($_SESSION['nowPaymentData']);
            $_SESSION['nowPaymentData'] = [
                'paymentID' => $transaction_id,
                'payCurrency' => $payCurrency,
                'payAddress' => $payAddress,
                'payAmount' => $payAmount,
                'payin_extra_id' => $payTag,
                'path' => $qrCodeUrl,
            ];
            echo json_encode(array("statusCode" => 200, "path" => $qrCodeUrl,"payin_extra_id" => $payTag,"paymentID" => $responseData['payment_id'],"payAddress" => $payAddress, "payAmount" => $payAmount, "payCurrency" => $payCurrency));
            exit();
        } else {
            // Handle API Error
            http_response_code(500);
            echo json_encode(array("statusCode" => 500, "message" => "Error creating payment: " . $responseData['message']));
            exit();
        }
    } else {
        http_response_code(400);
        echo json_encode(array("statusCode" => 400, "message" => "Invalid input."));
        exit();
    }
    echo json_encode(array("statusCode" => 'lora', "message" => "Invalid input."));
    exit();
}
if($route == '/CreateTransactionNowPayment'){
    $apiKey = $env['NOWPAYMENTS_API_KEY'] ?? '';
    $apiUrl = 'https://api.nowpayments.io/v1/payment';
    $bank_name = 'NowPayment';
    $price = $_POST['price'];
    $path = $_POST['path'];
    $tableName = $_POST["tableName"];
    $decision = $_POST['decision'];
    $LastInsertId = $_POST['LastInsertId'];
    $currency = $_POST['currency'];
// Payment data
    $data = [
        'price_amount' => $price, // Amount to be paid
        'price_currency' => 'USD', // Currency in which the amount is specified
        'pay_currency' => 'BTC', // Cryptocurrency the customer will pay with
        'ipn_callback_url' => 'https://www.biafrasecurityadministration.org/ipn', // Your IPN URL to handle payment notifications
        'order_id' => 'order_123', // Unique order ID from your system
        'order_description' => 'Order #123 for Product XYZ' // Description of the order
    ];

// Initialize cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'x-api-key: ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute the API call
    $response = curl_exec($ch);
    curl_close($ch);
//                                var_dump($response);
//                                exit();
// Decode the response
    $responseData = json_decode($response, true);


// Check if the response contains the required fields
    if (isset($responseData['pay_address']) && isset($responseData['pay_amount']) && isset($responseData['pay_currency'])) {
        // Extract necessary information
        $payAddress = $responseData['pay_address'];
        $payAmount = $responseData['pay_amount'];
        $payCurrency = $responseData['pay_currency'];

        // Generate the URI for the QR code
        $qrCodeData = $payCurrency . ':' . $payAddress . '?amount=' . $payAmount;
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrCodeData);

        // Display the QR code on the webpage
        echo '<h2>Scan to Pay</h2>';
        echo '<img src="' . $qrCodeUrl . '" alt="QR Code for payment">';
        echo '<p>Address: ' . $payAddress . '</p>';
        echo '<p>Amount: ' . $payAmount . ' ' . strtoupper($payCurrency) . '</p>';
    } else {
        echo 'Failed to retrieve payment details.';
    }
}
if($route == '/ipn'){
// Your API key from NOWPayments dashboard
$apiKey = $env['NOWPAYMENTS_API_KEY'] ?? '';

// The payment ID you want to check
$paymentId = $_POST['paymentID']; // Replace with the actual payment ID you received
$path = $_POST['path'];
$tableName = $_POST["tableName"];
$decision = $_POST['decision'];
$LastInsertId = $_POST['LastInsertId'];

// The API endpoint to check payment status
$apiUrl = 'https://api.nowpayments.io/v1/payment/' . $paymentId;

// Initialize cURL
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'x-api-key: ' . $apiKey,
    'Content-Type: application/json'
]);

// Execute the API call
$response = curl_exec($ch);
curl_close($ch);

// Decode the response
$responseData = json_decode($response, true);
//var_dump($responseData);
// Check if the API call was successful
if (isset($responseData['payment_status'])) {
    switch ($responseData['payment_status']) {
        case 'waiting':
            // Waiting for the customer to send the payment
            $message = 'Waiting for payment. Please complete the payment.';
            break;

        case 'confirming':
            // The transaction is being processed on the blockchain
            $message = 'Transaction is being processed on the blockchain.';
            break;

        case 'confirmed':
            // The process is confirmed by the blockchain
            $message = 'Payment confirmed by the blockchain.';
            break;

        case 'sending':
            // The funds are being sent to your personal wallet
            $message = 'Verifying payment. Ensuring the correct amount has been received.';
            break;

        case 'partially_paid':
            // Customer sent less than the actual price
            $message = 'Partial payment received. Please send the remaining amount.';
            break;

        case 'finished':
            // Payment successfully completed
            $message = 'Payment successfully completed.';
            break;

        case 'failed':
            // Payment wasn't completed due to an error
            $message = 'Payment failed. Please try again or contact support.';
            break;

        case 'refunded':
            // The funds were refunded back to the user
            $message = 'Payment was refunded to the user.';
            break;

        case 'expired':
            // The user didn't send the funds to the specified address in the time window
            $message = 'Payment request expired. Please initiate a new payment.';
            break;

        default:
            // Unexpected payment status
            $message = 'Unknown payment status. Please contact support.';
            break;
    }

    if($decision != '0') {
        if($responseData['payment_status'] == 'finished' || $responseData['payment_status'] == 'confirmed'){
            unset($_SESSION['nowPaymentData']);
            $res= $h->update('event_form')->values(['payment' => 'yes'])->where('id', '=', $LastInsertId)->run();
            $ticketNo = "BSA-" . sprintf("%'.04d", $LastInsertId) . "-" . date('y');
            $emailTitle = "Form No:" . $ticketNo;
            $balance_transaction=$responseData['payment_id'];
            $finalPrice=$responseData['pay_currency'].' '.$responseData['actually_paid'];
            include "views/email-template/purchase.php";
            mailSender($env['ADMIN_EMAIL'], $loginUser['email'], 'Thank you for purchase - ' . $env['SITE_NAME'], $message, $mail);
        }elseif($responseData['payment_status'] == 'expired'){
            unset($_SESSION['nowPaymentData']);
        }
        $path='/events/purchased';
        echo json_encode(array("statusCode" => 200, "path" => $path,"message" => $message, "paymentStatus" => $responseData['payment_status']));
    }else{
        if($responseData['payment_status'] == 'finished' || $responseData['payment_status'] == 'confirmed'){
            unset($_SESSION['nowPaymentData']);
            $ticketNo = "BSA-" . sprintf("%'.04d", $LastInsertId) . "-" . date('y');
            $emailTitle = "Form No:" . $ticketNo;
            $balance_transaction=$responseData['payment_id'];
            $finalPrice=$responseData['pay_currency'].' '.$responseData['actually_paid'];
            $sql= $h->update($tableName)->values(['payment' => 'yes'])->where('id', '=', $LastInsertId)->run();
            include "views/email-template/purchase.php";
            mailSender($env['ADMIN_EMAIL'], $loginUser['email'], 'Thank you for purchase - ' . $env['SITE_NAME'], $message, $mail);
        }elseif($responseData['payment_status'] == 'expired'){
            unset($_SESSION['nowPaymentData']);
        }
        echo json_encode(array("statusCode" => 200, "path" => $path,"message" => $message, "paymentStatus" => $responseData['payment_status']));
    }
    exit();
} else {
    echo json_encode(array("statusCode" => 200, "message" => 'Failed to retrieve payment status. Unknown error.'));
    exit();
}
}
