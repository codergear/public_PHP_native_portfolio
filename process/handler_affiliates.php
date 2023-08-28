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
        $retriveData->full_name=$retriveData->first_name . ' ' . $retriveData->last_name;
    if ($retriveData->picture_img_data !=''){
        $rawImageData = explode( ',', $retriveData->picture_img_data );
        $fileType = strtolower($rawImageData[0]);
        $fileType =  str_replace(';base64','',$fileType);
        $fileType =  str_replace('data:image/','',$fileType);
        $retriveData->picture = strtolower(uniqid()) . '.' . $fileType;
        $fullPath = '../'.'assets/images/'.$retriveData->picture;
        $rawFile = fopen( $fullPath, 'wb' );
        fwrite( $rawFile, base64_decode( $rawImageData[1] ) );
        fclose( $rawFile ); 
    };
        $affiliates = new classaffiliates;
        $affiliates->status = $retriveData->status;
        $affiliates->agreement_date = $retriveData->agreement_date;
        $affiliates->afiliate_level = $retriveData->afiliate_level;
        $affiliates->manager_afiliate = $retriveData->manager_afiliate;
        $affiliates->affiliate_to = $retriveData->affiliate_to;
        $affiliates->first_name = $retriveData->first_name;
        $affiliates->last_name = $retriveData->last_name;
        $affiliates->full_name = $retriveData->full_name;
        $affiliates->birth_date = $retriveData->birth_date;
        $affiliates->phone = $retriveData->phone;
        $affiliates->email = $retriveData->email;
        $affiliates->preferred_method_of_contact = $retriveData->preferred_method_of_contact;
        $affiliates->occupation = $retriveData->occupation;
        $affiliates->company = $retriveData->company;
        $affiliates->commission = $retriveData->commission;
        $affiliates->address = $retriveData->address;
        $affiliates->city = $retriveData->city;
        $affiliates->state = $retriveData->state;
        $affiliates->zip_code = $retriveData->zip_code;
        $affiliates->notes = $retriveData->notes;
        $affiliates->picture = $retriveData->picture;
        $affiliates->filemanagerlist = $retriveData->filemanagerlist;
        $affiliates->linked_user = $retriveData->linked_user;
     // extend PHP_create

            $security_users = new classsecurity_users;
            $security_users->users_status = 2;
            $security_users->user = strtolower(str_replace(" ","", $retriveData->first_name)); 
            $flat_pass = strtolower(Functions::random_password(10));
            $security_users->pass = $flat_pass;
            $security_users->full_name = $retriveData->full_name;
            $security_users->origin = 1;
            $security_users->phone =  $retriveData->phone;
            $security_users->email = $retriveData->email;
            if ($retriveData->afiliate_level == '1'){   $security_users->role = 4; }
            if ($retriveData->afiliate_level == '2'){   $security_users->role = 5; }
            if ($retriveData->afiliate_level == '3'){   $security_users->role = 6; }
            $security_users->photo = $retriveData->picture;
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
            $USER_OPCODE_RESULT = $security_users->insert($security_users,$id_user);
            if ($USER_OPCODE_RESULT) {
                $affiliates->linked_user =  $USER_OPCODE_RESULT;
                require_once('../integration/integration_sendgrid.php');
                if (isset($email_sendgrid) && isset($sendgrid)) {
                    try {
                        $email_sendgrid->addTo($security_users->email);             
                        $email_sendgrid->setSubject('dentalCare account created');
                        $email_message = ConfigMailTemplate::new_account_template($security_users->user, $flat_pass);
                        $email_sendgrid->addContent("text/html", $email_message);
                        $sendgrid_response = $sendgrid->send($email_sendgrid);
                        Functions::security_access_log($id_user, 'affiliates create account', 'mail notification', $security_users->id_security_users);
                    } catch (\Exception $e) {
                        Functions::security_access_log($id_user, 'affiliates create account', 'mail notification error ', $security_users->id_security_users);
                    }
                }
            } else{
                http_response_code(500);
                echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
                return false;
            }

     // extend PHP_create
     $OPCODE_RESULT = $affiliates->insert($affiliates,$id_user);
     if($OPCODE_RESULT){
        $retriveData->id_affiliates = $OPCODE_RESULT;
        Functions::security_access_log($id_user, 'affiliates', 'create', $retriveData->id_affiliates);
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
       $affiliates = new classaffiliates();     
       if ($retriveData->id_affiliates != 0){
           $affiliates_list = $affiliates->selectBy('id_affiliates', $retriveData->id_affiliates);
           $affiliates_list = $affiliates_list ? $affiliates_list[0] : null ;
       }else{
           $affiliates_list =  $affiliates->selectAll();
       }
       $affiliates_status=new classaffiliates_status();
       $affiliates_status=$affiliates_status->selectAll();
       if (($affiliates_list) && ($retriveData->id_affiliates == 0)){
           foreach ($affiliates_list as $affiliates_item){
               foreach ($affiliates_status as $item){
                   if ($item->id_affiliates_status == $affiliates_item->status){
                       $affiliates_item->_status = $item->status;
                   }
               }
               if (!isset($affiliates_item->_status)){$affiliates_item->_status='';}
           }
       } 
       $affiliate_level=new classaffiliate_level();
       $affiliate_level=$affiliate_level->selectAll();
       if (($affiliates_list) && ($retriveData->id_affiliates == 0)){
           foreach ($affiliates_list as $affiliates_item){
               foreach ($affiliate_level as $item){
                   if ($item->id_affiliate_level == $affiliates_item->afiliate_level){
                       $affiliates_item->_afiliate_level = $item->description;
                   }
               }
               if (!isset($affiliates_item->_afiliate_level)){$affiliates_item->_afiliate_level='';}
           }
       } 
       $affiliates=new classaffiliates();
       $affiliates=$affiliates->selectAll();
       if (($affiliates_list) && ($retriveData->id_affiliates == 0)){
           foreach ($affiliates_list as $affiliates_item){
               foreach ($affiliates as $item){
                   if ($item->id_affiliates == $affiliates_item->affiliate_to){
                       $affiliates_item->_affiliate_to = $item->full_name;
                   }
               }
               if (!isset($affiliates_item->_affiliate_to)){$affiliates_item->_affiliate_to='';}
           }
       } 
       $method_of_contact=new classmethod_of_contact();
       $method_of_contact=$method_of_contact->selectAll();
       if (($affiliates_list) && ($retriveData->id_affiliates == 0)){
           foreach ($affiliates_list as $affiliates_item){
               foreach ($method_of_contact as $item){
                   if ($item->id_method_of_contact == $affiliates_item->preferred_method_of_contact){
                       $affiliates_item->_preferred_method_of_contact = $item->method_of_contact;
                   }
               }
               if (!isset($affiliates_item->_preferred_method_of_contact)){$affiliates_item->_preferred_method_of_contact='';}
           }
       } 
       $occupation=new classoccupation();
       $occupation=$occupation->selectAll();
       if (($affiliates_list) && ($retriveData->id_affiliates == 0)){
           foreach ($affiliates_list as $affiliates_item){
               foreach ($occupation as $item){
                   if ($item->id_occupation == $affiliates_item->occupation){
                       $affiliates_item->_occupation = $item->occupation;
                   }
               }
               if (!isset($affiliates_item->_occupation)){$affiliates_item->_occupation='';}
           }
       } 
       $states=new classstates();
       $states=$states->selectAll();
       if ($states){
           $states = Functions::sort_items_by($states,'state_name','ASC');
       };
       if (($affiliates_list) && ($retriveData->id_affiliates == 0)){
           foreach ($affiliates_list as $affiliates_item){
               foreach ($states as $item){
                   if ($item->id_states == $affiliates_item->state){
                       $affiliates_item->_state = $item->state_name;
                   }
               }
               if (!isset($affiliates_item->_state)){$affiliates_item->_state='';}
           }
       } 
       if (($affiliates_list) && ($retriveData->id_affiliates == 0)){
           foreach ($affiliates_list as $affiliates_item){
              $affiliates_item->_picture = 'assets/images/'.$affiliates_item->picture;
           }
       }
       if (($affiliates_list) && ($retriveData->id_affiliates > 0)){
              $affiliates_list->_picture = 'assets/images/'.$affiliates_list->picture;
       }
     // extend PHP_read

        if (in_array($_SESSION['role_name'], ['MGA', 'GA', 'Affiliate'])) {

            $affiliate_tree_list = (new CustomSecFilters())->AffiliatesFilter->affiliate_tree_list($id_user, $_SESSION['role_name'], $affiliates_list);
            if (($affiliates_list) && ($retriveData->id_affiliates == 0)) {
                if ($affiliate_tree_list) {
                    $affiliates_list = $affiliate_tree_list->affiliates_list;
                } else {
                    $affiliates_list = null;
                }
            }

            $affiliate_level_list = (new CustomSecFilters())->AffiliatesFilter->affiliate_level_list($id_user, $_SESSION['role_name']);
            if (($affiliate_level) && ($retriveData->id_affiliates != 0)) {
                if ($affiliate_level_list) {
                    $affiliate_level = $affiliate_level_list->affiliate_level;
                } else {
                    $affiliate_level = [];
                }
            }
            
        }
   
     // extend PHP_read
       if(!$affiliates_list){
           $affiliates_list = new classaffiliates;
       }else{
       }
       if ($retriveData->id_affiliates_status != '-1') {
          Functions::security_access_log($id_user, 'affiliates', 'read', $retriveData->id_affiliates);
       }
       http_response_code(200);
       echo '{"message":"Successful","status":200,"data":{"affiliates":'. json_encode($affiliates_list).',"affiliates_status":'. json_encode($affiliates_status).',"affiliate_level":'. json_encode($affiliate_level).',"affiliates_catalog":'. json_encode($affiliates).',"method_of_contact":'. json_encode($method_of_contact).',"occupation":'. json_encode($occupation).',"states":'. json_encode($states).'}}';
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
    $retriveData->full_name=$retriveData->first_name . ' ' . $retriveData->last_name;
    if ($retriveData->picture_img_data !=''){
        $rawImageData = explode( ',', $retriveData->picture_img_data );
        $fileType = strtolower($rawImageData[0]);
        $fileType =  str_replace(';base64','',$fileType);
        $fileType =  str_replace('data:image/','',$fileType);
        $retriveData->picture = strtolower(uniqid()) . '.' . $fileType;
        $fullPath = '../'.'assets/images/'.$retriveData->picture;
        $rawFile = fopen( $fullPath, 'wb' );
        fwrite( $rawFile, base64_decode( $rawImageData[1] ) );
        fclose( $rawFile ); 
    };
    $affiliates = new classaffiliates;   
    $affiliates->status = $retriveData->status;
    $affiliates->agreement_date = $retriveData->agreement_date;
    $affiliates->afiliate_level = $retriveData->afiliate_level;
    $affiliates->manager_afiliate = $retriveData->manager_afiliate;
    $affiliates->affiliate_to = $retriveData->affiliate_to;
    $affiliates->first_name = $retriveData->first_name;
    $affiliates->last_name = $retriveData->last_name;
    $affiliates->full_name = $retriveData->full_name;
    $affiliates->birth_date = $retriveData->birth_date;
    $affiliates->phone = $retriveData->phone;
    $affiliates->email = $retriveData->email;
    $affiliates->preferred_method_of_contact = $retriveData->preferred_method_of_contact;
    $affiliates->occupation = $retriveData->occupation;
    $affiliates->company = $retriveData->company;
    $affiliates->commission = $retriveData->commission;
    $affiliates->address = $retriveData->address;
    $affiliates->city = $retriveData->city;
    $affiliates->state = $retriveData->state;
    $affiliates->zip_code = $retriveData->zip_code;
    $affiliates->notes = $retriveData->notes;
    $affiliates->picture = $retriveData->picture;
    $affiliates->filemanagerlist = $retriveData->filemanagerlist;
    $affiliates->linked_user = $retriveData->linked_user;
    $affiliates->id_affiliates = $retriveData->id_affiliates;
     // extend PHP_update
 
        $security_user_validator = (new classsecurity_users)->selectBy('id_security_users', $retriveData->linked_user);
        $security_user_validator =  $security_user_validator ? $security_user_validator[0] : null ;

        $affiliate_item_validator = (new classaffiliates)->selectBy('id_affiliates', $retriveData->id_affiliates);
        $affiliate_item_validator =  $affiliate_item_validator ? $affiliate_item_validator[0] : null ;

        $security_email_validator = (new classsecurity_users)->selectBy('email', $retriveData->email);
        if (is_array($security_email_validator)){
            if (count($security_email_validator) > 1){   
                http_response_code(400);
                echo '{"message":"error","status":400,"data":"duplicated email, email already exist"}';
                return false;
            } else {
                if ($affiliate_item_validator && $security_user_validator ){
                    if ($affiliate_item_validator->email != $security_user_validator->email){
                        http_response_code(400);
                        echo '{"message":"error","status":400,"data":"duplicated email, email already exist"}';
                        return false;
                    }
                }
            }
        }  

        $security_phone_validator = (new classsecurity_users)->selectBy('phone', $retriveData->phone);
        if (is_array($security_phone_validator)){
            if (count($security_phone_validator) > 1){   
                http_response_code(400);
                echo '{"message":"error","status":400,"data":"duplicated phone, phone already exist"}';
                return false;
            } else {
                if ($affiliate_item_validator && $security_user_validator ){
                    if ($affiliate_item_validator->phone != $security_user_validator->phone){
                        http_response_code(400);
                        echo '{"message":"error","status":400,"data":"duplicated phone, phone already exist"}';
                        return false;
                    }
                }
            }
        }  
     
        if ($affiliate_item_validator && $security_user_validator ){
            $security_user_validator->full_name = $retriveData->full_name;
            $security_user_validator->phone =  $retriveData->phone;
            $security_user_validator->photo = $retriveData->picture;
            $security_user_validator->email = $retriveData->email;
            $security_user_validator->users_status = $retriveData->status;
            if ($retriveData->afiliate_level == '1'){   $security_user_validator->role = 4; }
            if ($retriveData->afiliate_level == '2'){   $security_user_validator->role = 5; }
            if ($retriveData->afiliate_level == '3'){   $security_user_validator->role = 6; }
            (new classsecurity_users)->update($security_user_validator,$id_user);
        }

     // extend PHP_update
    $OPCODE_RESULT = $affiliates->update($affiliates,$id_user);
        if($OPCODE_RESULT){
        Functions::security_access_log($id_user, 'affiliates', 'update', $retriveData->id_affiliates);
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
      $affiliates = new classaffiliates;   
     // extend PHP_delete
        $security_user_validator = (new classsecurity_users)->selectBy('id_security_users', $retriveData->linked_user);
        $security_user_validator = $security_user_validator ? $security_user_validator[0] : null;

        if ($security_user_validator) {
            $security_user_validator->users_status = 3;
            (new classsecurity_users)->update($security_user_validator, $id_user);
        }
     // extend PHP_delete
      $QUERY_RESULT = $affiliates->delete($retriveData->id_affiliates,$id_user);
      if($QUERY_RESULT->error_flag === false ){
        Functions::security_access_log($id_user, 'affiliates', 'delete', $retriveData->id_affiliates);
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
