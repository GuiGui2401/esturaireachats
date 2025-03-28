<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessSettingSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run(): void
    {

        $types = [
            'type' => "afrikpay_payment",
            'type' => "eupay_payment",
            'type' => "mtnpay_payment",
            'type' => "orangepay_payment",
            'type' => "afrikpay_sandbox",
            'type' => "eupay_sandbox",
            'type' => "mtnpay_sandbox",
            'type' => "orangepay_sandbox",
            'type' => "visapay_payment",
            'type' => "masterpay_payment",
            'type' => "visapay_sandbox",
            'type' => "masterpay_sandbox",
        ];
        foreach ($types as $type) {
            $exist = BusinessSetting::where('type', $type)->first();
            if (!$exist) {
                BusinessSetting::create([
                    "type" => $type,
                    "value" => 0,
                    "lang" => null
                ]);
            }
        }
    }
}
