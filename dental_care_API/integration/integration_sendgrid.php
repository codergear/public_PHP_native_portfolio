<?php
namespace gtc_core;

require_once('../integration/vendor/autoload.php');
use SendGrid;
use SendGrid\Mail\Mail;

try {

    $email_sendgrid = new Mail();
    $email_sendgrid->setFrom("noreply@server.net", "dentalCare");
    $sendgrid = new SendGrid('api_key');

} catch (\Exception | \Throwable $e) {
    //silent exception
}


?>