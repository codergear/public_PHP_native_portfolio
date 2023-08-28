<?php
 namespace gtc_core;

session_name("dental_care");
$id_user =0;
if(!isset($_SESSION)) {
      session_start(); 
  } 
if(isset($_SESSION["id_user"])){
    $id_user = $_SESSION["id_user"];
}

set_error_handler(function($errno, $errstr, $errfile, $errline){
   if($errno === E_USER_WARNING){
      trigger_error($errstr, E_ERROR);
      return true;
   } 
});

foreach (glob('../config/*.php') as $class_filename)
{
    require $class_filename;
}

foreach (glob('../class/*.php') as $class_filename)
{
    require $class_filename;
}

date_default_timezone_set("America/New_York"); 
header('Content-type:application/json');
Functions::security_access_log($id_user, basename(__FILE__), ' API Call', 0);

$retriveData = json_decode(file_get_contents('php://input'));

if ((isset($retriveData->coreAction)) && (strlen($retriveData->coreAction) > 2)) {
   $retriveData->CoreAction = $retriveData->coreAction;
}

if ((isset($retriveData->coreaction)) && (strlen($retriveData->coreaction) > 2)) {
   $retriveData->CoreAction = $retriveData->coreaction;
}

if ((isset($retriveData->Coreaction)) && (strlen($retriveData->Coreaction) > 2)) {
   $retriveData->CoreAction = $retriveData->Coreaction;
}

if (!(isset($retriveData->CoreAction))) {
    bad_request();
}

$coreAction = $retriveData->CoreAction ;

if ((strpos($coreAction, 'custom_') !== false) or (strpos($coreAction, 'extend_') !== false)){
   $extend = basename(__FILE__, '.php') . '.extend.php';
   if (file_exists($extend)) {
      require $extend;
      return false;
   } else {
      http_response_code(400);
      echo '{"message":"bad request","status":400,"data":"missing '. $extend .'"}'; 
      return false;
   }
}

switch($coreAction) {
        case 'test' : test($retriveData);
                      break;
        case 'create' : create($retriveData,$id_user);
                      break;
        case 'read' : read($retriveData,$id_user);
                      break;
        case 'update' : update($retriveData,$id_user);
                      break;
        case 'delete' : delete($retriveData,$id_user);
                      break;
        default : bad_request();
}

function bad_request() {
    Functions::security_access_log(0, basename(__FILE__). ' '.str_replace('\\', '.', __FUNCTION__), ' executed', 0);
    http_response_code(400);
    echo '{"message":"bad request","status":400,"data":[]}';  
    return false;
}

function test($retriveData) {
    Functions::security_access_log(0, basename(__FILE__). ' '.str_replace('\\', '.', __FUNCTION__), ' executed', 0);
    http_response_code(200);
    echo '{"message":"Successful","status":200,"data":['. json_encode($retriveData).']}'; 
    return false;
}

function create($retriveData,$id_user) {
   Functions::security_access_log($id_user, basename(__FILE__). ' '.str_replace('\\', '.', __FUNCTION__), ' executed', 0);
  try {
        $retriveData->payment_period=$retriveData->begin_date . ' ' . $retriveData->end_date;
    if ($retriveData->check_picture_img_data !=''){
        $rawImageData = explode( ',', $retriveData->check_picture_img_data );
        $fileType = strtolower($rawImageData[0]);
        $fileType =  str_replace(';base64','',$fileType);
        $fileType =  str_replace('data:image/','',$fileType);
        $retriveData->check_picture = strtolower(uniqid()) . '.' . $fileType;
        $fullPath = '../'.'assets/images/'.$retriveData->check_picture;
        $rawFile = fopen( $fullPath, 'wb' );
        fwrite( $rawFile, base64_decode( $rawImageData[1] ) );
        fclose( $rawFile ); 
    };
        $group_pay_processing = new classgroup_pay_processing;
        $group_pay_processing->is_cancelled = $retriveData->is_cancelled;
        $group_pay_processing->payment_status = $retriveData->payment_status;
        $group_pay_processing->begin_date = $retriveData->begin_date;
        $group_pay_processing->end_date = $retriveData->end_date;
        $group_pay_processing->payment_date = $retriveData->payment_date;
        $group_pay_processing->payee = $retriveData->payee;
        $group_pay_processing->check_number = $retriveData->check_number;
        $group_pay_processing->check_amount = $retriveData->check_amount;
        $group_pay_processing->payment_period = $retriveData->payment_period;
        $group_pay_processing->check_picture = $retriveData->check_picture;
     $OPCODE_RESULT = $group_pay_processing->insert($group_pay_processing,$id_user);
     if($OPCODE_RESULT){
        $retriveData->id_group_pay_processing = $OPCODE_RESULT;
        Functions::security_access_log($id_user, 'group_pay_processing', 'create', $retriveData->id_group_pay_processing);
        http_response_code(200);
        echo '{"message":"Successful","status":200,"data":[]}';
     }else{
        http_response_code(500);
        echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
     }
   } catch (\Throwable | \Exception $e) { 
      Functions::security_access_log($id_user, basename(__FILE__). ' '.str_replace('\\', '.', __FUNCTION__).  ' exception', $e->getMessage(), 0);
      http_response_code(500);
      if ((Functions::get_enviroment() != 'production') && (Functions::get_enviroment() != 'sandbox')){
         $exception_file = explode('\\',$e->getFile()) ;
         $exception_file = end( $exception_file );
         $exception_msg = $e->getMessage() . ' in ' . $exception_file .':' .$e->getLine() ;
         $exception_msg = str_replace('"',"'", $exception_msg );
         echo '{"message":"error","status":500,"data":"'. $exception_msg .'"}';
      }else {
         echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
      }
   } finally {
     return false;
  }
}

