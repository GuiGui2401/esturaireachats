<?php

namespace App\Utility;

use Symfony\Component\VarDumper\VarDumper;

class AfrikPay
{
    public static function generateToken($username, $password, $accountId)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.afrikpay.com/account/generate/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accountId: ' . $accountId,
            'Content-Type: application/json',
        ]);

        $curl = curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "username" => $username,
            "password" => $password,
        ]));

        var_dump($curl);
        $response = curl_exec($ch);
        curl_close($ch);

        $token = json_decode($response, true)['content']['token'];

        return $token;
    }

    public static function cURL($token, $accountId, $provider, $reference, $amount, $purchaseref, $store, $hash, $code, $request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.afrikpay.com/api/ecommerce/collect/v3/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'accountid: ' . $accountId,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "provider" => $provider,
            "reference" => $reference,
            "amount" => $amount,
            "purchaseref" => $purchaseref,
            "store" => $store,
            "hash" => $hash,
            "code" => $code,
            "description" => "",
            "notifurl" => "https://webhook.site/5d12ff60-fb9a-44b9-b75f-2f4411a004f1",
            "accepturl" => "",
            "cancelurl" => "",
            "declineurl" => ""
        ]));

        return $ch;
    }


    public static function cURLCart($accountId, $provider, $reference, $amount, $purchaseref, $store, $hash, $code, $request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.afrikpay.com/api/ecommerce/collect/v2/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'accountId: ' . $accountId,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "provider" => $provider,
            "reference" => $reference,
            "amount" => $amount,
            "purchaseref" => $purchaseref,
            "store" => $store,
            "hash" => $hash,
            "code" => $code,
            "description" => "",
            "notifurl" => "https://webhook.site/5d12ff60-fb9a-44b9-b75f-2f4411a004f1",
            "accepturl" => "",
            "cancelurl" => "",
            "declineurl" => ""
        ]));

        return $ch;
    }
}
