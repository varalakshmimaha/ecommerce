<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('frontend.dashboard', compact('user'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('frontend.dashboard.profile', compact('user'));
    }

    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->with('items')
            ->get();
        return view('frontend.dashboard.orders', compact('orders'));
    }

    public function queries()
    {
        $queries = Query::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('frontend.dashboard.queries', compact('queries'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }

    public function createQuery(Request $request)
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'order_number' => ['nullable', 'string'],
            'message' => ['required', 'string'],
        ]);

        Query::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'order_number' => $request->order_number,
            'message' => $request->message,
            'status' => 'open',
        ]);

        return back()->with('success', 'Query submitted successfully!');
    }

    public function downloadInvoice($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items', 'addresses', 'user')
            ->firstOrFail();

        //dd($order->addresses);

        return view('frontend.invoice', compact('order'));
    }
}
