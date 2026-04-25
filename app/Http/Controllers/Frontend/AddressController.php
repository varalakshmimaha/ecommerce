<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->addresses()->orderByDesc('is_default')->get();
        return view('frontend.addresses.list', compact('addresses'))->render();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'country' => 'required|string|max:100',
            'is_default' => 'nullable',
        ]);
        $data['user_id'] = Auth::id();
        $data['is_default'] = $request->boolean('is_default');
        if ($data['is_default']) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }
        $address = Address::create($data);
        return response()->json(['success' => true, 'address' => $address]);
    }

    public function update(Request $request, Address $address)
    {
        $this->authorize('update', $address);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'country' => 'required|string|max:100',
            'is_default' => 'nullable',
        ]);
        $data['is_default'] = $request->boolean('is_default');
        if ($data['is_default']) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }
        $address->update($data);
        return response()->json(['success' => true, 'address' => $address]);
    }

    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);
        $address->delete();
        return response()->json(['success' => true]);
    }

    public function setDefault(Address $address)
    {
        $this->authorize('update', $address);
        Address::where('user_id', Auth::id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        return response()->json(['success' => true]);
    }
}
