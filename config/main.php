<?php
/////////////////////////
///////ZOTEC FRAMEWORK
//////admin@zotecsoft.com
////////////////////////
require 'vendor/autoload.php';
use PHRETS\Configuration;
use PHRETS\Session;
use PHRETS\Models\Search\Results;
use Intervention\Image\ImageManagerStatic as Image;
use Tracy\Debugger;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Soneso\StellarSDK\Crypto\KeyPair;
use Soneso\StellarSDK\Asset;
use Soneso\StellarSDK\PaymentOperationBuilder;
use Soneso\StellarSDK\TransactionBuilder;
use Soneso\StellarSDK\Network;
use Soneso\StellarSDK\StellarSDK;



// Debugger::enable();
session_start();


function get($route, $path_to_include, $page_name=NULL){
    if( $_SERVER['REQUEST_METHOD'] == 'GET' ){

        route($route, $path_to_include, $page_name);

    }
}
function post($route, $path_to_include,$page_name=NULL){
    if( $_SERVER['REQUEST_METHOD'] == 'POST' ){ route($route, $path_to_include,$page_name ); }
}
function put($route, $path_to_include){
    if( $_SERVER['REQUEST_METHOD'] == 'PUT' ){ route($route, $path_to_include); }
}
function patch($route, $path_to_include){
    if( $_SERVER['REQUEST_METHOD'] == 'PATCH' ){ route($route, $path_to_include); }
}
function delete($route, $path_to_include){
    if( $_SERVER['REQUEST_METHOD'] == 'DELETE' ){ route($route, $path_to_include); }
}
function any($route, $path_to_include){ route($route, $path_to_include); }
function route($route, $path_to_include, $page_name=NULL){
    $PAGE_NAME=$page_name;
    $ROOT = $_SERVER['DOCUMENT_ROOT'];
    if($route == "/404"){
        include_once("$ROOT/$path_to_include");
        exit();
    }
    $request_url = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
    $request_url = rtrim($request_url, '/');
    $request_url = strtok($request_url, '?');
    $route_parts = explode('/', $route);
    $request_url_parts = explode('/', $request_url);
    array_shift($route_parts);
    array_shift($request_url_parts);
    if( @$route_parts[0] == '' && count($request_url_parts) == 0 ){
        include_once("$ROOT/$path_to_include");
        exit();
    }
    if( count($route_parts) != count($request_url_parts) ){ return; }
    $parameters = [];
    for( $__i__ = 0; $__i__ < count($route_parts); $__i__++ ){
        $route_part = $route_parts[$__i__];
        if( preg_match("/^[$]/", $route_part) ){
            $route_part = ltrim($route_part, '$');
            array_push($parameters, $request_url_parts[$__i__]);
            $$route_part=$request_url_parts[$__i__];
        }
        else if( $route_parts[$__i__] != $request_url_parts[$__i__] ){
            return;
        }
    }
    include_once("$ROOT/$path_to_include");
    exit();
}
function out($text){echo htmlspecialchars($text);}
function set_csrf(){
    $csrf_token = bin2hex(random_bytes(25));
    $_SESSION['csrf'] = $csrf_token;
    return $csrf_token;
}
function is_csrf_GET_script(){
    if( ! isset($_SESSION['csrf']) || ! isset($_GET['csrf'])){ return false; }
    if( $_SESSION['csrf'] != $_GET['csrf']){ return false; }
    return true;
}
function is_csrf_valid(){
    if( ! isset($_SESSION['csrf']) || ! isset($_POST['csrf'])){ return false; }
    if( $_SESSION['csrf'] != $_POST['csrf']){ return false; }
    return true;
}

