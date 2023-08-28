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
    if ($retriveData->photo_img_data !=''){
        $rawImageData = explode( ',', $retriveData->photo_img_data );
        $fileType = strtolower($rawImageData[0]);
        $fileType =  str_replace(';base64','',$fileType);
        $fileType =  str_replace('data:image/','',$fileType);
        $retriveData->photo = strtolower(uniqid()) . '.' . $fileType;
        $fullPath = '../'.'assets/images/'.$retriveData->photo;
        $rawFile = fopen( $fullPath, 'wb' );
        fwrite( $rawFile, base64_decode( $rawImageData[1] ) );
        fclose( $rawFile ); 
    };
        $security_users = new classsecurity_users;
        $security_users->users_status = $retriveData->users_status;
        $security_users->user = $retriveData->user;
        $security_users->pass = $retriveData->pass;
        $security_users->full_name = $retriveData->full_name;
        $security_users->origin = $retriveData->origin;
        $security_users->phone = $retriveData->phone;
        $security_users->email = $retriveData->email;
        $security_users->role = $retriveData->role;
        $security_users->photo = $retriveData->photo;
     // extend PHP_create

        $security_users->user = strtolower(str_replace(" ","", $security_users->user)); 

        if (is_array((new classsecurity_users)->selectBy('user', $security_users->user))){
            $security_users->user = $security_users->user . '_' . uniqid();
        }
        if (is_array((new classsecurity_users)->selectBy('email', $security_users->email))){
            http_response_code(400);
            echo '{"message":"error","status":400,"data":"duplicated email, email already exist"}';            
            return false;
        } 
        if (is_array((new classsecurity_users)->selectBy('phone', $security_users->phone))){
            http_response_code(400);
            echo '{"message":"error","status":400,"data":"duplicated phone, phone already exist"}';
            return false;
        } 

        require_once('../integration/integration_sendgrid.php');
        if (isset($email_sendgrid) && isset($sendgrid)) {
            try {
                $email_sendgrid->addTo($security_users->email);             
                $email_sendgrid->setSubject('dentalCare account created');
                $email_message = ConfigMailTemplate::new_account_template($security_users->user, $security_users->pass);
                $email_sendgrid->addContent("text/html", $email_message);
                $sendgrid_response = $sendgrid->send($email_sendgrid);
                Functions::security_access_log($id_user, 'secUser create account', 'mail notification', $security_users->user);
            } catch (\Throwable | \Exception $e) {
                Functions::security_access_log($id_user, 'secUser create account', 'mail notification error ', $security_users->user);
            }
        }
        
     // extend PHP_create
     $OPCODE_RESULT = $security_users->insert($security_users,$id_user);
     if($OPCODE_RESULT){
        $retriveData->id_security_users = $OPCODE_RESULT;
        Functions::security_access_log($id_user, 'security_users', 'create', $retriveData->id_security_users);
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
       $security_users = new classsecurity_users();     
       if ($retriveData->id_security_users != 0){
           $security_users_list = $security_users->selectBy('id_security_users', $retriveData->id_security_users);
           $security_users_list = $security_users_list ? $security_users_list[0] : null ;
       }else{
           $security_users_list =  $security_users->selectAll();
       }
       $security_users_status=new classsecurity_users_status();
       $security_users_status=$security_users_status->selectAll();
       if (($security_users_list) && ($retriveData->id_security_users == 0)){
           foreach ($security_users_list as $security_users_item){
               foreach ($security_users_status as $item){
                   if ($item->id_security_users_status == $security_users_item->users_status){
                       $security_users_item->_users_status = $item->status;
                   }
               }
               if (!isset($security_users_item->_users_status)){$security_users_item->_users_status='';}
           }
       } 
       $security_user_origin=new classsecurity_user_origin();
       $security_user_origin=$security_user_origin->selectAll();
       if (($security_users_list) && ($retriveData->id_security_users == 0)){
           foreach ($security_users_list as $security_users_item){
               foreach ($security_user_origin as $item){
                   if ($item->id_security_user_origin == $security_users_item->origin){
                       $security_users_item->_origin = $item->origin;
                   }
               }
               if (!isset($security_users_item->_origin)){$security_users_item->_origin='';}
           }
       } 
       $security_roles=new classsecurity_roles();
       $security_roles=$security_roles->selectAll();
       if (($security_users_list) && ($retriveData->id_security_users == 0)){
           foreach ($security_users_list as $security_users_item){
               foreach ($security_roles as $item){
                   if ($item->id_security_roles == $security_users_item->role){
                       $security_users_item->_role = $item->name;
                   }
               }
               if (!isset($security_users_item->_role)){$security_users_item->_role='';}
           }
       } 
       if (($security_users_list) && ($retriveData->id_security_users == 0)){
           foreach ($security_users_list as $security_users_item){
              $security_users_item->_photo = 'assets/images/'.$security_users_item->photo;
           }
       }
       if (($security_users_list) && ($retriveData->id_security_users > 0)){
              $security_users_list->_photo = 'assets/images/'.$security_users_list->photo;
       }
     // extend PHP_read
      if(is_array($security_users_list)){
         foreach ($security_users_list as $item) {
            $item->pass = 'HiddenP4$$';
         } 
      } else {
        if($security_users_list){
            $security_users_list->pass = 'HiddenP4$$';
        }
      }
     // extend PHP_read
       if(!$security_users_list){
           $security_users_list = new classsecurity_users;
       }else{
       }
       if ($retriveData->id_affiliates_status != '-1') {
          Functions::security_access_log($id_user, 'security_users', 'read', $retriveData->id_security_users);
       }
       http_response_code(200);
       echo '{"message":"Successful","status":200,"data":{"security_users":'. json_encode($security_users_list).',"security_users_status":'. json_encode($security_users_status).',"security_user_origin":'. json_encode($security_user_origin).',"security_roles":'. json_encode($security_roles).'}}';
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
    if ($retriveData->photo_img_data !=''){
        $rawImageData = explode( ',', $retriveData->photo_img_data );
        $fileType = strtolower($rawImageData[0]);
        $fileType =  str_replace(';base64','',$fileType);
        $fileType =  str_replace('data:image/','',$fileType);
        $retriveData->photo = strtolower(uniqid()) . '.' . $fileType;
        $fullPath = '../'.'assets/images/'.$retriveData->photo;
        $rawFile = fopen( $fullPath, 'wb' );
        fwrite( $rawFile, base64_decode( $rawImageData[1] ) );
        fclose( $rawFile ); 
    };
    $security_users = new classsecurity_users;   
    $security_users->users_status = $retriveData->users_status;
    $security_users->user = $retriveData->user;
    $security_users->pass = $retriveData->pass;
    $security_users->full_name = $retriveData->full_name;
    $security_users->origin = $retriveData->origin;
    $security_users->phone = $retriveData->phone;
    $security_users->email = $retriveData->email;
    $security_users->role = $retriveData->role;
    $security_users->photo = $retriveData->photo;
    $security_users->id_security_users = $retriveData->id_security_users;
     // extend PHP_update

        $security_users->user = strtolower(str_replace(" ","", $security_users->user)); 
        
        $security_user_validator = (new classsecurity_users)->selectBy('id_security_users', $retriveData->id_security_users);
        $security_user_validator =  $security_user_validator ? $security_user_validator[0] : null ;

        $security_email_validator = (new classsecurity_users)->selectBy('email', $retriveData->email);
        if (is_array($security_email_validator)){
            if (count($security_email_validator) > 1){   
                http_response_code(400);
                echo '{"message":"error","status":400,"data":"duplicated email, email already exist"}';
                return false;
            } else {
                if ($security_users && $security_user_validator ){
                    if ($security_users->email != $security_user_validator->email){
                        http_response_code(400);
                        echo '{"message":"error","status":400,"data":"duplicated email, email already exist"}';
                        return false;
                    }
                }
            }
        }  

        if (($security_users->role != 7) && ($security_users->role != 9)){
            $security_phone_validator = (new classsecurity_users)->selectBy('phone', $retriveData->phone);
            if (is_array($security_phone_validator)){
                if (count($security_phone_validator) > 1){   
                    http_response_code(400);
                    echo '{"message":"error","status":400,"data":"duplicated phone, phone already exist"}';
                    return false;
                } else {
                    if ($security_users && $security_user_validator ){
                        if ($security_users->phone != $security_user_validator->phone){
                            http_response_code(400);
                            echo '{"message":"error","status":400,"data":"duplicated phone, phone already exist"}';
                            return false;
                        }
                    }
                }
            }  
        }

        if ($security_users->pass == 'HiddenP4$$') {
            $security_users_item = new classsecurity_users;
            $security_users_item = $security_users_item->selectBy("id_security_users", $security_users->id_security_users)[0];
            $security_users->pass = $security_users_item->pass;
        }else{
            $security_users->_new_pass_flag = true;
        }

     // extend PHP_update
    $OPCODE_RESULT = $security_users->update($security_users,$id_user);
        if($OPCODE_RESULT){
        Functions::security_access_log($id_user, 'security_users', 'update', $retriveData->id_security_users);
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
      $security_users = new classsecurity_users;   
      $QUERY_RESULT = $security_users->delete($retriveData->id_security_users,$id_user);
      if($QUERY_RESULT->error_flag === false ){
        Functions::security_access_log($id_user, 'security_users', 'delete', $retriveData->id_security_users);
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
