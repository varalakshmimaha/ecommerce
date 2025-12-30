<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserCredentials;
use Illuminate\Support\Facades\Hash;
use Razorpay\Api\Api;

class CheckoutController extends Controller
{
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'nullable|exists:addresses,id',
            'name' => 'required_without:address_id|string|max:255',
            'mobile' => 'required_without:address_id|string|max:15',
            'email' => 'nullable|email|max:255',
            'address' => 'required_without:address_id|string',
            'pincode' => 'required_without:address_id|string|max:10',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.attributes' => 'nullable|array',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'payment_method' => 'required|in:manual,cod,razorpay',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Additional validation for manual payment
        if ($request->payment_method === 'manual' && !$request->hasFile('payment_proof')) {
            return response()->json([
                'error' => 'Payment proof is required for manual payment'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $gstAmount = 0;
            $items = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock_quantity < $item['quantity']) {
                    return response()->json([
                        'error' => "Insufficient stock for {$product->name}"
                    ], 400);
                }

                $price = $product->final_price;
                
                // Apply attribute-based pricing if any
                if (!empty($item['attributes'])) {
                    foreach ($item['attributes'] as $attr) {
                        $attribute = $product->attributes()
                            ->where('attribute_name', $attr['name'])
                            ->where('attribute_value', $attr['value'])
                            ->first();
                        
                        if ($attribute) {
                            $price += $attribute->price_adjustment;
                        }
                    }
                }

                $itemSubtotal = $price * $item['quantity'];
                $itemGst = ($itemSubtotal * $product->gst) / 100;
                
                $subtotal += $itemSubtotal;
                $gstAmount += $itemGst;

                $items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $itemSubtotal,
                    'attributes' => $item['attributes'] ?? null,
                ];
            }

            $shippingCharge = (float) Setting::get('shipping_charge', 0);
            $totalAmount = $subtotal + $gstAmount + $shippingCharge;

            // Handle payment proof upload
            $paymentProofPath = null;
            if ($request->hasFile('payment_proof')) {
                $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
            }

            // Auto-create or attach user when guest provides email
            $userId = $request->user()->id;
            $newUserPassword = null;
            $isNewUser = false;

            if (!$userId) {
                $existing = User::where('mobile', $request->mobile)->first();
                if ($existing) {
                    $userId = $existing->id;
                } else {
                    // Generate random password for new user
                    $newUserPassword = Str::random(12);
                    $newUser = User::create([
                        'name' => $request->name,
                        'mobile' => $request->mobile,
                        'email' => $request->email,
                        'password' => Hash::make(12345678),
                        'is_verified' => true,
                    ]);
                    $userId = $newUser->id;
                    $isNewUser = true;
                }
            }

            // Create order
            $address = null;
            if ($request->address_id) {
                $address = \App\Models\Address::find($request->address_id);
            }
            if (!$address && $request->address) {
                $createAddress = \App\Models\Address::create([
                    'user_id' => $userId,
                    'name' => $request->name,
                    'phone' => $request->mobile,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'pincode' => $request->pincode,
                    'country' => $request->country
                ]);
                $address = $createAddress;
            }
            $order = Order::create([
                'user_id' => $userId,
                'address_id' => $address ? $address->id : null,
                'name' => $address ? $address->name : $request->name,
                'mobile' => $address ? $address->phone : $request->mobile,
                'email' => $request->email,
                'address' => $address ? $address->address . ', ' . $address->city . ', ' . $address->state . ' - ' . $address->pincode . ', ' . $address->country : $request->address,
                'pincode' => $address ? $address->pincode : $request->pincode,
                'subtotal' => $subtotal,
                'gst_amount' => $gstAmount,
                'shipping_charge' => $shippingCharge,
                'total_amount' => $totalAmount,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_proof' => $paymentProofPath,
            ]);