function commaSeperated($string){
    $prefix=$temp="";
    foreach ($string as $s){
        if(!empty($s || $s != NULL)):
            $temp.=$prefix.$s;
            $prefix=',';
        endif;
    }
    return $temp;
}
function commaSeperatedToArray($string){
    $str_arr = explode (",", trim($string));
    return $str_arr;
}
function checkLanguage($lang_arr, $value){
    $re="selected";
    foreach ($lang_arr as $lang){
        if($lang== $value){
            return $re;
        }
    }
}
//Multi  Image Resize Upload
function uploadMultiResizeImage($f_name, $f_path,$f_thumbnail,$width,$height)
{
    $insertValuesSQL[]='';
    $targetDir = $f_path;
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    $fileNames = array_filter($_FILES[$f_name]['name']);
    if (!empty($fileNames)) {
        foreach ($_FILES[$f_name]['name'] as $key => $val) {
            // File upload path
            $fileName = basename($_FILES[$f_name]['name'][$key]);
            $targetFilePath = $targetDir . $fileName;

            // Check whether file type is valid
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            if (in_array($fileType, $allowTypes)) {
                $temp = explode(".", $_FILES[$f_name]['name'][$key]);
                $newfilename = round(microtime(true)).rand(100,10000).'.' . end($temp);
                // Upload file to server
                if (move_uploaded_file($_FILES[$f_name]["tmp_name"][$key], $targetDir . $newfilename)) {
                    $filepath=$targetDir.$newfilename;
                    $thumbnailPath = $f_thumbnail . $newfilename;
                    $img = Image::make($filepath);
                    $img->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $img->save($thumbnailPath);
                    // Image db insert sql
                    $insertValuesSQL[] .= $newfilename;
                }
            }
        }
        $imgz=trim(implode(',',$insertValuesSQL), ",");
        return $imgz;
    }
}
//Single  Image Resize Upload
function upload($f_name, $f_path){
    $target_dir = $f_path;
    $target_file = $target_dir .basename($_FILES[$f_name]['name']);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES[$f_name]["tmp_name"]);
        if($check !== false) {
            return "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            return "File is not an image.";
            $uploadOk = 0;
        }
    }
// Check if file already exists
//    if (file_exists($target_file)) {
//        return "Sorry, file already exists.";
//        $uploadOk = 0;
//    }
// Check file size
    if ($_FILES[$f_name]["size"] > 5000000000) {
        return "null";
        $uploadOk = 0;
    }
// Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "webp"
        && $imageFileType != "svg" && $imageFileType != "pdf" && $imageFileType != "docx" && $imageFileType != "gif") {
        return "null";
        $uploadOk = 0;
    }
// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        return "null";
// if everything is ok, try to upload file
    } else {

        $temp = explode(".", $_FILES[$f_name]['name']);
        $orignalName = pathinfo($_FILES[$f_name]['name'], PATHINFO_FILENAME);
        $newfilename = rand().round(microtime(true)) . '.' . end($temp);

        if (move_uploaded_file($_FILES[$f_name]["tmp_name"], $target_dir.$newfilename)) {
            return $newfilename;
        } else {
            return "null";
        }
    }
}

//Favicon Resize Upload
function uploadResizeFavicon($f_name, $f_path,$f_thumbnail,$width,$height){

    $target_dir = $f_path;
    $target_file = $target_dir . basename($_FILES[$f_name]['name']);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES[$f_name]["tmp_name"]);
        if($check !== false) {
            return "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            return "File is not an image.";
            $uploadOk = 0;
        }
    }
// Check if file already exists
    if (file_exists($target_file)) {
        return "Sorry, file already exists.";
        $uploadOk = 0;
    }
// Check file size
    if ($_FILES[$f_name]["size"] > 5000000000) {
        return "Sorry, your file is too large.";
        $uploadOk = 0;
    }
// Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "svg" && $imageFileType != "pdf" && $imageFileType != "docx") {
        return "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        return "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
    } else {

        $temp = explode(".", $_FILES[$f_name]['name']);
        $newfilename = round(microtime(true)) . '.' . end($temp);

        if (move_uploaded_file($_FILES[$f_name]["tmp_name"], $target_dir.$newfilename)) {
            $filepath=$target_dir.$newfilename;
            $thumbnailPath = $f_thumbnail.$newfilename;
            $img = Image::make($filepath);
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($thumbnailPath);
            return $newfilename;
        } else {
            return "Sorry, there was an error uploading your file.";
        }
    }
}


