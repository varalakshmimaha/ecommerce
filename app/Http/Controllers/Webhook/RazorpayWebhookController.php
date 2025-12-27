<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $webhookSecret = PaymentSetting::getSettings('razorpay')['webhook_secret'] ?? null;
        
        if (!$webhookSecret) {
            Log::error('Razorpay webhook secret not configured');
            return response()->json(['error' => 'Webhook not configured'], 400);
        }

        // Verify webhook signature
        $razorpaySignature = $request->header('X-Razorpay-Signature');
        $payload = $request->getContent();
        
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        
        if (!hash_equals($razorpaySignature, $expectedSignature)) {
            Log::error('Invalid Razorpay webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event = $request->input('event');
        $payloadData = $request->input('payload');

        try {
            switch ($event) {
                case 'payment.captured':
                    $this->handlePaymentCaptured($payloadData['payment']['entity']);
                    break;
                    
                case 'payment.failed':
                    $this->handlePaymentFailed($payloadData['payment']['entity']);
                    break;
                    
                case 'order.paid':
                    $this->handleOrderPaid($payloadData['order']['entity']);
                    break;
                    
                default:
                    Log::info("Unhandled Razorpay webhook event: {$event}");
            }

            return response()->json(['status' => 'success']);
            
        } catch (\Exception $e) {
            Log::error("Error processing Razorpay webhook: {$e->getMessage()}");
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    private function handlePaymentCaptured($payment)
    {
        $orderId = $payment['order_id'];
        $paymentId = $payment['id'];
        
        $order = Order::where('razorpay_order_id', $orderId)->first();
        
        if (!$order) {
            Log::error("Order not found for Razorpay order ID: {$orderId}");
            return;
        }

        $order->update([
            'razorpay_payment_id' => $paymentId,
            'payment_status' => 'paid',
        ]);

        Log::info("Payment captured for order {$order->order_number}");
    }

    private function handlePaymentFailed($payment)
    {
        $orderId = $payment['order_id'];
        $paymentId = $payment['id'];
        
        $order = Order::where('razorpay_order_id', $orderId)->first();
        
        if (!$order) {
            Log::error("Order not found for Razorpay order ID: {$orderId}");
            return;
        }

        $order->update([
            'razorpay_payment_id' => $paymentId,
            'payment_status' => 'failed',
        ]);

        Log::info("Payment failed for order {$order->order_number}");
    }

    private function handleOrderPaid($orderData)
    {
        $orderId = $orderData['id'];
        
        $order = Order::where('razorpay_order_id', $orderId)->first();
        
        if (!$order) {
            Log::error("Order not found for Razorpay order ID: {$orderId}");
            return;
        }

        $order->update([
            'payment_status' => 'paid',
        ]);

        Log::info("Order paid: {$order->order_number}");
    }
}
