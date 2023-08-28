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
    if ($retriveData->image_img_data !=''){
        $rawImageData = explode( ',', $retriveData->image_img_data );
        $fileType = strtolower($rawImageData[0]);
        $fileType =  str_replace(';base64','',$fileType);
        $fileType =  str_replace('data:image/','',$fileType);
        $retriveData->image = strtolower(uniqid()) . '.' . $fileType;
        $fullPath = '../'.'assets/images/'.$retriveData->image;
        $rawFile = fopen( $fullPath, 'wb' );
        fwrite( $rawFile, base64_decode( $rawImageData[1] ) );
        fclose( $rawFile ); 
    };
        $doctors = new classdoctors;
        $doctors->status = $retriveData->status;
        $doctors->first_name = $retriveData->first_name;
        $doctors->last_name = $retriveData->last_name;
        $doctors->full_name = $retriveData->full_name;
        $doctors->facilities = $retriveData->facilities;
        $doctors->specialties = $retriveData->specialties;
        $doctors->phone = $retriveData->phone;
        $doctors->email = $retriveData->email;
        $doctors->preferred_method_of_contact = $retriveData->preferred_method_of_contact;
        $doctors->address = $retriveData->address;
        $doctors->city = $retriveData->city;
        $doctors->state = $retriveData->state;
        $doctors->zip_code = $retriveData->zip_code;
        $doctors->group_detail = $retriveData->group_detail;
        $doctors->surgical_coordinators = $retriveData->surgical_coordinators;
        $doctors->doctor_fee = $retriveData->doctor_fee;
        $doctors->linked_user = $retriveData->linked_user;
        $doctors->notes = $retriveData->notes;
        $doctors->image = $retriveData->image;
        $doctors->filemanagerlist = $retriveData->filemanagerlist;
     // extend PHP_create
    
            // $security_users = new classsecurity_users;
            // $security_users->users_status = 2;
            // $security_users->user = strtolower(str_replace(" ","", $retriveData->first_name));
            // $flat_pass = strtolower(Functions::random_password(10));
            // $security_users->pass = $flat_pass;
            // $security_users->full_name = $retriveData->full_name;
            // $security_users->origin = 1;
            // $security_users->phone =  $retriveData->phone;
            // $security_users->email = $retriveData->email;
            // $security_users->role = 8;
            // $security_users->photo = $retriveData->image;
            // if (is_array((new classsecurity_users)->selectBy('user', $security_users->user))){
            //     $security_users->user = $security_users->user . '_' . uniqid();
            // }
            // if (is_array((new classsecurity_users)->selectBy('email', $security_users->email))){
            //     http_response_code(400);
            //     echo '{"message":"error","status":400,"data":"duplicated email, email already exist"}';                
            //     return false;
            // } 
            // if (is_array((new classsecurity_users)->selectBy('phone', $security_users->phone))){
            //     http_response_code(400);
            //     echo '{"message":"error","status":400,"data":"duplicated phone, phone already exist"}';
            //     return false;
            // } 
            // $USER_OPCODE_RESULT = $security_users->insert($security_users,$id_user);
            // if ($USER_OPCODE_RESULT) {
            //     $doctors->linked_user =  $USER_OPCODE_RESULT;
            //     require_once('../integration/integration_sendgrid.php');
            //     /** @var \SendGrid\Mail\Mail $email_sendgrid  */ 
            //     /** @var \SendGrid $sendgrid  */ 
            //     if (isset($email_sendgrid) && isset($sendgrid)) {
            //         try {
            //             $email_sendgrid->addTo($security_users->email);
            //             $email_sendgrid->setSubject('dentalCare account created');
            //             $email_message = ConfigMailTemplate::new_account_template($security_users->user, $flat_pass);
            //             $email_sendgrid->addContent("text/html", $email_message);
            //             $sendgrid_response = $sendgrid->send($email_sendgrid);
            //             Functions::security_access_log($id_user, 'doctors create account', 'mail notification', $security_users->id_security_users);
            //         } catch (\Exception $e) {
            //             Functions::security_access_log($id_user, 'doctors create account', 'mail notification error ', $security_users->id_security_users);
            //         }
            //     }
            // } else{
            //     http_response_code(500);
            //     echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
            //     return false;
            // }
        
     // extend PHP_create
     $OPCODE_RESULT = $doctors->insert($doctors,$id_user);
     if($OPCODE_RESULT){
        $retriveData->id_doctors = $OPCODE_RESULT;
        Functions::security_access_log($id_user, 'doctors', 'create', $retriveData->id_doctors);
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
       $doctors = new classdoctors();     
       if ($retriveData->id_doctors != 0){
           $doctors_list = $doctors->selectBy('id_doctors', $retriveData->id_doctors);
           $doctors_list = $doctors_list ? $doctors_list[0] : null ;
       }else{
           $doctors_list =  $doctors->selectAll();
       }
       $doctors_status=new classdoctors_status();
       $doctors_status=$doctors_status->selectAll();
       if (($doctors_list) && ($retriveData->id_doctors == 0)){
           foreach ($doctors_list as $doctors_item){
               foreach ($doctors_status as $item){
                   if ($item->id_doctors_status == $doctors_item->status){
                       $doctors_item->_status = $item->status;
                   }
               }
               if (!isset($doctors_item->_status)){$doctors_item->_status='';}
           }
       } 
       $facilities=new classfacilities();
       $facilities=$facilities->selectAll();
       $facilities_list=new classdoctors();
       if ($retriveData->id_doctors > 0){
           $facilities_list = $facilities_list->select_facilities_multiselect($retriveData->id_doctors);
       } else {
           $facilities_list = null;
           if (($doctors_list) && ($retriveData->id_doctors == 0)){
               foreach ($doctors_list as $doctors_item){
                   $facilities_by_doctors=new classdoctors();
                   $facilities_by_doctors = $facilities_by_doctors->select_facilities_multiselect($doctors_item->id_doctors);
                   if ($facilities_by_doctors){
                       foreach ($facilities_by_doctors as $item){  
                           $doctors_item->_facilities = $doctors_item->_facilities . ' ' . $item->facilities;
                       }
                   } else {
                       $doctors_item->_facilities='';
                   }
               }
           } 
       }
       $specialties=new classspecialties();
       $specialties=$specialties->selectAll();
       $specialties_list=new classdoctors();
       if ($retriveData->id_doctors > 0){
           $specialties_list = $specialties_list->select_specialties_multiselect($retriveData->id_doctors);
       } else {
           $specialties_list = null;
           if (($doctors_list) && ($retriveData->id_doctors == 0)){
               foreach ($doctors_list as $doctors_item){
                   $specialties_by_doctors=new classdoctors();
                   $specialties_by_doctors = $specialties_by_doctors->select_specialties_multiselect($doctors_item->id_doctors);
                   if ($specialties_by_doctors){
                       foreach ($specialties_by_doctors as $item){  
                           $doctors_item->_specialties = $doctors_item->_specialties . ' ' . $item->specialties;
                       }
                   } else {
                       $doctors_item->_specialties='';
                   }
               }
           } 
       }
       $method_of_contact=new classmethod_of_contact();
       $method_of_contact=$method_of_contact->selectAll();
       if (($doctors_list) && ($retriveData->id_doctors == 0)){
           foreach ($doctors_list as $doctors_item){
               foreach ($method_of_contact as $item){
                   if ($item->id_method_of_contact == $doctors_item->preferred_method_of_contact){
                       $doctors_item->_preferred_method_of_contact = $item->method_of_contact;
                   }
               }
               if (!isset($doctors_item->_preferred_method_of_contact)){$doctors_item->_preferred_method_of_contact='';}
           }
       } 
       $states=new classstates();
       $states=$states->selectAll();
       if ($states){
           $states = Functions::sort_items_by($states,'state_name','ASC');
       };
       if (($doctors_list) && ($retriveData->id_doctors == 0)){
           foreach ($doctors_list as $doctors_item){
               foreach ($states as $item){
                   if ($item->id_states == $doctors_item->state){
                       $doctors_item->_state = $item->state_name;
                   }
               }
               if (!isset($doctors_item->_state)){$doctors_item->_state='';}
           }
       } 
       $groups=new classgroups();
       $groups=$groups->selectAll();
       if (($doctors_list) && ($retriveData->id_doctors == 0)){
           foreach ($doctors_list as $doctors_item){
               foreach ($groups as $item){
                   if ($item->id_groups == $doctors_item->group_detail){
                       $doctors_item->_group_detail = $item->entity_name;
                   }
               }
               if (!isset($doctors_item->_group_detail)){$doctors_item->_group_detail='';}
           }
       } 
       $surgical_coordinators=new classsurgical_coordinators();
       $surgical_coordinators=$surgical_coordinators->selectAll();
       $surgical_coordinators_list=new classdoctors();
       if ($retriveData->id_doctors > 0){
           $surgical_coordinators_list = $surgical_coordinators_list->select_surgical_coordinators_multiselect($retriveData->id_doctors);
       } else {
           $surgical_coordinators_list = null;
           if (($doctors_list) && ($retriveData->id_doctors == 0)){
               foreach ($doctors_list as $doctors_item){
                   $surgical_coordinators_by_doctors=new classdoctors();
                   $surgical_coordinators_by_doctors = $surgical_coordinators_by_doctors->select_surgical_coordinators_multiselect($doctors_item->id_doctors);
                   if ($surgical_coordinators_by_doctors){
                       foreach ($surgical_coordinators_by_doctors as $item){  
                           $doctors_item->_surgical_coordinators = $doctors_item->_surgical_coordinators . ' ' . $item->surgical_coordinators;
                       }
                   } else {
                       $doctors_item->_surgical_coordinators='';
                   }
               }
           } 
       }
       if (($doctors_list) && ($retriveData->id_doctors == 0)){
           foreach ($doctors_list as $doctors_item){
              $doctors_item->_image = 'assets/images/'.$doctors_item->image;
           }
       }
       if (($doctors_list) && ($retriveData->id_doctors > 0)){
              $doctors_list->_image = 'assets/images/'.$doctors_list->image;
       }
     // extend PHP_read

        $groups = Functions::get_items_by($groups, 'status', '2');
        $surgical_coordinators = Functions::get_items_by($surgical_coordinators, 'status', '2');

        if (($doctors_list) && ($retriveData->id_doctors > 0)){
            $surgical_coordinators_byGroup = (new CustomSecFilters())->SurgicalCoordinatorsFilter->surgical_coordinators_byGroup($id_user, $_SESSION['role_name'], $surgical_coordinators, $groups[0]->id_groups);
            if ($surgical_coordinators_byGroup) {
               $surgical_coordinators = $surgical_coordinators_byGroup->surgical_coordinators_list;
               $surgical_coordinators = Functions::get_items_by($surgical_coordinators, 'status', '2'); 
            } 
        }

        if (in_array(strtolower($_SESSION['role_name']), ['coordinator', 'group manager'])) {
            $coordinator_user = new classsurgical_coordinators();
            $coordinator_user = $coordinator_user->selectBy('linked_user', $id_user);
            $coordinator_user = $coordinator_user ? $coordinator_user[0] : null;
            if ($coordinator_user) {
                $surgical_coordinators_user_groups_list = (new classgroups)->selectBy("id_groups", $coordinator_user->group_detail);
            }

            $surgical_coordinators_user_groups_array = [];
            if ($surgical_coordinators_user_groups_list) {
                $groups_filter_list_full = new classgroups();
                $groups_filter_list_full = $groups_filter_list_full->selectAll();
                $groups_filter_list = [];
                foreach ($surgical_coordinators_user_groups_list as $item) {
                    array_push($surgical_coordinators_user_groups_array, $item->id_groups);
                    $groups_filter_list = array_merge($groups_filter_list, Functions::get_items_by($groups_filter_list_full, 'id_groups', $item->id_groups));
                }  
            } 
            
            if ($groups_filter_list) {
                $surgical_coordinators_filter_list_full = new classsurgical_coordinators();
                $surgical_coordinators_filter_list_full = $surgical_coordinators_filter_list_full->selectAll();
                $surgical_coordinators_filter_list_full = Functions::get_items_by($surgical_coordinators_filter_list_full, 'status', '2');
                $surgical_coordinators_filter_list = [];
                foreach ($surgical_coordinators_filter_list_full as $item) {
                    $surgical_coordinators_item_groups_list = (new classgroups)->selectBy("id_groups", $item->group_detail);
                    $surgical_coordinators_item_groups_array = [];
                    foreach ($surgical_coordinators_item_groups_list as $obj) {
                        array_push($surgical_coordinators_item_groups_array, $obj->id_groups);
                    }  

                    if($surgical_coordinators_item_groups_list){
                        if (count(array_intersect($surgical_coordinators_user_groups_array,$surgical_coordinators_item_groups_array))>0) {
                            if(!in_array($item, $surgical_coordinators_filter_list)){
                                array_push($surgical_coordinators_filter_list, $item);
                            }   
                        }
                    }
                }  
            } 

            if($retriveData->id_doctors !=0){
                    if ($surgical_coordinators_filter_list) {
                        $surgical_coordinators = $surgical_coordinators_filter_list;
                        $surgical_coordinators = Functions::get_items_by($surgical_coordinators, 'status', '2');
                } else {
                    $surgical_coordinators = null;
                }               
            }

            if ($retriveData->id_doctors != 0) {
                    if ($groups_filter_list) {
                        $groups = $groups_filter_list;
                        $groups = Functions::get_items_by($groups, 'status', '2');
                } else {
                    $groups = null;
                }
            }

            if (($doctors_list) && ($retriveData->id_doctors == 0)) {          
                if($groups_filter_list){
                    $doctors_list_full = $doctors_list;
                    $doctors_list = [];
                    foreach ($groups_filter_list as $item) {
                        $doctors_list = array_merge($doctors_list ,Functions::get_items_by($doctors_list_full, 'group_detail', $item->id_groups));
                    }
                } else {
                    $doctors_list = null;
                }                
            }
        }
       
        if (in_array(strtolower($_SESSION['role_name']), ['group principal'])) {
            $groups_filter_list = new classgroups();
            $groups_filter_list = $groups_filter_list->selectBy('linked_user', $id_user);
            
            if ($groups_filter_list) {
                $surgical_coordinators_filter_list_full = new classsurgical_coordinators();
                $surgical_coordinators_filter_list_full = $surgical_coordinators_filter_list_full->selectAll();
                $surgical_coordinators_filter_list_full = Functions::get_items_by($surgical_coordinators_filter_list_full, 'status', '2');
                $surgical_coordinators_filter_list =  Functions::get_items_by($surgical_coordinators_filter_list_full, 'group_detail', $groups_filter_list[0]->id_groups);   

            } 

            if($retriveData->id_doctors !=0){
                    if ($surgical_coordinators_filter_list) {
                        $surgical_coordinators = $surgical_coordinators_filter_list;
                        $surgical_coordinators = Functions::get_items_by($surgical_coordinators, 'status', '2');
                } else {
                    $surgical_coordinators = null;
                }               
            }

            if ($retriveData->id_doctors != 0) {
                    if ($groups_filter_list) {
                        $groups = $groups_filter_list;
                        $groups = Functions::get_items_by($groups, 'status', '2');
                } else {
                    $groups = null;
                }
            }

            if (($doctors_list) && ($retriveData->id_doctors == 0)) {          
                if($groups_filter_list){
                    $doctors_list_full = $doctors_list;
                    $doctors_list = [];
                    foreach ($groups_filter_list as $item) {
                        $doctors_list = array_merge($doctors_list ,Functions::get_items_by($doctors_list_full, 'group_detail', $item->id_groups));
                    }
                } else {
                    $doctors_list = null;
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

                if ($affiliates_filter_list) {
                    $groups_filter_list_full = new classgroups();
                    $groups_filter_list_full = $groups_filter_list_full->selectAll();
                    $groups_filter_list = [];
                    foreach ($affiliates_filter_list as $item) {
                        $groups_filter_list = array_merge($groups_filter_list, Functions::get_items_by($groups_filter_list_full, 'afiliate', $item->id_affiliates));
                    }
                }

                if ($groups_filter_list) {
                    $surgical_coordinators_filter_list_full = new classsurgical_coordinators();
                    $surgical_coordinators_filter_list_full = $surgical_coordinators_filter_list_full->selectAll();
                    $surgical_coordinators_filter_list = [];
                    foreach ($surgical_coordinators_filter_list_full as $item) {
                        $surgical_coordinators_filter_list_exist = false;
                        $surgical_coordinators_item_groups_list = (new classgroups)->selectBy("id_groups", $item->group_detail);
                        if ($surgical_coordinators_item_groups_list) {
                            foreach ($surgical_coordinators_item_groups_list as $item_group) {
                                if (in_array($item_group->id_groups, array_column($groups_filter_list, 'id_groups'))) {
                                    $surgical_coordinators_filter_list_exist = true;
                                    break;
                                }
                            }
                        }
                        if ($surgical_coordinators_filter_list_exist) {
                            $surgical_coordinators_filter_list_exist = false;
                            array_push($surgical_coordinators_filter_list, $item);
                        }
                    }
                }
            }

            if ($retriveData->id_doctors != 0) {
                if ($groups_filter_list) {
                        $groups = $groups_filter_list;
                        $groups = Functions::get_items_by($groups, 'status', '2');
                } else {
                    $groups = null;
                }
            }

            if ($retriveData->id_doctors != 0) {
                if ($surgical_coordinators_filter_list) {
                        $surgical_coordinators = $surgical_coordinators_filter_list;
                        $surgical_coordinators = Functions::get_items_by($surgical_coordinators, 'status', '2');
                } else {
                    $surgical_coordinators = null;
                }
            }

            if (($doctors_list) && ($retriveData->id_doctors == 0)) {
                if ($groups_filter_list) {
                    $doctors_list_full = $doctors_list;
                    $doctors_list = [];
                    foreach ($doctors_list_full as $item) {
                        if (in_array($item->group_detail, array_column($groups_filter_list, 'id_groups'))) {
                            array_push($doctors_list, $item);
                        }
                    }
                } else {
                    $doctors_list = null;
                }
            }
        }

     // extend PHP_read
       if(!$doctors_list){
           $doctors_list = new classdoctors;
       }else{
       }
       if ($retriveData->id_affiliates_status != '-1') {
          Functions::security_access_log($id_user, 'doctors', 'read', $retriveData->id_doctors);
       }
       http_response_code(200);
       echo '{"message":"Successful","status":200,"data":{"doctors":'. json_encode($doctors_list).',"doctors_status":'. json_encode($doctors_status).',"facilities":'. json_encode($facilities).',"facilities_list":'. json_encode($facilities_list).',"specialties":'. json_encode($specialties).',"specialties_list":'. json_encode($specialties_list).',"method_of_contact":'. json_encode($method_of_contact).',"states":'. json_encode($states).',"groups":'. json_encode($groups).',"surgical_coordinators":'. json_encode($surgical_coordinators).',"surgical_coordinators_list":'. json_encode($surgical_coordinators_list).'}}';
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
    if ($retriveData->image_img_data !=''){
        $rawImageData = explode( ',', $retriveData->image_img_data );
        $fileType = strtolower($rawImageData[0]);
        $fileType =  str_replace(';base64','',$fileType);
        $fileType =  str_replace('data:image/','',$fileType);
        $retriveData->image = strtolower(uniqid()) . '.' . $fileType;
        $fullPath = '../'.'assets/images/'.$retriveData->image;
        $rawFile = fopen( $fullPath, 'wb' );
        fwrite( $rawFile, base64_decode( $rawImageData[1] ) );
        fclose( $rawFile ); 
    };
    $doctors = new classdoctors;   
    $doctors->status = $retriveData->status;
    $doctors->first_name = $retriveData->first_name;
    $doctors->last_name = $retriveData->last_name;
    $doctors->full_name = $retriveData->full_name;
    $doctors->facilities = $retriveData->facilities;
    $doctors->specialties = $retriveData->specialties;
    $doctors->phone = $retriveData->phone;
    $doctors->email = $retriveData->email;
    $doctors->preferred_method_of_contact = $retriveData->preferred_method_of_contact;
    $doctors->address = $retriveData->address;
    $doctors->city = $retriveData->city;
    $doctors->state = $retriveData->state;
    $doctors->zip_code = $retriveData->zip_code;
    $doctors->group_detail = $retriveData->group_detail;
    $doctors->surgical_coordinators = $retriveData->surgical_coordinators;
    $doctors->doctor_fee = $retriveData->doctor_fee;
    $doctors->linked_user = $retriveData->linked_user;
    $doctors->notes = $retriveData->notes;
    $doctors->image = $retriveData->image;
    $doctors->filemanagerlist = $retriveData->filemanagerlist;
    $doctors->id_doctors = $retriveData->id_doctors;
     // extend PHP_update
    
        // $security_user_validator = (new classsecurity_users)->selectBy('id_security_users', $retriveData->linked_user);
        // $security_user_validator =  $security_user_validator ? $security_user_validator[0] : null ;

        // $element_item_validator = (new classdoctors)->selectBy('id_doctors', $retriveData->id_doctors);
        // $element_item_validator =  $element_item_validator ? $element_item_validator[0] : null ;

        // $security_email_validator = (new classsecurity_users)->selectBy('email', $retriveData->email);
        // if (is_array($security_email_validator)){
        //     if (count($security_email_validator) > 1){   
        //         http_response_code(400);
        //         echo '{"message":"error","status":400,"data":"duplicated email, email already exist"}';
        //         return false;
        //     } else {
        //         if ($element_item_validator && $security_user_validator ){
        //             if ($element_item_validator->email != $security_user_validator->email){
        //                 http_response_code(400);
        //                 echo '{"message":"error","status":400,"data":"duplicated email, email already exist"}';
        //                 return false;
        //             }
        //         }
        //     }
        // }  

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
     
        // if ($element_item_validator && $security_user_validator ){
        //     $security_user_validator->full_name = $retriveData->full_name;
        //     $security_user_validator->phone =  $retriveData->phone;
        //     $security_user_validator->photo = $retriveData->image;
        //     $security_user_validator->email = $retriveData->email;
        //     $security_user_validator->users_status = $retriveData->status;
        //     (new classsecurity_users)->update($security_user_validator,$id_user);
        // }

     // extend PHP_update
    $OPCODE_RESULT = $doctors->update($doctors,$id_user);
        if($OPCODE_RESULT){
        Functions::security_access_log($id_user, 'doctors', 'update', $retriveData->id_doctors);
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
      $doctors = new classdoctors;   
     // extend PHP_delete

        // $security_user_validator = (new classsecurity_users)->selectBy('id_security_users', $retriveData->linked_user);
        // $security_user_validator = $security_user_validator ? $security_user_validator[0] : null;

        // if ($security_user_validator) {
        //     $security_user_validator->users_status = 3;
        //     (new classsecurity_users)->update($security_user_validator, $id_user);
        // }

     // extend PHP_delete
      $QUERY_RESULT = $doctors->delete($retriveData->id_doctors,$id_user);
      if($QUERY_RESULT->error_flag === false ){
        Functions::security_access_log($id_user, 'doctors', 'delete', $retriveData->id_doctors);
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
