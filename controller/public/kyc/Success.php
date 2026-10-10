<?php
include "config/env.php";
@$verificationStatus=$_GET['acquisitionStatus'];
if($verificationStatus=='SUCCESS'){
    $API_response=$_SESSION['KycData']['accountCreation'];
    $accessToken=$_SESSION['KycData']['accessToken'];
    $table=$_SESSION['userData']['table'];
    @$databaseRecordID=$_SESSION['userData']['databaseRecordID'];
    @$cardType=$_SESSION['userData']['cardType'];
    @$accountID= $_GET['accountId'];
    @$workflowID= $_GET['workflowExecutionId'];
    @$credentialsID= $API_response->workflowExecution->credentials[0]->id;
    @$accountToken= $API_response->workflowExecution->credentials[0]->api->token;
    $otherDetail=GetWorkFlowDetails($accessToken, $accountID, $workflowID);

    @$o = json_decode($otherDetail);
//    @$dataArray=$o->capabilities->extraction[0]->data->type;
//   echo $type= $dataArray[0]->data->type;
    @$type = $o->capabilities->extraction[0]->data->type;

     $front=SaveJumioClassifier($accessToken, $accountID, $credentialsID,"FRONT");
     $f= json_decode($front);
     $front=$f->imageName;
    if($cardType!='PASSPORT'){
        @$back=SaveJumioClassifier($accessToken, $accountID, $credentialsID,"BACK");
        $b= json_decode($back);
        $back=$b->imageName;
    }else{
        $back='null';
    }
    if($type == 'ID_CARD'){
        $type='id card';
    }elseif($type == 'DRIVING_LICENSE'){
        $type='Driving license';
    }else{
        $type='passport';
    }
    $permission=$h->table('permissions')->select()->where('table_name','=',$table)->fetchAll();
    $checkTable=$h->table($table)->select()->where('id','=',$databaseRecordID)->fetchAll();
    if($permission[0]['price'] > '0') {
        if (empty($checkTable[0]['transaction_id'])) {
            $status = 'pending';
        } else {
            $status = 'completed';
        }
    }else{
        $status = 'completed';
    }

    $res= $h->table($table)->update(['account_jumio_id' => $accountID, 'kyc_verification' => 'yes', 'passport' => $type,'uploadfront' => $front, 'uploadback' => $back, 'status' => $status])->where('id', '=', $databaseRecordID)->run();
    if($res){
        $formName=$permission[0]['form_name'];
        $path=$permission[0]['path'];
        $payment=$permission[0]['price'];
//        $_SESSION['msg']='1';
//        if($payment > '0'){
//            if(empty($checkTable[0]['transaction_id'])){
//                header("Location: /payment-gateway".$path."/".$databaseRecordID);
//            }else{
//                header("Location: /view".$path."/".$formName);
//            }
//        }else{
            header("Location: /applications/".$table);

    }
}
?>

