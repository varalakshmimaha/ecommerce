<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $productSelect = ['id', 'name', 'slug', 'category_id', 'brand_id',
            'mrp', 'selling_price', 'discounted_price', 'main_image', 'stock_quantity', 'has_variations'];

        $featured = Product::select($productSelect)
            ->where('status', 'published')->where('is_featured', true)
            ->with(['category:id,name,slug', 'brand:id,name'])
            ->orderByDesc('created_at')->limit(10)->get();

        $newArrivals = Product::select($productSelect)
            ->where('status', 'published')->where('is_new_arrival', true)
            ->with(['category:id,name,slug', 'brand:id,name'])
            ->orderByDesc('created_at')->limit(10)->get();

        $trending = Product::select($productSelect)
            ->where('status', 'published')->where('is_trending', true)
            ->with(['category:id,name,slug', 'brand:id,name'])
            ->orderByDesc('created_at')->limit(10)->get();

        $topRated = Product::select($productSelect)
            ->where('status', 'published')->where('is_top_rated', true)
            ->with(['category:id,name,slug', 'brand:id,name'])
            ->orderByDesc('created_at')->limit(10)->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'banners'       => Banner::where('is_active', true)->orderBy('sort_order')->get(),
                'categories'    => Category::where('is_active', true)
                    ->orderBy('sort_order')
                    ->with('subCategories')
                    ->limit(20)->get(),
                'brands'        => Brand::where('is_active', true)->orderBy('sort_order')->limit(20)->get(),
                'featured'      => $featured,
                'new_arrivals'  => $newArrivals,
                'trending'      => $trending,
                'top_rated'     => $topRated,
                'features'      => [
                    'enable_favourites' => Setting::get('enable_favourites', 'false') === 'true',
                    'enable_compare'    => Setting::get('enable_compare', 'false') === 'true',
                    'shipping_charge'   => (float) Setting::get('shipping_charge', 0),
                    'free_shipping_above' => (float) Setting::get('free_shipping_above', 0),
                ],
            ],
        ]);
    }
}
