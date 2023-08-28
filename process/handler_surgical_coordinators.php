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
        $surgical_coordinators = new classsurgical_coordinators;
        $surgical_coordinators->status = $retriveData->status;
        $surgical_coordinators->first_name = $retriveData->first_name;
        $surgical_coordinators->last_name = $retriveData->last_name;
        $surgical_coordinators->full_name = $retriveData->full_name;
        $surgical_coordinators->birthday = $retriveData->birthday;
        $surgical_coordinators->phone = $retriveData->phone;
        $surgical_coordinators->email = $retriveData->email;
        $surgical_coordinators->preferred_method_of_contact = $retriveData->preferred_method_of_contact;
        $surgical_coordinators->group_detail = $retriveData->group_detail;
        $surgical_coordinators->group_manager = $retriveData->group_manager;
        $surgical_coordinators->address = $retriveData->address;
        $surgical_coordinators->city = $retriveData->city;
        $surgical_coordinators->state = $retriveData->state;
        $surgical_coordinators->zip_code = $retriveData->zip_code;
        $surgical_coordinators->notes = $retriveData->notes;
        $surgical_coordinators->picture = $retriveData->picture;
        $surgical_coordinators->filemanagerlist = $retriveData->filemanagerlist;
        $surgical_coordinators->linked_user = $retriveData->linked_user;
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
            if ($retriveData->group_manager == 'Yes'){   $security_users->role = 7; }
            if ($retriveData->group_manager == 'No'){   $security_users->role = 9; }
            $security_users->photo = $retriveData->picture;
            if (is_array((new classsecurity_users)->selectBy('user', $security_users->user))){
                $security_users->user = $security_users->user . '_' . uniqid();
            } 
            if (is_array((new classsecurity_users)->selectBy('email', $security_users->email))){
                http_response_code(400);
                echo '{"message":"error","status":400,"data":"duplicated email, email already exist"}';
                return false;
            }

            // do not validate duplicated phone in surgical coordinators and group managers
            // if (is_array((new classsecurity_users)->selectBy('phone', $security_users->phone))){
            //     http_response_code(400);
            //     echo '{"message":"error","status":400,"data":"duplicated phone, phone already exist"}';
            //     return false;
            // } 
            $USER_OPCODE_RESULT = $security_users->insert($security_users,$id_user);
            if ($USER_OPCODE_RESULT) {
                $surgical_coordinators->linked_user =  $USER_OPCODE_RESULT;
                require_once('../integration/integration_sendgrid.php');
                 /** @var \SendGrid\Mail\Mail $email_sendgrid  */ 
                 /** @var \SendGrid $sendgrid  */ 
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
     $OPCODE_RESULT = $surgical_coordinators->insert($surgical_coordinators,$id_user);
     if($OPCODE_RESULT){
        $retriveData->id_surgical_coordinators = $OPCODE_RESULT;
        Functions::security_access_log($id_user, 'surgical_coordinators', 'create', $retriveData->id_surgical_coordinators);
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
       $surgical_coordinators = new classsurgical_coordinators();     
       if ($retriveData->id_surgical_coordinators != 0){
           $surgical_coordinators_list = $surgical_coordinators->selectBy('id_surgical_coordinators', $retriveData->id_surgical_coordinators);
           $surgical_coordinators_list = $surgical_coordinators_list ? $surgical_coordinators_list[0] : null ;
       }else{
           $surgical_coordinators_list =  $surgical_coordinators->selectAll();
       }
       $surgical_coordinators_status=new classsurgical_coordinators_status();
       $surgical_coordinators_status=$surgical_coordinators_status->selectAll();
       if (($surgical_coordinators_list) && ($retriveData->id_surgical_coordinators == 0)){
           foreach ($surgical_coordinators_list as $surgical_coordinators_item){
               foreach ($surgical_coordinators_status as $item){
                   if ($item->id_surgical_coordinators_status == $surgical_coordinators_item->status){
                       $surgical_coordinators_item->_status = $item->status;
                   }
               }
               if (!isset($surgical_coordinators_item->_status)){$surgical_coordinators_item->_status='';}
           }
       } 
       $method_of_contact=new classmethod_of_contact();
       $method_of_contact=$method_of_contact->selectAll();
       if (($surgical_coordinators_list) && ($retriveData->id_surgical_coordinators == 0)){
           foreach ($surgical_coordinators_list as $surgical_coordinators_item){
               foreach ($method_of_contact as $item){
                   if ($item->id_method_of_contact == $surgical_coordinators_item->preferred_method_of_contact){
                       $surgical_coordinators_item->_preferred_method_of_contact = $item->method_of_contact;
                   }
               }
               if (!isset($surgical_coordinators_item->_preferred_method_of_contact)){$surgical_coordinators_item->_preferred_method_of_contact='';}
           }
       } 
       $groups=new classgroups();
       $groups=$groups->selectAll();
       if (($surgical_coordinators_list) && ($retriveData->id_surgical_coordinators == 0)){
           foreach ($surgical_coordinators_list as $surgical_coordinators_item){
               foreach ($groups as $item){
                   if ($item->id_groups == $surgical_coordinators_item->group_detail){
                       $surgical_coordinators_item->_group_detail = $item->entity_name;
                   }
               }
               if (!isset($surgical_coordinators_item->_group_detail)){$surgical_coordinators_item->_group_detail='';}
           }
       } 
       $states=new classstates();
       $states=$states->selectAll();
       if ($states){
           $states = Functions::sort_items_by($states,'state_name','ASC');
       };
       if (($surgical_coordinators_list) && ($retriveData->id_surgical_coordinators == 0)){
           foreach ($surgical_coordinators_list as $surgical_coordinators_item){
               foreach ($states as $item){
                   if ($item->id_states == $surgical_coordinators_item->state){
                       $surgical_coordinators_item->_state = $item->state_name;
                   }
               }
               if (!isset($surgical_coordinators_item->_state)){$surgical_coordinators_item->_state='';}
           }
       } 
       if (($surgical_coordinators_list) && ($retriveData->id_surgical_coordinators == 0)){
           foreach ($surgical_coordinators_list as $surgical_coordinators_item){
              $surgical_coordinators_item->_picture = 'assets/images/'.$surgical_coordinators_item->picture;
           }
       }
       if (($surgical_coordinators_list) && ($retriveData->id_surgical_coordinators > 0)){
              $surgical_coordinators_list->_picture = 'assets/images/'.$surgical_coordinators_list->picture;
       }
     // extend PHP_read

        $groups=Functions::get_items_by($groups, 'status', '2'); 

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

                if($affiliates_filter_list){
                    $groups_filter_list_full = new classgroups();
                    $groups_filter_list_full = $groups_filter_list_full->selectAll();
                    $groups_filter_list= [];
                    foreach ($affiliates_filter_list as $item) {
                        $groups_filter_list = array_merge($groups_filter_list ,Functions::get_items_by($groups_filter_list_full, 'afiliate', $item->id_affiliates));
                    }
                }
            }

            if ($retriveData->id_surgical_coordinators != 0) {           
                    if($groups_filter_list){
                        $groups = $groups_filter_list;
                        $groups = Functions::get_items_by($groups, 'status', '2');
                } else {
                    $groups = null;
                }
            }

            if (($surgical_coordinators_list) && ($retriveData->id_surgical_coordinators == 0)) {                
                if($groups_filter_list){
                    $surgical_coordinators_list_full = $surgical_coordinators_list;
                    $surgical_coordinators_list = [];
                    foreach ($surgical_coordinators_list_full as $item) {
                        $surgical_coordinators_list_exist = false;
                        $surgical_coordinators_item_groups_list = (new classgroups)->selectBy("id_groups", $item->group_detail);
                        if ($surgical_coordinators_item_groups_list) {
                            foreach ($surgical_coordinators_item_groups_list as $item_group) {
                                if(in_array($item_group->id_groups, array_column($groups_filter_list, 'id_groups'))){
                                    $surgical_coordinators_list_exist = true;
                                    break;
                                }
                            }                               
                            
                        }
                        if ($surgical_coordinators_list_exist) {
                            $surgical_coordinators_list_exist = false;
                            array_push($surgical_coordinators_list, $item);
                        }                           
                    }
                } else {
                    $surgical_coordinators_list = null;
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

            if ($retriveData->id_surgical_coordinators != 0) {           
                    if($groups_filter_list){
                        $groups = $groups_filter_list;
                        $groups = Functions::get_items_by($groups, 'status', '2');
                } else {
                    $groups = null;
                }
            }

            if (($surgical_coordinators_list) && ($retriveData->id_surgical_coordinators == 0)) {                
                if($groups_filter_list){
                    $surgical_coordinators_list_full = $surgical_coordinators_list;
                    $surgical_coordinators_list = [];
                    foreach ($surgical_coordinators_list_full as $item) {
                        $surgical_coordinators_list_exist = false;
                        $surgical_coordinators_item_groups_list = (new classgroups)->selectBy("id_groups", $item->group_detail);
                        if ($surgical_coordinators_item_groups_list) {
                            foreach ($surgical_coordinators_item_groups_list as $item_group) {
                                if(in_array($item_group->id_groups, array_column($groups_filter_list, 'id_groups'))){
                                    $surgical_coordinators_list_exist = true;
                                    break;
                                }
                            }                               
                            
                        }
                        if ($surgical_coordinators_list_exist) {
                            $surgical_coordinators_list_exist = false;
                            array_push($surgical_coordinators_list, $item);
                        }                           
                    }
                } else {
                    $surgical_coordinators_list = null;
                }                
            }
        }
        
        if (in_array(strtolower($_SESSION['role_name']), ['group principal'])) {
            $groups_filter_list = new classgroups();
            $groups_filter_list = $groups_filter_list->selectBy('linked_user', $id_user);

            if ($retriveData->id_surgical_coordinators != 0) {           
                    if($groups_filter_list){
                        $groups = $groups_filter_list;
                        $groups = Functions::get_items_by($groups, 'status', '2');
                } else {
                    $groups = null;
                }
            }

            if (($surgical_coordinators_list) && ($retriveData->id_surgical_coordinators == 0)) {                
                if($groups_filter_list){
                    $surgical_coordinators_list_full = $surgical_coordinators_list;
                    $surgical_coordinators_list = [];
                    foreach ($surgical_coordinators_list_full as $item) {
                        $surgical_coordinators_list_exist = false;
                        $surgical_coordinators_item_groups_list = (new classgroups)->selectBy("id_groups", $item->group_detail);
                        if ($surgical_coordinators_item_groups_list) {
                            foreach ($surgical_coordinators_item_groups_list as $item_group) {
                                if(in_array($item_group->id_groups, array_column($groups_filter_list, 'id_groups'))){
                                    $surgical_coordinators_list_exist = true;
                                    break;
                                }
                            }                               
                            
                        }
                        if ($surgical_coordinators_list_exist) {
                            $surgical_coordinators_list_exist = false;
                            array_push($surgical_coordinators_list, $item);
                        }                           
                    }
                } else {
                    $surgical_coordinators_list = null;
                }                
            }
        }

     // extend PHP_read
       if(!$surgical_coordinators_list){
           $surgical_coordinators_list = new classsurgical_coordinators;
       }else{
       }
       if ($retriveData->id_affiliates_status != '-1') {
          Functions::security_access_log($id_user, 'surgical_coordinators', 'read', $retriveData->id_surgical_coordinators);
       }
       http_response_code(200);
       echo '{"message":"Successful","status":200,"data":{"surgical_coordinators":'. json_encode($surgical_coordinators_list).',"surgical_coordinators_status":'. json_encode($surgical_coordinators_status).',"method_of_contact":'. json_encode($method_of_contact).',"groups":'. json_encode($groups).',"states":'. json_encode($states).'}}';
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
    $surgical_coordinators = new classsurgical_coordinators;   
    $surgical_coordinators->status = $retriveData->status;
    $surgical_coordinators->first_name = $retriveData->first_name;
    $surgical_coordinators->last_name = $retriveData->last_name;
    $surgical_coordinators->full_name = $retriveData->full_name;
    $surgical_coordinators->birthday = $retriveData->birthday;
    $surgical_coordinators->phone = $retriveData->phone;
    $surgical_coordinators->email = $retriveData->email;
    $surgical_coordinators->preferred_method_of_contact = $retriveData->preferred_method_of_contact;
    $surgical_coordinators->group_detail = $retriveData->group_detail;
    $surgical_coordinators->group_manager = $retriveData->group_manager;
    $surgical_coordinators->address = $retriveData->address;
    $surgical_coordinators->city = $retriveData->city;
    $surgical_coordinators->state = $retriveData->state;
    $surgical_coordinators->zip_code = $retriveData->zip_code;
    $surgical_coordinators->notes = $retriveData->notes;
    $surgical_coordinators->picture = $retriveData->picture;
    $surgical_coordinators->filemanagerlist = $retriveData->filemanagerlist;
    $surgical_coordinators->linked_user = $retriveData->linked_user;
    $surgical_coordinators->id_surgical_coordinators = $retriveData->id_surgical_coordinators;
     // extend PHP_update
                
        $security_user_validator = (new classsecurity_users)->selectBy('id_security_users', $retriveData->linked_user);
        $security_user_validator =  $security_user_validator ? $security_user_validator[0] : null ;

        $element_item_validator = (new classsurgical_coordinators)->selectBy('id_surgical_coordinators', $retriveData->id_surgical_coordinators);
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

        // do not validate duplicated phone in surgical coordinators and group managers
        // $security_phone_validator = (new classsecurity_users)->selectBy('phone', $retriveData->phone);
        // if (is_array($security_phone_validator)){
        //     if (count($security_phone_validator) > 1){   
        //         http_response_code(400);
        //         echo '{"message":"error","status":400,"data":"duplicated phone, phone already exist"}';
        //         return false;
        //     } else {
        //         if ($element_item_validator && $security_user_validator ){
        //             if ($element_item_validator->phone != $security_user_validator->phone){
        //                 http_response_code(400);
        //                 echo '{"message":"error","status":400,"data":"duplicated phone, phone already exist"}';
        //                 return false;
        //             }
        //         }
        //     }
        // }  
     
        if ($element_item_validator && $security_user_validator ){
            $security_user_validator->full_name = $retriveData->full_name;
            $security_user_validator->phone =  $retriveData->phone;
            $security_user_validator->photo = $retriveData->picture;
            $security_user_validator->email = $retriveData->email;
            $security_user_validator->users_status = $retriveData->status;
            if ($retriveData->group_manager == 'Yes'){   $security_user_validator->role = 7; }
            if ($retriveData->group_manager == 'No'){   $security_user_validator->role = 9; }
            (new classsecurity_users)->update($security_user_validator,$id_user);
        }

        if (in_array(strtolower($_SESSION['role_name']), ['coordinator', 'group manager'])) {
            if ($id_user == $retriveData->id_surgical_coordinators){
                $surgical_coordinator_item_validator =  (new classsurgical_coordinators())->selectBy('id_surgical_coordinators', $retriveData->id_surgical_coordinators);
                $surgical_coordinator_item_validator = $surgical_coordinator_item_validator ? $surgical_coordinator_item_validator[0] : null;
                $surgical_coordinator_item_validator->email = $retriveData->email;
                $surgical_coordinator_item_validator->phone = $retriveData->phone;
                $surgical_coordinator_item_validator->preferred_method_of_contact = $retriveData->preferred_method_of_contact;
                (new classsurgical_coordinators())->update($surgical_coordinator_item_validator, $id_user);
                Functions::security_access_log($id_user, 'surgical_coordinators', 'update', $retriveData->id_surgical_coordinators);
                http_response_code(200);
                echo '{"message":"Successful","status":200,"data":[]}';
                return false;
            }
        }

     // extend PHP_update
    $OPCODE_RESULT = $surgical_coordinators->update($surgical_coordinators,$id_user);
        if($OPCODE_RESULT){
        Functions::security_access_log($id_user, 'surgical_coordinators', 'update', $retriveData->id_surgical_coordinators);
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
      $surgical_coordinators = new classsurgical_coordinators;   
     // extend PHP_delete
        $security_user_validator = (new classsecurity_users)->selectBy('id_security_users', $retriveData->linked_user);
        $security_user_validator = $security_user_validator ? $security_user_validator[0] : null;

        if ($security_user_validator) {
            $security_user_validator->users_status = 3;
            (new classsecurity_users)->update($security_user_validator, $id_user);
        }
     // extend PHP_delete
      $QUERY_RESULT = $surgical_coordinators->delete($retriveData->id_surgical_coordinators,$id_user);
      if($QUERY_RESULT->error_flag === false ){
        Functions::security_access_log($id_user, 'surgical_coordinators', 'delete', $retriveData->id_surgical_coordinators);
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
