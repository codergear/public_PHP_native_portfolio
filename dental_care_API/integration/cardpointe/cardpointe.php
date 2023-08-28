<?php
namespace gtc_core;

use stdClass;

class dto_validate_merchant_id
{
    public $site;
    public $merchid;
    public $enabled;
    public $echeck;
    public $cardproc;
    public $fee_type;
    public $cvv;
    public $acctupdater;
    public $avs;

}

class dto_tokenize
{
    public $message;
    public $errorcode;
    public $token;

}

class dto_pay_ACH
{
    public $amount;
    public $resptext; // Success or error type
    public $commcard;
    public $cvvresp;
    public $respcode; // 00 = success or error code
    public $batchid;
    public $avsresp;
    public $merchid;
    public $token;
    public $authcode;
    public $respproc;
    public $bintype;
    public $retref; // transaction ID
    public $respstat;
    public $account;
}

class dto_pay_CARD
{
    public $amount;
    public $resptext; // Approval or error type
    public $commcard;
    public $cvvresp;
    public $respcode; // 000 = success or error code
    public $avsresp;
    public $entrymode;
    public $merchid;
    public $token;
    public $authcode;
    public $respproc;
    public $bintype;
    public $expiry;
    public $retref; // transaction ID
    public $respstat;
    public $account;
}


class classcardpointe
{



    public $status;

    //UAT
    public $merchant_id = '123456789012';
    public $credential = 'key';
    public $url_inquireMerchant = 'https://fts-uat.cardconnect.com/cardconnect/rest/inquireMerchant/';
    public $url_auth = 'https://fts-uat.cardconnect.com/cardconnect/rest/auth';
    public $url_tokenize = 'https://fts-uat.cardconnect.com/cardsecure/api/v1/ccn/tokenize';


    /**
     * @return dto_validate_merchant_id
     */
    function validate_merchant_id()
    {
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $this->url_inquireMerchant . $this->merchant_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . $this->credential,
                    'Content-type: application/json'
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, false);

    }

    /**
     * @return dto_tokenize
     */
    function tokenize($type, $account, $cvv = "", $expiry = "")
    {
        $curl = curl_init();

        $token = new stdClass();
        $token->account = $account;

        if ($type == 'CARD') {
            $expiry = str_replace("/", "", $expiry);
            $token->cvv2 = $cvv;
            $token->expiry = $expiry;
        }

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $this->url_tokenize,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($token),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, false);

    }


    /**
     * @return dto_pay_ACH
     */
    function pay_ACH($account, $amount, $name, $addess, $city, $state, $postal)
    {
        $curl = curl_init();

        $amount = str_replace("$", "", $amount);
        $amount = str_replace(".", "", $amount);
        $amount = str_replace(",", "", $amount);

        $payment = new stdClass();
        $payment->merchid = $this->merchant_id;
        $payment->account = $account;
        $payment->amount = $amount;
        $payment->currency = 'USD';
        $payment->accttype = 'ECHK';
        $payment->ecomind = 'E';
        $payment->name = $name;
        $payment->capture = 'Y';

        $payment->address = $addess;
        $payment->city = $city;
        $payment->region = $state;
        $payment->postal = $postal;
        $payment->country = "US";
        //    $payment->profile = "Y";

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $this->url_auth,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => json_encode($payment),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . $this->credential,
                    'Content-Type: application/json'
                ),
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, false);
    }

    /**
     * @return dto_pay_CARD
     */
    function pay_CARD($account, $card_cvv, $card_expiration, $amount, $name, $addess, $city, $state, $postal)
    {
        $curl = curl_init();

        $amount = str_replace("$", "", $amount);
        $amount = str_replace(".", "", $amount);
        $amount = str_replace(",", "", $amount);

        $payment = new stdClass();

        $payment->merchid = $this->merchant_id;
        $payment->account = $account;
        $payment->cvv2 = $card_cvv;
        $payment->expiry = $card_expiration;
        $payment->amount = $amount;
        $payment->currency = 'USD';
        $payment->ecomind = 'E';
        $payment->name = $name;
        $payment->capture = 'Y';

        $payment->address = $addess;
        $payment->city = $city;
        $payment->region = $state;
        $payment->postal = $postal;
        $payment->country = "US";
        //    $payment->profile = "Y";

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $this->url_auth,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => json_encode($payment),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . $this->credential,
                    'Content-Type: application/json'
                ),
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, false);
    }


    function refund_CARD($transaction_id)
    {
        $curl = curl_init();

        $refund = new stdClass();

        $refund->merchid = $this->merchant_id;
        $refund->retref = $transaction_id;

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $this->url_refund,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => json_encode($refund),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . $this->credential,
                    'Content-Type: application/json'
                ),
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, false);
    }
}



?>