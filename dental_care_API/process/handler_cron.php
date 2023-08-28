<?php
namespace gtc_core;

$id_user = 0;

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if ($errno === E_USER_WARNING) {
        trigger_error($errstr, E_ERROR);
        return true;
    }
});

foreach (glob('../config/*.php') as $class_filename) {
    require $class_filename;
}

foreach (glob('../class/*.php') as $class_filename) {
    require $class_filename;
}

/**  @var classauditor $auditor **/
$auditor = new classauditor();
$auditor = $auditor->selectBy("transaction", "Pending");

$contracts_payment = new classcontracts_payments();
$contracts_payment = $contracts_payment->selectAll();

/**  @var classgroups $group **/
$group = new classgroups();
$group = $group->selectAll();


require_once('../integration/cardpointe/cardpointe.php');
$cardpointe = new classcardpointe();

if ($auditor && $group) {
    /**  @var classgroups  $group_item **/
    /**  @var classauditor $auditor_item **/
    foreach ($group as $group_item) {
        $auditor_list = Functions::get_items_by($auditor, "payee_group_id", $group_item->id_groups);
        $totalToPay = 0;
        foreach ($auditor_list as $auditor_item) {
            $totalToPay = number_format($totalToPay + floatval(str_replace("$", "", $auditor_item->premium)), 2);
            if ((Functions::get_enviroment() == "local") || (Functions::get_enviroment() == "testing")) {
                //testing...
                echo $auditor_item->contract . " :: contract to pay :: " . $auditor_item->premium . "  <br>";
                //testing...
            }
        }

        if ($totalToPay != 0) {
            try {
                $provider_transaction_fee = new classprovider_transaction_fee();
                $provider_transaction_fee = $provider_transaction_fee->selectAll();
                $provider_transaction_fee = Functions::sort_items_by($provider_transaction_fee, "_date_umo", "desc");
                $provider_transaction_fee = $provider_transaction_fee ? $provider_transaction_fee[0] : null;
                if ($provider_transaction_fee) {
                    $totalToPay = number_format($totalToPay + floatval(str_replace("$", "", $provider_transaction_fee->transaction_fee)), 2);
                    $refund = 0;
                    $group_refund = new classgroup_refund();
                    $group_refund = $group_refund->selectBy("group_detail", $group_item->id_groups);
                    if ($group_refund) {
                        foreach ($group_refund as $group_refund_item) {

                            $refund = $refund + $group_refund_item->amount;
                        }
                    }

                    $subTotalToPay = $totalToPay;

                    if ($refund > $totalToPay) {
                        $refund = $totalToPay * -1;
                    } else {
                        $totalToPay = $totalToPay - $refund;
                        $refund = $refund * -1;
                    }
                }
            } catch (\Throwable | \Exception $e) {
                //silent exceltion
            }

            $state = (new classstates())->selectBy("id_states", $group_item->state)[0];

            $totalToPay = str_replace(".", "", $totalToPay);
            $totalToPay = str_replace(",", "", $totalToPay);

            if ((Functions::get_enviroment() == "local") || (Functions::get_enviroment() == "testing")) {
                //testing...
                echo "<pre>";
                echo $group_item->entity_name;
                echo " subtotal to pay " . $subTotalToPay . " " . "  <br>";
                echo " total to pay " . $totalToPay . " " . "  <br>";
                echo " refund $" . $refund . " " . "  <br>";
                echo $provider_transaction_fee->transaction_fee . " transaction fee " . "  <br>";
                echo "</pre>";
                //testing...
            }

            switch (functions::get_enviroment()) {
                case 'production':
                    if ($totalToPay == 0) {
                        $pay_ACH = new \stdClass();
                        $pay_ACH->retref = "000000000000";
                        $pay_ACH->respcode = "00";
                        $pay_ACH->resptext = "ACH payment processed successfully by internal discount.";
                    } else {
                        $pay_ACH = $cardpointe->pay_ACH($group_item->cardpointe_token, $totalToPay, $group_item->entity_name, $group_item->address, $group_item->city, $state->state_code, $group_item->zip_code);
                    }
                    break;
                case 'sandbox':
                case 'testing':
                case 'local':
                    $pay_ACH = new \stdClass();
                    $pay_ACH->retref = "000000000000";
                    $pay_ACH->respcode = "99";
                    $pay_ACH->resptext = "ACH payment is not available at this time. Please try again later.";
                    break;
            }

            if ($refund != 0) {
                $group_refund_apply = new classgroup_refund();
                $group_refund_apply->group_detail = $group_item->id_groups;
                $group_refund_apply->amount = $refund;
                $group_refund_apply->insert($group_refund_apply, 0);
            }

            if ($pay_ACH->respcode == "00") {
                try {
                    if ($refund != 0) {
                        $group_refund_apply = new classgroup_refund();
                        $group_refund_apply->group_detail = $group_item->id_groups;
                        $group_refund_apply->amount = $refund;
                        $group_refund_apply->insert($group_refund_apply, 0);
                    }
                } catch (\Throwable | \Exception $e) {
                    Functions::security_access_log(0, "contract by Provider refund", 'critical db error classgroup_refund', 0);
                }

                $body_ok = "";

                foreach ($auditor_list as $auditor_item) {
                    $auditor_item->transaction = $pay_ACH->retref;
                    (new classauditor())->update($auditor_item, 0);
                    $contracts_payment_item_paid = Functions::get_items_by($contracts_payment, "contract", $auditor_item->contract)[0];
                    $contracts_payment_item_paid->transaction_id = $pay_ACH->retref;
                    (new classcontracts_payments())->update($contracts_payment_item_paid, 0);
                    $body_ok .= "Contract Code: " . $auditor_item->contract . " :: Group Name: " . $group_item->entity_name . "  <br>";
                }
            } else {
                if (Functions::get_enviroment() == "production") {
                    $body_error = "<strong> Error in contract payments </strong> <br>";
                    $flag_ACH_error = true;
                    
                    foreach ($auditor_list as $auditor_item) {
                        $body_error .= "Contract Code: " . $auditor_item->contract . " :: Group Name: " . $group_item->entity_name . "   <br>";
                    }

                    $body_error .= "<br>";
                    $body_error .= "Transaction ID: " . $pay_ACH->retref . "<br>";
                    $body_error .= "Error Message: " . $pay_ACH->resptext . "  <br>";
                    $body_error .= "<br><br><br>";
                    require('../integration/integration_sendgrid.php');
                    /** @var \SendGrid\Mail\Mail $email_sendgrid */
                    /** @var \SendGrid $sendgrid */
                    if (isset($email_sendgrid) && isset($sendgrid)) {
                        $email_sendgrid->addTo($group_item->email, "");
                        $email_sendgrid->addTo("support@server.com", "");
                        $email_sendgrid->setSubject("Error in contract payment");
                        $email_sendgrid->addContent("text/html", $body_error);
                        try {
                            $sendgrid_response = $sendgrid->send($email_sendgrid);
                        } catch (\Exception | \Throwable $e) {
                            // silent
                        }
                        //var_dump($sendgrid_response);
                    }
                }
            }
        }
    }
}

http_response_code(200);
return false;
?>