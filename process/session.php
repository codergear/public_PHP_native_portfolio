<?php
namespace gtc_core;

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

$retriveData = json_decode(file_get_contents('php://input'));
date_default_timezone_set("America/New_York");
header('Content-type:application/json');

echo '{"message":"Successful","status":200,"data":1, "role": "'. $_SESSION['role_name'].'"}';
http_response_code(200);
return false;

?>