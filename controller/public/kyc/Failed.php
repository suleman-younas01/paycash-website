<?php
include "config/enviroment.php";
$verificationStatus=$_GET['acquisitionStatus'];
if($verificationStatus!='SUCCESS'){
    echo "<br><br><br><h2>KYC Verification Unsuccessful. Please return to the form and complete the verification process again. <a href='/'>Back to Home</a></h2>";
//    @$API_response=$_SESSION['accountCreation'];
//    @$table=$_SESSION['table'];
//    @$databaseRecordID=$_SESSION['databaseRecordID'];
//    @$accessToken=$_SESSION['accessToken'];
//    @$cardType=$_SESSION['cardType'];
//    @$accountID= $_GET['accountId'];
//    @$workflowID= $_GET['workflowExecutionId'];
//exit();
//    @$workflowID= $API_response->workflowExecution->id;
//    @$credentialsID= $API_response->workflowExecution->credentials[0]->id;
//    @$accountToken= $API_response->workflowExecution->credentials[0]->api->token;

//    $front=SaveJumioClassifier($accessToken, $accountID, $credentialsID,"FRONT");
//    $back=SaveJumioClassifier($accessToken, $accountID, $credentialsID,"BACK");
//$front='null';
//$back='null';
//    $res= $h->table($table)->update(['account_jumio_id' => $accountID, 'kyc_verification' => 'no', 'status' => 'pending'])->where('id', '=', $databaseRecordID)->execute();
//    if($res){
//        $permission=$h->table('permissions')->select()->where('table_name','=',$table)->one();
//        $formName=$permission['form_name'];
//        $path=$permission['path'];
//        $_SESSION['msg']='0';
//
//    }
//    header("Location: /update/".$path."/".$databaseRecordID);
}
?>