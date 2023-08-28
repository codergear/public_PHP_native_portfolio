<?php
namespace gtc_core;

if (isset($_GET['c'])) {
    $contract_code = $_GET['c'];
} else {
    die;
}

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



try {
    require '../integration/integration_dompdf.php';
    /** @var \Dompdf\Dompdf $dompdf */
    if (isset($dompdf)) {


        $pdf_template = file_get_contents('dental_service_agreement_warranty.htm');


        $contract = new classcontracts();
        $contract = $contract->selectBy('contract', Functions::data64UnMask($contract_code));
        $contract = $contract ? $contract[0] : null;

        if ($contract) {
            /** @var classcontracts $contract  */
            $pdf_template = str_replace("patientname", $contract->full_name, $pdf_template);
            $pdf_template = str_replace("contractnumber", $contract->contract, $pdf_template);
            $pdf_template = str_replace("proceduredate", $contract->surgery_date, $pdf_template);

            $transactionDateTime = date_create($contract->_date_umo, timezone_open("America/New_York"));

            $doctor = new classdoctors();
            $doctor = $doctor->selectBy('id_doctors', $contract->doctor);
            $group = new classgroups();
            $group = $group->selectBy('id_groups', $doctor[0]->group_detail)[0];
            $payment_methods = new classpayment_methods();
            $payment_methods = $payment_methods->selectBy('id_payment_methods', $contract->payment_method)[0];
            $plan = new classplans();
            $plan = $plan->selectBy('id_plans', $contract->plan)[0];
            $auditor = new classauditor();
            $auditor = $auditor->selectBy('contract', $contract->contract)[0];
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
            $declaration_page = str_replace("patientaddress2", $contract->city . " " . $state->state_code . " " . $contract->zip_code , $declaration_page);
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
            }else {
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
            $canvas->page_text($w/2, $h - 28, "{PAGE_NUM} of {PAGE_COUNT}", 'helvetica', 8, array(0.565, 0.565, 0.565));
            $canvas->close_object();            
            $dompdf->stream('dental_service_agreement.pdf', array("Attachment" => false));

            //  echo "Document delivered successfully";
            http_response_code(200);
        } else {
            echo '{"message":"Contract not found","status":404,"data":""}';
            http_response_code(404);
        }
    }
} catch (\Throwable | \Exception $e) {

    $exception_msg = $e->getMessage();
    $exception_msg = str_replace('"', "'", $exception_msg);
    //echo '{"message":"error","status":500,"data":"' . $exception_msg . '"}';
    echo '{"message":"API not available","status":500,"data":""}';
    http_response_code(500);
}





?>