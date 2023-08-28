<?php
namespace gtc_core;



if (isset($_GET['document'])) {
    session_name("dental_care");
    $id_user = 0;
    if (!isset($_SESSION)) {
        session_start();
    }
    if (isset($_SESSION["id_user"])) {
        $id_user = $_SESSION["id_user"];
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

    Functions::security_access_log($id_user, basename(__FILE__), ' API Call', 0);

    $retriveData = new \stdClass();
    date_default_timezone_set("America/New_York");


    $retriveData->CoreAction = $_GET['document'];


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
        default_document();
    }

    $coreAction = $retriveData->CoreAction;

    switch ($coreAction) {
        case 'non_competition_and_confidentiality_agreement':
            non_competition_and_confidentiality_agreement($retriveData, $id_user);
            break;
        case 'compensation_schedule':
            compensation_schedule($retriveData, $id_user);
            break;
        case 'software_license_agreement':
            software_license_agreement($retriveData, $id_user);
            break;
        default:
            default_document();
    }
}


function non_competition_and_confidentiality_agreement($retriveData, $id_user, $internal_call = false)
{
    Functions::security_access_log(0, basename(__FILE__) . ' ' . str_replace('\\', '.', __FUNCTION__), ' executed', 0);
    try {
        $non_competition_and_confidentiality_agreement = new classnon_competition_and_confidentiality_agreement();
        $non_competition_and_confidentiality_agreement = $non_competition_and_confidentiality_agreement->selectAll();
        if ($non_competition_and_confidentiality_agreement) {
            $non_competition_and_confidentiality_agreement = Functions::sort_items_by($non_competition_and_confidentiality_agreement, '_date_umo', 'DESC');
            $non_competition_and_confidentiality_agreement = $non_competition_and_confidentiality_agreement ? $non_competition_and_confidentiality_agreement[0] : null;
        }

        if ($non_competition_and_confidentiality_agreement) {
            $principal_legal_document = $non_competition_and_confidentiality_agreement->document_content;
            $group = new classgroups();
            $group = $group->selectBy('linked_user', $id_user);
            $group = $group ? $group[0] : null;
            if ($group) {
                $principal_legal_document = urldecode(base64_decode($principal_legal_document));
                $principal_legal_document = str_replace('[medicalfacility]', $group->entity_name, $principal_legal_document);
            }
        } else {
            $principal_legal_document = "";
        }

        if ($internal_call == true) {
            return $principal_legal_document;
        }

        http_response_code(200);
        echo $principal_legal_document;
    } catch (\Throwable | \Exception $e) {
        Functions::security_access_log($id_user, basename(__FILE__) . ' ' . str_replace('\\', '.', __FUNCTION__) . ' exception', $e->getMessage(), 0);
        http_response_code(500);
        if ($_SERVER['SERVER_NAME'] == 'localhost') {
            $exception_file = explode('\\', $e->getFile());
            $exception_file = end($exception_file);
            $exception_msg = $e->getMessage() . ' in ' . $exception_file . ':' . $e->getLine();
            $exception_msg = str_replace('"', "'", $exception_msg);
            echo 'document not available:' . $exception_msg;
        } else {
            echo 'document not available';
        }
    }

    return false;

}

function compensation_schedule($retriveData, $id_user, $internal_call = false)
{
    Functions::security_access_log(0, basename(__FILE__) . ' ' . str_replace('\\', '.', __FUNCTION__), ' executed', 0);
    try {
        $compensation_schedule = new classcompensation_schedule();
        $compensation_schedule = $compensation_schedule->selectAll();
        if ($compensation_schedule) {
            $compensation_schedule = Functions::sort_items_by($compensation_schedule, '_date_umo', 'DESC');
            $compensation_schedule = $compensation_schedule ? $compensation_schedule[0] : null;
        }

        if ($compensation_schedule) {
            $principal_legal_document = $compensation_schedule->document_content;
            $group = new classgroups();
            $group = $group->selectBy('linked_user', $id_user);
            $group = $group ? $group[0] : null;
            if ($group) {
                $principal_legal_document = urldecode(base64_decode($principal_legal_document));
                $principal_legal_document = str_replace('[medicalfacility]', $group->entity_name, $principal_legal_document);
            }
        } else {
            $principal_legal_document = "";
        }

        if ($internal_call == true) {
            return $principal_legal_document;
        }

        http_response_code(200);
        echo $principal_legal_document;
    } catch (\Throwable | \Exception $e) {
        Functions::security_access_log($id_user, basename(__FILE__) . ' ' . str_replace('\\', '.', __FUNCTION__) . ' exception', $e->getMessage(), 0);
        http_response_code(500);
        if ($_SERVER['SERVER_NAME'] == 'localhost') {
            $exception_file = explode('\\', $e->getFile());
            $exception_file = end($exception_file);
            $exception_msg = $e->getMessage() . ' in ' . $exception_file . ':' . $e->getLine();
            $exception_msg = str_replace('"', "'", $exception_msg);
            echo 'document not available:' . $exception_msg;
        } else {
            echo 'document not available';
        }
    }

    return false;

}



function software_license_agreement($retriveData, $id_user, $internal_call = false)
{
    Functions::security_access_log(0, basename(__FILE__) . ' ' . str_replace('\\', '.', __FUNCTION__), ' executed', 0);
    try {
        $software_license_agreement = new classsoftware_license_agreement();
        $software_license_agreement = $software_license_agreement->selectAll();
        if ($software_license_agreement) {
            $software_license_agreement = Functions::sort_items_by($software_license_agreement, '_date_umo', 'DESC');
            $software_license_agreement = $software_license_agreement ? $software_license_agreement[0] : null;
        }

        if ($software_license_agreement) {
            $principal_legal_document = $software_license_agreement->document_content;
            $group = new classgroups();
            $group = $group->selectBy('linked_user', $id_user);
            $group = $group ? $group[0] : null;
            if ($group) {
                $principal_legal_document = urldecode(base64_decode($principal_legal_document));
                $principal_legal_document = str_replace('[medicalfacility]', $group->entity_name, $principal_legal_document);
            }
        } else {
            $principal_legal_document = "";
        }

        if ($internal_call == true) {
            return $principal_legal_document;
        }

        http_response_code(200);
        echo $principal_legal_document;
    } catch (\Throwable | \Exception $e) {
        Functions::security_access_log($id_user, basename(__FILE__) . ' ' . str_replace('\\', '.', __FUNCTION__) . ' exception', $e->getMessage(), 0);
        http_response_code(500);
        if ($_SERVER['SERVER_NAME'] == 'localhost') {
            $exception_file = explode('\\', $e->getFile());
            $exception_file = end($exception_file);
            $exception_msg = $e->getMessage() . ' in ' . $exception_file . ':' . $e->getLine();
            $exception_msg = str_replace('"', "'", $exception_msg);
            echo 'document not available:' . $exception_msg;
        } else {
            echo 'document not available';
        }
    }

    return false;

}

function default_document()
{
    $principal_legal_document = 'Tap the links below and read them carfully. By checking the boxes, you acknowledge that you have read and agree to the following terms:';
    http_response_code(200);
    echo $principal_legal_document;
    return false;
}

?>