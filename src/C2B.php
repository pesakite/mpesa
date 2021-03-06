<?php

namespace Pesakite\Mpesa;

class C2B extends Service
{
    /**
     * @param null $callback
     * @param string $response_type
     * @return false|mixed
     * @psalm-suppress InvalidScalarArgument
     */
    public static function register($callback = null, $response_type = "Completed")
    {
        $endpoint = (parent::$config->env == "live")
            ? "https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl"
            : "https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl";

        $curl_post_data = [
            "ShortCode" => parent::$config->shortcode,
            "ResponseType" => $response_type,
            "ConfirmationURL" => parent::$config->confirmation_url,
            "ValidationURL" => parent::$config->validation_url,
        ];

        $response = parent::remote_post($endpoint, $curl_post_data);
        $result = json_decode($response, true);

        return is_null($callback)
            ? $result
            : \call_user_func_array($callback, [$result]);
    }

    /**
     * @param $phone
     * @param int $amount
     * @param string $reference
     * @param string $description
     * @param string $remark
     * @param null $callback
     * @return false|mixed
     * @psalm-suppress InvalidScalarArgument
     */
    public static function stk($phone, $amount = 100, $reference = "ACCOUNT", $description = "Transaction Description", $remark = "Remark", $callback = null)
    {
        $phone = Misc::formatPhoneNumber($phone);

        $timestamp = date("YmdHis");
        $password = base64_encode(parent::$config->shortcode . parent::$config->passkey . $timestamp);

        $endpoint = (parent::$config->env == "live")
            ? "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest"
            : "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

        $curl_post_data = [
            "BusinessShortCode" => parent::$config->headoffice,
            "Password" => $password,
            "Timestamp" => $timestamp,
            "TransactionType" => "CustomerPayBillOnline",
            "Amount" => round($amount),
            "PartyA" => $phone,
            "PartyB" => parent::$config->headoffice,
            "PhoneNumber" => $phone,
            "CallBackURL" => parent::$config->callback_url,
            "AccountReference" => $reference,
            "TransactionDesc" => $description,
            "Remark" => $remark,
        ];

        $response = parent::remote_post($endpoint, $curl_post_data);
        $result = json_decode($response, true);

        return is_null($callback)
            ? $result
            : \call_user_func_array($callback, [$result]);
    }

    /**
     * @param null $phone
     * @param int $amount
     * @param string $reference
     * @param string $command
     * @param null $callback
     * @return false|mixed
     * @psalm-suppress InvalidScalarArgument
     */
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
