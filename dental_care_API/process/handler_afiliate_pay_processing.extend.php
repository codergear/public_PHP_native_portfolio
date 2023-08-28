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
      $all_payee_filter_list = [];
      $all_payee_detail_list = [];

      $auditor = new classauditor();
      $auditor_list = $auditor->selectAll();
      if ($auditor_list) {
         $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
         $auditor_list = Functions::get_items_by($auditor_list, "a1_affiliate_is_payed", "0");
         $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $retriveData->begin_date, $retriveData->end_date);
         if ($auditor_list) {
            foreach ($auditor_list as $item) {
               $list_item = new \stdClass;
               $list_item->_id = $item->a1_affiliate_id;
               $list_item->_name = $item->a1_affiliate_name;
               $list_item->_amount = $item->a1_affiliate_amount;
               $list_item->_transaction = $item->transaction;
               $list_item->_contract = $item->contract;
               $list_item->_transaction_date = $item->transaction_date;
               $list_item->_level = 1;
               $payee_detail_list[] = $list_item;
            }
         }
         $payee_filter_list = Functions::sum_by($auditor_list, 'a1_affiliate_id', 'a1_affiliate_amount');
         if ($payee_filter_list) {
            foreach ($payee_filter_list as $item) {
               $item->_name = $item->a1_affiliate_name . " ($" . number_format($item->a1_affiliate_amount, 2) . ")";
               $item->_key = $item->a1_affiliate_id;
               $item->_amount = $item->a1_affiliate_amount;
               $item->_level = 1;
            }
         }
         if (($payee_filter_list) && ($payee_detail_list)) {
            $all_payee_filter_list = $payee_filter_list;
            $all_payee_detail_list = $payee_detail_list;
            $payee_filter_list = null;
            $payee_detail_list = null;
         }
      }


      $auditor = new classauditor();
      $auditor_list = $auditor->selectAll();
      if ($auditor_list) {
         $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
         $auditor_list = Functions::get_items_by($auditor_list, "a2_affiliate_is_payed", "0");
         $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $retriveData->begin_date, $retriveData->end_date);
         if ($auditor_list) {
            foreach ($auditor_list as $item) {
               $list_item = new \stdClass;
               $list_item->_id = $item->a2_affiliate_id;
               $list_item->_name = $item->a2_affiliate_name;
               $list_item->_amount = $item->a2_affiliate_amount;
               $list_item->_transaction = $item->transaction;
               $list_item->_contract = $item->contract;
               $list_item->_transaction_date = $item->transaction_date;
               $list_item->_level = 2;
               $payee_detail_list[] = $list_item;
            }
         }
         $payee_filter_list = Functions::sum_by($auditor_list, 'a2_affiliate_id', 'a2_affiliate_amount');
         if ($payee_filter_list) {
            foreach ($payee_filter_list as $item) {
               $item->_name = $item->a2_affiliate_name . " ($" . number_format($item->a2_affiliate_amount, 2) . ")";
               $item->_key = $item->a2_affiliate_id;
               $item->_amount = $item->a2_affiliate_amount;
               $item->_level = 2;
            }
         }

         if (($payee_filter_list) && ($payee_detail_list)) {
            $all_payee_filter_list = array_merge($all_payee_filter_list, $payee_filter_list);
            $all_payee_detail_list = array_merge($all_payee_detail_list, $payee_detail_list);
            $payee_filter_list = null;
            $payee_detail_list = null;
         }
      }



      $auditor = new classauditor();
      $auditor_list = $auditor->selectAll();
      if ($auditor_list) {
         $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
         $auditor_list = Functions::get_items_by($auditor_list, "a3_affiliate_is_payed", "0");
         $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $retriveData->begin_date, $retriveData->end_date);
         if ($auditor_list) {
            foreach ($auditor_list as $item) {
               $list_item = new \stdClass;
               $list_item->_id = $item->a3_affiliate_id;
               $list_item->_name = $item->a3_affiliate_name;
               $list_item->_amount = $item->a3_affiliate_amount;
               $list_item->_transaction = $item->transaction;
               $list_item->_contract = $item->contract;
               $list_item->_transaction_date = $item->transaction_date;
               $list_item->_level = 3;
               $payee_detail_list[] = $list_item;
            }
         }
         $payee_filter_list = Functions::sum_by($auditor_list, 'a3_affiliate_id', 'a3_affiliate_amount');

         if ($payee_filter_list) {
            foreach ($payee_filter_list as $item) {
               $item->_name = $item->a3_affiliate_name . " ($" . number_format($item->a3_affiliate_amount, 2) . ")";
               $item->_key = $item->a3_affiliate_id;
               $item->_amount = $item->a3_affiliate_amount;
               $item->_level = 3;
            }
         }
         if (($payee_filter_list) && ($payee_detail_list)) {
            $all_payee_filter_list = array_merge($all_payee_filter_list, $payee_filter_list);
            $all_payee_detail_list = array_merge($all_payee_detail_list, $payee_detail_list);
            $payee_filter_list = null;
            $payee_detail_list = null;
         }
      }

      echo '{"message":"Successful","status":200,"data":{"payee_filter_list":' . json_encode($all_payee_filter_list) . ',"payee_detail_list":' . json_encode($all_payee_detail_list) . '}}';
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
      $all_payee_filter_list = [];
      $all_payee_detail_list = [];

      $auditor = new classauditor();
      $auditor_list = $auditor->selectAll();
      if ($auditor_list) {
         $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
         $auditor_list = Functions::get_items_by($auditor_list, "a1_affiliate_is_payed", "1");
         $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $retriveData->begin_date, $retriveData->end_date);
         if ($auditor_list) {
            foreach ($auditor_list as $item) {
               $list_item = new \stdClass;
               $list_item->_id = $item->a1_affiliate_id;
               $list_item->_name = $item->a1_affiliate_name;
               $list_item->_amount = $item->a1_affiliate_amount;
               $list_item->_transaction = $item->transaction;
               $list_item->_contract = $item->contract;
               $list_item->_transaction_date = $item->transaction_date;
               $list_item->_level = 1;
               $payee_detail_list[] = $list_item;
            }
         }
         $payee_filter_list = Functions::sum_by($auditor_list, 'a1_affiliate_id', 'a1_affiliate_amount');
         if ($payee_filter_list) {
            foreach ($payee_filter_list as $item) {
               $item->_name = $item->a1_affiliate_name . " ($" . number_format($item->a1_affiliate_amount, 2) . ")";
               $item->_key = $item->a1_affiliate_id;
               $item->_amount = $item->a1_affiliate_amount;
               $item->_level = 1;
            }
         }
         if (($payee_filter_list) && ($payee_detail_list)) {
            $all_payee_filter_list = $payee_filter_list;
            $all_payee_detail_list = $payee_detail_list;
            $payee_filter_list = null;
            $payee_detail_list = null;
         }
      }


      $auditor = new classauditor();
      $auditor_list = $auditor->selectAll();
      if ($auditor_list) {
         $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
         $auditor_list = Functions::get_items_by($auditor_list, "a2_affiliate_is_payed", "1");
         $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $retriveData->begin_date, $retriveData->end_date);
         if ($auditor_list) {
            foreach ($auditor_list as $item) {
               $list_item = new \stdClass;
               $list_item->_id = $item->a2_affiliate_id;
               $list_item->_name = $item->a2_affiliate_name;
               $list_item->_amount = $item->a2_affiliate_amount;
               $list_item->_transaction = $item->transaction;
               $list_item->_contract = $item->contract;
               $list_item->_transaction_date = $item->transaction_date;
               $list_item->_level = 2;
               $payee_detail_list[] = $list_item;
            }
         }
         $payee_filter_list = Functions::sum_by($auditor_list, 'a2_affiliate_id', 'a2_affiliate_amount');
         if ($payee_filter_list) {
            foreach ($payee_filter_list as $item) {
               $item->_name = $item->a2_affiliate_name . " ($" . number_format($item->a2_affiliate_amount, 2) . ")";
               $item->_key = $item->a2_affiliate_id;
               $item->_amount = $item->a2_affiliate_amount;
               $item->_level = 2;
            }
         }

         if (($payee_filter_list) && ($payee_detail_list)) {
            $all_payee_filter_list = array_merge($all_payee_filter_list, $payee_filter_list);
            $all_payee_detail_list = array_merge($all_payee_detail_list, $payee_detail_list);
            $payee_filter_list = null;
            $payee_detail_list = null;
         }
      }



      $auditor = new classauditor();
      $auditor_list = $auditor->selectAll();
      if ($auditor_list) {
         $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
         $auditor_list = Functions::get_items_by($auditor_list, "a3_affiliate_is_payed", "1");
         $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $retriveData->begin_date, $retriveData->end_date);
         if ($auditor_list) {
            foreach ($auditor_list as $item) {
               $list_item = new \stdClass;
               $list_item->_id = $item->a3_affiliate_id;
               $list_item->_name = $item->a3_affiliate_name;
               $list_item->_amount = $item->a3_affiliate_amount;
               $list_item->_transaction = $item->transaction;
               $list_item->_contract = $item->contract;
               $list_item->_transaction_date = $item->transaction_date;
               $list_item->_level = 3;
               $payee_detail_list[] = $list_item;
            }
         }
         $payee_filter_list = Functions::sum_by($auditor_list, 'a3_affiliate_id', 'a3_affiliate_amount');

         if ($payee_filter_list) {
            foreach ($payee_filter_list as $item) {
               $item->_name = $item->a3_affiliate_name . " ($" . number_format($item->a3_affiliate_amount, 2) . ")";
               $item->_key = $item->a3_affiliate_id;
               $item->_amount = $item->a3_affiliate_amount;
               $item->_level = 3;
            }
         }
         if (($payee_filter_list) && ($payee_detail_list)) {
            $all_payee_filter_list = array_merge($all_payee_filter_list, $payee_filter_list);
            $all_payee_detail_list = array_merge($all_payee_detail_list, $payee_detail_list);
            $payee_filter_list = null;
            $payee_detail_list = null;
         }
      }

      echo '{"message":"Successful","status":200,"data":{"payee_filter_list":' . json_encode($all_payee_filter_list) . ',"payee_detail_list":' . json_encode($all_payee_detail_list) . '}}';
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
      switch ($retriveData->level) {
         case 1:
            if ($auditor_list) {
               $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
               $auditor_list = Functions::get_items_by($auditor_list, "a1_affiliate_is_payed", "0");
               $auditor_list = Functions::get_items_by($auditor_list, "a1_affiliate_id", $retriveData->payee);
               $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $retriveData->begin_date, $retriveData->end_date);
               if ($auditor_list) {
                  foreach ($auditor_list as $item) {
                     $item->a1_affiliate_is_payed = "1";
                     $item->a1_check_number = $retriveData->check_number;
                     $item->a1_payment_date = $retriveData->payment_date;
                     $auditor->update($item, $id_user);
                  }
               }
            }
            break;
         case 2:
            if ($auditor_list) {
               $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
               $auditor_list = Functions::get_items_by($auditor_list, "a2_affiliate_is_payed", "0");
               $auditor_list = Functions::get_items_by($auditor_list, "a2_affiliate_id", $retriveData->payee);
               $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $retriveData->begin_date, $retriveData->end_date);
               if ($auditor_list) {
                  foreach ($auditor_list as $item) {
                     $item->a2_affiliate_is_payed = "1";
                     $item->a2_check_number = $retriveData->check_number;
                     $item->a2_payment_date = $retriveData->payment_date;
                     $auditor->update($item, $id_user);
                  }
               }
            }
            break;
         case 3:
            if ($auditor_list) {
               $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
               $auditor_list = Functions::get_items_by($auditor_list, "a3_affiliate_is_payed", "0");
               $auditor_list = Functions::get_items_by($auditor_list, "a3_affiliate_id", $retriveData->payee);
               $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $retriveData->begin_date, $retriveData->end_date);
               if ($auditor_list) {
                  foreach ($auditor_list as $item) {
                     $item->a3_affiliate_is_payed = "1";
                     $item->a3_check_number = $retriveData->check_number;
                     $item->a3_payment_date = $retriveData->payment_date;
                     $auditor->update($item, $id_user);
                  }
               }
            }
            break;
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

   $afiliate_pay_processing = new classafiliate_pay_processing();
   $afiliate_pay_processing_item = $afiliate_pay_processing->selectBy('id_afiliate_pay_processing', $retriveData->payment)[0];
   $afiliate_pay_processing_item->is_cancelled = "1";
   $afiliate_pay_processing_item->payment_status = "Cancelled";

   $payee_filter_list = null;
   $auditor = new classauditor();
   $auditor_list = $auditor->selectAll();
   if ($auditor_list) {
      $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
      $auditor_list = Functions::get_items_by($auditor_list, "a1_affiliate_id", $afiliate_pay_processing_item->payee);
      $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $afiliate_pay_processing_item->begin_date, $afiliate_pay_processing_item->end_date);
      if ($auditor_list) {
         foreach ($auditor_list as $item) {
            $item->a1_affiliate_is_payed = "0";
            $auditor->update($item, $id_user);
         }
      }
   }

   $payee_filter_list = null;
   $auditor = new classauditor();
   $auditor_list = $auditor->selectAll();
   if ($auditor_list) {
      $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
      $auditor_list = Functions::get_items_by($auditor_list, "a2_affiliate_id", $afiliate_pay_processing_item->payee);
      $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $afiliate_pay_processing_item->begin_date, $afiliate_pay_processing_item->end_date);
      if ($auditor_list) {
         foreach ($auditor_list as $item) {
            $item->a2_affiliate_is_payed = "0";
            $auditor->update($item, $id_user);
         }
      }
   }

   $payee_filter_list = null;
   $auditor = new classauditor();
   $auditor_list = $auditor->selectAll();
   if ($auditor_list) {
      $auditor_list = Functions::get_items_by($auditor_list, "contract_status", "Completed");
      $auditor_list = Functions::get_items_by($auditor_list, "a3_affiliate_id", $afiliate_pay_processing_item->payee);
      $auditor_list = Functions::filter_items_by_interval($auditor_list, 'transaction_date', $afiliate_pay_processing_item->begin_date, $afiliate_pay_processing_item->end_date);
      if ($auditor_list) {
         foreach ($auditor_list as $item) {
            $item->a3_affiliate_is_payed = "0";
            $auditor->update($item, $id_user);
         }
      }
   }


   $afiliate_pay_processing->update($afiliate_pay_processing_item, $id_user);

   echo '{"message":"Successful","status":200,"data":""}';
   http_response_code(200);
   return false;
}

return false;
?>