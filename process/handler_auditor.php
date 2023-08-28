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
        $auditor = new classauditor;
        $auditor->transaction = $retriveData->transaction;
        $auditor->contract = $retriveData->contract;
        $auditor->premium = $retriveData->premium;
        $auditor->group_rate = $retriveData->group_rate;
        $auditor->dentalCare_fee = $retriveData->dentalCare_fee;
        $auditor->underwriter_cost = $retriveData->underwriter_cost;
        $auditor->affiliate = $retriveData->affiliate;
        $auditor->dentalCare_net = $retriveData->dentalCare_net;
        $auditor->transaction_date = $retriveData->transaction_date;
        $auditor->payee_affiliate_name = $retriveData->payee_affiliate_name;
        $auditor->payee_affiliate_id = $retriveData->payee_affiliate_id;
        $auditor->payee_group_name = $retriveData->payee_group_name;
        $auditor->is_payed_group = $retriveData->is_payed_group;
        $auditor->group_check_number = $retriveData->group_check_number;
        $auditor->group_payment_date = $retriveData->group_payment_date;
        $auditor->payee_group_id = $retriveData->payee_group_id;
        $auditor->payee_underwriter_name = $retriveData->payee_underwriter_name;
        $auditor->is_payed_underwriter = $retriveData->is_payed_underwriter;
        $auditor->payee_underwriter_check_number = $retriveData->payee_underwriter_check_number;
        $auditor->payee_underwriter_payment_date = $retriveData->payee_underwriter_payment_date;
        $auditor->payee_underwriter_id = $retriveData->payee_underwriter_id;
        $auditor->is_cancelled = $retriveData->is_cancelled;
        $auditor->is_payed_affiliate = $retriveData->is_payed_affiliate;
        $auditor->doctor_name = $retriveData->doctor_name;
        $auditor->doctor_id = $retriveData->doctor_id;
        $auditor->plan_name = $retriveData->plan_name;
        $auditor->plan_id = $retriveData->plan_id;
        $auditor->facility_name = $retriveData->facility_name;
        $auditor->facility_id = $retriveData->facility_id;
        $auditor->patient_name = $retriveData->patient_name;
        $auditor->coordinator_name = $retriveData->coordinator_name;
        $auditor->coordinator_id = $retriveData->coordinator_id;
        $auditor->procedure_date = $retriveData->procedure_date;
        $auditor->payment_method = $retriveData->payment_method;
        $auditor->a1_affiliate_name = $retriveData->a1_affiliate_name;
        $auditor->a1_affiliate_is_payed = $retriveData->a1_affiliate_is_payed;
        $auditor->a1_check_number = $retriveData->a1_check_number;
        $auditor->a1_payment_date = $retriveData->a1_payment_date;
        $auditor->a1_affiliate_id = $retriveData->a1_affiliate_id;
        $auditor->a1_affiliate_amount = $retriveData->a1_affiliate_amount;
        $auditor->a2_affiliate_name = $retriveData->a2_affiliate_name;
        $auditor->a2_affiliate_is_payed = $retriveData->a2_affiliate_is_payed;
        $auditor->a2_check_number = $retriveData->a2_check_number;
        $auditor->a2_payment_date = $retriveData->a2_payment_date;
        $auditor->a2_affiliate_id = $retriveData->a2_affiliate_id;
        $auditor->a2_affiliate_amount = $retriveData->a2_affiliate_amount;
        $auditor->a3_affiliate_name = $retriveData->a3_affiliate_name;
        $auditor->a3_affiliate_is_payed = $retriveData->a3_affiliate_is_payed;
        $auditor->a3_check_number = $retriveData->a3_check_number;
        $auditor->a3_payment_date = $retriveData->a3_payment_date;
        $auditor->a3_affiliate_id = $retriveData->a3_affiliate_id;
        $auditor->a3_affiliate_amount = $retriveData->a3_affiliate_amount;
        $auditor->contract_status = $retriveData->contract_status;
        $auditor->transaction_fee = $retriveData->transaction_fee;
        $auditor->pay_by = $retriveData->pay_by;
        $auditor->patient_email = $retriveData->patient_email;
        $auditor->patient_phone = $retriveData->patient_phone;
        $auditor->patient_full_address = $retriveData->patient_full_address;
        $auditor->purchase_date = $retriveData->purchase_date;
        $auditor->completion_date = $retriveData->completion_date;
        $auditor->cancelation_date = $retriveData->cancelation_date;
     $OPCODE_RESULT = $auditor->insert($auditor,$id_user);
     if($OPCODE_RESULT){
        $retriveData->id_auditor = $OPCODE_RESULT;
        Functions::security_access_log($id_user, 'auditor', 'create', $retriveData->id_auditor);
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
       $auditor = new classauditor();     
       if ($retriveData->id_auditor != 0){
           $auditor_list = $auditor->selectBy('id_auditor', $retriveData->id_auditor);
           $auditor_list = $auditor_list ? $auditor_list[0] : null ;
       }else{
           $auditor_list =  $auditor->selectAll();
       }
     // extend PHP_read

        if (($auditor_list) && ($retriveData->id_auditor == 0)) {
            foreach ($auditor_list as $auditor_item) {
                if ((strtotime($auditor_item->procedure_date) < strtotime("now")) && ($auditor_item->contract_status == "Active")) {
                $auditor_item->contract_status = "Due";
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

            if ($affiliates_filter_list) {

                $auditor_list_all = $auditor_list;
                $auditor_list = [];
                foreach ($affiliates_filter_list as $item) {
                    if (in_array(strtolower($_SESSION['role_name']), ['mga'])) {
                        $a1_affiliate_list = Functions::get_items_by($auditor_list_all, 'a1_affiliate_id', $item->id_affiliates);
                        if($a1_affiliate_list){
                            $auditor_list = array_merge($auditor_list, $a1_affiliate_list);
                        }
                        $a2_affiliate_list = Functions::get_items_by($auditor_list_all, 'a2_affiliate_id', $item->id_affiliates);
                        if($a2_affiliate_list){
                            $auditor_list = array_merge($auditor_list, $a2_affiliate_list);
                        }
                        $a3_affiliate_list = Functions::get_items_by($auditor_list_all, 'a3_affiliate_id', $item->id_affiliates);
                        if($a3_affiliate_list){
                            $auditor_list = array_merge($auditor_list, $a3_affiliate_list);
                        }     

                    }
                    if (in_array(strtolower($_SESSION['role_name']), ['ga'])) {
                        $a2_affiliate_list = Functions::get_items_by($auditor_list_all, 'a2_affiliate_id', $item->id_affiliates);
                        if($a2_affiliate_list){
                            $auditor_list = array_merge($auditor_list, $a2_affiliate_list);
                        }
                        $a3_affiliate_list = Functions::get_items_by($auditor_list_all, 'a3_affiliate_id', $item->id_affiliates);
                        if($a3_affiliate_list){
                            $auditor_list = array_merge($auditor_list, $a3_affiliate_list);
                        }     
                    }
                    if (in_array(strtolower($_SESSION['role_name']), ['affiliate'])) {
                        $a3_affiliate_list = Functions::get_items_by($auditor_list_all, 'a3_affiliate_id', $item->id_affiliates);
                        if($a3_affiliate_list){
                            $auditor_list = array_merge($auditor_list, $a3_affiliate_list);
                        }     
                    }
                }
            }

        }


        if (in_array($_SESSION['role_name'], ['Group Manager','Coordinator'])) {
            $surgical_coordinator_list = (new classsurgical_coordinators())->selectAll();
            $surgical_coordinators_group_list = (new CustomSecFilters())->SurgicalCoordinatorsFilter->surgical_coordinators_group_list($id_user, $_SESSION['role_name'],$surgical_coordinator_list);
        
            if (($auditor_list)){
                if ($surgical_coordinators_group_list) {

                    $auditor_list_full = $auditor_list ;
                    $auditor_list = [];
                    foreach ($surgical_coordinators_group_list->groups_filter_list as $group_item ) {
                        $auditor_list = array_merge($auditor_list, Functions::get_items_by($auditor_list_full,'payee_group_id',$group_item->id_groups) );
                    }                 
                } else {
                    $auditor_list = [];
                }
            }    
        }

        if (in_array($_SESSION['role_name'], ['Group Principal'])) {
            $groups_filter_list = new classgroups();
            $surgical_coordinators_group_list = new \stdClass();
            $surgical_coordinators_group_list->groups_filter_list = $groups_filter_list->selectBy('linked_user', $id_user);

            if (($auditor_list)){
                if ($surgical_coordinators_group_list) {

                    $auditor_list_full = $auditor_list ;
                    $auditor_list = [];
                    foreach ($surgical_coordinators_group_list->groups_filter_list as $group_item ) {
                        $auditor_list = array_merge($auditor_list, Functions::get_items_by($auditor_list_full,'payee_group_id',$group_item->id_groups) );
                    }                 
                } else {
                    $auditor_list = [];
                }
            }    
        }

        $auditor_list_full = $auditor_list;
        $auditor_list = [];
        $duplicated = [];
        foreach ($auditor_list_full as $item) {
            if (!in_array($item->contract, $duplicated)) {
                array_push($auditor_list, $item);
                array_push($duplicated, $item->contract);
            } 
        }

     // extend PHP_read
       if(!$auditor_list){
           $auditor_list = new classauditor;
       }else{
           if ($retriveData->id_auditor == 0){
               $auditor_list = Functions::filter_items_by_interval($auditor_list,'transaction_date',$retriveData->datepickerCustomFilterBegin,$retriveData->datepickerCustomFilterEnd);
           }
       }
       if ($retriveData->id_affiliates_status != '-1') {
          Functions::security_access_log($id_user, 'auditor', 'read', $retriveData->id_auditor);
       }
       http_response_code(200);
       echo '{"message":"Successful","status":200,"data":{"auditor":'. json_encode($auditor_list).'}}';
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
    $auditor = new classauditor;   
    $auditor->transaction = $retriveData->transaction;
    $auditor->contract = $retriveData->contract;
    $auditor->premium = $retriveData->premium;
    $auditor->group_rate = $retriveData->group_rate;
    $auditor->dentalCare_fee = $retriveData->dentalCare_fee;
    $auditor->underwriter_cost = $retriveData->underwriter_cost;
    $auditor->affiliate = $retriveData->affiliate;
    $auditor->dentalCare_net = $retriveData->dentalCare_net;
    $auditor->transaction_date = $retriveData->transaction_date;
    $auditor->payee_affiliate_name = $retriveData->payee_affiliate_name;
    $auditor->payee_affiliate_id = $retriveData->payee_affiliate_id;
    $auditor->payee_group_name = $retriveData->payee_group_name;
    $auditor->is_payed_group = $retriveData->is_payed_group;
    $auditor->group_check_number = $retriveData->group_check_number;
    $auditor->group_payment_date = $retriveData->group_payment_date;
    $auditor->payee_group_id = $retriveData->payee_group_id;
    $auditor->payee_underwriter_name = $retriveData->payee_underwriter_name;
    $auditor->is_payed_underwriter = $retriveData->is_payed_underwriter;
    $auditor->payee_underwriter_check_number = $retriveData->payee_underwriter_check_number;
    $auditor->payee_underwriter_payment_date = $retriveData->payee_underwriter_payment_date;
    $auditor->payee_underwriter_id = $retriveData->payee_underwriter_id;
    $auditor->is_cancelled = $retriveData->is_cancelled;
    $auditor->is_payed_affiliate = $retriveData->is_payed_affiliate;
    $auditor->doctor_name = $retriveData->doctor_name;
    $auditor->doctor_id = $retriveData->doctor_id;
    $auditor->plan_name = $retriveData->plan_name;
    $auditor->plan_id = $retriveData->plan_id;
    $auditor->facility_name = $retriveData->facility_name;
    $auditor->facility_id = $retriveData->facility_id;
    $auditor->patient_name = $retriveData->patient_name;
    $auditor->coordinator_name = $retriveData->coordinator_name;
    $auditor->coordinator_id = $retriveData->coordinator_id;
    $auditor->procedure_date = $retriveData->procedure_date;
    $auditor->payment_method = $retriveData->payment_method;
    $auditor->a1_affiliate_name = $retriveData->a1_affiliate_name;
    $auditor->a1_affiliate_is_payed = $retriveData->a1_affiliate_is_payed;
    $auditor->a1_check_number = $retriveData->a1_check_number;
    $auditor->a1_payment_date = $retriveData->a1_payment_date;
    $auditor->a1_affiliate_id = $retriveData->a1_affiliate_id;
    $auditor->a1_affiliate_amount = $retriveData->a1_affiliate_amount;
    $auditor->a2_affiliate_name = $retriveData->a2_affiliate_name;
    $auditor->a2_affiliate_is_payed = $retriveData->a2_affiliate_is_payed;
    $auditor->a2_check_number = $retriveData->a2_check_number;
    $auditor->a2_payment_date = $retriveData->a2_payment_date;
    $auditor->a2_affiliate_id = $retriveData->a2_affiliate_id;
    $auditor->a2_affiliate_amount = $retriveData->a2_affiliate_amount;
    $auditor->a3_affiliate_name = $retriveData->a3_affiliate_name;
    $auditor->a3_affiliate_is_payed = $retriveData->a3_affiliate_is_payed;
    $auditor->a3_check_number = $retriveData->a3_check_number;
    $auditor->a3_payment_date = $retriveData->a3_payment_date;
    $auditor->a3_affiliate_id = $retriveData->a3_affiliate_id;
    $auditor->a3_affiliate_amount = $retriveData->a3_affiliate_amount;
    $auditor->contract_status = $retriveData->contract_status;
    $auditor->transaction_fee = $retriveData->transaction_fee;
    $auditor->pay_by = $retriveData->pay_by;
    $auditor->patient_email = $retriveData->patient_email;
    $auditor->patient_phone = $retriveData->patient_phone;
    $auditor->patient_full_address = $retriveData->patient_full_address;
    $auditor->purchase_date = $retriveData->purchase_date;
    $auditor->completion_date = $retriveData->completion_date;
    $auditor->cancelation_date = $retriveData->cancelation_date;
    $auditor->id_auditor = $retriveData->id_auditor;
    $OPCODE_RESULT = $auditor->update($auditor,$id_user);
        if($OPCODE_RESULT){
        Functions::security_access_log($id_user, 'auditor', 'update', $retriveData->id_auditor);
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
      $auditor = new classauditor;   
      $QUERY_RESULT = $auditor->delete($retriveData->id_auditor,$id_user);
      if($QUERY_RESULT->error_flag === false ){
        Functions::security_access_log($id_user, 'auditor', 'delete', $retriveData->id_auditor);
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
