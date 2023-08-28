<?php
namespace gtc_core;

require_once('../integration/vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {

    $mail_php_mailer = new PHPMailer(true); // Passing `true` enables exceptions
    $mail_php_mailer->SMTPDebug = 0;
    $mail_php_mailer->SMTPAuth = true;
    $mail_php_mailer->CharSet = 'UTF-8';

    /** @var \stdclass $config_mail_php_mailer */
    $config_mail_php_mailer = $GLOBALS["config_mail_php_mailer"];

    $mail_php_mailer->SMTPSecure = $config_mail_php_mailer->SMTPSecure;
    $mail_php_mailer->Port = $config_mail_php_mailer->Port;
    $mail_php_mailer->Host = $config_mail_php_mailer->Host;
    $mail_php_mailer->Username = $config_mail_php_mailer->Username;
    $mail_php_mailer->Password = $config_mail_php_mailer->Password;
    $mail_php_mailer->setFrom($config_mail_php_mailer->Username);
    $mail_php_mailer->isSMTP();

} catch (Exception $e) {
    //silent exception
}


?>