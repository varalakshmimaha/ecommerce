<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?php echo e($order->order_number); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
        body {
            background: linear-gradient(to bottom right, #f9fafb, #ffffff);
        }
    </style>
</head>
<body class="min-h-screen py-12 px-4">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Invoice Header -->
        <div class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">INVOICE</h1>
                    <p class="text-white/90 mt-1"><?php echo e(config('app.name', 'Suvee')); ?></p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-white">#<?php echo e($order->order_number); ?></p>
                    <p class="text-sm text-white/90"><?php echo e($order->created_at->format('M d, Y')); ?></p>
                </div>
            </div>
        </div>

        <!-- Invoice Body -->
        <div class="p-8">
            <!-- Customer & Shipping Info -->
            <div class="grid grid-cols-2 gap-8 mb-8">
                <div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Billed From
                    </h3>
                    <div class="text-gray-700 space-y-1 text-sm">
                    
                    <?php if(\App\Models\Setting::get('company_name')): ?>
                        <p class="font-semibold text-gray-900">
                            <?php echo e(\App\Models\Setting::get('company_name')); ?>

                        </p>
                    <?php endif; ?>

                    
                    <?php if(\App\Models\Setting::get('phone_number')): ?>
                        <p>Phone: <?php echo e(\App\Models\Setting::get('phone_number')); ?></p>
                    <?php endif; ?>

                    <?php if(\App\Models\Setting::get('whatsapp_number')): ?>
                        <p>WhatsApp: <?php echo e(\App\Models\Setting::get('whatsapp_number')); ?></p>
                    <?php endif; ?>

                    <?php if(\App\Models\Setting::get('email')): ?>
                        <p>Email: <?php echo e(\App\Models\Setting::get('email')); ?></p>
                    <?php endif; ?>

                    
                    <?php if(\App\Models\Setting::get('gstin')): ?>
                        <p class="mt-1 font-medium">GSTIN: <?php echo e(\App\Models\Setting::get('gstin')); ?></p>
                    <?php endif; ?>
                    
                    <?php if(\App\Models\Setting::get('address')): ?>
                        <p><?php echo e(\App\Models\Setting::get('address')); ?></p>
                    <?php endif; ?>
                </div>
                </div>
                <div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Billed To
                    </h3>
                    <div class="text-gray-700 space-y-1">
                        <p class="font-semibold text-gray-900"><?php echo e($order->user->name); ?></p>
                        <p class="text-sm"><?php echo e($order->user->mobile); ?></p>
                        <?php if($order->user->email): ?>
                            <p class="text-sm"><?php echo e($order->user->email); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="text-gray-700 space-y-1 text-sm">
                        <p><?php echo e($order->addresses->address); ?></p>
                        <p><?php echo e($order->addresses->city); ?>, <?php echo e($order->addresses->state); ?></p>
                        <p><?php echo e($order->addresses->pincode); ?></p>
                    </div>
                </div>
            </div>

            <!-- Order Items Table -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Order Items
                </h3>
                <div class="overflow-hidden border border-gray-200 rounded-lg">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Price</th>
                                <?php if($order->items->where('discount', '>', 0)->count() > 0): ?>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Discount</th>
                                <?php endif; ?>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Subtotal</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">GST</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                                $totalGST = 0;
                            ?>
                            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $gstAmount = $item->subtotal * ($item->gst_percentage ?? 0) / 100;
                                    $totalGST += $gstAmount;
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-900"><?php echo e($item->product_name); ?></p>
                                        <?php if($item->variant_details): ?>
                                            <p class="text-sm text-gray-500"><?php echo e($item->variant_details); ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-800 font-medium"><?php echo e($item->quantity); ?></td>
                                    <td class="px-6 py-4 text-right text-gray-800">₹<?php echo e(number_format($item->price, 2)); ?></td>
                                    <?php if($order->items->where('discount', '>', 0)->count() > 0): ?>
                                    <td class="px-6 py-4 text-right">
                                        <?php if($item->discount > 0): ?>
                                            <span class="text-green-600 font-medium">-₹<?php echo e(number_format($item->discount * $item->quantity, 2)); ?></span>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                    <td class="px-6 py-4 text-right text-gray-800 font-medium">₹<?php echo e(number_format($item->subtotal, 2)); ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if($item->gst_percentage): ?>
                                            <span class="text-sm text-gray-700"><?php echo e($item->gst_percentage); ?>%</span>
                                            <span class="block text-xs text-[#D4AF37] font-medium">₹<?php echo e(number_format($gstAmount, 2)); ?></span>
                                        <?php else: ?>
                                            <span class="text-sm text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-[#D4AF37]">₹<?php echo e(number_format($item->subtotal + $gstAmount, 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totals -->
            <div class="flex justify-end mb-8">
                <div class="w-96">
                    <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-lg p-6 border-2 border-[#D4AF37]/40">
                        <div class="space-y-3">
                            <div class="flex justify-between text-gray-700">
                                <span class="font-medium">Subtotal:</span>
                                <span class="font-semibold">₹<?php echo e(number_format($order->items->sum('subtotal'), 2)); ?></span>
                            </div>

                            <div class="flex justify-between text-gray-700">
                                <span class="font-medium">Total GST:</span>
                                <span class="font-semibold text-[#D4AF37]">₹<?php echo e(number_format($totalGST, 2)); ?></span>
                            </div>

                            <?php if($order->shipping_cost > 0): ?>
                                <div class="flex justify-between text-gray-700">
                                    <span class="font-medium">Shipping:</span>
                                    <span class="font-semibold">₹<?php echo e(number_format($order->shipping_cost, 2)); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if($order->discount > 0): ?>
                                <div class="flex justify-between text-green-600">
                                    <span class="font-medium">Discount:</span>
                                    <span class="font-semibold">-₹<?php echo e(number_format($order->discount, 2)); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="border-t-2 border-[#D4AF37]/50 pt-3 mt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-bold text-gray-900">Grand Total:</span>
                                    <span class="text-2xl font-bold text-[#D4AF37]">₹<?php echo e(number_format($order->total_amount, 2)); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t-2 border-gray-200 pt-6 text-center">
                <p class="text-lg font-semibold text-gray-800 mb-2">Thank you for your business!</p>
                <p class="text-sm text-gray-600">For any queries, please contact us at: <span class="text-[#D4AF37] font-medium">support@suvee.com</span></p>
                <p class="text-xs text-gray-500 mt-3"><?php echo e(parse_url(config('app.url', 'https://suvee.com'), PHP_URL_HOST)); ?></p>
            </div>
        </div>

        <!-- Print Button -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200 no-print">
            <div class="flex gap-4 justify-center">
                <button onclick="window.print()" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg hover:shadow-[#D4AF37]/50 transition-all duration-300">
                    Print Invoice
                </button>
                <button onclick="window.close()" class="bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-700 transition-all duration-300">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
<?php /**PATH /home/bestprime-ecommerce/htdocs/ecommerce.bestprime.live/suvee/resources/views/frontend/invoice.blade.php ENDPATH**/ ?>