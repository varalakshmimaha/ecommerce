<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'subCategory', 'images', 'brand'])
            ->where('status', 'published');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by subcategory
        if ($request->has('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        // Filter by brand
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->whereRaw('COALESCE(discounted_price, selling_price) >= ?', [$request->min_price]);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->whereRaw('COALESCE(discounted_price, selling_price) <= ?', [$request->max_price]);
        }

        // Filter by flags
        if ($request->has('type')) {
            switch ($request->type) {
                case 'popular':
                    $query->where('is_featured', true);
                    break;
                case 'featured':
                    $query->where('is_featured', true);
                    break;
                case 'trending':
                    $query->where('is_trending', true);
                    break;
                case 'top-rated':
                    $query->where('is_top_rated', true);
                    break;
                case 'new-arrival':
                    $query->where('is_new_arrival', true);
                    break;
            }
        }

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price-low':
                $query->orderByRaw('COALESCE(discounted_price, selling_price) ASC');
                break;
            case 'price-high':
                $query->orderByRaw('COALESCE(discounted_price, selling_price) DESC');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage);

        return response()->json($products);
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'subCategory', 'images', 'attributes', 'relatedProducts.images'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return response()->json($product);
    }

    public function categories()
    {
        $categories = Category::with('subCategories')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function showCategory($id)
    {
        $category = Category::with('subCategories')->findOrFail($id);

        return response()->json($category);
    }

    public function brands()
    {
        $brands = \App\Models\Brand::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $brands
        ]);
    }

    public function banners()
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json($banners);
    }
}

