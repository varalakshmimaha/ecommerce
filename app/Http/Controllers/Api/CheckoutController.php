<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Setting;

class CheckoutController extends Controller
{
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'pincode' => 'required|string|max:10',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.attributes' => 'nullable|array',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
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
            $userId = auth()->id();
            if (!$userId && $request->email) {
                $existing = User::where('email', $request->email)->first();
                if ($existing) {
                    $userId = $existing->id;
                } else {
                    $password = Str::random(12);
                    $newUser = User::create([
                        'name' => $request->name,
                        'mobile' => $request->mobile,
                        'email' => $request->email,
                        'password' => $password,
                        'is_verified' => false,
                    ]);
                    $userId = $newUser->id;
                }
            }

            // Create order
            $order = Order::create([
                'user_id' => $userId,
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
}

