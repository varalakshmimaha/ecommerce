<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductVariationController extends Controller
{
    public function index(Product $product)
    {
        return view('admin.products.variations.index', compact('product'));
    }

    public function create(Product $product)
    {
        return view('admin.products.variations.create', compact('product'));
    }

    public function store()
    {
        return redirect()->back()->with('success', 'Variation created successfully');
    }

    public function show()
    {
        return 'Show variation';
    }

    public function edit()
    {
        return 'Edit variation';
    }

    public function update()
    {
        return redirect()->back()->with('success', 'Variation updated successfully');
    }

    public function destroy()
    {
        return redirect()->back()->with('success', 'Variation deleted successfully');
    }
}
