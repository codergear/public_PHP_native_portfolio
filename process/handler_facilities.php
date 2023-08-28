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
    if ($retriveData->logo_img_data !=''){
        $rawImageData = explode( ',', $retriveData->logo_img_data );
        $fileType = strtolower($rawImageData[0]);
        $fileType =  str_replace(';base64','',$fileType);
        $fileType =  str_replace('data:image/','',$fileType);
        $retriveData->logo = strtolower(uniqid()) . '.' . $fileType;
        $fullPath = '../'.'assets/images/'.$retriveData->logo;
        $rawFile = fopen( $fullPath, 'wb' );
        fwrite( $rawFile, base64_decode( $rawImageData[1] ) );
        fclose( $rawFile ); 
    };
        $facilities = new classfacilities;
        $facilities->name = $retriveData->name;
        $facilities->phone = $retriveData->phone;
        $facilities->email = $retriveData->email;
        $facilities->contact_person = $retriveData->contact_person;
        $facilities->address = $retriveData->address;
        $facilities->city = $retriveData->city;
        $facilities->state = $retriveData->state;
        $facilities->zip_code = $retriveData->zip_code;
        $facilities->notes = $retriveData->notes;
        $facilities->logo = $retriveData->logo;
     $OPCODE_RESULT = $facilities->insert($facilities,$id_user);
     if($OPCODE_RESULT){
        $retriveData->id_facilities = $OPCODE_RESULT;
        Functions::security_access_log($id_user, 'facilities', 'create', $retriveData->id_facilities);
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
       $facilities = new classfacilities();     
       if ($retriveData->id_facilities != 0){
           $facilities_list = $facilities->selectBy('id_facilities', $retriveData->id_facilities);
           $facilities_list = $facilities_list ? $facilities_list[0] : null ;
       }else{
           $facilities_list =  $facilities->selectAll();
       }
       $states=new classstates();
       $states=$states->selectAll();
       if ($states){
           $states = Functions::sort_items_by($states,'state_name','ASC');
       };
       if (($facilities_list) && ($retriveData->id_facilities == 0)){
           foreach ($facilities_list as $facilities_item){
               foreach ($states as $item){
                   if ($item->id_states == $facilities_item->state){
                       $facilities_item->_state = $item->state_name;
                   }
               }
               if (!isset($facilities_item->_state)){$facilities_item->_state='';}
           }
       } 
       if (($facilities_list) && ($retriveData->id_facilities == 0)){
           foreach ($facilities_list as $facilities_item){
              $facilities_item->_logo = 'assets/images/'.$facilities_item->logo;
           }
       }
       if (($facilities_list) && ($retriveData->id_facilities > 0)){
              $facilities_list->_logo = 'assets/images/'.$facilities_list->logo;
       }
       if(!$facilities_list){
           $facilities_list = new classfacilities;
       }else{
       }
       if ($retriveData->id_affiliates_status != '-1') {
          Functions::security_access_log($id_user, 'facilities', 'read', $retriveData->id_facilities);
       }
       http_response_code(200);
       echo '{"message":"Successful","status":200,"data":{"facilities":'. json_encode($facilities_list).',"states":'. json_encode($states).'}}';
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
    if ($retriveData->logo_img_data !=''){
        $rawImageData = explode( ',', $retriveData->logo_img_data );
        $fileType = strtolower($rawImageData[0]);
        $fileType =  str_replace(';base64','',$fileType);
        $fileType =  str_replace('data:image/','',$fileType);
        $retriveData->logo = strtolower(uniqid()) . '.' . $fileType;
        $fullPath = '../'.'assets/images/'.$retriveData->logo;
        $rawFile = fopen( $fullPath, 'wb' );
        fwrite( $rawFile, base64_decode( $rawImageData[1] ) );
        fclose( $rawFile ); 
    };
    $facilities = new classfacilities;   
    $facilities->name = $retriveData->name;
    $facilities->phone = $retriveData->phone;
    $facilities->email = $retriveData->email;
    $facilities->contact_person = $retriveData->contact_person;
    $facilities->address = $retriveData->address;
    $facilities->city = $retriveData->city;
    $facilities->state = $retriveData->state;
    $facilities->zip_code = $retriveData->zip_code;
    $facilities->notes = $retriveData->notes;
    $facilities->logo = $retriveData->logo;
    $facilities->id_facilities = $retriveData->id_facilities;
    $OPCODE_RESULT = $facilities->update($facilities,$id_user);
        if($OPCODE_RESULT){
        Functions::security_access_log($id_user, 'facilities', 'update', $retriveData->id_facilities);
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
      $facilities = new classfacilities;   
      $QUERY_RESULT = $facilities->delete($retriveData->id_facilities,$id_user);
      if($QUERY_RESULT->error_flag === false ){
        Functions::security_access_log($id_user, 'facilities', 'delete', $retriveData->id_facilities);
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
