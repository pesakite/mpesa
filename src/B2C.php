<?php

namespace Pesakite\Mpesa;

class B2C extends Service
{
    /**
     * Transfer funds from B2C paybill to account holders phone number
     * @param $phone
     * @param int $amount
     * @param string $command
     * @param string $remarks
     * @param string $occassion
     * @param null $callback
     * @return false|mixed
     */
    public static function send($phone, $amount = 10, $command = "BusinessPayment", $remarks = "", $occassion = "", $callback = null)
    {
        $env = parent::$config->env;

        $phone = Misc::formatPhoneNumber($phone);
        $phone = (parent::$config->env == "live") ? $phone : "254708374149";

        $endpoint = ($env == "live")
            ? "https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest"
            : "https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest";

        $plaintext = parent::$config->password;
        $publicKey = file_get_contents(__DIR__ . "/certs/{$env}/cert.cer");

        openssl_public_encrypt($plaintext, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING);
        $password = base64_encode($encrypted);
        $password = ($env == "live") ? $password : "Safaricom568!#";

        $curl_post_data = [
            "InitiatorName" => parent::$config->username,
            "SecurityCredential" => $password,
            "CommandID" => $command,
            "Amount" => round($amount),
            "PartyA" => parent::$config->shortcode,
            "PartyB" => $phone,
            "Remarks" => $remarks,
            "QueueTimeOutURL" => parent::$config->timeout_url,
            "ResultURL" => parent::$config->results_url,
            "Occasion" => $occassion,
        ];

        $response = parent::remote_post($endpoint, $curl_post_data);
        $result = json_decode($response, true);

        return is_null($callback)
            ? $result
            : \call_user_func_array($callback, [$result]);
    }
}
