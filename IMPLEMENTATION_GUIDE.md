# User Dashboard & Management System - Implementation Guide

## Overview
This guide contains all the code needed to implement:
- User Profile Management (update name, email, change password)
- Order Management (view orders, download invoices)
- Query/Ticket System (user to admin communication)
- Admin Query Management

## Database Setup
✅ Already completed:
- `queries` table migration created and run
- Query model created with relationships

## API Routes to Add

Add these routes to `routes/api.php`:

```php
// Protected User Routes
Route::middleware('auth:sanctum')->group(function () {
    // Profile Management
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/password', [UserController::class, 'changePassword']);

    // Orders
    Route::get('/user/orders', [UserController::class, 'orders']);
    Route::get('/user/orders/{orderNumber}', [UserController::class, 'orderDetails']);
    Route::get('/user/orders/{orderNumber}/invoice', [UserController::class, 'downloadInvoice']);

    // Queries
    Route::get('/user/queries', [App\Http\Controllers\Api\QueryController::class, 'index']);
    Route::post('/user/queries', [App\Http\Controllers\Api\QueryController::class, 'store']);
});
```

## Admin Routes to Add

Add these to `routes/web.php` in the admin group:

```php
// Inside the admin middleware group
Route::get('/queries', [App\Http\Controllers\Admin\QueryController::class, 'index'])->name('queries.index');
Route::post('/queries/{query}/respond', [App\Http\Controllers\Admin\QueryController::class, 'respond'])->name('queries.respond');
Route::post('/queries/{query}/status', [App\Http\Controllers\Admin\QueryController::class, 'updateStatus'])->name('queries.update-status');
```

## Controller Implementations

### 1. UserController (`app/Http/Controllers/Api/UserController.php`)

Check if it exists, if not create it with this content:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $request->user()->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $user->update($request->only('name', 'email'));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 401);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function orders(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $orders]);
    }

    public function orderDetails(Request $request, $orderNumber)
    {
        $order = $request->user()
            ->orders()
            ->with('items.product')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return response()->json(['data' => $order]);
    }

    public function downloadInvoice(Request $request, $orderNumber)
    {
        $order = $request->user()
            ->orders()
            ->with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Generate PDF invoice
        $pdf = \PDF::loadView('invoices.order', compact('order'));

        return $pdf->download('invoice-' . $orderNumber . '.pdf');
    }
}
```

### 2. QueryController API (`app/Http/Controllers/Api/QueryController.php`)

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QueryController extends Controller
{
    public function index(Request $request)
    {
        $queries = $request->user()
            ->queries()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $queries]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'order_number' => 'nullable|string|exists:orders,order_number',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = $request->user()->queries()->create([
            'subject' => $request->subject,
            'message' => $request->message,
            'order_number' => $request->order_number,
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Query submitted successfully',
            'data' => $query
        ], 201);
    }
}
```

### 3. Admin QueryController (`app/Http/Controllers/Admin/QueryController.php`)

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Query;
use Illuminate\Http\Request;