function read($retriveData,$id_user) {
   Functions::security_access_log($id_user, basename(__FILE__). ' '.str_replace('\\', '.', __FUNCTION__), ' executed', 0);
   try {
       $group_pay_processing = new classgroup_pay_processing();     
       if ($retriveData->id_group_pay_processing != 0){
           $group_pay_processing_list = $group_pay_processing->selectBy('id_group_pay_processing', $retriveData->id_group_pay_processing);
           $group_pay_processing_list = $group_pay_processing_list ? $group_pay_processing_list[0] : null ;
       }else{
           $group_pay_processing_list =  $group_pay_processing->selectAll();
       }
       $groups=new classgroups();
       $groups=$groups->selectAll();
       if (($group_pay_processing_list) && ($retriveData->id_group_pay_processing == 0)){
           foreach ($group_pay_processing_list as $group_pay_processing_item){
               foreach ($groups as $item){
                   if ($item->id_groups == $group_pay_processing_item->payee){
                       $group_pay_processing_item->_payee = $item->entity_name;
                   }
               }
               if (!isset($group_pay_processing_item->_payee)){$group_pay_processing_item->_payee='';}
           }
       } 
       if (($group_pay_processing_list) && ($retriveData->id_group_pay_processing == 0)){
           foreach ($group_pay_processing_list as $group_pay_processing_item){
              $group_pay_processing_item->_check_picture = 'assets/images/'.$group_pay_processing_item->check_picture;
           }
       }
       if (($group_pay_processing_list) && ($retriveData->id_group_pay_processing > 0)){
              $group_pay_processing_list->_check_picture = 'assets/images/'.$group_pay_processing_list->check_picture;
       }
       if(!$group_pay_processing_list){
           $group_pay_processing_list = new classgroup_pay_processing;
       }else{
           if ($retriveData->id_group_pay_processing == 0){
               $group_pay_processing_list = Functions::filter_items_by_interval($group_pay_processing_list,'payment_date',$retriveData->datepickerCustomFilterBegin,$retriveData->datepickerCustomFilterEnd);
           }
       }
       if ($retriveData->id_affiliates_status != '-1') {
          Functions::security_access_log($id_user, 'group_pay_processing', 'read', $retriveData->id_group_pay_processing);
       }
       http_response_code(200);
       echo '{"message":"Successful","status":200,"data":{"group_pay_processing":'. json_encode($group_pay_processing_list).',"groups":'. json_encode($groups).'}}';
   } catch (\Throwable | \Exception $e) { 
      Functions::security_access_log($id_user, basename(__FILE__). ' '.str_replace('\\', '.', __FUNCTION__).  ' exception', $e->getMessage(), 0);
      http_response_code(500);
      if ((Functions::get_enviroment() != 'production') && (Functions::get_enviroment() != 'sandbox')){
         $exception_file = explode('\\',$e->getFile()) ;
         $exception_file = end( $exception_file );
         $exception_msg = $e->getMessage() . ' in ' . $exception_file .':' .$e->getLine() ;
         $exception_msg = str_replace('"',"'", $exception_msg );
         echo '{"message":"error","status":500,"data":"'. $exception_msg .'"}';
      }else {
         echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
      }
   } finally {
       return false;
   }
}

