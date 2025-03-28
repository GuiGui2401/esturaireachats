<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EupayController extends Controller
{
    public function pay(Request $request)
    {
        // Récupérer les données du formulaire
        $provider = "express_union_mobilemoney";
        $reference = $request->reference;
        $amount = $request->amount;
        $store = env('AFRIKPAY_STORE');
        $code = '';
        $purchaseref = '';
        $secret = env('AFRIKPAY_SECRET');
        $hash = md5($store . $provider . $reference . $amount . $purchaseref . $code . $secret);

        // Effectuer la requête cURL vers l'API de paiement
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://sandbox.api.afrikpay.com/api/ecommerce/collect/v3/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            "provider" => $provider,
            "reference" => $reference,
            "amount" => $amount,
            "purchaseref" => $purchaseref,
            "store" => $store,
            "hash" => $hash,
            "code" => $code,
            "description" => "",
            "notifurl" => "https://webhook.site/6ce2d6e8-1667-4adc-a78d-e9d2684c3d6e",
            "accepturl" => "",
            "cancelurl" => "",
            "declineurl" => ""
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            flash(translate("Your order has been placed successfully"))->success();
            return redirect()->route('order_confirmed');
        } else {
            dd("Order cancelled");
            flash(translate("Erreur lors du paiement. Veuillez réessayer."))->error();
            return redirect()->back();
        }
    }
}