            // Create order items
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                    'attributes' => $item['attributes'],
                ]);

                // Update stock
                $item['product']->decrement('stock_quantity', $item['quantity']);
            }

            DB::commit();

            // Send email to new user with credentials
            if ($isNewUser && $newUserPassword && $request->email) {
                try {
                    Mail::to($request->email)->send(
                        new NewUserCredentials(
                            $request->name,
                            $request->email,
                            $newUserPassword,
                            $order->order_number
                        )
                    );
                } catch (\Exception $e) {
                    // Log email error but don't fail the order
                    \Log::error('Failed to send new user credentials email: ' . $e->getMessage());
                }
            }

            $order->load('items.product');

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function calculateTotal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.attributes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subtotal = 0;
        $gstAmount = 0;
        $items = [];

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $price = $product->final_price;
            
            if (!empty($item['attributes'])) {
                foreach ($item['attributes'] as $attr) {
                    $attribute = $product->attributes()
                        ->where('attribute_name', $attr['name'])
                        ->where('attribute_value', $attr['value'])
                        ->first();
                    
                    if ($attribute) {
                        $price += $attribute->price_adjustment;
                    }
                }
            }

            $itemSubtotal = $price * $item['quantity'];
            $itemGst = ($itemSubtotal * $product->gst) / 100;
            
            $subtotal += $itemSubtotal;
            $gstAmount += $itemGst;

            $items[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $item['quantity'],
                'price' => $price,
                'subtotal' => $itemSubtotal,
            ];
        }

        $shippingCharge = (float) Setting::get('shipping_charge', 0);
        $totalAmount = $subtotal + $gstAmount + $shippingCharge;

        return response()->json([
            'subtotal' => $subtotal,
            'gst_amount' => $gstAmount,
            'shipping_charge' => $shippingCharge,
            'total_amount' => $totalAmount,
            'items' => $items,
        ]);
    }

    public function createRazorpayOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.attributes' => 'nullable|array',
            'address_id' => 'nullable|exists:addresses,id',
            'name' => 'required_without:address_id|string|max:255',
            'mobile' => 'required_without:address_id|string|max:15',
            'email' => 'nullable|email|max:255',
            'address' => 'required_without:address_id|string',
            'pincode' => 'required_without:address_id|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Calculate total amount
            $subtotal = 0;
            $gstAmount = 0;
            $items = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock_quantity < $item['quantity']) {
                    return response()->json([
                        'error' => "Insufficient stock for {$product->name}"
                    ], 400);
                }

                $price = $product->final_price;
                
                // Apply attribute-based pricing if any
                if (!empty($item['attributes'])) {
                    foreach ($item['attributes'] as $attr) {
                        $attribute = $product->attributes()
                            ->where('attribute_name', $attr['name'])
                            ->where('attribute_value', $attr['value'])
                            ->first();
                        
                        if ($attribute) {
                            $price += $attribute->price_adjustment;
                        }
                    }
                }

                $itemSubtotal = $price * $item['quantity'];
                $itemGst = ($itemSubtotal * $product->gst) / 100;
                
                $subtotal += $itemSubtotal;
                $gstAmount += $itemGst;

                $items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $itemSubtotal,
                    'attributes' => $item['attributes'] ?? null,
                ];
            }

            $shippingCharge = (float) Setting::get('shipping_charge', 0);
            $totalAmount = $subtotal + $gstAmount + $shippingCharge;

            // Get Razorpay settings
            $razorpaySettings = PaymentSetting::getSettings('razorpay');
            
            if (empty($razorpaySettings['key_id']) || empty($razorpaySettings['key_secret'])) {
                return response()->json([
                    'error' => 'Razorpay is not configured'
                ], 400);
            }

            // Create Razorpay order
            $api = new Api($razorpaySettings['key_id'], $razorpaySettings['key_secret']);
            
            $razorpayOrder = $api->order->create([
                'receipt' => 'receipt_' . time(),
                'amount' => $totalAmount * 100, // Convert to paise
                'currency' => 'INR',
                'payment_capture' => 1
            ]);

            // Create order in database with pending status
            $userId = $request->user()->id;
            $order = Order::create([
                'user_id' => $userId,
                'razorpay_order_id' => $razorpayOrder['id'],
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'address' => $request->address,
                'pincode' => $request->pincode,
                'subtotal' => $subtotal,
                'gst_amount' => $gstAmount,
                'shipping_charge' => $shippingCharge,
                'total_amount' => $totalAmount,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'payment_method' => 'razorpay',
            ]);

            // Create order items
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                    'attributes' => $item['attributes'],
                ]);

                // Update stock
                $item['product']->decrement('stock_quantity', $item['quantity']);
            }

            return response()->json([
                'success' => true,
                'razorpay_order_id' => $razorpayOrder['id'],
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'key_id' => $razorpaySettings['key_id']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create Razorpay order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyRazorpayPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'order_id' => 'required|exists:orders,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $order = Order::findOrFail($request->order_id);
            
            // Verify payment with Razorpay
            $razorpaySettings = PaymentSetting::getSettings('razorpay');
            $api = new Api($razorpaySettings['key_id'], $razorpaySettings['key_secret']);
            
            // Fetch payment details
            $payment = $api->payment->fetch($request->razorpay_payment_id);
            
            // Verify payment status and order ID
            if ($payment->status === 'captured' && $payment->order_id === $request->razorpay_order_id) {
                // Update order status
                $order->update([
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'payment_status' => 'verified',
                    'order_status' => 'confirmed'
                ]);

                return response()->json([
                    'success' => true,
                    'order_number' => $order->order_number
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Payment verification failed'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Payment verification failed: ' . $e->getMessage()
            ], 500);
        }
    }
}