function update($retriveData,$id_user) {
   Functions::security_access_log($id_user, basename(__FILE__). ' '.str_replace('\\', '.', __FUNCTION__), ' executed', 0);
   try {
    $retriveData->payment_period=$retriveData->begin_date . ' ' . $retriveData->end_date;
    if ($retriveData->check_picture_img_data !=''){
        $rawImageData = explode( ',', $retriveData->check_picture_img_data );
        $fileType = strtolower($rawImageData[0]);
        $fileType =  str_replace(';base64','',$fileType);
        $fileType =  str_replace('data:image/','',$fileType);
        $retriveData->check_picture = strtolower(uniqid()) . '.' . $fileType;
        $fullPath = '../'.'assets/images/'.$retriveData->check_picture;
        $rawFile = fopen( $fullPath, 'wb' );
        fwrite( $rawFile, base64_decode( $rawImageData[1] ) );
        fclose( $rawFile ); 
    };
    $group_pay_processing = new classgroup_pay_processing;   
    $group_pay_processing->is_cancelled = $retriveData->is_cancelled;
    $group_pay_processing->payment_status = $retriveData->payment_status;
    $group_pay_processing->begin_date = $retriveData->begin_date;
    $group_pay_processing->end_date = $retriveData->end_date;
    $group_pay_processing->payment_date = $retriveData->payment_date;
    $group_pay_processing->payee = $retriveData->payee;
    $group_pay_processing->check_number = $retriveData->check_number;
    $group_pay_processing->check_amount = $retriveData->check_amount;
    $group_pay_processing->payment_period = $retriveData->payment_period;
    $group_pay_processing->check_picture = $retriveData->check_picture;
    $group_pay_processing->id_group_pay_processing = $retriveData->id_group_pay_processing;
    $OPCODE_RESULT = $group_pay_processing->update($group_pay_processing,$id_user);
        if($OPCODE_RESULT){
        Functions::security_access_log($id_user, 'group_pay_processing', 'update', $retriveData->id_group_pay_processing);
            http_response_code(200);
            echo '{"message":"Successful","status":200,"data":[]}';
        }else{
            http_response_code(500);
            echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
        }
    } catch (\Throwable | \Exception $e) { 
      Functions::security_access_log($id_user, basename(__FILE__). ' '.str_replace('\\', '.', __FUNCTION__).  ' exception', $e->getMessage(), 0);
        http_response_code(500);
        if ((Functions::get_enviroment() != 'production') && (Functions::get_enviroment() != 'sandbox')){
            $exception_file = explode('\\',$e->getFile()) ;
            $exception_file = end( $exception_file );
            $exception_msg = $e->getMessage() . ' in ' . $exception_file .':' .$e->getLine() ;
            $exception_msg = str_replace('"',"'", $exception_msg );
            echo '{"message":"error","status":500,"data":"'. $exception_msg .'"}';
        }else {
            echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
        }
    } finally {
        return false;
    }
}

function delete($retriveData,$id_user) {
   Functions::security_access_log($id_user, basename(__FILE__). ' '.str_replace('\\', '.', __FUNCTION__), ' executed', 0);
   try {
      $group_pay_processing = new classgroup_pay_processing;   
      $QUERY_RESULT = $group_pay_processing->delete($retriveData->id_group_pay_processing,$id_user);
      if($QUERY_RESULT->error_flag === false ){
        Functions::security_access_log($id_user, 'group_pay_processing', 'delete', $retriveData->id_group_pay_processing);
         http_response_code(200);
         echo '{"message":"Successful","status":200,"data":[]}';
      }else{
         http_response_code(500);
         echo '{"message":"error","status":500,"data":"'.$QUERY_RESULT->error_msg.'"}';
      }
   } catch (\Throwable | \Exception $e) { 
      Functions::security_access_log($id_user, basename(__FILE__). ' '.str_replace('\\', '.', __FUNCTION__).  ' exception', $e->getMessage(), 0);
      http_response_code(500);
      if ((Functions::get_enviroment() != 'production') && (Functions::get_enviroment() != 'sandbox')){
         $exception_file = explode('\\',$e->getFile()) ;
         $exception_file = end( $exception_file );
         $exception_msg = $e->getMessage() . ' in ' . $exception_file .':' .$e->getLine() ;
         $exception_msg = str_replace('"',"'", $exception_msg );
         echo '{"message":"error","status":500,"data":"'. $exception_msg .'"}';
      }else {
         echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
      }
   } finally {
      return false;
   }
}
return false;
?>
