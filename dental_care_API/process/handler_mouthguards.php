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
        $mouthguards = new classmouthguards;
        $mouthguards->manufacturer = $retriveData->manufacturer;
        $mouthguards->model = $retriveData->model;
        $mouthguards->notes = $retriveData->notes;
     $OPCODE_RESULT = $mouthguards->insert($mouthguards,$id_user);
     if($OPCODE_RESULT){
        $retriveData->id_mouthguards = $OPCODE_RESULT;
        Functions::security_access_log($id_user, 'mouthguards', 'create', $retriveData->id_mouthguards);
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
       $mouthguards = new classmouthguards();     
       if ($retriveData->id_mouthguards != 0){
           $mouthguards_list = $mouthguards->selectBy('id_mouthguards', $retriveData->id_mouthguards);
           $mouthguards_list = $mouthguards_list ? $mouthguards_list[0] : null ;
       }else{
           $mouthguards_list =  $mouthguards->selectAll();
       }
       $mouthguards_manufacturer=new classmouthguards_manufacturer();
       $mouthguards_manufacturer=$mouthguards_manufacturer->selectAll();
       if (($mouthguards_list) && ($retriveData->id_mouthguards == 0)){
           foreach ($mouthguards_list as $mouthguards_item){
               foreach ($mouthguards_manufacturer as $item){
                   if ($item->id_mouthguards_manufacturer == $mouthguards_item->manufacturer){
                       $mouthguards_item->_manufacturer = $item->manufacturer;
                   }
               }
               if (!isset($mouthguards_item->_manufacturer)){$mouthguards_item->_manufacturer='';}
           }
       } 
       $mouthguards_models=new classmouthguards_models();
       $mouthguards_models=$mouthguards_models->selectAll();
       if (($mouthguards_list) && ($retriveData->id_mouthguards == 0)){
           foreach ($mouthguards_list as $mouthguards_item){
               foreach ($mouthguards_models as $item){
                   if ($item->id_mouthguards_models == $mouthguards_item->model){
                       $mouthguards_item->_model = $item->models;
                   }
               }
               if (!isset($mouthguards_item->_model)){$mouthguards_item->_model='';}
           }
       } 
       if(!$mouthguards_list){
           $mouthguards_list = new classmouthguards;
       }else{
       }
       if ($retriveData->id_affiliates_status != '-1') {
          Functions::security_access_log($id_user, 'mouthguards', 'read', $retriveData->id_mouthguards);
       }
       http_response_code(200);
       echo '{"message":"Successful","status":200,"data":{"mouthguards":'. json_encode($mouthguards_list).',"mouthguards_manufacturer":'. json_encode($mouthguards_manufacturer).',"mouthguards_models":'. json_encode($mouthguards_models).'}}';
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
    $mouthguards = new classmouthguards;   
    $mouthguards->manufacturer = $retriveData->manufacturer;
    $mouthguards->model = $retriveData->model;
    $mouthguards->notes = $retriveData->notes;
    $mouthguards->id_mouthguards = $retriveData->id_mouthguards;
    $OPCODE_RESULT = $mouthguards->update($mouthguards,$id_user);
        if($OPCODE_RESULT){
        Functions::security_access_log($id_user, 'mouthguards', 'update', $retriveData->id_mouthguards);
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
      $mouthguards = new classmouthguards;   
      $QUERY_RESULT = $mouthguards->delete($retriveData->id_mouthguards,$id_user);
      if($QUERY_RESULT->error_flag === false ){
        Functions::security_access_log($id_user, 'mouthguards', 'delete', $retriveData->id_mouthguards);
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
