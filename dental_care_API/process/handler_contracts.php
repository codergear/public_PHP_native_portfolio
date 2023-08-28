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
        $retriveData->contract_date=date('m/d/Y');
        $retriveData->full_name=$retriveData->first_name . ' ' . $retriveData->last_name;
        $contracts = new classcontracts;
        $contracts->contract = $retriveData->contract;
        $contracts->contract_status = $retriveData->contract_status;
        $contracts->is_cancelled = $retriveData->is_cancelled;
        $contracts->contract_date = $retriveData->contract_date;
        $contracts->full_name = $retriveData->full_name;
        $contracts->first_name = $retriveData->first_name;
        $contracts->last_name = $retriveData->last_name;
        $contracts->email = $retriveData->email;
        $contracts->phone = $retriveData->phone;
        $contracts->address = $retriveData->address;
        $contracts->city = $retriveData->city;
        $contracts->state = $retriveData->state;
        $contracts->zip_code = $retriveData->zip_code;
        $contracts->surgery_date = $retriveData->surgery_date;
        $contracts->doctor = $retriveData->doctor;
        $contracts->facility = $retriveData->facility;
        $contracts->plan = $retriveData->plan;
        $contracts->premium = $retriveData->premium;
        $contracts->payment_method = $retriveData->payment_method;
        $contracts->card_number = $retriveData->card_number;
        $contracts->card_valid_to = $retriveData->card_valid_to;
        $contracts->card_cvv = $retriveData->card_cvv;
        $contracts->routing_number = $retriveData->routing_number;
        $contracts->account_number = $retriveData->account_number;
        $contracts->pay_by = $retriveData->pay_by;
        $contracts->coordinator_name = $retriveData->coordinator_name;
        $contracts->group_name = $retriveData->group_name;
     // extend PHP_create
        
        $doctors = new classdoctors();
        $doctors =  $doctors->selectBy('id_doctors',  $retriveData->doctor)[0];

        $groups = new classgroups();
        $groups =  $groups->selectBy('id_groups', $doctors->group_detail)[0];

        $contracts->pay_by = $groups->pay_by;
        $contracts->group_name = $groups->entity_name;

        $contracts->card_number = "";
        $contracts->card_valid_to = "";
        $contracts->card_cvv = "";
        $contracts->routing_number = "";
        $contracts->account_number = ""; 

        $surgical_coordinators = new \stdClass();
        switch ($_SESSION['role_name']) {
            case 'Group Manager':
            case 'Coordinator':
                $surgical_coordinators_user = new classsurgical_coordinators();
                $surgical_coordinators_user = $surgical_coordinators_user->selectBy('linked_user', $_SESSION['id_user'])[0];
                $surgical_coordinators->full_name = $surgical_coordinators_user->full_name;
                $surgical_coordinators->id_surgical_coordinators = $surgical_coordinators_user->id_surgical_coordinators;
                break;
            case 'Group Principal':
                $group_principal = new classgroups();
                $group_principal = $group_principal->selectBy('linked_user', $_SESSION['id_user'])[0];
                $surgical_coordinators->full_name = "Principal::" . $group_principal->principal_full_name;
                $surgical_coordinators->id_surgical_coordinators = $group_principal->id_groups;
                break;
            default:
                $surgical_coordinators->full_name = "Admin::" . $_SESSION['full_name'];
                $surgical_coordinators->id_surgical_coordinators = $_SESSION['id_user'];
                break;
        }

        $contracts->coordinator_name =  $surgical_coordinators->full_name;

     // extend PHP_create
     $OPCODE_RESULT = $contracts->insert($contracts,$id_user);
     if($OPCODE_RESULT){
        $retriveData->id_contracts = $OPCODE_RESULT;
        Functions::security_access_log($id_user, 'contracts', 'create', $retriveData->id_contracts);
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
       $contracts = new classcontracts();     
       if ($retriveData->id_contracts != 0){
           $contracts_list = $contracts->selectBy('id_contracts', $retriveData->id_contracts);
           $contracts_list = $contracts_list ? $contracts_list[0] : null ;
       }else{
           $contracts_list =  $contracts->selectAll();
       }
       $states=new classstates();
       $states=$states->selectAll();
       if ($states){
           $states = Functions::sort_items_by($states,'state_name','ASC');
       };
       if (($contracts_list) && ($retriveData->id_contracts == 0)){
           foreach ($contracts_list as $contracts_item){
               foreach ($states as $item){
                   if ($item->id_states == $contracts_item->state){
                       $contracts_item->_state = $item->state_name;
                   }
               }
               if (!isset($contracts_item->_state)){$contracts_item->_state='';}
           }
       } 
       $doctors=new classdoctors();
       $doctors=$doctors->selectAll();
       if (($contracts_list) && ($retriveData->id_contracts == 0)){
           foreach ($contracts_list as $contracts_item){
               foreach ($doctors as $item){
                   if ($item->id_doctors == $contracts_item->doctor){
                       $contracts_item->_doctor = $item->full_name;
                   }
               }
               if (!isset($contracts_item->_doctor)){$contracts_item->_doctor='';}
           }
       } 
       $facilities=new classfacilities();
       $facilities=$facilities->selectAll();
       if (($contracts_list) && ($retriveData->id_contracts == 0)){
           foreach ($contracts_list as $contracts_item){
               foreach ($facilities as $item){
                   if ($item->id_facilities == $contracts_item->facility){
                       $contracts_item->_facility = $item->name;
                   }
               }
               if (!isset($contracts_item->_facility)){$contracts_item->_facility='';}
           }
       } 
       $plans=new classplans();
       $plans=$plans->selectAll();
       if (($contracts_list) && ($retriveData->id_contracts == 0)){
           foreach ($contracts_list as $contracts_item){
               foreach ($plans as $item){
                   if ($item->id_plans == $contracts_item->plan){
                       $contracts_item->_plan = $item->description;
                   }
               }
               if (!isset($contracts_item->_plan)){$contracts_item->_plan='';}
           }
       } 
       $payment_methods=new classpayment_methods();
       $payment_methods=$payment_methods->selectAll();
       if (($contracts_list) && ($retriveData->id_contracts == 0)){
           foreach ($contracts_list as $contracts_item){
               foreach ($payment_methods as $item){
                   if ($item->id_payment_methods == $contracts_item->payment_method){
                       $contracts_item->_payment_method = $item->payment_method;
                   }
               }
               if (!isset($contracts_item->_payment_method)){$contracts_item->_payment_method='';}
           }
       } 
     // extend PHP_read
     
        $doctors=Functions::get_items_by($doctors, 'status', '2'); 
        $plans=Functions::get_items_by($plans, 'status', '2'); 

        if (($contracts_list) && ($retriveData->id_contracts == 0)){
            foreach ($contracts_list as $contracts_item){
                if ((strtotime($contracts_item->surgery_date) < strtotime("now")) && ($contracts_item->contract_status == "Active")) {
                    $contracts_item->contract_status = "Due";
                }
            }
        } 

        if (in_array($_SESSION['role_name'], ['Group Manager','Coordinator'])) {
            $surgical_coordinator_list = (new classsurgical_coordinators())->selectAll();
            $surgical_coordinators_group_list = (new CustomSecFilters())->SurgicalCoordinatorsFilter->surgical_coordinators_group_list($id_user, $_SESSION['role_name'],$surgical_coordinator_list);
       
            if (($contracts_list) && ($retriveData->id_contracts == 0)){
                if ($surgical_coordinators_group_list) {

                    $auditor_list_full = (new classauditor())->selectAll();
                    $auditor_list = [];
                    foreach ($surgical_coordinators_group_list->groups_filter_list as $group_item ) {
                        $auditor_list = array_merge($auditor_list, Functions::get_items_by($auditor_list_full,'payee_group_id',$group_item->id_groups) );
                    }

                    $contracts_list_full = $contracts_list;
                    $contracts_list = [];
                    foreach ($auditor_list as $auditor_item ) {                        
                        $contracts_list = array_merge($contracts_list, Functions::get_items_by($contracts_list_full,'contract',$auditor_item->contract) );
                    }
                    
                } else {
                    $contracts_list = [];
                }
            }

            if ($retriveData->id_contracts != 0) {
                if ($surgical_coordinators_group_list) {
                    $doctors_full = $doctors;
                    $doctors = [];
                    foreach ($doctors_full as $item) {
                        if (in_array($item->group_detail, array_column($surgical_coordinators_group_list->groups_filter_list, 'id_groups'))) {
                            array_push($doctors, $item);
                        }
                    }
                } else {
                    $doctors = null;
                }
            }
        }

        if (in_array($_SESSION['role_name'], ['Group Principal'])) {
            $groups_filter_list = new classgroups();
            $surgical_coordinators_group_list = new \stdClass();
            $surgical_coordinators_group_list->groups_filter_list = $groups_filter_list->selectBy('linked_user', $id_user);

            if (($contracts_list) && ($retriveData->id_contracts == 0)){
                if ($surgical_coordinators_group_list) {

                    $auditor_list_full = (new classauditor())->selectAll();
                    $auditor_list = [];
                    foreach ($surgical_coordinators_group_list->groups_filter_list as $group_item ) {
                        $auditor_list = array_merge($auditor_list, Functions::get_items_by($auditor_list_full,'payee_group_id',$group_item->id_groups) );
                    }

                    $contracts_list_full = $contracts_list;
                    $contracts_list = [];
                    foreach ($auditor_list as $auditor_item ) {                        
                        $contracts_list = array_merge($contracts_list, Functions::get_items_by($contracts_list_full,'contract',$auditor_item->contract) );
                    }
                    
                } else {
                    $contracts_list = [];
                }
            }

            if ($retriveData->id_contracts != 0) {
                if ($surgical_coordinators_group_list) {
                    $doctors_full = $doctors;
                    $doctors = [];
                    foreach ($doctors_full as $item) {
                        if (in_array($item->group_detail, array_column($surgical_coordinators_group_list->groups_filter_list, 'id_groups'))) {
                            array_push($doctors, $item);
                        }
                    }
                } else {
                    $doctors = null;
                }
            }
        }

     // extend PHP_read
       if(!$contracts_list){
           $contracts_list = new classcontracts;
       }else{
           if ($retriveData->id_contracts == 0){
               $contracts_list = Functions::filter_items_by_interval($contracts_list,'contract_date',$retriveData->datepickerCustomFilterBegin,$retriveData->datepickerCustomFilterEnd);
           }
       }
       if ($retriveData->id_affiliates_status != '-1') {
          Functions::security_access_log($id_user, 'contracts', 'read', $retriveData->id_contracts);
       }
       http_response_code(200);
       echo '{"message":"Successful","status":200,"data":{"contracts":'. json_encode($contracts_list).',"states":'. json_encode($states).',"doctors":'. json_encode($doctors).',"facilities":'. json_encode($facilities).',"plans":'. json_encode($plans).',"payment_methods":'. json_encode($payment_methods).'}}';
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
    $retriveData->contract_date=date('m/d/Y');
    $retriveData->full_name=$retriveData->first_name . ' ' . $retriveData->last_name;
    $contracts = new classcontracts;   
    $contracts->contract = $retriveData->contract;
    $contracts->contract_status = $retriveData->contract_status;
    $contracts->is_cancelled = $retriveData->is_cancelled;
    $contracts->contract_date = $retriveData->contract_date;
    $contracts->full_name = $retriveData->full_name;
    $contracts->first_name = $retriveData->first_name;
    $contracts->last_name = $retriveData->last_name;
    $contracts->email = $retriveData->email;
    $contracts->phone = $retriveData->phone;
    $contracts->address = $retriveData->address;
    $contracts->city = $retriveData->city;
    $contracts->state = $retriveData->state;
    $contracts->zip_code = $retriveData->zip_code;
    $contracts->surgery_date = $retriveData->surgery_date;
    $contracts->doctor = $retriveData->doctor;
    $contracts->facility = $retriveData->facility;
    $contracts->plan = $retriveData->plan;
    $contracts->premium = $retriveData->premium;
    $contracts->payment_method = $retriveData->payment_method;
    $contracts->card_number = $retriveData->card_number;
    $contracts->card_valid_to = $retriveData->card_valid_to;
    $contracts->card_cvv = $retriveData->card_cvv;
    $contracts->routing_number = $retriveData->routing_number;
    $contracts->account_number = $retriveData->account_number;
    $contracts->pay_by = $retriveData->pay_by;
    $contracts->coordinator_name = $retriveData->coordinator_name;
    $contracts->group_name = $retriveData->group_name;
    $contracts->id_contracts = $retriveData->id_contracts;
    $OPCODE_RESULT = $contracts->update($contracts,$id_user);
        if($OPCODE_RESULT){
        Functions::security_access_log($id_user, 'contracts', 'update', $retriveData->id_contracts);
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
      $contracts = new classcontracts;   
      $QUERY_RESULT = $contracts->delete($retriveData->id_contracts,$id_user);
      if($QUERY_RESULT->error_flag === false ){
        Functions::security_access_log($id_user, 'contracts', 'delete', $retriveData->id_contracts);
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