//Single  Image Resize Upload
function uploadResizeImage($f_name, $f_path,$f_thumbnail,$width,$height){

    $target_dir = $f_path;
    $target_file = $target_dir . basename($_FILES[$f_name]['name']);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES[$f_name]["tmp_name"]);
        if($check !== false) {
            return array('error'=>true, 'message'=>"File is an image - " . $check["mime"] . ".");
            $uploadOk = 1;
        } else {
            return array('error'=>true, 'message'=>"File is not an image.");
            $uploadOk = 0;
        }
    }
// Check if file already exists
    if (file_exists($target_file)) {
        return array('error'=>true, 'message'=>"Sorry, file already exists.");
        $uploadOk = 0;
    }
// Check file size
    if ($_FILES[$f_name]["size"] > 5000000000) {
        return array('error'=>true, 'message'=>"Sorry, your file is too large.");
        $uploadOk = 0;
    }
// Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "svg" && $imageFileType != "pdf" && $imageFileType != "docx") {
        return array('error'=>true, 'message'=>"Sorry, only JPG, JPEG, PNG files are allowed.");

        $uploadOk = 0;
    }
// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        return array('error'=>true, 'message'=>"Sorry, your file was not uploaded.");
// if everything is ok, try to upload file
    } else {

        $temp = explode(".", $_FILES[$f_name]['name']);
        $newfilename = round(microtime(true)) . '.' . end($temp);

        if (move_uploaded_file($_FILES[$f_name]["tmp_name"], $target_dir.$newfilename)) {
            $filepath=$target_dir.$newfilename;
            $thumbnailPath = $f_thumbnail.$newfilename;
            $img = Image::make($filepath);
            $img->resize($width, $height);//, function ($constraint) {
            //$constraint->aspectRatio();
            //$constraint->upsize();
            //});
            $img->save($thumbnailPath);
            return array('error'=>false, 'message'=>"Upload Successfully",'filename'=>$newfilename );
            //return $newfilename;
        } else {
            return array('error'=>true, 'message'=>"Sorry, there was an error uploading your file.");
        }
    }
}
function uploadDoc($f_name, $f_path){
    $target_dir = $f_path;
    $target_file = $target_dir . basename($_FILES[$f_name]['name']);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES[$f_name]["tmp_name"]);
        if($check !== false) {
            return "File is an Zip - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            return "File is not an image.";
            $uploadOk = 0;
        }
    }
// Check if file already exists
    if (file_exists($target_file)) {
        return $arr=array("file_name"=>"not found", "status"=>"202", "error"=>"Sorry, file already exists.");
        $uploadOk = 0;
    }
// Check file size
    if ($_FILES[$f_name]["size"] > 5000000000) {
        return $arr=array("file_name"=>"not found", "status"=>"202", "error"=>"Sorry, your file is too large.");
        $uploadOk = 0;
    }
// Allow certain file formats
    if($imageFileType != "zip" && $imageFileType != "ZIP" && $imageFileType != "rar") {
        return $arr=array("file_name"=>"not found", "status"=>"202", "error"=>"Sorry, only zip, rar files are allowed.");
        $uploadOk = 0;
    }
// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        return $arr=array("file_name"=>"not found", "status"=>"202", "error"=>"Sorry, your file was not uploaded.");

// if everything is ok, try to upload file
    } else {
        $temp = explode(".", $_FILES[$f_name]['name']);
        $newfilename = round(microtime(true)) . '.' . end($temp);

        if (move_uploaded_file($_FILES[$f_name]["tmp_name"], $target_dir.$newfilename)) {

            return $arr=array("file_name"=>$newfilename, "status"=>"200", "error"=>"File Successfully Submitted.");
        } else {
            return $arr=array("file_name"=>"not found", "status"=>"202", "error"=>"Sorry, there was an error uploading your file.");
        }
    }
}
function datetimeToDate($date){
    $datetime   = strtotime($date);
    return date('Y-m-d', $datetime);
}

