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
   case 'custom_affiliate_filter':
      custom_affiliate_filter($retriveData, $id_user);
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

         $template_filename = "";

         /** @var classaffiliates $affiliates */
         $affiliates = new classaffiliates();

         if ($retriveData->id_affiliates == 0) {
            if ($retriveData->level == 1) {
               $template_filename = "mga_agreement.htm";
            } else {
               $template_filename = "agent_agreement.htm";
            }
         } else {
            $affiliates = $affiliates->selectBy('id_affiliates', $retriveData->id_affiliates)[0];
            if ($affiliates->afiliate_level == 1) {
               $template_filename = "mga_agreement.htm";
            } else {
               $template_filename = "agent_agreement.htm";
            }
         }

         $pdf_template = file_get_contents($template_filename);

         if ($retriveData->id_affiliates == 0) {
            /** @var classstates $state */
            $state = new classstates();
            $state = $state->selectBy('id_states', $retriveData->state)[0];

            $address = $retriveData->address . ", " . $state->state_code . " " . $retriveData->zip_code;
            $pdf_template = str_replace("affiliatename", $retriveData->affiliate_name, $pdf_template);
            $pdf_template = str_replace("affiliateaddress", $address, $pdf_template);
         } else {


            if ($affiliates) {
               /** @var classstates $state */
               $state = new classstates();
               $state = $state->selectBy('id_states', $affiliates->state)[0];

               $address = $affiliates->address . ", " . $affiliates->city . ", " . $state->state_code . " " . $affiliates->zip_code;
               $pdf_template = str_replace("affiliatename", $affiliates->full_name, $pdf_template);
               $pdf_template = str_replace("affiliateaddress", $address, $pdf_template);
            } else {
               $pdf_template = str_replace("affiliatename", "", $pdf_template);
               $pdf_template = str_replace("affiliateaddress", "", $pdf_template);
            }
         }


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

function custom_bad_request()
{
   echo '{"message":"custom bad request","status":400,"data":[]}';
   http_response_code(400);
   return false;
}

function custom_affiliate_filter($retriveData, $id_user)
{
   try {
      $affiliates = new classaffiliates();
      $affiliates_filter_list = null;
      $affiliates = $affiliates->selectAll();

      $affiliates = Functions::get_items_by($affiliates, 'status', '2');

      if ($affiliates) {
         if ($retriveData->afiliate_level == 2) {
            $affiliates_filter_list = Functions::get_items_by($affiliates, "afiliate_level", "1");
         }
         if ($retriveData->afiliate_level == 3) {
            $affiliates_filter_list = Functions::get_items_by($affiliates, "afiliate_level", "2");
         }
      }

      if ($affiliates_filter_list) {
         if (in_array($_SESSION['role_name'], ['MGA', 'GA', 'Affiliate'])) {

            $affiliateTo_list = (new CustomSecFilters())->AffiliatesFilter->affiliateTo_list($id_user, $_SESSION['role_name'], $affiliates_filter_list, $retriveData->afiliate_level);

            if ($affiliateTo_list) {
               $affiliates_filter_list = $affiliateTo_list->affiliates_filter_list;
            } else {
               $affiliates_filter_list = null;
            }

         }
      }


      echo '{"message":"Successful","status":200,"data":{"affiliates_filter_list":' . json_encode($affiliates_filter_list) . '}}';
      http_response_code(200);
   } catch (\Throwable | \Exception $e) {
      echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
      http_response_code(500);
   } finally {
      return false;
   }

}


return false;
?>