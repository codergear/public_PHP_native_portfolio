<?php

namespace gtc_core;


$config_mail_php_mailer = new \stdClass;


$config_mail_php_mailer->SMTPSecure = 'tls'; // Enable tls encryption, `ssl` also accepted
$config_mail_php_mailer->Port = 587;
$config_mail_php_mailer->Host = 'servers'; // Specify main and backup servers
$config_mail_php_mailer->Username = 'username'; //  username
$config_mail_php_mailer->Password = 'password'; //  password 









?>