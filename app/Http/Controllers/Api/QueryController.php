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
