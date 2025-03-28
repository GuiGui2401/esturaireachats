<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AfrikPayController extends Controller
{

    public function collect_payement(Request $request)
    {
        // Recuperation des donnees du fichier .env pour le test de payement

        $provider = env('AFRIKPAY_PROVIDER');
        $reference = env('AFRIKPAY_REFERENCE');
        $amount = env('AFRIKPAY_AMOUNT');
        $secret = env('AFRIKPAY_SECRET');
        $purchaseref = env('AFRIKPAY_PURCHASEREF');
        $store = env('AFRIKPAY_STORE');
        $code = env('AFRIKPAY_CODE');

        $hash = md5($store . $provider . $reference . $amount . $purchaseref . $code . $secret);

        //Requetes cURL vers l'API de paiement
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://sandbox.api.afrikpay.com/api/ecommerce/collect/v3/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MDE3MDMzMTMsImV4cCI6MTcwMTcwNjkxMywicm9sZXMiOiJST0xFX0RFViIsInVzZXJuYW1lIjoidGVzdCJ9.QKO6f06jdrnhIBiu2sETK48Tl13bs6hzgJE4MMVm2Sp9g8sD8PpnPwTAHqK5DyLGMUI7j6yu-ngvxp0-JJMn8OIsKc2rRufSlZkjYwRhNEjk_aO1f8vdWMBcDznRI9j3wOFkw1t_qLyffJ0XKMzAcOpKutCrFcdvNoCkB7qR9gSFgsB9lr27hiwu650m0wHpgdPWz09OX7PhGRNSIsRldbOP5Jr5CshZg6032nuBmYqBcZ0857ZwCTs1o0tNwxE7NKLASbctF7MI9SMj6pAk7UeKa4wZsVuC8zOenjR0Nr8pI9OOJW-qAOarG2pjZEjr9c4H2usriv2vOt5K52efcg',
            'Content-Type: application/json'
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
            "notifurl" => "https://webhook.site/6ce2d6e8-1667-4adc-a78d-e9d2684c3d6e",
            "accepturl" => "",
            "cancelurl" => "",
            "declineurl" => ""
        ]));
        $response = curl_exec($ch);
        curl_close($ch);

        return response()->json(['message' => 'Payment collected successfully']);
    }
    public function payout(Request $request)
    {

        // Recuperation des donnees du fichier .env pour le test de payement

        $provider = env('AFRIKPAY_PROVIDER');
        $reference = env('AFRIKPAY_REFERENCE');
        $amount = env('AFRIKPAY_AMOUNT');
        $secret = env('AFRIKPAY_SECRET');
        $purchaseref = env('AFRIKPAY_PURCHASEREF');
        $store = env('AFRIKPAY_STORE');
        $password = env('AFRIKPAY_CODE');

        $hash = md5($store . $provider . $reference . $amount . $purchaseref . $password . $secret);

        //Requetes cURL vers l'API de paiement
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://sandbox.api.afrikpay.com/api/ecommerce/payout/v3/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MDE3MDQ2MTUsImV4cCI6MTcwMTcwODIxNSwicm9sZXMiOiJST0xFX0RFViIsInVzZXJuYW1lIjoidGVzdCJ9.WoKsbOiXmxf3fpBsqhHpaEExhXlLaUhkeBcCfJmDnlI719J_nA8op91M0ho1gUesKUMMB-iUlMf03_lklNZQC3zEtftiA3DvrDXcbw6gubPK9SDyXAVUh8VStuPN7Rm0BoqaIA3QPo8KnSh1R9oi_ffZnI8eA9DNo-m25ZNY8j8VLUGud_jgsLIVroionmuRBtcxg76dhXgfdye1HloLjTR_hKX437-XNQR4o8nkEDDHevW2wQ2svJ5J4etHR95TE0bvY5c5hV5aPhzPgp-6sz0MhEgKUUhrcUOpyaHMWNCnOCj99HwSNMcn3QXvOP83WA9xuU5S-8Gij9W_3yZUbw',
            'Content-Type: application/json'
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "provider" => $provider,
            "reference" => $reference,
            "amount" => $amount,
            "purchaseref" => $purchaseref,
            "store" => $store,
            "hash" => $hash,
            "password" => $password,
            "description" => "",
            "notifurl" => "https://webhook.site/6ce2d6e8-1667-4adc-a78d-e9d2684c3d6e",
            "accepturl" => "",
            "cancelurl" => "",
            "declineurl" => ""
        ]));
        $response = curl_exec($ch);
        curl_close($ch);

        return response()->json(['message' => 'Payment collected successfully']);
    }
    public function check_status(Request $request)
    {
        // Recuperation des donnees du fichier .env pour le test de payement

        $purchaseref = env('AFRIKPAY_PURCHASEREF');
        $secret = env('AFRIKPAY_SECRET');

        $hash = md5($purchaseref . $secret);

        //Requetes cURL vers l'API de paiement
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://34.86.5.170:8086/api/ecommerce/status/v3/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MDE3MDUxMTEsImV4cCI6MTcwMTcwODcxMSwicm9sZXMiOiJST0xFX0RFViIsInVzZXJuYW1lIjoidGVzdCJ9.gvkTJEQadc0ctTbAQ11Msh2Sxz6Ve4uehMQP153nROuzCzJL43MctoyJa2y49Dq6iLiflL_rTRWYu3U3kSfm7FMMewABUuI6s8KL30nYfKEkL4wOFEgfvL6YWAFobnRFXIkkJR9VsFZ9JfPfJMlfwxjCKvoO8y83WPhMENcmW4g8S3NPmb6VX8jXFu32vbEQwhK_bQvFm-a6KiaaDMP5I5jVKwhg5lCL7DNigKj56GsYKdlbSlQ6k-6VhGSs0Bt8ELWilyoCIQEL8zUl6LgRpHnRc0o8qq3w0urlqOneJgX6CNpZnaTCL5dEwG7zhK9JgnMwpbjV3ASc0gPvxARvlQ',
            'Content-Type: application/json'
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "purchaseref" => $purchaseref,
            "store" => "AFC9160",
            "hash" => $hash,
        ]));
        $response = curl_exec($ch);
        curl_close($ch);

        return response()->json(['message' => 'Payment collected successfully']);
    }


    public function pay(Request $request)
    {
        // Récupérer les données du formulaire
        dd($request);
        $provider = $request->provider;
        $reference = $request->reference;
        $amount = $request->amount;
        $store = env('AFRIKPAY_STORE');
        $code = '';
        $purchaseref = '';
        $secret = env('AFRIKPAY_SECRET ');
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
            return redirect()->back()->with('success', 'Paiement effectué avec succès!');
        } else {
            return redirect()->back()->with('error', 'Erreur lors du paiement. Veuillez réessayer.');
        }
    }
}