function get_words($sentence, $count = 15) {
    preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
    return $matches[0].'....';
}
function getIPAddress() {
    //whether ip is from the share internet
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];

    }

    //whether ip is from the proxy
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
//whether ip is from the remote address
    else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
//GET COUNTRY
function getClientCountry($ip) {
    //whether ip is from the share internet

    $iptolocation = 'http://api.hostip.info/country.php?ip='.$ip;

    return $creatorlocation = trim(file_get_contents($iptolocation));

}
//GET OS
$user_agent = $_SERVER['HTTP_USER_AGENT'];
function getOS() {

    global $user_agent;

    $os_platform  = "Unknown OS Platform";

    $os_array     = array(
        '/windows nt 10/i'      =>  'Windows',
        '/windows nt 6.3/i'     =>  'Windows',
        '/windows nt 6.2/i'     =>  'Windows',
        '/windows nt 6.1/i'     =>  'Windows',
        '/windows nt 6.0/i'     =>  'Windows',
        '/windows nt 5.2/i'     =>  'Windows',
        '/windows nt 5.1/i'     =>  'Windows',
        '/windows xp/i'         =>  'Windows',
        '/windows nt 5.0/i'     =>  'Windows',
        '/windows me/i'         =>  'Windows',
        '/win98/i'              =>  'Windows',
        '/win95/i'              =>  'Windows',
        '/win16/i'              =>  'Windows',
        '/macintosh|mac os x/i' =>  'Mac',
        '/mac_powerpc/i'        =>  'Mac',
        '/linux/i'              =>  'Linux',
        '/ubuntu/i'             =>  'Ubuntu',
        '/iphone/i'             =>  'iPhone',
        '/ipod/i'               =>  'iPod',
        '/ipad/i'               =>  'iPad',
        '/android/i'            =>  'Android',
        '/blackberry/i'         =>  'BlackBerry',
        '/webos/i'              =>  'Mobile'
    );

    foreach ($os_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $os_platform = $value;

    return $os_platform;
}
function getBrowser() {

    global $user_agent;

    $browser        = "Unknown Browser";

    $browser_array = array(
        '/msie/i'      => 'Internet Explorer',
        '/firefox/i'   => 'Firefox',
        '/safari/i'    => 'Safari',
        '/chrome/i'    => 'Chrome',
        '/edge/i'      => 'Edge',
        '/opera/i'     => 'Opera',
        '/netscape/i'  => 'Netscape',
        '/maxthon/i'   => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i'    => 'Handheld Browser'
    );

    foreach ($browser_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $browser = $value;

    return $browser;
}
function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}
function random_strings($length_of_string)
{
    $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
    return substr(str_shuffle($str_result), 0, $length_of_string);
}
function userLogin($email, $password, $table_name) {
    global $h;
    if (isset($email) && !empty($email) && isset($password) && !empty($password)) {

        // Check CSRF token if needed
        // Uncomment and implement CSRF check if needed
        // if( !is_csrf_v_script()) {
        //    http_response_code(202);
        //    return json_encode(array("statusCode" => 202, "message"=>"Invalid CSRF Token. Please <a href='#' onclick='refresh_page()'> Refresh Page.</a>"));
        //    exit();
        // }

        // Fetch user data from the database where email matches and status is active
        $stmt = $h->$table_name->select()->where('email', '=', $email)->fetchAll();

        // Check if user exists
        if (!empty($stmt[0])) {
            // Verify password
            if (password_verify($password, $stmt[0]['password'])) {
                if($stmt[0]['status'] == 'active'){
                    // Set session with user data
                    $_SESSION[$table_name] = $stmt[0];
                    if ($stmt[0]['type'] == 'admin') {
                        http_response_code(200);
                        return json_encode(array("statusCode" => 200, "message" => "Successfully Login..", "path" => "/admin/dashboard"));

                    } else {
                        http_response_code(200);
                        return json_encode(array("statusCode" => 200, "message" => "Successfully Login..", "path" => '/dashboard'));
                    }
                }else{
                    http_response_code(202);
                    return json_encode(array("statusCode" => 202, "message" => "Your account has been blocked. Please contact support for assistance."));

                }
               } else {
                // Invalid password
                http_response_code(202);
                return json_encode(array("statusCode" => 202, "message" => "Invalid Password"));
            }
        } else {
            // Invalid email or user does not exist
            http_response_code(202);
            return json_encode(array("statusCode" => 202, "message" => "Invalid Email or Password. Please try again."));
        }
    } else {
        // Missing email or password
        http_response_code(400);
        return json_encode(array("statusCode" => 400, "message" => "Email and Password are required"));
    }
}


function userRegister($first_name, $last_name, $email,$phone, $password, $table_name){
    global $h,$env,$mail;
    if(isset($email) && !empty($email) && isset($password) && !empty($password)){
//        if( ! is_csrf_valid()){
//            http_response_code(202);
//            return json_encode(array("statusCode" => 202, "message"=>"Invalid CSRF Token. Please <a href='javascript:refresh_page()' onclick='refresh_page();return false;'> Refresh Page.</a>"));
//            exit();
//        }
        if (isset($password) && !empty($password)) {
            if (strlen($_POST['password']) < 8) {
                http_response_code(202);
                return json_encode(array("statusCode" => 202, "message"=>"A minimum 8 characters password is required."));
                exit();
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            }
        } else {
            http_response_code(202);
            return json_encode(array("statusCode" => 202, "message"=>"Password is Required."));
            exit();
        }
        $userAvailable = $h->table($table_name)->select()->where('email', $email);
//        $userAvailable = $CONN->query("SELECT COUNT(*) FROM $table_name WHERE `email`= '$email'")->fetchColumn();
        if($userAvailable->count() < 1){
            $verify_code=rand(9999,999999);
            try{
                $h->table($table_name)->insertOne([
                    'fname'=> $first_name,
                    'lname'=> $last_name,
                    'phone_no'=> $phone,
                    'email' => $email,
                    'password'=> $hashed_password,
                    'verify_code' => $verify_code,
                    'status' => 'block'
                ]);
//                $sql = "INSERT INTO $table_name (email, password, fullname) VALUES (?,?,?,?)";
//                $CONN->prepare($sql)->execute([$email, $hashed_password, $fullname, $country]);
                unset($_SESSION['AccountVerify']);
                $_SESSION['AccountVerify'] = $email;
                $settings= $h->table('site_setting')->select()->where('id','=','1')->fetchAll();
                include "views/email-template/account_verification.php";
                mailSender($env['ADMIN_EMAIL'],$email,'Email Verification - '.$env['SITE_NAME'],$message,$mail);
                return json_encode(array("statusCode" => 200, "message"=>"Successfully Registered."));

            }catch(PDOException $e) {
                return json_encode(array("statusCode" => 202, "message"=>"Server Side error try again!"));
                exit();
            }
        }else
            http_response_code(202);
        return json_encode(array("statusCode" => 202, "message"=>"Email Already Exist. Login to continue."));
    }
}

function setPassword($email, $password, $verify_code, $table_name){
    global $h;
//CHECK PASSWORD
    if (isset($password) && !empty($password)) {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);

        if (!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
            http_response_code(202);
            return json_encode(array("statusCode" => 202, "message"=>"A minimum 8 characters password contains a combination of uppercase and lowercase letter and number."));
            exit();
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        }
    } else {
        http_response_code(202);
        return json_encode(array("statusCode" => 202, "message"=>"Password is Required."));
        exit();
    }
    $noRows = $h->$table_name->select()
        ->where('email', '=', $email)
        ->where('verify_code', '=', $verify_code);

    // $noRows = $CONN->query("SELECT COUNT(*) FROM $table_name WHERE `email`='$email' AND verify_code='$verify_code'")->fetchColumn();
    if($noRows->count() < 1){
        http_response_code(202);
        return json_encode(array("statusCode" => 202, "message"=>"Wrong Email or Verification Code."));
    }else{
        try{
            $updatePassSQL= $h->$table_name->update([
                'password' => $hashed_password,
            ])
                ->where('email', '=', $email)
                ->where('verify_code', '=', $verify_code)->run();

//            $updatePassSQL = "UPDATE $table_name SET `password`=? WHERE `email`=? AND `verify_code`=?";
//            $CONN->prepare($updatePassSQL)->execute([$hashed_password, $email, $verify_code]);

            //change Verify Code
            $verify_code=round(microtime(true));
            $h->$table_name->update([
                'verify_code' => $verify_code,
            ])
                ->where('email', '=', $email)
                ->run();

//            $changeVerifyCodeSQL = "UPDATE $table_name SET `verify_code`=? WHERE `email`=?";
//            $CONN->prepare($changeVerifyCodeSQL)->execute([$verify_code, $email]);

            http_response_code(200);
            return json_encode(array("statusCode" => 200, "message"=>"Password Successfully Changed"));
        }catch (PDOException $e){
            http_response_code(202);
            return json_encode(array("statusCode" => 202, "message"=>$e));
        }
    }
}
function forgetPassword($email, $table_name){
    global $h;
    $userAvailable = $h->$table_name->select()
        ->where('email', '=', $email);
    // $userAvailable = $CONN->query("SELECT COUNT(*) FROM $table_name WHERE `email`='$email'")->fetchColumn();
    if($userAvailable->count() < 1){
        http_response_code(202);
        return json_encode(array("statusCode" => 202, "message"=>"Email Not Found. Please SignUp to Continue."));
        exit();
    }else{
        try{
            forgetPasswordEmail($email, $table_name);

            http_response_code(200);
            return json_encode(array("statusCode" => 200, "message"=>"Code has been sent on your Email!"));
        }catch(PDOException $e){
            http_response_code(202);
            return json_encode(array("statusCode" => 202, "message"=>$e));
        }

    }
}
///MAIL SENDER
function mailSender($admin_email,$email,$subject,$message,$mail){
    global $env;
    //Recipients
    $mail->setFrom($admin_email, $env['SITE_NAME']);
    $mail->addAddress($email);               //Name is optional
    $mail->addReplyTo($admin_email);
    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $message;

    $mail->send();
}
function forgetPasswordEmail($email, $table_name){
    global $h;
    global $env;
    global $mail;
    $verify_code=round(microtime(true));
    //SAVING VERIFICATION CODE
    $sql= $h->$table_name->update([
        'verify_code' => $verify_code,
    ])->where('email', '=', $email)->run();
    $_SESSION['reset']=$email;
    //FORGET EMAIL
    include "views/email-template/forget-password.php";
    mailSender($env['SENDER_EMAIL'],$email,'Forget Password - '.$env['SITE_NAME'],$message,$mail);

}

