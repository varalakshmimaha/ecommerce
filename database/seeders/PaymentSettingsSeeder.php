<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentSetting;

class PaymentSettingsSeeder extends Seeder
{
    public function run()
    {
        // Create default payment settings
        PaymentSetting::create([
            'payment_method' => 'manual',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        PaymentSetting::create([
            'payment_method' => 'cod',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        PaymentSetting::create([
            'payment_method' => 'razorpay',
            'is_active' => false,
            'sort_order' => 3,
        ]);
    }
}
