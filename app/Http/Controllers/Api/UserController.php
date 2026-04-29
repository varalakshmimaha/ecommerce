<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $query = $user->orders()->with(['items.product:id,name,slug', 'items.product.images']);

        if ($status = $request->query('status')) {
            $query->where('order_status', $status);
        }

        $perPage = (int) $request->query('per_page', 15);
        $orders  = $query->orderByDesc('created_at')->paginate(min(max($perPage, 1), 50));

        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function orderDetails(Request $request, $orderNumber)
    {
        $order = $request->user()
            ->orders()
            ->with(['items.product.images', 'addresses'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return response()->json(['success' => true, 'data' => $order]);
    }

    public function cancelOrder(Request $request, $orderNumber)
    {
        $validator = Validator::make($request->all(), [
            'cancellation_remark' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = $request->user()->orders()->where('order_number', $orderNumber)->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        if (!in_array($order->order_status, ['pending', 'processing'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled at this stage.',
            ], 422);
        }

        $order->order_status        = 'cancelled';
        $order->cancellation_remark = $request->cancellation_remark;
        $order->save();

        return response()->json(['success' => true, 'message' => 'Order cancelled', 'data' => $order]);
    }

    public function downloadInvoice(Request $request, $orderNumber)
    {
        $order = $request->user()
            ->orders()
            ->with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Generate PDF invoice
        $pdf = Pdf::loadView('invoices.order', compact('order'));

        return $pdf->download('invoice-' . $orderNumber . '.pdf');
    }
}
