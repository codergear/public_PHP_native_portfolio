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
        $retriveData->principal_full_name=$retriveData->principal_name . ' ' . $retriveData->principal_last_name;
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
        $groups = new classgroups;
        $groups->status = $retriveData->status;
        $groups->agreement_date = $retriveData->agreement_date;
        $groups->entity_name = $retriveData->entity_name;
        $groups->principal_name = $retriveData->principal_name;
        $groups->principal_last_name = $retriveData->principal_last_name;
        $groups->principal_title = $retriveData->principal_title;
        $groups->principal_full_name = $retriveData->principal_full_name;
        $groups->phone = $retriveData->phone;
        $groups->email = $retriveData->email;
        $groups->group_rate = $retriveData->group_rate;
        $groups->afiliate = $retriveData->afiliate;
        $groups->address = $retriveData->address;
        $groups->city = $retriveData->city;
        $groups->state = $retriveData->state;
        $groups->zip_code = $retriveData->zip_code;
        $groups->pay_by = $retriveData->pay_by;
        $groups->routing_number = $retriveData->routing_number;
        $groups->account_number = $retriveData->account_number;
        $groups->cardpointe_token = $retriveData->cardpointe_token;
        $groups->linked_user = $retriveData->linked_user;
        $groups->plan = $retriveData->plan;
        $groups->notes = $retriveData->notes;
        $groups->logo = $retriveData->logo;
        $groups->filemanagerlist = $retriveData->filemanagerlist;
     // extend PHP_create
        if ( $retriveData->pay_by == "Provider") {
            $routingNumber = str_replace(' ', '', $retriveData->routing_number);
            $accountNumber = str_replace(' ', '', $retriveData->account_number);
            try {
                require_once('../integration/cardpointe/cardpointe.php');
                $cardpointe = new classcardpointe();
                $tokenize = $cardpointe->tokenize("ACH", $routingNumber . "/" . $accountNumber);
                if ($tokenize->errorcode == 0) {
                    $groups->cardpointe_token = $tokenize->token;
                } else {
                    Functions::security_access_log($id_user, 'groups', 'create token error', $retriveData->id_groups);
                }
            } catch (\Throwable | \Exception $e) {
                //silent exception
                $groups->cardpointe_token = "";
            }
            $groups->routing_number = substr_replace($retriveData->routing_number, str_repeat("*", 5), 0, 5);
            $groups->account_number = substr_replace($retriveData->account_number, str_repeat("*", 5), 0, 5);
        } else {
            $groups->cardpointe_token = "";
            $groups->routing_number = "";
            $groups->account_number = "";
        }
    
        $security_users = new classsecurity_users;
        $security_users->users_status = 2;
        $security_users->user = strtolower(str_replace(" ","", $retriveData->principal_name));
        $flat_pass = strtolower(Functions::random_password(10));
        $security_users->pass = $flat_pass;
        $security_users->full_name = $retriveData->principal_full_name;
        $security_users->origin = 1;
        $security_users->phone =  $retriveData->phone;
        $security_users->email = $retriveData->email;
        $security_users->role = 10; 
        $security_users->photo = $retriveData->logo;
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
        $groups->linked_user =  $USER_OPCODE_RESULT;
        if ($USER_OPCODE_RESULT) {
            require_once('../integration/integration_sendgrid.php');
            /** @var \SendGrid\Mail\Mail $email_sendgrid */ 
            /** @var \SendGrid $sendgrid */ 
            if (isset($email_sendgrid) && isset($sendgrid)) {
                try {
                    $email_sendgrid->addTo($security_users->email);             
                    $email_sendgrid->setSubject('dentalCare account created');
                    $email_message = ConfigMailTemplate::new_account_template($security_users->user, $flat_pass);
                    $email_sendgrid->addContent("text/html", $email_message);
                    $sendgrid_response = $sendgrid->send($email_sendgrid);
                    Functions::security_access_log($id_user, 'Group Principal create account', 'mail notification', $security_users->id_security_users);
                } catch (\Exception $e) {
                    Functions::security_access_log($id_user, 'Group Principal create account', 'mail notification error ', $security_users->id_security_users);
                }
            }
        } else{
            echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
            http_response_code(500);
            return false;
        }
     // extend PHP_create
     $OPCODE_RESULT = $groups->insert($groups,$id_user);
     if($OPCODE_RESULT){
        $retriveData->id_groups = $OPCODE_RESULT;
        Functions::security_access_log($id_user, 'groups', 'create', $retriveData->id_groups);
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
       $groups = new classgroups();     
       if ($retriveData->id_groups != 0){
           $groups_list = $groups->selectBy('id_groups', $retriveData->id_groups);
           $groups_list = $groups_list ? $groups_list[0] : null ;
       }else{
           $groups_list =  $groups->selectAll();
       }
       $groups_status=new classgroups_status();
       $groups_status=$groups_status->selectAll();
       if (($groups_list) && ($retriveData->id_groups == 0)){
           foreach ($groups_list as $groups_item){
               foreach ($groups_status as $item){
                   if ($item->id_groups_status == $groups_item->status){
                       $groups_item->_status = $item->status;
                   }
               }
               if (!isset($groups_item->_status)){$groups_item->_status='';}
           }
       } 
       $affiliates=new classaffiliates();
       $affiliates=$affiliates->selectAll();
       if (($groups_list) && ($retriveData->id_groups == 0)){
           foreach ($groups_list as $groups_item){
               foreach ($affiliates as $item){
                   if ($item->id_affiliates == $groups_item->afiliate){
                       $groups_item->_afiliate = $item->full_name;
                   }
               }
               if (!isset($groups_item->_afiliate)){$groups_item->_afiliate='';}
           }
       } 
       $states=new classstates();
       $states=$states->selectAll();
       if ($states){
           $states = Functions::sort_items_by($states,'state_name','ASC');
       };
       if (($groups_list) && ($retriveData->id_groups == 0)){
           foreach ($groups_list as $groups_item){
               foreach ($states as $item){
                   if ($item->id_states == $groups_item->state){
                       $groups_item->_state = $item->state_name;
                   }
               }
               if (!isset($groups_item->_state)){$groups_item->_state='';}
           }
       } 
       $plans=new classplans();
       $plans=$plans->selectAll();
       $plans_list=new classgroups();
       if ($retriveData->id_groups > 0){
           $plans_list = $plans_list->select_plan_multiselect($retriveData->id_groups);
       } else {
           $plans_list = null;
           if (($groups_list) && ($retriveData->id_groups == 0)){
               foreach ($groups_list as $groups_item){
                   $plan_by_groups=new classgroups();
                   $plan_by_groups = $plan_by_groups->select_plan_multiselect($groups_item->id_groups);
                   if ($plan_by_groups){
                       foreach ($plan_by_groups as $item){  
                           $groups_item->_plan = $groups_item->_plan . ' ' . $item->plan;
                       }
                   } else {
                       $groups_item->_plan='';
                   }
               }
           } 
       }
       if (($groups_list) && ($retriveData->id_groups == 0)){
           foreach ($groups_list as $groups_item){
              $groups_item->_logo = 'assets/images/'.$groups_item->logo;
           }
       }
       if (($groups_list) && ($retriveData->id_groups > 0)){
              $groups_list->_logo = 'assets/images/'.$groups_list->logo;
       }
     // extend PHP_read
  
        $affiliates=Functions::get_items_by($affiliates, 'status', '2');

        
        if (in_array(strtolower($_SESSION['role_name']), ['group principal'])) {
            $groups_filter_list = new classgroups();
            $groups_filter_list = $groups_filter_list->selectBy('linked_user', $id_user);
            
            if($groups_filter_list){
                $affiliates_filter_list_full = new classaffiliates();
                $affiliates_filter_list_full = $affiliates_filter_list_full->selectAll();
                $affiliates_filter_list = [];
                foreach ($affiliates_filter_list_full as $item) {
                    if(in_array($item->id_affiliates, array_column($groups_filter_list, 'afiliate'))){
                        $affiliates_filter_list = array_merge($affiliates_filter_list, Functions::get_items_by($affiliates_filter_list_full, 'id_affiliates', $item->id_affiliates));
                    }
                }

                foreach ($affiliates_filter_list as $item ) {
                    $affiliates_filter_list_up = Functions::get_items_by($affiliates_filter_list_full, 'id_affiliates', $item->affiliate_to);
                    $affiliates_filter_list_up = $affiliates_filter_list_up ? $affiliates_filter_list_up[0] : null;
                    if($affiliates_filter_list_up && !in_array($affiliates_filter_list_up->id_affiliates, array_column($affiliates_filter_list, 'id_affiliates'))){
                        array_push($affiliates_filter_list, $affiliates_filter_list_up);
                    }
                }
            }

            if ($retriveData->id_groups != 0) {                
                if ($groups_filter_list) {             
                    if($groups_filter_list){
                        $affiliates_full = $affiliates;
                        $affiliates = [];
                        foreach ($affiliates_filter_list as $item) {
                            $affiliates = array_merge($affiliates ,Functions::get_items_by($affiliates_full, 'id_affiliates', $item->id_affiliates));
                        }
                        $affiliates=Functions::get_items_by($affiliates, 'status', '2');
                    }
                } else {
                    $affiliates = null;
                }                
            }

            if (($groups_list) && ($retriveData->id_groups == 0)) {                
                if ($groups_filter_list) {             
                    if($groups_filter_list){
                        $groups_list_full = $groups_list;
                        $groups_list = [];
                        foreach ($groups_filter_list as $item) {
                            $groups_list = array_merge($groups_list ,Functions::get_items_by($groups_list_full, 'id_groups', $item->id_groups));
                        }
                    }
                } else {
                    $groups_list = null;
                }                
            }
        }


        if (in_array(strtolower($_SESSION['role_name']), ['coordinator', 'group manager'])) {
            $coordinator_user = new classsurgical_coordinators();
            $coordinator_user = $coordinator_user->selectBy('linked_user', $id_user);
            $coordinator_user = $coordinator_user ? $coordinator_user[0] : null;
            if ($coordinator_user) {
                $surgical_coordinators_item_groups_list = (new classgroups)->selectBy("id_groups", $coordinator_user->group_detail);
            }

            if ($surgical_coordinators_item_groups_list) {
                $groups_filter_list_full = new classgroups();
                $groups_filter_list_full = $groups_filter_list_full->selectAll();
                $groups_filter_list = [];
                foreach ($surgical_coordinators_item_groups_list as $item) {
                    $groups_filter_list = array_merge($groups_filter_list, Functions::get_items_by($groups_filter_list_full, 'id_groups', $item->id_groups));
                }  
            } 
            
            if($groups_filter_list){
                $affiliates_filter_list_full = new classaffiliates();
                $affiliates_filter_list_full = $affiliates_filter_list_full->selectAll();
                $affiliates_filter_list = [];
                foreach ($affiliates_filter_list_full as $item) {
                    if(in_array($item->id_affiliates, array_column($groups_filter_list, 'afiliate'))){
                        $affiliates_filter_list = array_merge($affiliates_filter_list, Functions::get_items_by($affiliates_filter_list_full, 'id_affiliates', $item->id_affiliates));
                    }
                }

                foreach ($affiliates_filter_list as $item ) {
                    $affiliates_filter_list_up = Functions::get_items_by($affiliates_filter_list_full, 'id_affiliates', $item->affiliate_to);
                    $affiliates_filter_list_up = $affiliates_filter_list_up ? $affiliates_filter_list_up[0] : null;
                    if($affiliates_filter_list_up && !in_array($affiliates_filter_list_up->id_affiliates, array_column($affiliates_filter_list, 'id_affiliates'))){
                        array_push($affiliates_filter_list, $affiliates_filter_list_up);
                    }
                }
            }

            if ($retriveData->id_groups != 0) {                
                if ($coordinator_user) {             
                    if($groups_filter_list){
                        $affiliates_full = $affiliates;
                        $affiliates = [];
                        foreach ($affiliates_filter_list as $item) {
                            $affiliates = array_merge($affiliates ,Functions::get_items_by($affiliates_full, 'id_affiliates', $item->id_affiliates));
                        }
                        $affiliates=Functions::get_items_by($affiliates, 'status', '2');
                    }
                } else {
                    $affiliates = null;
                }                
            }

            if (($groups_list) && ($retriveData->id_groups == 0)) {                
                if ($coordinator_user) {             
                    if($groups_filter_list){
                        $groups_list_full = $groups_list;
                        $groups_list = [];
                        foreach ($groups_filter_list as $item) {
                            $groups_list = array_merge($groups_list ,Functions::get_items_by($groups_list_full, 'id_groups', $item->id_groups));
                        }
                    }
                } else {
                    $groups_list = null;
                }                
            }
        }

        if (in_array(strtolower($_SESSION['role_name']), ['mga', 'ga', 'affiliate'])) {
            $affiliate_user = new classaffiliates();
            $affiliate_user = $affiliate_user->selectBy('linked_user', $id_user);
            $affiliate_user = $affiliate_user ? $affiliate_user[0] : null;
            if ($affiliate_user) {
                $affiliates_filter_list = new classaffiliates();
                $affiliates_filter_list = $affiliates_filter_list->selectAll();
                if ($affiliates_filter_list) {
                    $affiliates_filter_array = [];
                    $affiliates_filter_list_full = $affiliates_filter_list;
                    
                    $affiliates_filter_list = Functions::get_items_by($affiliates_filter_list_full, 'affiliate_to', $affiliate_user->id_affiliates);
                    
                    foreach ($affiliates_filter_list as $item) { 
                        if (!in_array($item->id_affiliates, $affiliates_filter_array)) {
                            $affiliates_filter_list = array_merge($affiliates_filter_list, Functions::get_items_by($affiliates_filter_list_full, 'affiliate_to', $item->id_affiliates));
                            array_push($affiliates_filter_array, $item->id_affiliates);
                        }
                    }
                    
                    array_push($affiliates_filter_list, $affiliate_user);
                }
            }

            if ($retriveData->id_groups != 0) {
                if ($affiliate_user) {             
                    if($affiliates_filter_list){
                        $affiliates = $affiliates_filter_list;
                        $affiliates = Functions::get_items_by($affiliates, 'status', '2');
                    }
                } else {
                    $affiliates= null;
                }
            }

            if (($groups_list) && ($retriveData->id_groups == 0)) {                
                if ($affiliate_user) {             
                    if($affiliates_filter_list){
                        $groups_list_full = $groups_list;
                        $groups_list = [];
                        foreach ($affiliates_filter_list as $item) {
                            $groups_list = array_merge($groups_list ,Functions::get_items_by($groups_list_full, 'afiliate', $item->id_affiliates));
                        }
                    }
                } else {
                    $groups_list = null;
                }                
            }
        }

     // extend PHP_read
       if(!$groups_list){
           $groups_list = new classgroups;
       }else{
       }
       if ($retriveData->id_affiliates_status != '-1') {
          Functions::security_access_log($id_user, 'groups', 'read', $retriveData->id_groups);
       }
       http_response_code(200);
       echo '{"message":"Successful","status":200,"data":{"groups":'. json_encode($groups_list).',"groups_status":'. json_encode($groups_status).',"affiliates":'. json_encode($affiliates).',"states":'. json_encode($states).',"plans":'. json_encode($plans).',"plans_list":'. json_encode($plans_list).'}}';
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
    $retriveData->principal_full_name=$retriveData->principal_name . ' ' . $retriveData->principal_last_name;
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
    $groups = new classgroups;   
    $groups->status = $retriveData->status;
    $groups->agreement_date = $retriveData->agreement_date;
    $groups->entity_name = $retriveData->entity_name;
    $groups->principal_name = $retriveData->principal_name;
    $groups->principal_last_name = $retriveData->principal_last_name;
    $groups->principal_title = $retriveData->principal_title;
    $groups->principal_full_name = $retriveData->principal_full_name;
    $groups->phone = $retriveData->phone;
    $groups->email = $retriveData->email;
    $groups->group_rate = $retriveData->group_rate;
    $groups->afiliate = $retriveData->afiliate;
    $groups->address = $retriveData->address;
    $groups->city = $retriveData->city;
    $groups->state = $retriveData->state;
    $groups->zip_code = $retriveData->zip_code;
    $groups->pay_by = $retriveData->pay_by;
    $groups->routing_number = $retriveData->routing_number;
    $groups->account_number = $retriveData->account_number;
    $groups->cardpointe_token = $retriveData->cardpointe_token;
    $groups->linked_user = $retriveData->linked_user;
    $groups->plan = $retriveData->plan;
    $groups->notes = $retriveData->notes;
    $groups->logo = $retriveData->logo;
    $groups->filemanagerlist = $retriveData->filemanagerlist;
    $groups->id_groups = $retriveData->id_groups;
     // extend PHP_update
             
        $element_status_validator = (new classgroups)->selectBy('id_groups', $retriveData->id_groups);
        $element_status_validator =  $element_status_validator ? $element_status_validator[0] : null ;
        if (($element_status_validator->status == "2") && ($retriveData->status == "")){
            $groups->status = $element_status_validator->status;
        }else {
            $groups->status = $retriveData->status;
        }

        $security_user_validator = (new classsecurity_users)->selectBy('id_security_users', $retriveData->linked_user);
        $security_user_validator =  $security_user_validator ? $security_user_validator[0] : null ;

        $element_item_validator = (new classgroups)->selectBy('id_groups', $retriveData->id_groups);
        $element_item_validator =  $element_item_validator ? $element_item_validator[0] : null ;

        $security_email_validator = (new classsecurity_users)->selectBy('email', $retriveData->email);
        if (is_array($security_email_validator)){
            if (count($security_email_validator) > 1){   
                http_response_code(400);
                echo '{"message":"error","status":400,"data":"duplicated email, email already exist"}';
                return false;
            } else {
                if ($element_item_validator && $security_user_validator ){
                    if ($element_item_validator->email != $security_user_validator->email){
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
                if ($element_item_validator && $security_user_validator ){
                    if ($element_item_validator->phone != $security_user_validator->phone){
                        http_response_code(400);
                        echo '{"message":"error","status":400,"data":"duplicated phone, phone already exist"}';
                        return false;
                    }
                }
            }
        }  
     
        if ($element_item_validator && $security_user_validator ){
            $security_user_validator->full_name = $retriveData->principal_full_name;
            $security_user_validator->phone =  $retriveData->phone;
            $security_user_validator->photo = $retriveData->logo;
            $security_user_validator->email = $retriveData->email;
            if (($element_status_validator->status == "2") && ($retriveData->status == "")){
                $security_user_validator->users_status = $element_status_validator->status;
            }else {
                $security_user_validator->users_status = $retriveData->status;
            }
            (new classsecurity_users)->update($security_user_validator,$id_user);
        }

        if (( $retriveData->pay_by == "Provider") && (strpos($retriveData->account_number, "*") === false)  && (strpos($retriveData->routing_number, "*") === false)) {
            $routingNumber = str_replace(' ', '', $retriveData->routing_number);
            $accountNumber = str_replace(' ', '', $retriveData->account_number);
            try {
                require_once('../integration/cardpointe/cardpointe.php');
                $cardpointe = new classcardpointe();
                $tokenize = $cardpointe->tokenize("ACH", $routingNumber . "/" . $accountNumber);
                if ($tokenize->errorcode == 0) {
                    $groups->cardpointe_token = $tokenize->token;
                } else {
                    Functions::security_access_log($id_user, 'groups', 'create token error', $retriveData->id_groups);
                }
            } catch (\Throwable | \Exception $e) {
                //silent exception                
                $groups->cardpointe_token = "";
            }
            $groups->routing_number = substr_replace($retriveData->routing_number, str_repeat("*", 5), 0, 5);
            $groups->account_number = substr_replace($retriveData->account_number, str_repeat("*", 5), 0, 5);
        } else {
            $groups->cardpointe_token = "";
            $groups->routing_number = "";
            $groups->account_number = "";
        }

     // extend PHP_update
    $OPCODE_RESULT = $groups->update($groups,$id_user);
        if($OPCODE_RESULT){
        Functions::security_access_log($id_user, 'groups', 'update', $retriveData->id_groups);
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
      $groups = new classgroups;   
     // extend PHP_delete
        $security_user_validator = (new classsecurity_users)->selectBy('id_security_users', $retriveData->linked_user);
        $security_user_validator = $security_user_validator ? $security_user_validator[0] : null;

        if ($security_user_validator) {
            $security_user_validator->users_status = 3;
            (new classsecurity_users)->update($security_user_validator, $id_user);
        }
     // extend PHP_delete
      $QUERY_RESULT = $groups->delete($retriveData->id_groups,$id_user);
      if($QUERY_RESULT->error_flag === false ){
        Functions::security_access_log($id_user, 'groups', 'delete', $retriveData->id_groups);
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
