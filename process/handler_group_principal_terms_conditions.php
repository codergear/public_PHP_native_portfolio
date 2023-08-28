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
        $group_principal_terms_conditions = new classgroup_principal_terms_conditions;
        $group_principal_terms_conditions->principal_group = $retriveData->principal_group;
        $group_principal_terms_conditions->doc_1 = $retriveData->doc_1;
        $group_principal_terms_conditions->doc_2 = $retriveData->doc_2;
        $group_principal_terms_conditions->doc_3 = $retriveData->doc_3;
     // extend PHP_create

      $group = new classgroups();
      $group = $group->selectBy('id_groups', $retriveData->principal_group);
      $group = $group ? $group[0] : null;
      if ($group) {
         $group->status = 2;
         $group->agreement_date = date('m/d/Y');
         (new classgroups())->update($group, $id_user);
      }

      try {
         require_once('../integration/integration_sendgrid.php');

         require_once('../integration/integration_dompdf.php');

         require_once('principal_legal.php');

         $pdf_template = "";

         $pdf_template = $pdf_template . file_get_contents('principal_legal_table_of_content.htm');
         $pdf_template = str_replace('</body>', '', $pdf_template);         
         $pdf_template = str_replace('</html>', '', $pdf_template);

         $pdf_template = $pdf_template . "<div style='page-break-after:always;'> </div>";

         $pdf_template = $pdf_template . software_license_agreement($retriveData, $id_user, true);

         $pdf_template = $pdf_template . "<div style='page-break-after:always;'> </div>";

         $pdf_template = $pdf_template . compensation_schedule($retriveData, $id_user, true);

         $pdf_template = $pdf_template . "<div style='page-break-after:always;'> </div>";

         $pdf_template = $pdf_template . non_competition_and_confidentiality_agreement($retriveData, $id_user, true);

         $dompdf->loadHtml($pdf_template);
         $dompdf->setPaper('letter', 'portrait');
         $dompdf->render();
         $canvas = $dompdf->getCanvas();
         $canvas->open_object();
         $w = $canvas->get_width();
         $h = $canvas->get_height();
         // $canvas->page_text($w - 590, $h - 28, "dentalCare™ 2022", 'helvetica', 8, array(0.565, 0.565, 0.565));
         // $canvas->page_text($w - 90, $h - 28, "AWS DEN 12/6/22", 'helvetica', 8, array(0.565, 0.565, 0.565));
         $canvas->page_text($w / 2, $h - 28, "{PAGE_NUM} of {PAGE_COUNT}", 'helvetica', 8, array(0.565, 0.565, 0.565));
         $canvas->close_object();

         $email_sendgrid->addTo($group->email);
         $email_sendgrid->setSubject('dentalCare group principal documents');
         $email_sendgrid->addContent("text/html", 'Your dentalCare group principal documents has been attached' );
         $email_sendgrid->addAttachment($dompdf->output(), "application/pdf", 'group_principal_documents.pdf', "attachment");
         $sendgrid_response = $sendgrid->send($email_sendgrid);
         Functions::security_access_log($id_user, 'group principal documents notification', 'notification OK', 0);

      } catch (\Throwable | \Exception $e) {
         Functions::security_access_log($id_user, 'group principal documents notification', 'notification error ', 0);
      }

     // extend PHP_create
     $OPCODE_RESULT = $group_principal_terms_conditions->insert($group_principal_terms_conditions,$id_user);
     if($OPCODE_RESULT){
        $retriveData->id_group_principal_terms_conditions = $OPCODE_RESULT;
        Functions::security_access_log($id_user, 'group_principal_terms_conditions', 'create', $retriveData->id_group_principal_terms_conditions);
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
       $group_principal_terms_conditions = new classgroup_principal_terms_conditions();     
       if ($retriveData->id_group_principal_terms_conditions != 0){
           $group_principal_terms_conditions_list = $group_principal_terms_conditions->selectBy('id_group_principal_terms_conditions', $retriveData->id_group_principal_terms_conditions);
           $group_principal_terms_conditions_list = $group_principal_terms_conditions_list ? $group_principal_terms_conditions_list[0] : null ;
       }else{
           $group_principal_terms_conditions_list =  $group_principal_terms_conditions->selectAll();
       }
       $groups=new classgroups();
       $groups=$groups->selectAll();
       if (($group_principal_terms_conditions_list) && ($retriveData->id_group_principal_terms_conditions == 0)){
           foreach ($group_principal_terms_conditions_list as $group_principal_terms_conditions_item){
               foreach ($groups as $item){
                   if ($item->id_groups == $group_principal_terms_conditions_item->principal_group){
                       $group_principal_terms_conditions_item->_principal_group = $item->entity_name;
                   }
               }
               if (!isset($group_principal_terms_conditions_item->_principal_group)){$group_principal_terms_conditions_item->_principal_group='';}
           }
       } 
       if(!$group_principal_terms_conditions_list){
           $group_principal_terms_conditions_list = new classgroup_principal_terms_conditions;
       }else{
       }
       if ($retriveData->id_affiliates_status != '-1') {
          Functions::security_access_log($id_user, 'group_principal_terms_conditions', 'read', $retriveData->id_group_principal_terms_conditions);
       }
       http_response_code(200);
       echo '{"message":"Successful","status":200,"data":{"group_principal_terms_conditions":'. json_encode($group_principal_terms_conditions_list).',"groups":'. json_encode($groups).'}}';
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
    $group_principal_terms_conditions = new classgroup_principal_terms_conditions;   
    $group_principal_terms_conditions->principal_group = $retriveData->principal_group;
    $group_principal_terms_conditions->doc_1 = $retriveData->doc_1;
    $group_principal_terms_conditions->doc_2 = $retriveData->doc_2;
    $group_principal_terms_conditions->doc_3 = $retriveData->doc_3;
    $group_principal_terms_conditions->id_group_principal_terms_conditions = $retriveData->id_group_principal_terms_conditions;
    $OPCODE_RESULT = $group_principal_terms_conditions->update($group_principal_terms_conditions,$id_user);
        if($OPCODE_RESULT){
        Functions::security_access_log($id_user, 'group_principal_terms_conditions', 'update', $retriveData->id_group_principal_terms_conditions);
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
      $group_principal_terms_conditions = new classgroup_principal_terms_conditions;   
      $QUERY_RESULT = $group_principal_terms_conditions->delete($retriveData->id_group_principal_terms_conditions,$id_user);
      if($QUERY_RESULT->error_flag === false ){
        Functions::security_access_log($id_user, 'group_principal_terms_conditions', 'delete', $retriveData->id_group_principal_terms_conditions);
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
