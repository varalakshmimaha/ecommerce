<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = $request->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return response()->json(['success' => true, 'data' => $addresses]);
    }

    public function show(Request $request, $id)
    {
        $address = $request->user()->addresses()->find($id);
        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $address]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['user_id']    = $request->user()->id;
        $data['country']    = $data['country'] ?? 'India';
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
        } elseif ($request->user()->addresses()->count() === 0) {
            // First address is default automatically
            $data['is_default'] = true;
        }

        $address = Address::create($data);

        return response()->json(['success' => true, 'data' => $address], 201);
    }

    public function update(Request $request, $id)
    {
        $address = $request->user()->addresses()->find($id);
        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found'], 404);
        }

        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return response()->json(['success' => true, 'data' => $address]);
    }

    public function destroy(Request $request, $id)
    {
        $address = $request->user()->addresses()->find($id);
        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found'], 404);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $next = $request->user()->addresses()->orderByDesc('id')->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Address deleted']);
    }

    public function setDefault(Request $request, $id)
    {
        $address = $request->user()->addresses()->find($id);
        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found'], 404);
        }

        Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json(['success' => true, 'data' => $address]);
    }

    private function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string|max:20',
            'address'    => 'required|string|max:500',
            'city'       => 'required|string|max:100',
            'state'      => 'required|string|max:100',
            'pincode'    => 'required|string|max:10',
            'country'    => 'nullable|string|max:100',
            'is_default' => 'nullable|boolean',
        ];
    }
}