function define_once($name, $value){
    if (!defined($name)) define($name, $value);
}
function userDetails($loginUserId){
    global $CONN;
    $userDetails = $CONN->query("SELECT * FROM users WHERE id='$loginUserId'")->fetch();
    return $userDetails;
}
function slugify($str, $delimiter = '-')
{
    $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
    return $slug;
}

function walletDeposit($client,$amount,$asset,$wallet_id)
{
    try {
        $response = $client->post('/wallet/deposit', [
            'json' => [
                "wallet_id" => $wallet_id,
                "amount" => $amount,
                "currency" => $asset
            ]
        ]);

        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        if ($statusCode == 200 && isset($body['deposit_address'], $body['currency'])) {
            // Extract address_id using regex
            preg_match('/address_id:\s*([a-zA-Z0-9]+)/', $body['deposit_address'], $matches);
            $address_id = $matches[1] ?? null;

            // Extract currency
            $currency = $body['currency'];

            return [
                'address_id' => $address_id,
                'currency' => $currency
            ];
        } else {
            return ['error' => 'Failed to create wallet. Invalid response.'];
        }
    } catch (RequestException $e) {
        // Extract error response
        $responseBody = $e->getResponse() ? (string) $e->getResponse()->getBody() : null;
        $errorMessage = 'Unknown error occurred.';

        if ($responseBody) {
            $errorData = json_decode($responseBody, true);
            if (isset($errorData['error'])) {
                $errorMessage = $errorData['error']; // Extracting API error message
            }
        }

        return ['error' => $errorMessage];
    }
}
function createCoinBaseWebhook($apiUrl,$apiKey,$client,$wallet_id,$callback_url)
{
    $data = [
        "wallet_id" => $wallet_id,
        "callback_url" => $callback_url,
    ];

    try {
        $response = $client->post($apiUrl.'wallet/webhook', [
            'headers' => [
                'x-api-key' => $apiKey,
                'Content-Type' => 'application/json'
            ],
            'json' => $data // Automatically encodes data as JSON
        ]);

        // Get the response body
        $body = $response->getBody();
        $result = json_decode($body, true);

        echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
    } catch (RequestException $e) {
        if ($e->hasResponse()) {
            $errorResponse = $e->getResponse()->getBody()->getContents();
            echo "Error Response: " . $errorResponse . PHP_EOL;
        } else {
            echo "Request Failed: " . $e->getMessage() . PHP_EOL;
        }
    }

}

