<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $activeMethods = PaymentSetting::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $methods = [];
        
        foreach ($activeMethods as $method) {
            switch ($method->payment_method) {
                case 'manual':
                    $methods[] = [
                        'method' => 'manual',
                        'name' => 'Manual Payment',
                        'description' => 'Pay via QR code, UPI, or bank transfer'
                    ];
                    break;
                case 'cod':
                    $methods[] = [
                        'method' => 'cod',
                        'name' => 'Cash on Delivery',
                        'description' => 'Pay cash when your order is delivered'
                    ];
                    break;
                case 'razorpay':
                    $methods[] = [
                        'method' => 'razorpay',
                        'name' => 'Online Payment',
                        'description' => 'Pay securely using credit card, debit card, UPI, wallets, etc.'
                    ];
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'methods' => $methods
        ]);
    }
}
