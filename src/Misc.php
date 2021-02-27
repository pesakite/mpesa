<?php

namespace Pesakite\Mpesa;

class Misc
{
    /**
     * @param $phone
     * @return mixed|string|string[]|null
     */
    public static function formatPhoneNumber($phone)
    {
        $phone = (substr($phone, 0, 1) == "+") ? str_replace("+", "", $phone) : $phone;
        $phone = (substr($phone, 0, 1) == "0") ? preg_replace("/^0/", "254", $phone) : $phone;
        $phone = (substr($phone, 0, 1) == "7") ? "254{$phone}" : $phone;

        return $phone;
    }
}
