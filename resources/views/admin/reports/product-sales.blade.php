@extends('layouts.admin')

@section('title', 'Product Sales Report')

@section('content')
<div class="space-y-6">
    <!-- Header with Title and Filters -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-lg shadow">
        <h2 class="text-3xl font-bold text-gray-900">Product Sales Report</h2>
        <form method="GET" class="flex flex-col md:flex-row gap-2">
            <div class="flex gap-2">
                <input type="date" name="start_date" value="{{ $startDate }}" class="input-field px-3 py-2 border border-gray-300 rounded">
                <span class="flex items-center text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="input-field px-3 py-2 border border-gray-300 rounded">
            </div>
            <button type="submit" class="btn-primary px-6">Filter</button>
            <a href="?download=1&start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn px-6 text-center">Download</a>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg shadow hover:shadow-lg transition">
            <p class="text-sm text-gray-600 font-medium">Total Items Sold</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $items->count() }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg shadow hover:shadow-lg transition">
            <p class="text-sm text-gray-600 font-medium">Total Revenue</p>
            <p class="text-3xl font-bold text-green-600 mt-2">₹{{ number_format($items->sum('subtotal'), 2) }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg shadow hover:shadow-lg transition">
            <p class="text-sm text-gray-600 font-medium">Avg Item Price</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">₹{{ $items->count() > 0 ? number_format($items->avg('price'), 2) : 0 }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Product</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Quantity</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Price</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Subtotal</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Order #</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($items as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item->product_name ?? ($item->product->name ?? 'N/A') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600"><span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">{{ $item->quantity }}</span></td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">₹{{ number_format($item->price,2) }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-green-600">₹{{ number_format($item->subtotal,2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600"><span class="font-mono bg-gray-100 px-2 py-1 rounded text-xs">{{ $item->order->order_number ?? 'N/A' }}</span></td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ optional($item->order->created_at)->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">{{ $items->withQueryString()->links() }}</div>
</div>
@endsection
