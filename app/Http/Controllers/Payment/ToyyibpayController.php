<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToyyibpayController extends Controller
{
    protected $secretKey;
    protected $someOtherConfig; // Add other relevant config

    public function __construct()
    {
        // Retrieve Toyyibpay secret key and other configurations from .env or config file
        $this->secretKey = env('TOYYIBPAY_SECRET_KEY');
        $this->someOtherConfig = config('services.toyyibpay.some_other_config'); // Example
    }

    /**
     * Handles the redirection to Toyyibpay payment gateway.
     * This method should create a bill on Toyyibpay and redirect the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function initiatePayment(Request $request)
    {
        // Validate the payment request (amount, description, etc.)
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'order_id' => 'required|string|unique:orders,order_id', // Example: associate with your order
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            // Add other necessary fields based on Toyyibpay API requirements
        ]);

        $billData = [
            'merchantCode' => env('TOYYIBPAY_MERCHANT_CODE'), // Ensure this is in your .env
            'categoryCode' => env('TOYYIBPAY_CATEGORY_CODE'), // Ensure this is in your .env
            'billName' => $request->input('description'),
            'billDescription' => $request->input('description'),
            'billPriceSetting' => 1, // 0 for fixed price, 1 for open price
            'billAmount' => round($request->input('amount') * 100), // Amount in cents/smallest unit
            'billReturnUrl' => route('toyyibpay-status'), // URL to redirect after payment
            'billCallbackUrl' => route('toyyibpay-callback'), // URL for background callback
            'billExternalReferenceNo' => $request->input('order_id'), // Your order ID
            'billTo' => $request->input('name'),
            'billEmail' => $request->input('email'),
            'billPhone' => $request->input('phone'),
            'billPaymentOption' => 1, // Enable all payment options (adjust as needed)
            // Add other optional parameters as needed based on Toyyibpay API
        ];

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, 'https://www.toyyibpay.com.my/index.php/api/createBill');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $billData);

        $result = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            // Log the error
            \Log::error('Toyyibpay Bill Creation Error: ' . $err);
            // Redirect back with an error message
            return back()->with('error', 'Failed to initiate payment with Toyyibpay. Please try again.');
        }

        $result = json_decode($result, true);

        if ($result && $result['status'] == 1) {
            // Redirect the user to the Toyyibpay payment URL
            return redirect('https://www.toyyibpay.com.my/' . $result['BillCode']);
        } else {
            // Log the error response from Toyyibpay
            \Log::error('Toyyibpay Bill Creation Failed: ' . json_encode($result));
            // Redirect back with an error message
            return back()->with('error', 'Failed to initiate payment with Toyyibpay. ' . ($result['msg'] ?? ''));
        }
    }

    /**
     * Handles the callback from Toyyibpay after payment (both success and failure).
     * This is the URL specified in 'billReturnUrl'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function paymentstatus(Request $request)
    {
        // Retrieve the payment status and transaction details from the request
        $status = $request->input('status'); // 1 for success, 2 for failure, 3 for pending
        $billCode = $request->input('billcode');
        $orderId = $request->input('order_id'); // You might need to configure Toyyibpay to return your external reference

        // Verify the payment status (optional, but recommended for security)
        $verificationData = [
            'billcode' => $billCode,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, 'https://www.toyyibpay.com.my/index.php/api/getBillTransactions');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $verificationData);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $this->secretKey, // Some APIs might require Bearer token
        ]);

        $verificationResult = curl_exec($curl);
        $verificationErr = curl_error($curl);
        curl_close($curl);

        if ($verificationErr) {
            \Log::error('Toyyibpay Transaction Verification Error: ' . $verificationErr);
            // Handle verification error (maybe show a generic status)
            return view('payment.toyyibpay_status', ['status' => 'error', 'message' => 'Error verifying payment status.']);
        }

        $verificationResult = json_decode($verificationResult, true);

        if ($verificationResult && is_array($verificationResult) && !empty($verificationResult)) {
            // Find the relevant transaction based on billCode or other identifiers
            $transaction = $verificationResult[0]; // Assuming the first transaction is the relevant one

            if ($transaction['TransactionStatus'] == '1') {
                // Payment successful
                // Update your order status in the database
                \Log::info('Toyyibpay Payment Successful: ' . json_encode($transaction));
                // Redirect the user to a success page
                return view('payment.toyyibpay_status', ['status' => 'success', 'message' => 'Payment successful!', 'transaction' => $transaction]);
            } elseif ($transaction['TransactionStatus'] == '2') {
                // Payment failed
                // Update your order status in the database
                \Log::warning('Toyyibpay Payment Failed: ' . json_encode($transaction));
                // Redirect the user to a failure page
                return view('payment.toyyibpay_status', ['status' => 'failed', 'message' => 'Payment failed.', 'transaction' => $transaction]);
            } elseif ($transaction['TransactionStatus'] == '0') {
                // Payment pending
                // Update your order status accordingly
                \Log::info('Toyyibpay Payment Pending: ' . json_encode($transaction));
                // Redirect the user to a pending page
                return view('payment.toyyibpay_status', ['status' => 'pending', 'message' => 'Payment is pending.', 'transaction' => $transaction]);
            } else {
                // Unknown status
                \Log::warning('Toyyibpay Unknown Payment Status: ' . json_encode($transaction));
                return view('payment.toyyibpay_status', ['status' => 'unknown', 'message' => 'Unknown payment status.', 'transaction' => $transaction]);
            }
        } else {
            // Could not verify transaction details
            \Log::warning('Toyyibpay Could Not Verify Transaction: ' . json_encode($verificationResult));
            return view('payment.toyyibpay_status', ['status' => 'error', 'message' => 'Could not retrieve payment details.']);
        }
    }

    /**
     * Handles the background callback from Toyyibpay.
     * This is the URL specified in 'billCallbackUrl'.
     * It's crucial for updating order status reliably.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function callback(Request $request)
    {
        // Log the entire callback request for debugging
        \Log::info('Toyyibpay Callback Received: ' . json_encode($request->all()));

        $billCode = $request->input('billcode');
        $transactionId = $request->input('transaction_id');
        $orderId = $request->input('order_id'); // Ensure Toyyibpay sends this
        $status = $request->input('status'); // 1 for success

        // It's crucial to verify the callback data with Toyyibpay's API
        // to prevent fraudulent updates.

        $verificationData = [
            'billcode' => $billCode,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, 'https://www.toyyibpay.com.my/index.php/api/getBillTransactions');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $verificationData);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $this->secretKey, // Adjust if needed
        ]);

        $verificationResult = curl_exec($curl);
        $verificationErr = curl_error($curl);
        curl_close($curl);

        if ($verificationErr) {
            \Log::error('Toyyibpay Callback Verification Error: ' . $verificationErr);
            return response('Callback verification failed.', 500);
        }

        $verificationResult = json_decode($verificationResult, true);

        if ($verificationResult && is_array($verificationResult) && !empty($verificationResult)) {
            $transaction = $verificationResult[0]; // Assuming the latest transaction is relevant

            if ($transaction['TransactionStatus'] == '1') {
                // Payment successful (confirmed via API)
                // Update your order status in the database based on $orderId
                // Mark the payment as completed
                \Log::info('Toyyibpay Callback Successful: ' . json_encode($transaction));
                // Respond to Toyyibpay to acknowledge receipt (usually a 200 OK)
                return response('Payment successful.', 200);
            } else {
                // Payment not successful (or still pending)
                // Update your order status accordingly
                \Log::warning('Toyyibpay Callback Not Successful: ' . json_encode($transaction));
                // Respond to Toyyibpay to acknowledge receipt
                return response('Payment not successful.', 200);
            }
        } else {
            // Could not verify callback data
            \Log::warning('Toyyibpay Callback Verification Failed: ' . json_encode($verificationResult));
            return response('Callback verification failed.', 400);
        }
    }
}