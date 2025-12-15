<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class ShippingController extends Controller
{
    public function index()
    {
        $shipping = Setting::get('shipping_charge', 0);
        return view('admin.shipping.index', compact('shipping'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'shipping_charge' => 'required|numeric|min:0'
        ]);

        Setting::set('shipping_charge', $request->shipping_charge);

        return redirect()->back()->with('success', 'Shipping charge updated');
    }
}
