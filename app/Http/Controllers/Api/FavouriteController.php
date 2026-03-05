<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FavouriteController extends Controller
{
    /**
     * Check if favourites feature is enabled
     */
    private function isFavouritesEnabled(): bool
    {
        return Setting::get('enable_favourites', 'false') === 'true';
    }

    /**
     * Get all favourites for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        if (!$this->isFavouritesEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Favourites feature is disabled'
            ], 403);
        }

        $favourites = Favourite::with('product')
            ->where('user_id', $request->user()->id)
            ->get()
            ->pluck('product');

        return response()->json([
            'success' => true,
            'data' => $favourites
        ]);
    }

    /**
     * Add a product to favourites
     */
    public function store(Request $request): JsonResponse
    {
        if (!$this->isFavouritesEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Favourites feature is disabled'
            ], 403);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = $request->user()->id;
        $productId = $validated['product_id'];

        // Check if already in favourites
        $existing = Favourite::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in favourites'
            ], 422);
        }

        Favourite::create([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to favourites'
        ]);
    }

    /**
     * Remove a product from favourites
     */
    public function destroy(Request $request, $productId): JsonResponse
    {
        if (!$this->isFavouritesEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Favourites feature is disabled'
            ], 403);
        }

        $favourite = Favourite::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->first();

        if (!$favourite) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in favourites'
            ], 404);
        }

        $favourite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from favourites'
        ]);
    }

    /**
     * Check if a product is in user's favourites
     */
    public function check(Request $request, $productId): JsonResponse
    {
        if (!$this->isFavouritesEnabled()) {
            return response()->json([
                'success' => false,
                'is_favourite' => false
            ]);
        }

        $isFavourite = Favourite::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'success' => true,
            'is_favourite' => $isFavourite
        ]);
    }

    /**
     * Toggle favourite status for a product
     */
    public function toggle(Request $request): JsonResponse
    {
        if (!$this->isFavouritesEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Favourites feature is disabled'
            ], 403);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = $request->user()->id;
        $productId = $validated['product_id'];

        $existing = Favourite::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'success' => true,
                'is_favourite' => false,
                'message' => 'Product removed from favourites'
            ]);
        }

        Favourite::create([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        return response()->json([
            'success' => true,
            'is_favourite' => true,
            'message' => 'Product added to favourites'
        ]);
    }
}
