<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class WebhookController extends Controller
{

    public function retrieveRequestDetails()
    {
        $client = new Client();
        $url = 'https://webhook.site/5d12ff60-fb9a-44b9-b75f-2f4411a004f1';

        $response = $client->get($url);
        $responseData = $response->getBody()->getContents();

        $jsonData = json_decode($responseData, true);

        dd($jsonData);
        $requestUrl = $jsonData['url'];
        $requestHeaders = $jsonData['headers'];
        $requestBody = $jsonData['body'];

        return response()->json([
            'url' => $requestUrl,
            'headers' => $requestHeaders,
            'body' => $requestBody
        ]);
    }
}