function generateStellarAccount()
{
    // Create a new random KeyPair
    $keyPair = KeyPair::random();

    return [
        'public_key' => $keyPair->getAccountId(), // Starts with "G..."
        'secret_key' => $keyPair->getSecretSeed() // Starts with "S..."
    ];
}
function fundStellarAccount($publicKey) {
    $url = "https://friendbot.stellar.org/?addr=" . urlencode($publicKey);
    $response = file_get_contents($url);
    if ($response) {
        echo "Account funded successfully.";
    } else {
        echo "Failed to fund the account.";
    }
}

function getCryptoPrice($asset) {
    $asset = strtolower($asset); // Ensure lowercase
    $validAssets = ['eth', 'usdc', 'weth'];

    if (!in_array($asset, $validAssets)) {
        return null; // Invalid asset ID
    }

    $url = "https://api.coinbase.com/v2/prices/{$asset}-USD/spot";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['data']['amount'])) {
        return floatval($data['data']['amount']);
    }

    return null; // Price fetch failed
}

function checkStellarAccountExists($publicKey) {
    $url = "https://horizon-testnet.stellar.org/accounts/$publicKey";
    $response = @file_get_contents($url);
    return $response !== false;
}
function issuePCTTokens($amount,$walletAddress,$stellarPublicKey,$stellarSecretKey) {
    $sdk = StellarSDK::getTestNetInstance(); // Use getPublicNetInstance() for production

    $issuerSecret = $stellarSecretKey; // Stellar distribution account
    $issuerKeyPair = KeyPair::fromSeed($issuerSecret);
    $issuerAccount = $sdk->requestAccount($issuerKeyPair->getAccountId());

    $custodialWalletPublic = $walletAddress; // Wallet where user balances are tracked

    // Define the PayCash token asset
    $payCashAsset = Asset::createNonNativeAsset("PCT", $stellarPublicKey);

    // Create a payment operation to send PCT tokens
    $paymentOp = (new PaymentOperationBuilder($custodialWalletPublic, $payCashAsset, strval($amount)))
        ->build();

    // Create transaction
    $transaction = (new TransactionBuilder($issuerAccount))
        ->addOperation($paymentOp)
        ->build();

    // Sign transaction
    $transaction->sign($issuerKeyPair, Network::testnet());

    // Submit transaction
    $response = $sdk->submitTransaction($transaction);

    if ($response->isSuccessful()) {
        return $response->getHash();
    } else {
        return null;
    }
}

