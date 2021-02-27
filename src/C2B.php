<?php

namespace Pesakite\Mpesa;

class C2B extends Service
{
    public static function simulate($phone = null, $amount = 100, $reference = "TRX", $command = "CustomerPayBillOnline", $callback = null)
    {

        $phone = Misc::formatPhoneNumber($phone);
        $phone = (parent::$config->env == "live") ? $phone : "254708374149";

        $endpoint = (parent::$config->env == "live")
            ? "https://api.safaricom.co.ke/mpesa/c2b/v1/simulate"
            : "https://sandbox.safaricom.co.ke/mpesa/c2b/v1/simulate";

        $curl_post_data = [
            "ShortCode" => parent::$config->shortcode,
            "CommandID" => $command,
            "Amount" => round($amount),
            "Msisdn" => $phone,
            "BillRefNumber" => $reference,
        ];

        $response = parent::remote_post($endpoint, $curl_post_data);
        $result = json_decode($response, true);

        return is_null($callback)
            ? $result
            : \call_user_func_array($callback, [$result]);
    }
}
