<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yesb Confident - {{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @php
    $activeTheme = \App\Models\ThemeColor::getActive();
    if ($activeTheme) {
        $cssVariables = $activeTheme->toCssVariables();
    } else {
        // Fallback colors
        $cssVariables = [
            '--brand-gold' => '#D4AF37',
            '--brand-amber' => '#B8962E', 
            '--brand-crimson' => '#8B0000',
            '--text-heading' => '#1A1A1A',
            '--text-muted' => '#6B6B6B'
        ];
    }
@endphp

<style>
        @media print {
            .no-print {
                display: none;
            }
        }
        body {
            background: linear-gradient(to bottom right, #f9fafb, #ffffff);
        }
        
        /* Theme Colors */
        :root {
            @foreach($cssVariables as $variable => $value)
                {{ $variable }}: {{ $value }};
            @endforeach
        }
        
        /* Debug: Ensure colors are applied */
        .bg-gradient-to-r {
            background: linear-gradient(to right, var(--brand-gold), var(--brand-amber), var(--brand-crimson)) !important;
        }
    </style>
</head>
<body class="min-h-screen py-12 px-4">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Invoice Header -->
        <div class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Yesb Confident</h1>
                    <p class="text-white/90 mt-1">{{ \App\Models\Setting::get('company_name', 'Suwish') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-white">#{{ $order->order_number }}</p>
                    <p class="text-sm text-white/90">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Invoice Body -->
        <div class="p-8">
            <!-- Customer & Shipping Info -->
            <div class="grid grid-cols-2 gap-8 mb-8">
                <div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Billed From
                    </h3>
                    <div class="text-gray-700 space-y-1 text-sm">
                    {{-- Company Name --}}
                    @if(\App\Models\Setting::get('company_name'))
                        <p class="font-semibold text-gray-900">
                            {{ \App\Models\Setting::get('company_name') }}
                        </p>
                    @endif

                    {{-- Contact Info --}}
                    @if(\App\Models\Setting::get('phone_number'))
                        <p>Phone: {{ \App\Models\Setting::get('phone_number') }}</p>
                    @endif

                    @if(\App\Models\Setting::get('whatsapp_number'))
                        <p>WhatsApp: {{ \App\Models\Setting::get('whatsapp_number') }}</p>
                    @endif

                    @if(\App\Models\Setting::get('email'))
                        <p>Email: {{ \App\Models\Setting::get('email') }}</p>
                    @endif

                    {{-- GST --}}
                    @if(\App\Models\Setting::get('gstin'))
                        <p class="mt-1 font-medium">GSTIN: {{ \App\Models\Setting::get('gstin') }}</p>
                    @endif
                    {{-- Address --}}
                    @if(\App\Models\Setting::get('address'))
                        <p>{{ \App\Models\Setting::get('address') }}</p>
                    @endif
                </div>
                </div>
                <div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Billed To
                    </h3>
                    <div class="text-gray-700 space-y-1">
                        <p class="font-semibold text-gray-900">{{ $order->user->name ?? $order->name }}</p>
                        <p class="text-sm">{{ $order->user->mobile ?? $order->mobile }}</p>
                        @if($order->user->email ?? $order->email)
                            <p class="text-sm">{{ $order->user->email ?? $order->email }}</p>
                        @endif
                    </div>
                    <div class="text-gray-700 space-y-1 text-sm">
                        <p>{{ $order->addresses->address ?? $order->address }}</p>
                        <p>{{ $order->addresses->city ?? '' }}, {{ $order->addresses->state ?? '' }}</p>
                        <p>{{ $order->addresses->pincode ?? $order->pincode }}</p>
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
                                @if($order->items->where('discount', '>', 0)->count() > 0)
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Discount</th>
                                @endif
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Subtotal</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">GST</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $totalGST = 0;
                            @endphp
                            @foreach($order->items as $item)
                                @php
                                    $gstAmount = $item->subtotal * ($item->gst_percentage ?? 0) / 100;
                                    $totalGST += $gstAmount;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                                        @if($item->variant_details)
                                            <p class="text-sm text-gray-500">{{ $item->variant_details }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-800 font-medium">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-right text-gray-800">₹{{ number_format($item->price, 2) }}</td>
                                    @if($order->items->where('discount', '>', 0)->count() > 0)
                                    <td class="px-6 py-4 text-right">
                                        @if($item->discount > 0)
                                            <span class="text-green-600 font-medium">-₹{{ number_format($item->discount * $item->quantity, 2) }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    @endif
                                    <td class="px-6 py-4 text-right text-gray-800 font-medium">₹{{ number_format($item->subtotal, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($item->gst_percentage)
                                            <span class="text-sm text-gray-700">{{ $item->gst_percentage }}%</span>
                                            <span class="block text-xs text-brand-gold font-medium">₹{{ number_format($gstAmount, 2) }}</span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-brand-gold">₹{{ number_format($item->subtotal + $gstAmount, 2) }}</td>
                                </tr>
                            @endforeach
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
                                <span class="font-semibold">₹{{ number_format($order->items->sum('subtotal'), 2) }}</span>
                            </div>

                            <div class="flex justify-between text-gray-700">
                                <span class="font-medium">Total GST:</span>
                                <span class="font-semibold text-brand-gold">₹{{ number_format($totalGST, 2) }}</span>
                            </div>

                            @if($order->shipping_cost > 0)
                                <div class="flex justify-between text-gray-700">
                                    <span class="font-medium">Shipping:</span>
                                    <span class="font-semibold">₹{{ number_format($order->shipping_cost, 2) }}</span>
                                </div>
                            @endif

                            @if($order->discount > 0)
                                <div class="flex justify-between text-green-600">
                                    <span class="font-medium">Discount:</span>
                                    <span class="font-semibold">-₹{{ number_format($order->discount, 2) }}</span>
                                </div>
                            @endif

                            @if((float)$order->wallet_used > 0)
                            <div class="flex justify-between text-green-700 font-medium">
                                <span>Wallet Applied:</span>
                                <span>-₹{{ number_format($order->wallet_used, 2) }}</span>
                            </div>
                            @endif

                            <div class="border-t-2 border-[#D4AF37]/50 pt-3 mt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-bold text-gray-900">Grand Total:</span>
                                    <span class="text-2xl font-bold text-brand-gold">₹{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                                @if((float)$order->wallet_used > 0)
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-base font-semibold text-gray-700">Amount Payable:</span>
                                    <span class="text-lg font-bold text-red-600">₹{{ number_format($order->remaining_payable, 2) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t-2 border-gray-200 pt-6 text-center">
                <p class="text-lg font-semibold text-gray-800 mb-2">Thank you for your business!</p>
                <p class="text-sm text-gray-600">For any queries, please contact us at: <span class="text-brand-gold font-medium">{{ \App\Models\Setting::get('email', 'support@' . strtolower(str_replace(' ', '', \App\Models\Setting::get('company_name', 'suvee'))) . '.com') }}</span></p>
                <p class="text-xs text-gray-500 mt-3">{{ parse_url(config('app.url', 'https://' . strtolower(str_replace(' ', '', \App\Models\Setting::get('company_name', 'suvee'))) . '.com'), PHP_URL_HOST) }}</p>
            </div>
        </div>

        <!-- Print Button -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200 no-print">
            <div class="flex gap-4 justify-center">
                <button onclick="window.print()" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg hover:shadow-brand-gold/50 transition-all duration-300">
                    Print Yesb Confident
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