function transferFunds($senderId, $recipientEmail, $amount) {
    global $h,$env,$mail;

    // Check if amount is valid
    if ($amount <= 0) {
        return ['status' => 'error', 'message' => 'Invalid transfer amount.'];
    }

    // Get recipient ID
    $recipient = $h->table('users')->select()->where('email','=',$recipientEmail)->fetchAll();

    if (!$recipient) {
        return ['status' => 'error', 'message' => 'Recipient not found'];
    }
    $recipientId = $recipient[0]['id'];

    // Get sender and recipient balances before transfer
    $senderBalance = getUserBalance($senderId);
    $recipientBalance = getUserBalance($recipientId);

    if ($senderBalance < $amount) {
        return ['status' => 'error', 'message' => 'Insufficient funds'];
    }
    // Deduct from sender
    $newSenderBalance = $senderBalance - $amount;
    $newRecipientBalance = $recipientBalance + $amount;

    // Log transaction
    $insert=$h->insert('transactions')->values([
        'user_id' => $senderId,
        'sender_id' => $senderId,
        'recipient_id' => $recipientId,
        'currency' => 'usd',
        'amount' => $amount,
        'amount_in_usd' => $amount,
        'transaction_type' => 'transfer',
        'sender_balance' => $newSenderBalance,
        'recipient_balance' => $newRecipientBalance,
         'status' => 'completed'
    ])->run();
  if($insert){
      $system_transaction_id=generateSystemTransactionId($insert);
      $res= $h->table('transactions')->update([
          'system_transaction_id' =>$system_transaction_id])->where('id','=',$insert)->run();
    return ['status' => 'success', 'message' => 'Transfer successful!'];
  }else{
      return ['status' => 'error', 'message' => 'Something went wrong!'];
  }
}

