<?php
namespace gtc_core;

if (!isset($coreAction)) {
   echo '{"message":"bad request","status":400,"data":""}';
   http_response_code(400);
   return false;
}

switch ($coreAction) {
   case 'custom_underwriter_print_contract':
      custom_underwriter_print_contract($retriveData, $id_user);
      break;
   case 'custom_complete_contract':
      custom_complete_contract($retriveData, $id_user);
      break;
   case 'custom_cancel_contract':
      custom_cancel_contract($retriveData, $id_user);
      break;
   default:
      custom_bad_request();
      break;
}

function custom_underwriter_print_contract($retriveData, $id_user)
{
   try {
      require '../integration/integration_dompdf.php';
      /** @var \Dompdf\Dompdf $dompdf */
      if (isset($dompdf)) {

         $pdf_template = file_get_contents('dental_service_agreement_warranty.htm');

         $contract = new classcontracts();
         $contract = $contract->selectBy('contract', Functions::data64UnMask($retriveData->contract));
         $contract = $contract ? $contract[0] : null;
         /** @var classcontracts $contract */
         if ($contract) {
            $transactionDateTime = date_create($contract->_date_umo, timezone_open("America/New_York"));


            $plan = new classplans();
            $plan = $plan->selectBy('id_plans', $contract->plan)[0];

            $pdf_template = str_replace("patientname", $contract->full_name, $pdf_template);
            $pdf_template = str_replace("contractnumber", $contract->contract, $pdf_template);
            $pdf_template = str_replace("proceduredate", $contract->surgery_date, $pdf_template);
            $pdf_template = str_replace("planpremium", $plan->protection, $pdf_template);

         } else {
            $pdf_template = str_replace("planpremium", "", $pdf_template);
         }

         $dompdf->loadHtml($pdf_template);
         $dompdf->setPaper('letter', 'portrait');
         $dompdf->render();
         $canvas = $dompdf->getCanvas();
         $canvas->open_object();
         $w = $canvas->get_width();
         $h = $canvas->get_height();
         $canvas->page_text($w - 590, $h - 28, "dentalCare™ 2022", 'helvetica', 8, array(0.565, 0.565, 0.565));
         $canvas->page_text($w - 90, $h - 28, "AWS DEN 12/6/22", 'helvetica', 8, array(0.565, 0.565, 0.565));
         $canvas->page_text($w / 2, $h - 28, "{PAGE_NUM} of {PAGE_COUNT}", 'helvetica', 8, array(0.565, 0.565, 0.565));
         $canvas->close_object();
         $dompdf->stream('dental_service_agreement.pdf', array("Attachment" => false));
      }
      http_response_code(200);
   } catch (\Throwable | \Exception $e) {
      $exception_msg = $e->getMessage();
      $exception_msg = str_replace('"', "'", $exception_msg);
      echo '{"message":"error","status":500,"data":"' . $exception_msg . '"}';
      http_response_code(500);
   }
}

function custom_complete_contract($retriveData, $id_user)
{

   $contract = new classcontracts();
   $contract_item = $contract->selectBy('contract', $retriveData->contract)[0];
   $contract_item->is_cancelled = "0";
   $contract_item->contract_status = "Completed";

   $auditor = new classauditor();
   $auditor_item = $auditor->selectBy('contract', $contract_item->contract)[0];
   $auditor_item->is_cancelled = "0";
   $auditor_item->contract_status = "Completed";

   $contract->update($contract_item, $id_user);
   $auditor->update($auditor_item, $id_user);

   echo '{"message":"Successful","status":200,"data":""}';
   http_response_code(200);
   return false;
}

function custom_cancel_contract($retriveData, $id_user)
{

   $contract = new classcontracts();
   $contract_item = $contract->selectBy('contract', $retriveData->contract)[0];
   $contract_item->is_cancelled = "1";
   $contract_item->contract_status = "Cancelled";

   $auditor = new classauditor();
   $auditor_item = $auditor->selectBy('contract', $contract_item->contract)[0];
   $auditor_item->is_cancelled = "1";
   $auditor_item->contract_status = "Cancelled";

   $contract->update($contract_item, $id_user);
   $auditor->update($auditor_item, $id_user);

   echo '{"message":"Successful","status":200,"data":""}';
   http_response_code(200);
   return false;
}

function custom_bad_request()
{
   echo '{"message":"custom bad request","status":400,"data":[]}';
   http_response_code(400);
   return false;
}

return false;
?>