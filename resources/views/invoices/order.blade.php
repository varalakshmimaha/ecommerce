<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 30px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table thead {
            background-color: #f0f0f0;
        }
        .items-table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #333;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }
        .totals-section {
            width: 300px;
            margin-left: auto;
            margin-bottom: 30px;
        }
        .total-row {
            display: table;
            width: 100%;
            padding: 8px 0;
        }
        .total-label {
            display: table-cell;
            text-align: left;
        }
        .total-value {
            display: table-cell;
            text-align: right;
        }
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #fff4e6;
            color: #f59e0b;
        }
        .status-processing {
            background-color: #dbeafe;
            color: #3b82f6;
        }
        .status-shipped {
            background-color: #f3e8ff;
            color: #a855f7;
        }
        .status-delivered {
            background-color: #d1fae5;
            color: #10b981;
        }
        .status-cancelled {
            background-color: #fee2e2;
            color: #ef4444;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="company-name">{{ config('app.name', 'Your Store') }}</div>
                <div>Your Store Address</div>
                <div>Phone: +91 1234567890</div>
                <div>Email: info@yourstore.com</div>
            </div>
            <div class="header-right">
                <div class="invoice-title">INVOICE</div>
                <div><strong>Invoice #:</strong> {{ $order->order_number }}</div>
                <div><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</div>
                <div>
                    <span class="status-badge status-{{ strtolower($order->order_status) }}">
                        {{ strtoupper($order->order_status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Customer & Shipping Info -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-column">
                    <div class="info-label">BILL TO:</div>
                    <div>{{ $order->user->name }}</div>
                    <div>{{ $order->user->email }}</div>
                    <div>{{ $order->user->mobile }}</div>
                </div>
                <div class="info-column">
                    <div class="info-label">SHIP TO:</div>
                    <div>{{ $order->user->name }}</div>
                    <div>{{ $order->shipping_address }}</div>
                    <div>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_pincode }}</div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if($item->product_sku)
                            <div style="font-size: 12px; color: #666;">SKU: {{ $item->product_sku }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">₹{{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right;">₹{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="total-row">
                <div class="total-label">Subtotal:</div>
                <div class="total-value">₹{{ number_format($order->items->sum('subtotal'), 2) }}</div>
            </div>
            @if($order->shipping_cost && $order->shipping_cost > 0)
            <div class="total-row">
                <div class="total-label">Shipping:</div>
                <div class="total-value">₹{{ number_format($order->shipping_cost, 2) }}</div>
            </div>
            @endif
            @if($order->tax_amount && $order->tax_amount > 0)
            <div class="total-row">
                <div class="total-label">Tax:</div>
                <div class="total-value">₹{{ number_format($order->tax_amount, 2) }}</div>
            </div>
            @endif
            @if($order->discount_amount && $order->discount_amount > 0)
            <div class="total-row">
                <div class="total-label">Discount:</div>
                <div class="total-value">-₹{{ number_format($order->discount_amount, 2) }}</div>
            </div>
            @endif
            <div class="total-row grand-total">
                <div class="total-label">TOTAL:</div>
                <div class="total-value">₹{{ number_format($order->total_amount, 2) }}</div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="info-section">
            <div class="info-label">PAYMENT INFORMATION:</div>
            <div><strong>Payment Method:</strong> {{ ucfirst($order->payment_method ?? 'N/A') }}</div>
            <div><strong>Payment Status:</strong> {{ ucfirst($order->payment_status ?? 'Pending') }}</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>Thank you for your business!</div>
            <div style="margin-top: 10px;">
                For any questions regarding this invoice, please contact us at info@yourstore.com
            </div>
        </div>
    </div>
</body>
</html>