function getUserBalance($loginUserId){
    global $h, $env, $mail;

    $totalDepositBalance = 0;
    $totalReceivedBalance = 0;
    $totalTransferBalance = 0;
    $totalWithdrawBalance = 0;

    // ✅ Fetch Deposits
    $depositBalance = $h->table('transactions')
        ->select()
        ->where('user_id', '=', $loginUserId)
        ->where('transaction_type', '=', 'deposit')
        ->where('status', '=', 'completed')
        ->fetchAll();

    // ✅ Fetch Transfers (Sent)
    $transferBalance = $h->table('transactions')
        ->select()
        ->where('user_id', '=', $loginUserId)
        ->where('transaction_type', '=', 'transfer')
        ->where('status', '=', 'completed')
        ->fetchAll();

    // ✅ Fetch Transfers (Received)
    $receivedBalance = $h->table('transactions')
        ->select()
        ->where('recipient_id', '=', $loginUserId)
        ->where('transaction_type', '=', 'transfer')
        ->where('status', '=', 'completed')
        ->fetchAll();

    // ✅ Fetch Withdrawals
    $withdrawBalance = $h->table('transactions')
        ->select()
        ->where('user_id', '=', $loginUserId)
        ->where('transaction_type', '=', 'withdraw')
        ->where('status', '!=', 'cancelled') // Deduct even if pending
        ->fetchAll();

    // ✅ Process Deposits
    if ($depositBalance) {
        foreach ($depositBalance as $credit) {
            $totalDepositBalance += $credit['amount_in_usd'];
        }
    }

    // ✅ Process Transfers (Sent)
    if ($transferBalance) {
        foreach ($transferBalance as $debit) {
            $totalTransferBalance += $debit['amount_in_usd'];
        }
    }

    // ✅ Process Transfers (Received)
    if ($receivedBalance) {
        foreach ($receivedBalance as $credit) {
            $totalReceivedBalance += $credit['amount_in_usd'];
        }
    }

    // ✅ Process Withdrawals
    if ($withdrawBalance) {
        foreach ($withdrawBalance as $withdraw) {
            $totalWithdrawBalance += $withdraw['amount_in_usd'];
        }
    }

    // ✅ Total Balance Calculation
    $totalBalance = ($totalDepositBalance + $totalReceivedBalance) - ($totalTransferBalance + $totalWithdrawBalance);

    return $totalBalance;
}
// Function to Generate System Transaction ID
function generateSystemTransactionId($transactionId) {
    return "PTC-" . date("Ymd") . "-" . str_pad($transactionId, 5, "0", STR_PAD_LEFT);
}

?>