<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariation;
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
use App\Services\CommissionService;
use App\Models\WalletTransaction;

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
            'items.*.variation_id' => 'nullable|exists:product_variations,id',
            'items.*.quantity' => 'required|integer|min:1',
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
                $variation = null;
                $price = $product->final_price;
                $stock = $product->stock_quantity;
                
                // Handle variation if provided
                if (!empty($item['variation_id'])) {
                    $variation = ProductVariation::findOrFail($item['variation_id']);
                    
                    // Ensure variation belongs to the product
                    if ($variation->product_id !== $product->id) {
                        return response()->json([
                            'error' => "Variation does not belong to product {$product->name}"
                        ], 400);
                    }
                    
                    if (!$variation->is_active) {
                        return response()->json([
                            'error' => "Variation is not available for {$product->name}"
                        ], 400);
                    }
                    
                    if ($variation->stock_quantity < $item['quantity']) {
                        return response()->json([
                            'error' => "Insufficient stock for {$product->name} - {$variation->variation_title}"
                        ], 400);
                    }

                    $price = $variation->price;
                    $stock = $variation->stock_quantity;
                } else {
                    // Check product stock if no variation
                    if ($stock < $item['quantity']) {
                        return response()->json([
                            'error' => "Insufficient stock for {$product->name}"
                        ], 400);
                    }
                }

                $itemSubtotal = $price * $item['quantity'];
                $itemGst = ($itemSubtotal * $product->gst) / 100;
                
                $subtotal += $itemSubtotal;
                $gstAmount += $itemGst;

                $items[] = [
                    'product' => $product,
                    'variation' => $variation,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $shippingCharge = (float) Setting::get('shipping_charge', 0);
            $totalAmount = $subtotal + $gstAmount + $shippingCharge;

            // Wallet deduction (affiliate / rm / manager roles only)
            $walletUsed = 0.0;
            $remainingPayable = $totalAmount;

            // Handle payment proof upload
            $paymentProofPath = null;
            if ($request->hasFile('payment_proof')) {
                $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
            }

            // Handle user identification
            $userId = null;
            $isNewUser = false;
            $newUserPassword = null;

            // Try to authenticate user from token if provided
            $token = $request->bearerToken();
            if ($token) {
                // Try to find user by token
                $sanctumToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($sanctumToken) {
                    $userId = $sanctumToken->tokenable_id;
                }
            }

            // If still no user ID, check guest user logic
            if (!$userId) {
                // For guest users, check by mobile if provided
                if ($request->has('mobile')) {
                    $existing = User::where('mobile', $request->mobile)->first();
                    if ($existing) {
                        $userId = $existing->id;
                    } else {
                        // Create new user with random password
                        $newUserPassword = Str::random(8);
                        $newUser = User::create([
                            'name' => $request->name,
                            'mobile' => $request->mobile,
                            'email' => $request->email,
                            'password' => Hash::make($newUserPassword),
                            'is_verified' => true,
                        ]);
                        $userId = $newUser->id;
                        $isNewUser = true;
                    }
                } else {
                    return response()->json([
                        'error' => 'Mobile number is required for guest checkout'
                    ], 422);
                }
            }

            // Compute wallet contribution for any logged-in user (only if explicitly opted in)
            // Cap: max wallet discount = 50% of order total
            $useWallet = filter_var($request->input('use_wallet', false), FILTER_VALIDATE_BOOLEAN);
            $userModel = \App\Models\User::find($userId);
            if ($useWallet && $userModel) {
                $walletBalance = WalletTransaction::balanceFor($userId);
                $maxWalletAllowed = round($totalAmount * 0.50, 2);
                $walletUsed = min($walletBalance, $maxWalletAllowed);
                $remainingPayable = $totalAmount - $walletUsed;
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
                'wallet_used' => $walletUsed,
                'remaining_payable' => $remainingPayable,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_proof' => $paymentProofPath,
            ]);

            // Create order items
            foreach ($items as $item) {
                $productName = $item['product']->name;
                if ($item['variation']) {
                    $productName .= ' - ' . $item['variation']->variation_title;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'variation_id' => $item['variation'] ? $item['variation']->id : null,
                    'product_name' => $productName,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Update stock
                if ($item['variation']) {
                    $item['variation']->decrement('stock_quantity', $item['quantity']);
                } else {
                    $item['product']->decrement('stock_quantity', $item['quantity']);
                }
            }

            app(CommissionService::class)->generateForOrder($order);

            if ($walletUsed > 0) {
                WalletTransaction::create([
                    'user_id'    => $userId,
                    'type'       => 'debit',
                    'amount'     => $walletUsed,
                    'remark'     => 'Wallet applied to order #' . $order->order_number,
                    'created_by' => null,
                    'status'     => 'approved',
                ]);
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
            'items.*.variation_id' => 'nullable|exists:product_variations,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subtotal = 0;
        $gstAmount = 0;
        $items = [];

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $variation = null;
            $price = $product->final_price;
            
            // Handle variation if provided
            if (!empty($item['variation_id'])) {
                $variation = ProductVariation::findOrFail($item['variation_id']);
                
                // Ensure variation belongs to the product
                if ($variation->product_id !== $product->id) {
                    return response()->json([
                        'error' => "Variation does not belong to product {$product->name}"
                    ], 400);
                }
                
                if (!$variation->is_active) {
                    return response()->json([
                        'error' => "Variation is not available for {$product->name}"
                    ], 400);
                }
                
                $price = $variation->price;
            }

            $itemSubtotal = $price * $item['quantity'];
            $itemGst = ($itemSubtotal * $product->gst) / 100;
            
            $subtotal += $itemSubtotal;
            $gstAmount += $itemGst;

            $items[] = [
                'product_id' => $product->id,
                'variation_id' => $variation ? $variation->id : null,
                'product_name' => $product->name . ($variation ? ' - ' . $variation->variation_title : ''),
                'quantity' => $item['quantity'],
                'price' => $price,
                'subtotal' => $itemSubtotal,
            ];
        }

        $shippingCharge = (float) Setting::get('shipping_charge', 0);
        $totalAmount = $subtotal + $gstAmount + $shippingCharge;

        $walletBalance = 0.0;
        $walletUsed = 0.0;
        $remainingPayable = $totalAmount;
        $maxWalletApplicable = round($totalAmount * 0.50, 2);

        $useWallet = filter_var($request->input('use_wallet', false), FILTER_VALIDATE_BOOLEAN);

        $token = $request->bearerToken();
        if ($token) {
            $sanctumToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($sanctumToken) {
                $authUser = \App\Models\User::find($sanctumToken->tokenable_id);
                if ($authUser) {
                    $walletBalance = WalletTransaction::balanceFor($authUser->id);
                    if ($useWallet && $walletBalance > 0) {
                        $walletUsed = min($walletBalance, $maxWalletApplicable);
                        $remainingPayable = $totalAmount - $walletUsed;
                    }
                }
            }
        }

        return response()->json([
            'subtotal'               => $subtotal,
            'gst_amount'             => $gstAmount,
            'shipping_charge'        => $shippingCharge,
            'total_amount'           => $totalAmount,
            'wallet_balance'         => $walletBalance,
            'max_wallet_applicable'  => $maxWalletApplicable,
            'wallet_used'            => $walletUsed,
            'remaining_payable'      => $remainingPayable,
            'items'                  => $items,
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

            // Wallet contribution for Razorpay orders (only if user explicitly opted in)
            // Cap: max wallet discount = 50% of order total
            $useWallet = filter_var($request->input('use_wallet', false), FILTER_VALIDATE_BOOLEAN);
            $walletUsed = 0.0;
            $remainingPayable = $totalAmount;
            $userId = auth()->id();
            if ($useWallet && $userId) {
                $walletBalance = WalletTransaction::balanceFor($userId);
                $maxWalletAllowed = round($totalAmount * 0.50, 2);
                $walletUsed = min($walletBalance, $maxWalletAllowed);
                $remainingPayable = $totalAmount - $walletUsed;
            }

            // Get Razorpay settings
            $razorpaySettings = PaymentSetting::getSettings('razorpay');
            
            if (empty($razorpaySettings['key_id']) || empty($razorpaySettings['key_secret'])) {
                return response()->json([
                    'error' => 'Razorpay is not configured'
                ], 400);
            }

            // Create Razorpay order
            $api = new Api($razorpaySettings['key_id'], $razorpaySettings['key_secret']);
            
            $chargeAmount = $remainingPayable > 0 ? $remainingPayable : $totalAmount;
            $razorpayOrder = $api->order->create([
                'receipt' => 'receipt_' . time(),
                'amount' => (int) round($chargeAmount * 100), // paise
                'currency' => 'INR',
                'payment_capture' => 1
            ]);

            // Resolve name/mobile/address from address_id if provided
            $addr = null;
            if ($request->address_id) {
                $addr = \App\Models\Address::find($request->address_id);
            }
            $orderName    = $addr ? $addr->name    : $request->name;
            $orderMobile  = $addr ? $addr->phone   : $request->mobile;
            $orderAddress = $addr ? ($addr->address . ', ' . $addr->city . ', ' . $addr->state . ' - ' . $addr->pincode . ', ' . ($addr->country ?? '')) : $request->address;
            $orderPincode = $addr ? $addr->pincode  : $request->pincode;

            // Create order in database with pending status
            $order = Order::create([
                'user_id'           => $userId,
                'address_id'        => $addr ? $addr->id : null,
                'razorpay_order_id' => $razorpayOrder['id'],
                'name'              => $orderName,
                'mobile'            => $orderMobile,
                'email'             => $request->email ?? ($addr ? null : null),
                'address'           => $orderAddress,
                'pincode'           => $orderPincode,
                'subtotal'          => $subtotal,
                'gst_amount'        => $gstAmount,
                'shipping_charge'   => $shippingCharge,
                'total_amount'      => $totalAmount,
                'wallet_used'       => $walletUsed,
                'remaining_payable' => $remainingPayable,
                'payment_status'    => 'pending',
                'order_status'      => 'pending',
                'payment_method'    => 'razorpay',
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

            app(CommissionService::class)->generateForOrder($order);

            return response()->json([
                'success' => true,
                'razorpay_order_id' => $razorpayOrder['id'],
                'order_id' => $order->id,
                'amount' => $chargeAmount,
                'total_amount' => $totalAmount,
                'wallet_used' => $walletUsed,
                'remaining_payable' => $remainingPayable,
                'key_id' => $razorpaySettings['key_id']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create Razorpay order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyWalletOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $order = Order::findOrFail($request->order_id);

            if ((float) $order->remaining_payable > 0) {
                return response()->json(['success' => false, 'error' => 'Payment still required.'], 400);
            }

            DB::transaction(function () use ($order) {
                $order->update([
                    'payment_status' => 'verified',
                    'order_status'   => 'confirmed',
                ]);

                if ((float) $order->wallet_used > 0) {
                    WalletTransaction::create([
                        'user_id'    => $order->user_id,
                        'type'       => 'debit',
                        'amount'     => $order->wallet_used,
                        'remark'     => 'Wallet applied to order #' . $order->order_number,
                        'created_by' => null,
                        'status'     => 'approved',
                    ]);
                }
            });

            return response()->json(['success' => true, 'order_number' => $order->order_number]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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
                DB::transaction(function () use ($order, $request) {
                    $order->update([
                        'razorpay_payment_id' => $request->razorpay_payment_id,
                        'payment_status' => 'verified',
                        'order_status' => 'confirmed',
                    ]);

                    if ((float) $order->wallet_used > 0) {
                        WalletTransaction::create([
                            'user_id'    => $order->user_id,
                            'type'       => 'debit',
                            'amount'     => $order->wallet_used,
                            'remark'     => 'Wallet applied to order #' . $order->order_number,
                            'created_by' => null,
                            'status'     => 'approved',
                        ]);
                    }
                });

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

