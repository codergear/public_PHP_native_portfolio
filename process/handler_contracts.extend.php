<?php
namespace gtc_core;

require "../integration/twilio-php-main/src/Twilio/autoload.php";
use \Twilio\Rest\Client;

if (!isset($coreAction)) {
    echo '{"message":"bad request","status":400,"data":""}';
    http_response_code(400);
    return false;
}

switch ($coreAction) {
    case 'custom_send_terms_conditions':
        custom_send_terms_conditions($retriveData, $id_user);
        break;
    case 'custom_change_filter_doctor':
        custom_change_filter_doctor($retriveData, $id_user);
        break;
    case 'custom_change_contract':
        custom_change_contract($retriveData, $id_user);
        break;
    case 'custom_print_contract':
        custom_print_contract($retriveData, $id_user);
        break;
    case 'custom_pay':
        custom_pay($retriveData, $id_user);
        break;
    case 'custom_complete_contract':
        custom_complete_contract($retriveData, $id_user);
        break;
    case 'custom_cancel_contract':
        custom_cancel_contract($retriveData, $id_user);
        break;
    case 'custom_premium_detail':
        custom_premium_detail($retriveData, $id_user);
        break;
    case 'custom_create_auditor':
        custom_create_auditor($retriveData, $id_user);
        break;
    case 'custom_due_read':
        custom_due_read($retriveData, $id_user);
        break;
    case 'custom_due_count':
        custom_due_count($retriveData, $id_user);
        break;
    case 'custom_send_dentalCare_tickets':
        custom_send_dentalCare_tickets($retriveData, $id_user);
        break;
    case 'custom_terms_conditions':
        custom_terms_conditions($retriveData, $id_user);
        break;
    default:
        custom_bad_request();
}


function custom_send_terms_conditions($retriveData, $id_user)
{


    require_once('../integration/integration_sendgrid.php');
    /** @var \SendGrid\Mail\Mail $email_sendgrid */
    /** @var \SendGrid $sendgrid */

    require_once('../integration/integration_dompdf.php');
    /** @var \Dompdf\Dompdf $dompdf */

    try {
        if (isset($email_sendgrid) && isset($sendgrid) && ($retriveData->email != '')) {

            $email_sendgrid->addTo($retriveData->email);

            if (isset($dompdf)) {

                $pdf_template = file_get_contents('../modules/terms_conditions_template.php');
                $dompdf->loadHtml($pdf_template);
                $dompdf->setPaper('letter', 'portrait');
                $dompdf->render();
                $canvas = $dompdf->getCanvas();
                $canvas->open_object();
                $w = $canvas->get_width();
                $h = $canvas->get_height();
                $canvas->page_text($w - 590, $h - 28, "dentalCare™ 2022", 'helvetica', 8, array(0.565, 0.565, 0.565));
                // $canvas->page_text($w - 90, $h - 28, "AWS DEN 12/6/22", 'helvetica', 8, array(0.565, 0.565, 0.565));
                $canvas->page_text($w / 2, $h - 28, "{PAGE_NUM} of {PAGE_COUNT}", 'helvetica', 8, array(0.565, 0.565, 0.565));
                $canvas->close_object();

                $email_sendgrid->setSubject('dentalCare terms and conditions');
                $email_sendgrid->addContent("text/html", 'Accepted terms and conditions are attached');
                $email_sendgrid->addAttachment($dompdf->output(), "application/pdf", 'terms_conditions.pdf', "attachment");
                try {
                    $sendgrid_response = $sendgrid->send($email_sendgrid);
                    Functions::security_access_log($id_user, 'contract', 'mail terms_conditions successful', $retriveData->email);
                } catch (\Exception | \Throwable $e) {
                    Functions::security_access_log($id_user, 'contract', 'mail terms_conditions error', $retriveData->email);
                }
            }
        }
        echo '{"message":"success","status":200,"data":""}';
        http_response_code(200);
    } catch (\Exception | \Throwable $e) {
        //  silent
    }
}


function custom_change_filter_doctor($retriveData, $id_user)
{
    $doctor_list = [];
    $facility_list = [];
    if ($retriveData->contract != 0) {
        $contracts = new classcontracts;
        $contracts = $contracts->selectBy('id_contracts', $retriveData->contract);
        $contracts = $contracts ? $contracts[0] : null;

        if ($contracts) {
            $doctors = new classdoctors();
            $facility = $doctors->select_facilities_multiselect($contracts->doctor);
            $doctor_group_detail = $doctors->selectBy('id_doctors', $contracts->doctor)[0]->group_detail;
            $doctors = $doctors->selectBy('group_detail', $doctor_group_detail);
            $doctors = Functions::get_items_by($doctors, 'status', '2');

            foreach ($doctors as $item) {
                $doctor_dto = new \stdClass();
                if ($item->id_doctors == $contracts->doctor) {
                    $doctor_dto->_is_selected = true;
                } else {
                    $doctor_dto->_is_selected = false;
                }
                $doctor_dto->id_doctors = $item->id_doctors;
                $doctor_dto->full_name = $item->full_name;
                array_push($doctor_list, $doctor_dto);
            }

            foreach ($facility as $item) {
                if ($item->id_facilities == $contracts->facility) {
                    $item->_is_selected = true;
                } else {
                    $item->_is_selected = false;
                }
            }

            $facility_list = $facility;
        }

    }

    if ($retriveData->contract == 0) {
        // only filter facilities by doctorId
        $doctors = new classdoctors();
        $facility = $doctors->select_facilities_multiselect($retriveData->doctor);
        foreach ($facility as $item) {
            $item->_is_selected = false;
        }

        if ($facility) {
            $facility_list = $facility;
        }
    }

    $result = new \stdClass();
    $result->facility_list = $facility_list;
    $result->doctor_list = $doctor_list;

    echo '{"message":"success","status":200,"data":' . json_encode($result) . '}';
    http_response_code(200);
}
function custom_change_contract($retriveData, $id_user)
{
    try {

        $contracts = new classcontracts;
        $contracts = $contracts->selectBy('id_contracts', $retriveData->contract);
        $contracts = $contracts ? $contracts[0] : null;

        if ($contracts) {
            $contracts->surgery_date = $retriveData->change_surgery_date;
            $contracts->doctor = $retriveData->change_doctor;
            $contracts->facility = $retriveData->change_facility;
            $OPCODE_RESULT = (new classcontracts)->update($contracts, $id_user);
        }

        echo '{"message":"success","status":200,"data":""}';
        http_response_code(200);
    } catch (\Throwable | \Exception $e) {
        $exception_msg = $e->getMessage();
        $exception_msg = str_replace('"', "'", $exception_msg);
        echo '{"message":"error","status":500,"data":"' . $exception_msg . '"}';
        http_response_code(500);
    }
}

