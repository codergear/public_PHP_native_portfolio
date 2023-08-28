<?php
namespace gtc_core;

if (!isset($coreAction)) {
   echo '{"message":"bad request","status":400,"data":""}';
   http_response_code(400);
   return false;
}

switch ($coreAction) {
   case 'custom_payee_filter_paid':
      custom_payee_filter_paid($retriveData, $id_user);
      break;
   case 'custom_payee_pay':
      custom_payee_pay($retriveData, $id_user);
      break;
   case 'custom_payee_filter':
      custom_payee_filter($retriveData, $id_user);
      break;
   case 'custom_cancel_payment':
      custom_cancel_payment($retriveData, $id_user);
      break;
   default:
      custom_bad_request();
}

function custom_bad_request()
{
   echo '{"message":"custom bad request","status":400,"data":[]}';
   http_response_code(400);
   return false;
}

function custom_payee_filter($retriveData, $id_user)
{
   try {
      $payee_filter_list = null;
      $payee_detail_list = null;
      $auditor = new classauditor();
      $auditor_list = $auditor->selectAll();
      if ($auditor_list) {
         $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
         $auditor_list = Functions::get_items_by($auditor_list, "is_payed_underwriter", "0");
         $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $retriveData->begin_date, $retriveData->end_date);
         if ($auditor_list) {
            /** @var classauditor $item */
            foreach ($auditor_list as $item) {
               $list_item = new \stdClass;
               $list_item->_id = $item->payee_underwriter_id;
               $list_item->_patient_name = $item->patient_name;
               $list_item->_amount = $item->underwriter_cost;
               $list_item->_transaction = $item->transaction;
               $list_item->_contract = $item->contract;
               $list_item->_transaction_date = $item->transaction_date;
               $list_item->_plan_name = $item->plan_name;
               $list_item->_name = $item->payee_underwriter_name;
               $payee_detail_list[] = $list_item;
            }
         }
         $payee_filter_list = Functions::sum_by($auditor_list, 'payee_underwriter_id', 'underwriter_cost');
         if ($payee_filter_list) {
            foreach ($payee_filter_list as $item) {
               $item->_name = $item->payee_underwriter_name . " ($" . number_format($item->underwriter_cost, 2) . ")";
               $item->_key = $item->payee_underwriter_id;
               $item->_amount = $item->underwriter_cost;
            }
         }
      }
      echo '{"message":"Successful","status":200,"data":{"payee_filter_list":' . json_encode($payee_filter_list) . ',"payee_detail_list":' . json_encode($payee_detail_list) . '}}';
      http_response_code(200);
   } catch (\Throwable | \Exception $e) {
      echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
      http_response_code(500);
   } finally {
      return false;
   }
}

function custom_payee_filter_paid($retriveData, $id_user)
{
   try {
      $payee_filter_list = null;
      $payee_detail_list = null;
      $auditor = new classauditor();
      $auditor_list = $auditor->selectAll();
      if ($auditor_list) {
         $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
         $auditor_list = Functions::get_items_by($auditor_list, "is_payed_underwriter", "1");
         $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $retriveData->begin_date, $retriveData->end_date);
         if ($auditor_list) {
            foreach ($auditor_list as $item) {
               $list_item = new \stdClass;
               $list_item->_id = $item->payee_underwriter_id;
               $list_item->_name = $item->payee_underwriter_name;
               $list_item->_amount = $item->underwriter_cost;
               $list_item->_transaction = $item->transaction;
               $list_item->_contract = $item->contract;
               $list_item->_transaction_date = $item->transaction_date;
               $list_item->_plan_name = $item->plan_name;
               $payee_detail_list[] = $list_item;
            }
         }
         $payee_filter_list = Functions::sum_by($auditor_list, 'payee_underwriter_id', 'underwriter_cost');
         if ($payee_filter_list) {
            foreach ($payee_filter_list as $item) {
               $item->_name = $item->payee_underwriter_name . " ($" . number_format($item->underwriter_cost, 2) . ")";
               $item->_key = $item->payee_underwriter_id;
               $item->_amount = $item->underwriter_cost;
            }
         }
      }
      echo '{"message":"Successful","status":200,"data":{"payee_filter_list":' . json_encode($payee_filter_list) . ',"payee_detail_list":' . json_encode($payee_detail_list) . '}}';
      http_response_code(200);
   } catch (\Throwable | \Exception $e) {
      echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
      http_response_code(500);
   } finally {
      return false;
   }
}

function custom_payee_pay($retriveData, $id_user)
{
   try {
      $payee_filter_list = null;
      $auditor = new classauditor();
      $auditor_list = $auditor->selectAll();
      if ($auditor_list) {
         $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
         $auditor_list = Functions::get_items_by($auditor_list, "is_payed_underwriter", "0");
         $auditor_list = Functions::get_items_by($auditor_list, "payee_underwriter_id", $retriveData->payee);
         $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $retriveData->begin_date, $retriveData->end_date);
         if ($auditor_list) {
            foreach ($auditor_list as $item) {
               $item->is_payed_underwriter = "1";
               $item->payee_underwriter_check_number = $retriveData->check_number;
               $item->payee_underwriter_payment_date = $retriveData->payment_date;
               $auditor->update($item, $id_user);
            }
         }
      }
      echo '{"message":"Successful","status":200,"data":""}';
      http_response_code(200);
   } catch (\Throwable | \Exception $e) {
      echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
      http_response_code(500);
   } finally {
      return false;
   }
}

function custom_cancel_payment($retriveData, $id_user)
{

   $underwriter_pay_processing = new classunderwriter_pay_processing();
   $underwriter_pay_processing_item = $underwriter_pay_processing->selectBy('id_underwriter_pay_processing', $retriveData->payment)[0];
   $underwriter_pay_processing_item->is_cancelled = "1";
   $underwriter_pay_processing_item->payment_status = "Cancelled";

   $payee_filter_list = null;
   $auditor = new classauditor();
   $auditor_list = $auditor->selectAll();
   if ($auditor_list) {
      $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
      $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $underwriter_pay_processing_item->begin_date, $underwriter_pay_processing_item->end_date);
      if ($auditor_list) {
         foreach ($auditor_list as $item) {
            $item->is_payed_underwriter = "0";
            $auditor->update($item, $id_user);
         }
      }
   }

   $underwriter_pay_processing->update($underwriter_pay_processing_item, $id_user);

   echo '{"message":"Successful","status":200,"data":""}';
   http_response_code(200);
   return false;
}

return false;
?>