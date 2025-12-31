<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'subCategory', 'brand']);
        
        // Search by product name or SKU
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        
        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        
        // Filter by stock status
        if ($request->filled('stock_status')) {
            $stockStatus = $request->input('stock_status');
            if ($stockStatus === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($stockStatus === 'out_of_stock') {
                $query->where('stock_quantity', '=', 0);
            } elseif ($stockStatus === 'low_stock') {
                $query->where('stock_quantity', '<=', 10);
            }
        }
        
        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('selling_price', '>=', $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('selling_price', '<=', $request->input('max_price'));
        }
        
        // Sort products
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $products = $query->paginate(20)->withQueryString();
        
        // Get filter options for dropdowns
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        
        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $subCategories = SubCategory::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'subCategories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'mrp' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'gst' => 'required|numeric|min:0|max:100',
            'min_order_quantity' => 'required|integer|min:1',
            'stock_quantity' => 'required|integer|min:0',
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'short_description' => 'nullable|string',
            'full_description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_new_arrival' => 'boolean',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'is_top_rated' => 'boolean',
            'status' => 'required|in:published,unpublished',
            'has_variations' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Upload main image
        if ($request->hasFile('main_image')) {
            $validated['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product = Product::create($validated);

        // Upload gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $image) {
                $product->images()->create([
                    'image_path' => $image->store('products/gallery', 'public'),
                    'sort_order' => $index,
                ]);
            }
        }

        // Sync related products if provided
        if ($request->has('related_products')) {
            $product->relatedProducts()->sync($request->related_products);
        }
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $subCategories = SubCategory::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $product->load('images', 'relatedProducts');
        return view('admin.products.edit', compact('product', 'categories', 'subCategories', 'brands'));
    }

    // ProductController.php
    public function search(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $exclude = $request->get('exclude', '');
            
            // Return empty if search term is too short
            if (strlen($search) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Enter at least 2 characters'
                ]);
            }
            
            // Build query
            $query = Product::query()
                ->select('id', 'name', 'main_image', 'category_id', 'status')
                ->where('status', 'published');
            
            // Exclude current product
            if ($exclude) {
                $query->where('id', '!=', $exclude);
            }
            
            // Apply search filter
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereHas('category', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }
            
            // Get results
            $products = $query->orderBy('name')->limit(20)->get();
            
            // Format response with category name
            $formattedProducts = $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'main_image' => $product->main_image,
                    'category_name' => $product->category->name ?? 'Uncategorized'
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedProducts,
                'count' => $formattedProducts->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Product search error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return error response but keep the app working
            return response()->json([
                'success' => false,
                'message' => 'Error searching products. Please try again.',
                'data' => [],
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'mrp' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'gst' => 'required|numeric|min:0|max:100',
            'min_order_quantity' => 'required|integer|min:1',
            'stock_quantity' => 'required|integer|min:0',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'short_description' => 'nullable|string',
            'full_description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_new_arrival' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
            'is_top_rated' => 'nullable|boolean',
            'status' => 'required|in:published,unpublished',
            'has_variations' => 'boolean',
        ]);

        // Update slug if name changed
        if ($validated['name'] !== $product->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle boolean fields - if not present in request, set to false
        $validated['is_new_arrival'] = $request->has('is_new_arrival') ? (bool)$request->is_new_arrival : false;
        $validated['is_featured'] = $request->has('is_featured') ? (bool)$request->is_featured : false;
        $validated['is_trending'] = $request->has('is_trending') ? (bool)$request->is_trending : false;
        $validated['is_top_rated'] = $request->has('is_top_rated') ? (bool)$request->is_top_rated : false;
        $validated['has_variations'] = $request->has('has_variations') ? (bool)$request->has_variations : false;

        if ($request->hasFile('main_image')) {
            if ($product->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }
            $validated['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product->update($validated);

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $image) {
                $product->images()->create([
                    'image_path' => $image->store('products/gallery', 'public'),
                    'sort_order' => $product->images()->count() + $index,
                ]);
            }
        }

        // Sync related products if provided
        if ($request->filled('related_products')) {

            $relatedIds = $request->related_products;

            // If coming as "3,4"
            if (is_string($relatedIds)) {
                $relatedIds = explode(',', $relatedIds);
            }

            $relatedIds = collect($relatedIds)
                ->map(fn ($id) => (int) trim($id))
                ->unique()
                ->reject(fn ($id) => $id === $product->id) // prevent self relation
                ->values()
                ->toArray();

            $product->relatedProducts()->sync($relatedIds);

        } else {

            $product->relatedProducts()->detach();

        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        if ($product->main_image) {
            Storage::disk('public')->delete($product->main_image);
        }
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully');
    }
}

