<?php
namespace gtc_core;

if (!isset($coreAction)) {
   echo '{"message":"bad request","status":400,"data":""}';
   http_response_code(400);
   return false;
}

switch ($coreAction) {
   case 'custom_print_document':
      custom_print_document($retriveData, $id_user);
      break;
   case 'custom_test':
      custom_test($retriveData, $id_user);
      break;
   default:
      custom_bad_request();
}

function custom_print_document($retriveData, $id_user)
{
   try {
      require '../integration/integration_dompdf.php';
      /** @var \Dompdf\Dompdf $dompdf */
      if (isset($dompdf)) {

         $pdf_template = file_get_contents('master_agreement_providers.htm');

         if ($retriveData->id_groups == 0) {
            /** @var classstates $state */
            $state = new classstates();
            $state = $state->selectBy('id_states', $retriveData->state)[0];

            $group_address = $retriveData->group_address . ", " . $state->state_code . " " . $retriveData->zip_code;
            $pdf_template = str_replace("groupname", $retriveData->group_name, $pdf_template);
            $pdf_template = str_replace("groupaddress", $group_address, $pdf_template);
         } else {
            $groups = new classgroups();
            $groups = $groups->selectBy('id_groups', $retriveData->id_groups);
            $groups = $groups ? $groups[0] : null;

            if ($groups) {
               /** @var classstates $state */
               $state = new classstates();
               $state = $state->selectBy('id_states', $groups->state)[0];

               $group_address = $groups->address . ", " . $groups->city . ", " . $state->state_code . " " . $groups->zip_code;
               $pdf_template = str_replace("groupname", $groups->entity_name, $pdf_template);
               $pdf_template = str_replace("groupaddress", $group_address, $pdf_template);
            } else {
               $pdf_template = str_replace("groupname", "", $pdf_template);
               $pdf_template = str_replace("groupaddress", "", $pdf_template);
            }
         }

         /** @var classplans $plan */
         $plan = new classplans();
         $planDT3 = $plan->selectBy('description', 'DT3')[0];
         $planDT5 = $plan->selectBy('description', 'DT5')[0];

         $pdf_template = str_replace("dentalCarefee3", $planDT3->sales_price, $pdf_template);
         $pdf_template = str_replace("dentalCarefee5", $planDT5->sales_price, $pdf_template);

         $dompdf->loadHtml($pdf_template);
         $dompdf->setPaper('letter', 'portrait');
         $dompdf->render();
         $canvas = $dompdf->getCanvas();
         $footer = $canvas->open_object();
         $w = $canvas->get_width();
         $h = $canvas->get_height();
         $canvas->page_text($w - 590, $h - 28, "dentalCare™ 2022", 'helvetica', 8, array(0.565, 0.565, 0.565));
         $canvas->page_text($w / 2, $h - 28, "{PAGE_NUM} of {PAGE_COUNT}", 'helvetica', 8, array(0.565, 0.565, 0.565));
         $canvas->close_object();
         $canvas->add_object($footer, "all");
         $dompdf->stream('master_agreement_providers.pdf', array("Attachment" => false));

      }
      //    echo '{"message":"success","status":200,"data":""}';
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