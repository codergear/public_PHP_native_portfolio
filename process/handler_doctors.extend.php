<?php
namespace gtc_core;

if (!isset($coreAction)) {
   echo '{"message":"bad request","status":400,"data":""}';
   http_response_code(400);
   return false;
}

switch ($coreAction) {
   case 'custom_test':
      custom_test($retriveData, $id_user);
      break;
   case 'custom_surgical_coordinators_filter':
      custom_surgical_coordinators_filter($retriveData, $id_user);
      break;
   default:
      custom_bad_request();
}

function custom_surgical_coordinators_filter($retriveData, $id_user)
{
   try {
      $surgical_coordinators_filter_list = null;

      $surgical_coordinators_list = (new classsurgical_coordinators())->selectAll();
      $surgical_coordinators_byGroup = (new CustomSecFilters())->SurgicalCoordinatorsFilter->surgical_coordinators_byGroup($id_user, $_SESSION['role_name'], $surgical_coordinators_list, $retriveData->group_detail);
      if ($surgical_coordinators_byGroup) {
         $surgical_coordinators_filter_list = $surgical_coordinators_byGroup->surgical_coordinators_list;
      }


      echo '{"message":"Successful","status":200,"data":{"surgical_coordinators_filter_list":' . json_encode($surgical_coordinators_filter_list) . '}}';
      http_response_code(200);



   } catch (\Throwable | \Exception $e) {
      $exception_msg = $e->getMessage();
      $exception_msg = str_replace('"', "'", $exception_msg);
      echo '{"message":"error","status":500,"data":"' . $exception_msg . '"}';
      http_response_code(500);
   } finally {
      return false;
   }
}



function custom_test($retriveData, $id_user)
{
   try {
      echo '{"message":"Successful","status":200,"data":[' . json_encode($retriveData) . ']}';
      http_response_code(200);
   } catch (\Throwable | \Exception $e) {
      $exception_msg = $e->getMessage();
      $exception_msg = str_replace('"', "'", $exception_msg);
      echo '{"message":"error","status":500,"data":"' . $exception_msg . '"}';
      http_response_code(500);
   } finally {
      return false;
   }
}

function custom_bad_request()
{
   echo '{"message":"custom bad request","status":400,"data":[]}';
   http_response_code(400);
   return false;
}

return false;
?>