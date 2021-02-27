<?php

namespace Pesakite\Mpesa;

class Service
{
    public static object $config;

    public static function init($configs)
    {
        $base = (isset($_SERVER["HTTPS"]) ? "https" : "http") . "://" . (isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : '');
        $defaults = [
            "env" => "sandbox",
            "type" => 4,
            "shortcode" => "603021",
            "headoffice" => "603021",
            "key" => "eHwhb4ywqThDSRwsdsaBRE3Y31uNk6jy",
            "secret" => "tK3RFHKKPlCxFZzi",
            "username" => "apiop37",
            "password" => "Safaricom3021#",
            "passkey" => "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919",
            "validation_url" => $base . "/kite/validate",
            "confirmation_url" => $base . "/kite/confirm",
            "callback_url" => $base . "/kite/reconcile",
            "timeout_url" => $base . "/kite/timeout",
            "results_url" => $base . "/kite/results",
        ];

        if (! empty($configs) && (! isset($configs["headoffice"]) || empty($configs["headoffice"]))) {
            $defaults["headoffice"] = $configs["shortcode"];
        }

        foreach ($defaults as $key => $value) {
            if (isset($configs[$key])) {
                $defaults[$key] = $configs[$key];
            }
        }

        self::$config = (object) $defaults;
    }

    public static function remote_get($endpoint, $credentials = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        return curl_exec($curl);
    }

    public static function remote_post($endpoint, $data = [])
    {
        $token = self::token();
        $curl = curl_init();
        $data_string = json_encode($data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            [
                "Content-Type:application/json",
                "Authorization:Bearer " . $token,
            ]
        );

        return curl_exec($curl);
    }

    public static function token(): string
    {
        $endpoint = (self::$config->env == "live")
            ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"
            : "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";

        $credentials = base64_encode(self::$config->key . ":" . self::$config->secret);
        $response = self::remote_get($endpoint, $credentials);
        $result = json_decode($response);

        return isset($result->access_token) ? $result->access_token : "";
    }
}
