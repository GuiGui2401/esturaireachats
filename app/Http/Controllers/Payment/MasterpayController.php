<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderController;
use App\Models\Cart;
use App\Models\Product;
use App\Utility\AfrikPay;
use Illuminate\Http\Request;

class MasterpayController extends Controller
{
    public function pay(Request $request)
    {
        try {
            $request->validate([
                'reference' => 'required|email',
                'amount' => 'required',
            ]);

            $user = auth()->user();
            $carts = Cart::where('user_id', $user->id)->get();

            // Minumum order amount check
            if (get_setting('minimum_order_amount_check') == 1) {
                $subtotal = 0;
                foreach ($carts as $key => $cartItem) {
                    $product = Product::find($cartItem['product_id']);
                    $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                }
                if ($subtotal < get_setting('minimum_order_amount')) {
                    flash(translate('You order amount is less than the minimum order amount'))->warning();
                    return redirect()->route('home');
                }
            }

            $provider = "master_card";
            $reference = $request->reference;
            $amount = $request->amount;
            $store = env('AFRIKPAY_STORE');
            $code = md5("0000");
            $purchaseref = time();
            $secret = env('AFRIKPAY_SECRET');
            $accountId = env('AFRIKPAY_ACCOUNT_ID');
            $hash = hash_hmac('sha256', $store . $provider . $reference . $amount . $purchaseref . $code, $secret);


            $ch = AfrikPay::cURLCart($accountId, $provider, $reference, $amount, $purchaseref, $store, $hash, $code, $request);

            $response = curl_exec($ch);

            if (
                $response === false
            ) {
                $error = curl_error($ch);
                flash(translate("An error occurred: " . $error))->error();
                session()->put('payment_data', $request->all());
                return redirect()->route('process.payment.master.index');
            } else {
                $data = json_decode($response);
                if (isset($data->message) && $data->message == 'success') {
                    curl_close($ch);

                    $paymentLink = $data->result->paymentlink;
                    return redirect()->away($paymentLink);

                    // (new OrderController)->store($request);

                    // if (count($carts) > 0) {
                    //     Cart::where('user_id', $user->id)->delete();
                    // }

                    // $request->session()->put('payment_type', 'cart_payment');

                    // $data['combined_order_id'] = $request->session()->get('combined_order_id');
                    // $request->session()->put('payment_data', $data);

                    // flash(translate("Your payment has successfully"))->success();
                    // return redirect()->route('order_confirmed');
                } else {
                    dd($data);
                    flash(translate($data->message))->error();
                    session()->put('payment_data', $request->all());
                    return redirect()->route('process.payment.master.index');
                }
            }
        } catch (\Throwable $th) {
            flash($th->getMessage())->error();
            session()->put('payment_data', $request->all());
            return redirect()->route('process.payment.master.index');
        }
    }





    public function index(Request $request)
    {
        $payment_data = session()->get('payment_data');
        $amount = $payment_data['amount'];
        $price = $payment_data['price'];
        $reference = $payment_data['reference'];
        return view('frontend.payment.visa', compact('amount', 'price', 'reference'));
    }
}
