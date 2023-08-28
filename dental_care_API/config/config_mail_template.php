<?php

namespace gtc_core;

class ConfigMailTemplate
{
    public static function new_account_template($user_login, $user_password)
    {
        $email_message = "<p>Welcome to dentalCare! <br><br>As a trusted partner</p>";
        $email_message = str_replace('[user_login]', $user_login, $email_message);
        $email_message = str_replace('[user_password]', $user_password, $email_message);
        return $email_message;
    }
}



?>