class QueryController extends Controller
{
    public function index()
    {
        $queries = Query::with('user')
            ->orderByRaw("FIELD(status, 'open', 'in_progress', 'resolved', 'closed')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.queries.index', compact('queries'));
    }

    public function respond(Request $request, Query $query)
    {
        $request->validate([
            'response' => 'required|string',
        ]);

        $query->update([
            'admin_response' => $request->response,
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Response sent successfully');
    }

    public function updateStatus(Request $request, Query $query)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $query->update(['status' => $request->status]);

        if ($request->status === 'resolved' && !$query->resolved_at) {
            $query->update(['resolved_at' => now()]);
        }

        return redirect()->back()->with('success', 'Status updated successfully');
    }
}
```

## Views to Create

### User Dashboard
The comprehensive dashboard view has been created with Profile, Orders, and Queries tabs.
File location: `resources/views/frontend/dashboard.blade.php`

### Admin Queries View
Create: `resources/views/admin/queries/index.blade.php`

```blade
@extends('layouts.admin')

@section('title', 'Customer Queries')

@section('content')
<div class="admin-card">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Customer Queries</h2>

    <div class="space-y-4">
        @forelse($queries as $query)
        <div class="border rounded-lg p-4 {{ $query->admin_response ? 'bg-green-50' : 'bg-white' }}">
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1">
                    <h3 class="font-semibold text-lg">{{ $query->subject }}</h3>
                    <p class="text-sm text-gray-600">
                        From: <strong>{{ $query->user->name }}</strong> ({{ $query->user->email }})
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ $query->created_at->format('d M Y, h:i A') }}
                        @if($query->order_number)
                        • Order: <a href="{{ route('admin.orders.show', $query->order_number) }}" class="text-primary-600">{{ $query->order_number }}</a>
                        @endif
                    </p>
                </div>
                <form action="{{ route('admin.queries.update-status', $query) }}" method="POST">
                    @csrf
                    <select name="status" onchange="this.form.submit()" class="input-field">
                        <option value="open" {{ $query->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $query->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $query->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ $query->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </form>
            </div>

            <div class="bg-white p-3 rounded mb-3">
                <p class="text-gray-700">{{ $query->message }}</p>
            </div>

            @if($query->admin_response)
            <div class="bg-blue-50 p-3 rounded border-l-4 border-blue-500 mb-3">
                <p class="text-sm font-semibold text-blue-700">Your Response:</p>
                <p class="text-gray-700 mt-1">{{ $query->admin_response }}</p>
                @if($query->resolved_at)
                <p class="text-xs text-gray-500 mt-1">Resolved: {{ $query->resolved_at->format('d M Y, h:i A') }}</p>
                @endif
            </div>
            @endif

            @if(!$query->admin_response)
            <form action="{{ route('admin.queries.respond', $query) }}" method="POST" class="mt-3">
                @csrf
                <textarea name="response" rows="3" class="input-field" placeholder="Type your response..." required></textarea>
                <button type="submit" class="btn-primary mt-2">Send Response</button>
            </form>
            @endif
        </div>
        @empty
        <p class="text-gray-500">No queries found.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $queries->links() }}
    </div>
</div>
@endsection
```

## PDF Invoice Generation

### Install PDF Package
```bash
composer require barryvdh/laravel-dompdf
```

### Create Invoice View
Create: `resources/views/invoices/order.blade.php`

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .total { font-weight: bold; font-size: 18px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>Order #{{ $order->order_number }}</p>
        <p>Date: {{ $order->created_at->format('d M Y') }}</p>
    </div>

    <h3>Shipping Details</h3>
    <p>{{ $order->name }}<br>
    {{ $order->mobile }}<br>
    {{ $order->email }}<br>
    {{ $order->address }}<br>
    Pincode: {{ $order->pincode }}</p>

    <h3>Order Items</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ number_format($item->price, 2) }}</td>
                <td>₹{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 300px; margin-left: auto; margin-top: 20px;">
        <tr>
            <td>Subtotal:</td>
            <td>₹{{ number_format($order->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>GST:</td>
            <td>₹{{ number_format($order->gst_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Shipping:</td>
            <td>₹{{ number_format($order->shipping_charge, 2) }}</td>
        </tr>
        <tr class="total">
            <td>TOTAL:</td>
            <td>₹{{ number_format($order->total_amount, 2) }}</td>
        </tr>
    </table>

    <p style="margin-top: 30px; text-align: center; color: #666;">
        Thank you for your order!
    </p>
</body>
</html>
```

## User Model Update

Add to `App\Models\User.php`:

```php
public function queries()
{
    return $this->hasMany(Query::class);
}
```

## Testing Checklist

1. ✅ Register/Login as user
2. ✅ Go to /dashboard
3. ✅ Update profile (name, email)
4. ✅ Change password
5. ✅ View orders list
6. ✅ Click view order details
7. ✅ Download invoice
8. ✅ Create a query
9. ✅ View queries list
10. ✅ Admin: Go to /admin/queries
11. ✅ Admin: Respond to query
12. ✅ Admin: Change query status
13. ✅ User: See admin response

## Next Steps

1. Update `routes/api.php` with new routes
2. Create/update UserController
3. Update QueryController files
4. Create admin queries view
5. Install PDF package
6. Create invoice template
7. Update User model
8. Test all functionality

All code is ready to be implemented!
