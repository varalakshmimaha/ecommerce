<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompareItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompareController extends Controller
{
    /**
     * Check if compare feature is enabled
     */
    private function isCompareEnabled(): bool
    {
        return Setting::get('enable_compare', 'false') === 'true';
    }

    /**
     * Get all compare items for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        if (!$this->isCompareEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Compare feature is disabled'
            ], 403);
        }

        $compareItems = CompareItem::with(['product' => function($query) {
                $query->with(['category', 'brand']);
            }])
            ->where('user_id', $request->user()->id)
            ->get()
            ->pluck('product');

        return response()->json([
            'success' => true,
            'data' => $compareItems
        ]);
    }

    /**
     * Add a product to compare list
     */
    public function store(Request $request): JsonResponse
    {
        if (!$this->isCompareEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Compare feature is disabled'
            ], 403);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = $request->user()->id;
        $productId = $validated['product_id'];

        // Check if already in compare list
        $existing = CompareItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in compare list'
            ], 422);
        }

        // Limit compare list to 4 items
        $count = CompareItem::where('user_id', $userId)->count();
        if ($count >= 4) {
            return response()->json([
                'success' => false,
                'message' => 'You can only compare up to 4 products at a time'
            ], 422);
        }

        CompareItem::create([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to compare list'
        ]);
    }

    /**
     * Remove a product from compare list
     */
    public function destroy(Request $request, $productId): JsonResponse
    {
        if (!$this->isCompareEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Compare feature is disabled'
            ], 403);
        }

        $compareItem = CompareItem::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->first();

        if (!$compareItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in compare list'
            ], 404);
        }

        $compareItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from compare list'
        ]);
    }

    /**
     * Check if a product is in user's compare list
     */
    public function check(Request $request, $productId): JsonResponse
    {
        if (!$this->isCompareEnabled()) {
            return response()->json([
                'success' => false,
                'is_in_compare' => false
            ]);
        }

        $isInCompare = CompareItem::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'success' => true,
            'is_in_compare' => $isInCompare
        ]);
    }

    /**
     * Toggle compare status for a product
     */
    public function toggle(Request $request): JsonResponse
    {
        if (!$this->isCompareEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Compare feature is disabled'
            ], 403);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = $request->user()->id;
        $productId = $validated['product_id'];

        $existing = CompareItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'success' => true,
                'is_in_compare' => false,
                'message' => 'Product removed from compare list'
            ]);
        }

        // Limit compare list to 4 items
        $count = CompareItem::where('user_id', $userId)->count();
        if ($count >= 4) {
            return response()->json([
                'success' => false,
                'message' => 'You can only compare up to 4 products at a time'
            ], 422);
        }

        CompareItem::create([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        return response()->json([
            'success' => true,
            'is_in_compare' => true,
            'message' => 'Product added to compare list'
        ]);
    }

    /**
     * Clear all compare items
     */
    public function clear(Request $request): JsonResponse
    {
        if (!$this->isCompareEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Compare feature is disabled'
            ], 403);
        }

        CompareItem::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Compare list cleared'
        ]);
    }
}