function custom_print_contract($retriveData, $id_user)
{
    try {
        require '../integration/integration_dompdf.php';
        /** @var \Dompdf\Dompdf $dompdf */
        if (isset($dompdf)) {



            $pdf_template = file_get_contents('dental_service_agreement_warranty.htm');
            $pdf_template = str_replace("patientname", $retriveData->full_name, $pdf_template);
            $pdf_template = str_replace("contractnumber", $retriveData->contract, $pdf_template);
            $pdf_template = str_replace("proceduredate", $retriveData->procedure_date, $pdf_template);

            $contract = new classcontracts();
            $contract = $contract->selectBy('contract', $retriveData->contract);
            $contract = $contract ? $contract[0] : null;

            /**  @var classcontracts $contract  **/
            if ($contract) {
                $transactionDateTime = date_create($contract->_date_umo, timezone_open("America/New_York"));

                $doctor = new classdoctors();
                $doctor = $doctor->selectBy('id_doctors', $contract->doctor);
                $group = new classgroups();
                $group = $group->selectBy('id_groups', $doctor[0]->group_detail)[0];
                $payment_methods = new classpayment_methods();
                $payment_methods = $payment_methods->selectBy('id_payment_methods', $contract->payment_method)[0];
                /**  @var classplans $plan  **/
                $plan = new classplans();
                $plan = $plan->selectBy('id_plans', $contract->plan)[0];
                $auditor = new classauditor();
                $auditor = $auditor->selectBy('contract', $contract->contract)[0];
                /**  @var classstates $state  **/
                $state = new classstates();
                $state = $state->selectBy('id_states', $contract->state)[0];

                $pdf_receipt = file_get_contents('service_agreement_receipt.htm');
                $pdf_receipt = str_replace("patientname", $contract->full_name, $pdf_receipt);
                $pdf_receipt = str_replace("groupname", $group->entity_name, $pdf_receipt);
                $pdf_receipt = str_replace("paymentmethod", $payment_methods->payment_method, $pdf_receipt);
                $pdf_receipt = str_replace("contractnumber", $contract->contract, $pdf_receipt);

                $pdf_receipt = str_replace("planname", $plan->description, $pdf_receipt);
                $pdf_receipt = str_replace("planprice", $contract->premium, $pdf_receipt);
                $pdf_receipt = str_replace("transactionid", $auditor->transaction, $pdf_receipt);

                $pdf_receipt = str_replace("transactiondate", date_format($transactionDateTime, "m/d/Y"), $pdf_receipt);
                $pdf_receipt = str_replace("transactiontime", date_format($transactionDateTime, "H:i:s"), $pdf_receipt);

                $pdf_template = str_replace("planpremium", $plan->protection, $pdf_template);

                $declaration_page = file_get_contents('declaration_page.htm');
                $declaration_page = str_replace("groupname", $group->entity_name, $declaration_page);
                $declaration_page = str_replace("patientname", $contract->full_name, $declaration_page);
                $declaration_page = str_replace("patientaddress1", $contract->address, $declaration_page);
                $declaration_page = str_replace("patientaddress2", $contract->city . " " . $state->state_code . " " . $contract->zip_code, $declaration_page);
                $declaration_page = str_replace("patientphone", $contract->phone, $declaration_page);
                $declaration_page = str_replace("patientemail", $contract->email, $declaration_page);
                $declaration_page = str_replace("plancoverage", $plan->protection, $declaration_page);
                $declaration_page = str_replace("contractnumber", $contract->contract, $declaration_page);
                $declaration_page = str_replace("premiumcost", $contract->premium, $declaration_page);
                $declaration_page = str_replace("transactiondate", date_format($transactionDateTime, "m/d/Y"), $declaration_page);
                $pdf_template = $declaration_page . $pdf_template;

                $terms_conditions = file_get_contents('terms_conditions.htm');
                $pdf_template = $pdf_template . $terms_conditions;

                if (strtolower($auditor->transaction) != "pending") {
                    $pdf_table_of_content = file_get_contents('table_of_content_full.htm');
                    $pdf_template = $pdf_table_of_content . $pdf_template . $pdf_receipt;
                } else {
                    $pdf_table_of_content = file_get_contents('table_of_content_partial.htm');
                    $pdf_template = $pdf_table_of_content . $pdf_template;
                }



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
        //    echo '{"message":"success","status":200,"data":""}';
        http_response_code(200);
    } catch (\Throwable | \Exception $e) {
        $exception_msg = $e->getMessage();
        $exception_msg = str_replace('"', "'", $exception_msg);
        echo '{"message":"error","status":500,"data":"' . $exception_msg . '"}';
        http_response_code(500);
    }
}

function custom_terms_conditions($retriveData, $id_user)
{
    try {
        $terms_conditions = new classterms_conditions();
        $terms_conditions = $terms_conditions->selectAll();
        $terms_conditions = $terms_conditions ? $terms_conditions[0] : null;

        $result = "";

        if ($terms_conditions) {
            $result = $terms_conditions->terms_conditions_eng;
        }

        echo '{"message":"success","status":200,"data":' . json_encode($result) . '}';
        http_response_code(200);
    } catch (\Throwable | \Exception $e) {
        $exception_msg = $e->getMessage();
        $exception_msg = str_replace('"', "'", $exception_msg);
        echo '{"message":"error","status":500,"data":"' . $exception_msg . '"}';
        http_response_code(500);
    }
}

function custom_send_dentalCare_tickets($retriveData, $id_user)
{
    try {
        $dentalCare_tickets = new classdentalCare_tickets;
        $dentalCare_tickets->tickets_user = $id_user;
        $dentalCare_tickets->tickets_status = "Open";
        $dentalCare_tickets->tickets_date = date("m/d/Y H:i");
        $dentalCare_tickets->patient_name = $retriveData->ticket_patient_name;
        $dentalCare_tickets->contract_code = $retriveData->ticket_contract_code;
        $dentalCare_tickets->notes = $retriveData->notes;
        $OPCODE_RESULT = $dentalCare_tickets->insert($dentalCare_tickets, $id_user);

        require_once('../integration/integration_sendgrid.php');
        /** @var \SendGrid\Mail\Mail $email_sendgrid */
        /** @var \SendGrid $sendgrid */
        if (isset($email_sendgrid) && isset($sendgrid)) {
            try {
                $email_sendgrid->addTo('dentalCare@servername.com');

                $email_sendgrid->setSubject('Contract ' . $retriveData->ticket_contract_code . ' support');
                $body = 'Contract: ' . $retriveData->ticket_contract_code . ' <br>';
                $body .= 'Patient name: ' . $retriveData->ticket_patient_name . ' <br>';
                $body .= 'Notes: ' . $retriveData->notes . ' <br>';
                $email_sendgrid->addContent("text/html", $body);

                $sendgrid_response = $sendgrid->send($email_sendgrid);
                Functions::security_access_log($id_user, 'contract', 'mail dentalCare_tickets successful', $retriveData->ticket_contract_code);
            } catch (\Exception | \Throwable $e) {
                Functions::security_access_log($id_user, 'contract', 'mail dentalCare_tickets error', $retriveData->ticket_contract_code);
            }
        }

        if ($OPCODE_RESULT) {
            $retriveData->id_dentalCare_tickets = $OPCODE_RESULT;
            Functions::security_access_log($id_user, 'dentalCare_tickets', 'create', $retriveData->id_dentalCare_tickets);
            echo '{"message":"Successful","status":200,"data":[]}';
            http_response_code(200);
        } else {
            echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
            http_response_code(500);
        }

    } catch (\Throwable | \Exception $e) {
        $exception_msg = $e->getMessage();
        $exception_msg = str_replace('"', "'", $exception_msg);
        echo '{"message":"error","status":500,"data":"' . $exception_msg . '"}';
        http_response_code(500);
    }
}

function custom_pay($retriveData, $id_user)
{
    try {
        $plans = new classplans();
        $plans = $plans->selectBy('id_plans', $retriveData->plan)[0];

        $doctors = new classdoctors();
        $doctors = $doctors->selectBy('id_doctors', $retriveData->doctor)[0];

        $groups = new classgroups();
        $groups = $groups->selectBy('id_groups', $doctors->group_detail)[0];

        $state = new classstates();
        $state = $state->selectBy('id_states', $retriveData->state)[0];

        $payment_methods = new classpayment_methods();
        $payment_methods = $payment_methods->selectBy('id_payment_methods', $retriveData->payment_method)[0];

        $premium = number_format(floatval(str_replace("$", "", $plans->sales_price) + str_replace("$", "", $groups->group_rate)), 2);
        $pay_result = 'error';
        $responseMessage = 'Error in payment process';
        $ACH_flag = false;

        require_once('../integration/cardpointe/cardpointe.php');
        $cardpointe = new classcardpointe();
        if (strtolower($groups->pay_by) == 'patient') {
            if (strtolower($retriveData->payment_method) == '3') { // ACH    
                $routingNumber = str_replace(' ', '', $retriveData->routing_number); // '122000030'; 
                $accountNumber = str_replace(' ', '', $retriveData->account_number); // '1357902468'; 
                $ACH_flag = true;
                $tokenize = $cardpointe->tokenize("ACH", $routingNumber . "/" . $accountNumber);

            } else { // credit-debit          
                $card_valid_to = explode('/', $retriveData->card_valid_to);
                $card_number = str_replace(' ', '', $retriveData->card_number); //"4111111111111111";
                $card_expiration = $card_valid_to[0] . $card_valid_to[1]; //"1125";
                $cvv = $retriveData->card_cvv; //"123"; 
                $ACH_flag = false;
                $tokenize = $cardpointe->tokenize("CARD", $card_number, $cvv, $card_expiration);
            }
        }
        /** @var \stdclass $response */


        try {
            if (strtolower($groups->pay_by) == 'provider') {
                $response = new \stdclass();
                $response->respcode = "00";
                $response->retref = "pending";
                $response->resptext = "Transaction Accepted";
            }

            if (($ACH_flag == true) && (strtolower($groups->pay_by) == 'patient')) {
                if ($tokenize->errorcode == 0) {
                    $response = $cardpointe->pay_ACH($tokenize->token, $premium, $retriveData->full_name, $retriveData->address, $retriveData->city, $state->state_code, $retriveData->zip_code);
                } else {
                    $response = new \stdclass();
                    $response->respcode = "99";
                    $response->resptext = "Incorrect payment data";
                }
            }

            if (($ACH_flag == false) && (strtolower($groups->pay_by) == 'patient')) {
                if ($tokenize->errorcode == 0) {
                    $response = $cardpointe->pay_CARD($tokenize->token, $cvv, $card_expiration, $premium, $retriveData->full_name, $retriveData->address, $retriveData->city, $state->state_code, $retriveData->zip_code);
                } else {
                    $response = new \stdclass();
                    $response->respcode = "99";
                    $response->resptext = "Incorrect payment data";
                }
            }


            if (Functions::get_enviroment() == "sandbox") {
                $response->respcode = "00";
                $response->retref = $retriveData->contract . "_sandbox";
                $response->resptext = "Transaction Accepted";
            }

            if (($response->respcode == "00") || ($response->respcode == "000")) {
                $pay_result = 'OK';
                $responseMessage = $response->resptext;

                $contracts_payments = new classcontracts_payments;
                $contracts_payments->contract = $retriveData->contract;
                $contracts_payments->transaction_id = $response->retref;
                $contracts_payments->insert($contracts_payments, $id_user);

                require '../integration/integration_dompdf.php';
                /** @var \Dompdf\Dompdf $dompdf */
                if (isset($dompdf)) {
                    $ContractCodeName = $contracts_payments->transaction_id . '_' . $retriveData->contract;
                    $pdf_template = file_get_contents('dental_service_agreement_warranty.htm');
                    $pdf_template = str_replace("patientname", $retriveData->full_name, $pdf_template);
                    $pdf_template = str_replace("contractnumber", $retriveData->contract, $pdf_template);
                    $pdf_template = str_replace("proceduredate", $retriveData->procedure_date, $pdf_template);
                    $pdf_template = str_replace("planpremium", $plans->protection, $pdf_template);

                    $declaration_page = file_get_contents('declaration_page.htm');
                    $declaration_page = str_replace("groupname", $groups->entity_name, $declaration_page);
                    $declaration_page = str_replace("patientname", $retriveData->full_name, $declaration_page);
                    $declaration_page = str_replace("patientaddress1", $retriveData->address, $declaration_page);
                    $declaration_page = str_replace("patientaddress2", $retriveData->city . " " . $state->state_code . " " . $retriveData->zip_code, $declaration_page);
                    $declaration_page = str_replace("patientphone", $retriveData->phone, $declaration_page);
                    $declaration_page = str_replace("patientemail", $retriveData->email, $declaration_page);
                    $declaration_page = str_replace("plancoverage", $plans->protection, $declaration_page);
                    $declaration_page = str_replace("contractnumber", $retriveData->contract, $declaration_page);
                    $declaration_page = str_replace("premiumcost", $premium, $declaration_page);
                    $declaration_page = str_replace("transactiondate", date("m/d/Y"), $declaration_page);
                    $pdf_template = $declaration_page . $pdf_template;

                    $terms_conditions = file_get_contents('terms_conditions.htm');
                    $pdf_template = $pdf_template . $terms_conditions;

                    if (strtolower($groups->pay_by) == 'patient') {
                        $pdf_receipt = file_get_contents('service_agreement_receipt.htm');

                        $pdf_receipt = str_replace("patientname", $retriveData->full_name, $pdf_receipt);
                        $pdf_receipt = str_replace("groupname", $groups->entity_name, $pdf_receipt);
                        $pdf_receipt = str_replace("paymentmethod", $payment_methods->payment_method, $pdf_receipt);
                        $pdf_receipt = str_replace("contractnumber", $retriveData->contract, $pdf_receipt);

                        $pdf_receipt = str_replace("planname", $plans->description, $pdf_receipt);
                        $pdf_receipt = str_replace("planprice", $premium, $pdf_receipt);
                        $pdf_receipt = str_replace("transactionid", $response->retref, $pdf_receipt);

                        $pdf_receipt = str_replace("transactiondate", date("m/d/Y"), $pdf_receipt);
                        $pdf_receipt = str_replace("transactiontime", date("H:i:s"), $pdf_receipt);


                        $pdf_table_of_content = file_get_contents('table_of_content_full.htm');
                        $pdf_template = $pdf_table_of_content . $pdf_template . $pdf_receipt;
                    } else {
                        $pdf_table_of_content = file_get_contents('table_of_content_partial.htm');
                        $pdf_template = $pdf_table_of_content . $pdf_template;
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
                    //file_put_contents('../assets/files/' . $ContractCodeName . '.pdf', $dompdf->output() );

                    if ($retriveData->email != '') {
                        require_once('../integration/integration_sendgrid.php');
                        /** @var \SendGrid\Mail\Mail $email_sendgrid */
                        /** @var \SendGrid $sendgrid */
                        if (isset($email_sendgrid) && isset($sendgrid)) {
                            try {
                                $email_sendgrid->addTo($retriveData->email);
                                $email_sendgrid->setSubject('dentalCare contract created');
                                $email_sendgrid->addContent("text/html", 'Your dentalCare contract is attached:');
                                $email_sendgrid->addAttachment($dompdf->output(), "application/pdf", $ContractCodeName . '.pdf', "attachment");
                                $sendgrid_response = $sendgrid->send($email_sendgrid);
                                Functions::security_access_log($id_user, 'contract', 'mail contract notification successful', $retriveData->contract);
                            } catch (\Exception | \Throwable $e) {
                                Functions::security_access_log($id_user, 'contract', 'mail contract notification error', $retriveData->contract);
                            }
                        }
                    }
                }

                if ($retriveData->phone != '') {
                    try {
                        $sms_link = Functions::url()->url_folder() . "handler_contracts.services.php?c=";
                        $sms_link = $sms_link . Functions::data64Mask($retriveData->contract);
                        $sms_phone = $retriveData->phone;
                        $sms_phone = str_replace(" ", "", $sms_phone);
                        $sms_phone = str_replace("(", "", $sms_phone);
                        $sms_phone = str_replace(")", "", $sms_phone);
                        $sms_phone = str_replace("-", "", $sms_phone);
                        $account_sid = '12345678901234567';
                        $auth_token = 'qwertyuioplkkjhg';
                        $twilio_number = "+1555555555555";
                        $client = new Client($account_sid, $auth_token);
                        $client->messages->create(
                            $sms_phone,
                            array(
                                'from' => $twilio_number,
                                'body' => 'dentalCare dental service agreement: ' . $sms_link
                            )
                        );
                        Functions::security_access_log($id_user, 'contract', 'SMS notification successful', $retriveData->contract);
                    } catch (\Throwable | \Exception $e) {
                        Functions::security_access_log($id_user, 'contract', 'SMS notification error ', $retriveData->contract);

                        // // silent exception
                        // $exception_file = explode('\\', $e->getFile());
                        // $exception_file = end($exception_file);
                        // $exception_msg = $e->getMessage() . ' in ' . $exception_file . ':' . $e->getLine();
                        // $exception_msg = str_replace('"', "'", $exception_msg);
                        // echo '{"message":"error","status":500,"data":"' . $exception_msg . '"}';
                    }
                }
            } else {
                $pay_result = 'error';
                $responseMessage = $response->resptext;
            }

        } catch (\Throwable | \Exception $e) {
            $exception_msg = $e->getMessage();
            $exception_msg = str_replace('"', "'", $exception_msg);
            $responseMessage = $exception_msg;
            $pay_result = 'error';
        }


        echo '{"message":"Successful","status":200,"data":{"pay_result":"' . $pay_result . '","responseMessage":"' . $responseMessage . '"}}';
        http_response_code(200);
    } catch (\Throwable | \Exception $e) {
        if ($_SERVER['SERVER_NAME'] == 'localhost') {
            $exception_file = explode('\\', $e->getFile());
            $exception_file = end($exception_file);
            $exception_msg = $e->getMessage() . ' in ' . $exception_file . ':' . $e->getLine();
            $exception_msg = str_replace('"', "'", $exception_msg);
            echo '{"message":"error","status":500,"data":"' . $exception_msg . '"}';
        } else {
            echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
        }
        http_response_code(500);
    } finally {
        return false;
    }
}

function custom_due_count($retriveData, $id_user)
{
    try {
        $contracts = new classcontracts();
        $contracts_list = $contracts->selectAll();

        if ($contracts_list) {
            $contracts_list = Functions::get_items_by($contracts_list, 'contract_status', 'Active');
            $contracts_list = Functions::filter_items_by_interval($contracts_list, 'surgery_date', date('m/d/Y', 1), date('m/d/Y', strtotime("now")));
        }

        // extend PHP_read
        //  $doctors=Functions::get_items_by($doctors, 'status', '2'); 
        //  $plans=Functions::get_items_by($plans, 'status', '2'); 


        if (in_array($_SESSION['role_name'], ['Group Manager', 'Coordinator'])) {

            $surgical_coordinator_list = (new classsurgical_coordinators())->selectAll();
            $surgical_coordinators_group_list = (new CustomSecFilters())->SurgicalCoordinatorsFilter->surgical_coordinators_group_list($id_user, $_SESSION['role_name'], $surgical_coordinator_list);


            if ($surgical_coordinators_group_list) {

                $auditor_list_full = (new classauditor())->selectAll();
                $auditor_list = [];
                foreach ($surgical_coordinators_group_list->groups_filter_list as $group_item) {
                    $auditor_list = array_merge($auditor_list, Functions::get_items_by($auditor_list_full, 'payee_group_id', $group_item->id_groups));
                }

                $contracts_list_full = $contracts_list;
                $contracts_list = [];
                foreach ($auditor_list as $auditor_item) {
                    $contracts_list = array_merge($contracts_list, Functions::get_items_by($contracts_list_full, 'contract', $auditor_item->contract));
                }
            } else {
                $contracts_list = [];
            }

        }

        // extend PHP_read


        $due_count = 0;
        if ($contracts_list) {
            $due_count = count($contracts_list);
        }

        echo '{"message":"Successful","status":200,"data":{"contracts_due":"' . $due_count . '"}}';
        http_response_code(200);
    } catch (\Throwable | \Exception $e) {
        if ($_SERVER['SERVER_NAME'] == 'localhost') {
            $exception_file = explode('\\', $e->getFile());
            $exception_file = end($exception_file);
            $exception_msg = $e->getMessage() . ' in ' . $exception_file . ':' . $e->getLine();
            $exception_msg = str_replace('"', "'", $exception_msg);
            echo '{"message":"error","status":500,"data":"' . $exception_msg . '"}';
        } else {
            echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
        }
        http_response_code(500);
    } finally {
        return false;
    }
}

function custom_due_read($retriveData, $id_user)
{
    try {
        $contracts = new classcontracts();
        if ($retriveData->id_contracts != 0) {
            $contracts_list = $contracts->selectBy('id_contracts', $retriveData->id_contracts);
            $contracts_list = $contracts_list ? $contracts_list[0] : null;
        } else {
            $contracts_list = $contracts->selectAll();
        }
        $states = new classstates();
        $states = $states->selectAll();
        if ($states) {
            $states = Functions::sort_items_by($states, 'state_name', 'ASC');
        }
        ;
        if (($contracts_list) && ($retriveData->id_contracts == 0)) {
            foreach ($contracts_list as $contracts_item) {
                foreach ($states as $item) {
                    if ($item->id_states == $contracts_item->state) {
                        $contracts_item->_state = $item->state_name;
                    }
                }
                if (!isset($contracts_item->_state)) {
                    $contracts_item->_state = '';
                }
            }
        }
        $doctors = new classdoctors();
        $doctors = $doctors->selectAll();
        if (($contracts_list) && ($retriveData->id_contracts == 0)) {
            foreach ($contracts_list as $contracts_item) {
                foreach ($doctors as $item) {
                    if ($item->id_doctors == $contracts_item->doctor) {
                        $contracts_item->_doctor = $item->full_name;
                    }
                }
                if (!isset($contracts_item->_doctor)) {
                    $contracts_item->_doctor = '';
                }
            }
        }
        $facilities = new classfacilities();
        $facilities = $facilities->selectAll();
        if (($contracts_list) && ($retriveData->id_contracts == 0)) {
            foreach ($contracts_list as $contracts_item) {
                foreach ($facilities as $item) {
                    if ($item->id_facilities == $contracts_item->facility) {
                        $contracts_item->_facility = $item->name;
                    }
                }
                if (!isset($contracts_item->_facility)) {
                    $contracts_item->_facility = '';
                }
            }
        }
        $plans = new classplans();
        $plans = $plans->selectAll();
        if (($contracts_list) && ($retriveData->id_contracts == 0)) {
            foreach ($contracts_list as $contracts_item) {
                foreach ($plans as $item) {
                    if ($item->id_plans == $contracts_item->plan) {
                        $contracts_item->_plan = $item->description;
                    }
                }
                if (!isset($contracts_item->_plan)) {
                    $contracts_item->_plan = '';
                }
            }
        }
        $payment_methods = new classpayment_methods();
        $payment_methods = $payment_methods->selectAll();
        if (($contracts_list) && ($retriveData->id_contracts == 0)) {
            foreach ($contracts_list as $contracts_item) {
                foreach ($payment_methods as $item) {
                    if ($item->id_payment_methods == $contracts_item->payment_method) {
                        $contracts_item->_payment_method = $item->payment_method;
                    }
                }
                if (!isset($contracts_item->_payment_method)) {
                    $contracts_item->_payment_method = '';
                }
            }
        }
        // extend PHP_read
        $doctors = Functions::get_items_by($doctors, 'status', '2');
        $plans = Functions::get_items_by($plans, 'status', '2');

        if (($contracts_list) && ($retriveData->id_contracts == 0)) {
            foreach ($contracts_list as $contracts_item) {
                if ((strtotime($contracts_item->surgery_date) < strtotime("now")) && ($contracts_item->contract_status == "Active")) {
                    $contracts_item->contract_status = "Due";
                }
            }
        }

        if (in_array($_SESSION['role_name'], ['Group Manager', 'Coordinator'])) {
            $surgical_coordinator_list = (new classsurgical_coordinators())->selectAll();
            $surgical_coordinators_group_list = (new CustomSecFilters())->SurgicalCoordinatorsFilter->surgical_coordinators_group_list($id_user, $_SESSION['role_name'], $surgical_coordinator_list);

            if (($contracts_list) && ($retriveData->id_contracts == 0)) {
                if ($surgical_coordinators_group_list) {

                    $auditor_list_full = (new classauditor())->selectAll();
                    $auditor_list = [];
                    foreach ($surgical_coordinators_group_list->groups_filter_list as $group_item) {
                        $auditor_list = array_merge($auditor_list, Functions::get_items_by($auditor_list_full, 'payee_group_id', $group_item->id_groups));
                    }

                    $contracts_list_full = $contracts_list;
                    $contracts_list = [];
                    foreach ($auditor_list as $auditor_item) {
                        $contracts_list = array_merge($contracts_list, Functions::get_items_by($contracts_list_full, 'contract', $auditor_item->contract));
                    }

                } else {
                    $contracts_list = [];
                }
            }
        }

        // extend PHP_read
        if (!$contracts_list) {
            $contracts_list = new classcontracts;
        } else {
            if ($retriveData->id_contracts == 0) {
                //   $contracts_list = Functions::filter_items_by_interval($contracts_list, 'surgery_date', date('m/d/Y', 1), date('m/d/Y', strtotime("now")));
                $contracts_list = Functions::get_items_by($contracts_list, 'contract_status', 'Due');
                //   $contracts_list = Functions::filter_items_by_interval($contracts_list, 'contract_date', $retriveData->datepickerCustomFilterBegin, $retriveData->datepickerCustomFilterEnd);
            }
        }
        echo '{"message":"Successful","status":200,"data":{"contracts":' . json_encode($contracts_list) . ',"states":' . json_encode($states) . ',"doctors":' . json_encode($doctors) . ',"facilities":' . json_encode($facilities) . ',"plans":' . json_encode($plans) . ',"payment_methods":' . json_encode($payment_methods) . '}}';
        http_response_code(200);
    } catch (\Throwable | \Exception $e) {
        if ($_SERVER['SERVER_NAME'] == 'localhost') {
            $exception_file = explode('\\', $e->getFile());
            $exception_file = end($exception_file);
            $exception_msg = $e->getMessage() . ' in ' . $exception_file . ':' . $e->getLine();
            $exception_msg = str_replace('"', "'", $exception_msg);
            echo '{"message":"error","status":500,"data":"' . $exception_msg . '"}';
        } else {
            echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
        }
        http_response_code(500);
    } finally {
        return false;
    }
}

function custom_complete_contract($retriveData, $id_user)
{

    $contract = new classcontracts();
    $contract_item = $contract->selectBy('id_contracts', $retriveData->contract)[0];
    $contract_item->is_cancelled = "0";
    $contract_item->contract_status = "Completed";

    $auditor = new classauditor();
    $auditor_item = $auditor->selectBy('contract', $contract_item->contract)[0];
    $auditor_item->is_cancelled = "0";
    $auditor_item->contract_status = "Completed";
    $auditor_item->cancelation_date = date('m/d/Y');

    $contract->update($contract_item, $id_user);
    $auditor->update($auditor_item, $id_user);

    echo '{"message":"Successful","status":200,"data":""}';
    http_response_code(200);
    return false;
}

function custom_cancel_contract($retriveData, $id_user)
{

    $contract = new classcontracts();
    $contract_item = $contract->selectBy('id_contracts', $retriveData->contract)[0];
    $contract_item->is_cancelled = "1";
    $contract_item->contract_status = "Cancelled";

    $auditor = new classauditor();
    $auditor_item = $auditor->selectBy('contract', $contract_item->contract)[0];
    $auditor_item->is_cancelled = "1";
    $auditor_item->contract_status = "Cancelled";
    $auditor_item->cancelation_date = date('m/d/Y');

    $contract->update($contract_item, $id_user);
    $auditor->update($auditor_item, $id_user);

    if ($auditor_item->pay_by == "Provider") {
        $group_refund = new classgroup_refund();
        $group_refund->group_detail = $auditor_item->payee_group_id;
        $group_refund->amount = str_replace("$", "", $auditor_item->premium);
        $group_refund->insert($group_refund, $id_user);
    }

    if ($auditor_item->pay_by == "Patient") {
        $contract_payments = new classcontracts_payments();
        $contract_payments = $contract_payments->selectBy('contract', $auditor_item->contract)[0];
        $refund = $auditor_item->premium;
        $refund = str_replace("$", "", $refund);
        $refund = str_replace(",", "", $refund);
        $refund = str_replace(".", "", $refund);
        $transaction_id = $contract_payments->transaction_id;
        require_once('../integration/cardpointe/cardpointe.php');
        $cardpointe = new classcardpointe();
        try {
            $cardpointe->refund_CARD($transaction_id);
        } catch (\Throwable | \Exception $e) {
            Functions::security_access_log($id_user, "contract by Patient refund", 'cardpointe refund error ' . $transaction_id, 0);
        }


    }

    echo '{"message":"Successful","status":200,"data":""}';
    http_response_code(200);
    return false;
}

function custom_create_auditor($retriveData, $id_user)
{
    try {
        $contract_item = new classcontracts();
        $contract_item = $contract_item->selectBy('id_contracts', $retriveData->contract)[0];

        $contracts_payments = new classcontracts_payments();
        $contracts_payments_list = $contracts_payments->selectBy('contract', $retriveData->contract);
        $transaction_id = $contracts_payments_list[0]->transaction_id;

        $plans = new classplans();
        $plans = $plans->selectBy('id_plans', $retriveData->plan)[0];

        $underwriter = new classunderwriter();
        $underwriter = $underwriter->selectBy('id_underwriter', $plans->underwriter)[0];

        $doctors = new classdoctors();
        $doctors = $doctors->selectBy('id_doctors', $retriveData->doctor)[0];

        $groups = new classgroups();
        $groups = $groups->selectBy('id_groups', $doctors->group_detail)[0];

        $affiliates = new classaffiliates();
        $affiliates = $affiliates->selectBy('id_affiliates', $groups->afiliate)[0];

        $facilities = new classfacilities();
        $facilities = $facilities->selectBy('id_facilities', $retriveData->facility_name)[0];

        $payment_methods = new classpayment_methods();
        $payment_methods = $payment_methods->selectBy('id_payment_methods', $retriveData->payment_method)[0];

        // $surgical_coordinators = new classsurgical_coordinators();
        // $surgical_coordinators = $surgical_coordinators->selectBy('id_surgical_coordinators', $doctors->surgical_coordinators)[0];

        // $surgical_coordinators = new \stdClass();
        // $surgical_coordinators_by_doctors=new classdoctors();
        // $surgical_coordinators_by_doctors = $surgical_coordinators_by_doctors->select_surgical_coordinators_multiselect($doctors->id_doctors);
        // if ($surgical_coordinators_by_doctors){
        //     $surgical_coordinators->full_name = [];
        //     $surgical_coordinators->id_surgical_coordinators = [];
        //      foreach ($surgical_coordinators_by_doctors as $item){  
        //         array_push( $surgical_coordinators->full_name , $item->full_name);
        //         array_push( $surgical_coordinators->id_surgical_coordinators ,$item->id_surgical_coordinators);
        //     }
        //     $surgical_coordinators->full_name = implode(",", $surgical_coordinators->full_name);
        //     $surgical_coordinators->id_surgical_coordinators = implode(",", $surgical_coordinators->id_surgical_coordinators);
        // }

        if ($groups->pay_by == "Patient") {
            $transaction_fee = new classpatient_transaction_fee();
        } else {
            $transaction_fee = new classprovider_transaction_fee();
        }

        $transaction_fee = $transaction_fee->selectAll();
        if ($transaction_fee) {
            $transaction_fee = Functions::sort_items_by($transaction_fee, '_date_umo', 'DESC');
            $transaction_fee = $transaction_fee ? $transaction_fee[0] : null;
        }

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



        $premium = "$" . (str_replace("$", "", $plans->sales_price) + str_replace("$", "", $groups->group_rate));
        $premium = str_replace("$-", "-$", $premium);
        $retriveData->premium = $premium;
        $retriveData->underwriter_cost = $plans->underwriter_cost;
        $retriveData->group_rate = $groups->group_rate;
        $retriveData->dentalCare_fee = $plans->sales_price;

        $auditor = new classauditor;
        if ($groups->pay_by == "Patient") {
            if ($transaction_fee) {
                $auditor->transaction_fee = str_replace("%", "", $transaction_fee->transaction_fee) . " %";
            } else {
                $auditor->transaction_fee = "0 %";
            }
        } else {
            if ($transaction_fee) {
                $auditor->transaction_fee = "$" . str_replace("$", "", $transaction_fee->transaction_fee);
            } else {
                $auditor->transaction_fee = "$0.00";
            }
        }

        $auditor->payee_affiliate_name = $affiliates->full_name;
        $auditor->payee_affiliate_id = $affiliates->id_affiliates;
        $auditor->payee_group_name = $groups->entity_name;
        $auditor->payee_group_id = $groups->id_groups;
        $auditor->payee_underwriter_name = $underwriter->underwriter;
        $auditor->payee_underwriter_id = $underwriter->id_underwriter;

        $auditor->doctor_name = $doctors->full_name;
        $auditor->doctor_id = $doctors->id_doctors;

        $auditor->plan_name = $plans->description;
        $auditor->plan_id = $plans->id_plans;

        $auditor->facility_name = $facilities->name;
        $auditor->facility_id = $facilities->id_facilities;

        $auditor->coordinator_name = $surgical_coordinators->full_name;
        $auditor->coordinator_id = $surgical_coordinators->id_surgical_coordinators;

        $auditor->patient_name = $retriveData->patient_name;
        $auditor->procedure_date = $retriveData->procedure_date;
        $auditor->payment_method = $payment_methods->payment_method;
        $auditor->contract_status = "Active";

        $total_afiliate_commission = 0;
        $vendor_afiliate_level = $affiliates->afiliate_level;
        switch ($vendor_afiliate_level) {
            case 1:
                $auditor->a1_affiliate_name = $affiliates->full_name;
                $auditor->a1_affiliate_id = $affiliates->id_affiliates;
                $auditor->a1_affiliate_amount = $affiliates->commission;
                $auditor->a1_affiliate_is_payed = "0";

                $auditor->a2_affiliate_name = "";
                $auditor->a2_affiliate_id = "0";
                $auditor->a2_affiliate_amount = "$0.00";
                $auditor->a2_affiliate_is_payed = "N/A";

                $auditor->a3_affiliate_name = "";
                $auditor->a3_affiliate_id = "0";
                $auditor->a3_affiliate_amount = "$0.00";
                $auditor->a3_affiliate_is_payed = "N/A";

                $total_afiliate_commission = str_replace("$", "", $affiliates->commission);
                break;
            case 2:
                $affiliates_A1 = new classaffiliates();
                $affiliates_A1 = $affiliates_A1->selectBy('id_affiliates', $affiliates->affiliate_to)[0];

                $auditor->a1_affiliate_name = $affiliates_A1->full_name;
                $auditor->a1_affiliate_id = $affiliates_A1->id_affiliates;
                $auditor->a1_affiliate_amount = "$" . number_format(str_replace("$", "", $affiliates_A1->commission) - str_replace("$", "", $affiliates->commission), 2);
                $auditor->a1_affiliate_is_payed = "0";

                $auditor->a2_affiliate_name = $affiliates->full_name;
                $auditor->a2_affiliate_id = $affiliates->id_affiliates;
                $auditor->a2_affiliate_amount = $affiliates->commission;
                $auditor->a2_affiliate_is_payed = "0";

                $auditor->a3_affiliate_name = "";
                $auditor->a3_affiliate_id = "0";
                $auditor->a3_affiliate_amount = "$0.00";
                $auditor->a3_affiliate_is_payed = "N/A";

                $total_afiliate_commission = str_replace("$", "", $affiliates_A1->commission);
                break;
            case 3:
                $affiliates_A2 = new classaffiliates();
                $affiliates_A2 = $affiliates_A2->selectBy('id_affiliates', $affiliates->affiliate_to)[0];

                $affiliates_A1 = new classaffiliates();
                $affiliates_A1 = $affiliates_A1->selectBy('id_affiliates', $affiliates_A2->affiliate_to)[0];

                $auditor->a1_affiliate_name = $affiliates_A1->full_name;
                $auditor->a1_affiliate_id = $affiliates_A1->id_affiliates;
                $auditor->a1_affiliate_amount = "$" . number_format(str_replace("$", "", $affiliates_A1->commission) - str_replace("$", "", $affiliates_A2->commission), 2);
                $auditor->a1_affiliate_is_payed = "0";

                $auditor->a2_affiliate_name = $affiliates_A2->full_name;
                $auditor->a2_affiliate_id = $affiliates_A2->id_affiliates;
                $auditor->a2_affiliate_amount = "$" . number_format(str_replace("$", "", $affiliates_A2->commission) - str_replace("$", "", $affiliates->commission), 2);
                $auditor->a2_affiliate_is_payed = "0";

                $auditor->a3_affiliate_name = $affiliates->full_name;
                $auditor->a3_affiliate_id = $affiliates->id_affiliates;
                $auditor->a3_affiliate_amount = $affiliates->commission;
                $auditor->a3_affiliate_is_payed = "0";

                $total_afiliate_commission = str_replace("$", "", $affiliates_A1->commission);
                break;
        }

        $auditor->is_cancelled = "0";
        $auditor->is_payed_group = "0";
        $auditor->is_payed_underwriter = "0";
        $auditor->is_payed_affiliate = "0";
        $auditor->transaction = $transaction_id;
        $auditor->contract = $retriveData->contract;
        $auditor->premium = $retriveData->premium;

        if ($groups->pay_by == "Patient") {
            if ($transaction_fee) {
                $group_rate = str_replace("$", "", $groups->group_rate);
                $group_rate = $group_rate - (($transaction_fee->transaction_fee / 100) * str_replace("$", "", $retriveData->premium));
                $group_rate = "$" . number_format($group_rate, 2);
            } else {
                $group_rate = $groups->group_rate;
            }
        } else {
            $group_rate = "$0.00";
        }

        $auditor->group_rate = $group_rate;
        $auditor->dentalCare_fee = $retriveData->dentalCare_fee;
        $auditor->underwriter_cost = $retriveData->underwriter_cost;
        $auditor->affiliate = "$" . $total_afiliate_commission;
        $dentalCare_net = "$" . (str_replace("$", "", $retriveData->dentalCare_fee) - str_replace("$", "", $retriveData->underwriter_cost) - $total_afiliate_commission);
        $dentalCare_net = str_replace("$-", "-$", $dentalCare_net);
        $auditor->dentalCare_net = $dentalCare_net;
        $auditor->transaction_date = date('m/d/Y');

        $auditor->pay_by = $groups->pay_by;
        $auditor->patient_email = $contract_item->email;
        $auditor->patient_phone = $contract_item->phone;
        $auditor->patient_full_address = $contract_item->address . " " . $contract_item->city . " " . $contract_item->state . " " . $contract_item->zip_code;
        $auditor->purchase_date = date('m/d/Y');

        if ($auditor->insert($auditor, $id_user)) {
            echo '{"message":"Successful","status":200,"data":[]}';
            http_response_code(200);
        } else {
            echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
            http_response_code(500);
        }
    } catch (\Throwable | \Exception $e) {
        echo '{"message":"error","status":500,"data":"OPCODE_ERROR"}';
        http_response_code(500);
    } finally {
        return false;
    }
}


function custom_premium_detail($retriveData, $id_user)
{
    $doctors = new classdoctors();
    $facility = $doctors->select_facilities_multiselect($retriveData->doctor);
    $doctors = $doctors->selectBy('id_doctors', $retriveData->doctor)[0];

    $groups = new classgroups();
    $groups = $groups->selectBy('id_groups', $doctors->group_detail)[0];

    $plans = new classplans();

    $plan_multiselect = (new classgroups())->select_plan_multiselect($groups->id_groups);
    if (!$plan_multiselect){
        echo '{"message":"error","status":500,"data":"Group has not assigned plan"}';
        http_response_code(500);
        return false;
    }else{
        $plan_multiselect = Functions::sort_items_by($plan_multiselect, 'id_plans', 'ASC');
    }

    if($retriveData->plan == "0"){
        $retriveData->plan = $plan_multiselect[0]->id_plans;
    } 
    
    $plans = $plans->selectBy('id_plans', $retriveData->plan)[0];

    $result = new \stdClass;
    $result->premium = number_format(floatval(str_replace("$", "", $plans->sales_price) + str_replace("$", "", $groups->group_rate)), 2);
    $result->pay_by = $groups->pay_by;
    $result->routing_number = $groups->routing_number;
    $result->account_number = $groups->account_number;
    $result->facility = $facility;
    $result->plan = $plan_multiselect;
    $result->contract = date("ymd") . rand(0, 99999);

    if (strlen($result->contract) != 11) {
        $result->contract = str_pad($result->contract, 11, "0");
    }

    echo '{"message":"Successful","status":200,"data":' . json_encode($result) . '}';
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