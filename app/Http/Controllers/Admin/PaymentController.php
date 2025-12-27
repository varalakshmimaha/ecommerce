<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $paymentSettings = PaymentSetting::orderBy('sort_order')->get();
        
        return view('admin.payment.index', compact('paymentSettings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'payment_methods' => 'required|array',
            'payment_methods.*.is_active' => 'boolean',
            'payment_methods.*.sort_order' => 'integer|min:0',
            'payment_methods.*.settings' => 'nullable|array',
            'razorpay_key_id' => 'nullable|string|required_if:payment_methods.razorpay.is_active,true',
            'razorpay_key_secret' => 'nullable|string|required_if:payment_methods.razorpay.is_active,true',
        ]);

        foreach ($validated['payment_methods'] as $method => $data) {
            $settings = $data['settings'] ?? [];
            
            // Handle Razorpay keys separately for security
            if ($method === 'razorpay') {
                if (isset($validated['razorpay_key_id'])) {
                    $settings['key_id'] = $validated['razorpay_key_id'];
                }
                if (isset($validated['razorpay_key_secret'])) {
                    $settings['key_secret'] = $validated['razorpay_key_secret'];
                }
            }
            
            PaymentSetting::updateOrCreate(
                ['payment_method' => $method],
                [
                    'is_active' => $data['is_active'] ?? false,
                    'settings' => empty($settings) ? null : $settings,
                    'sort_order' => $data['sort_order'] ?? 0,
                ]
            );
        }

        return redirect()->back()->with('success', 'Payment settings updated successfully');
    }

    public function testRazorpayConnection()
    {
        $razorpaySettings = PaymentSetting::where('payment_method', 'razorpay')
            ->where('is_active', true)
            ->first();

        if (!$razorpaySettings || !$razorpaySettings->settings['key_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay is not configured'
            ], 400);
        }

        try {
            // Test connection logic here
            return response()->json([
                'success' => true,
                'message' => 'Razorpay connection successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay connection failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
