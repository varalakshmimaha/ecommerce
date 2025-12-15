<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    public function index()
    {
        $attributes = ProductAttribute::paginate(20);
        return view('admin.product-attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.product-attributes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'attribute_value' => 'required|string|max:255|unique:product_attributes,attribute_value',
            'attribute_name' => 'required|in:color,size,other',
            'price_adjustment' => 'nullable|numeric',
            'stock_adjustment' => 'nullable|integer',
        ]);

        ProductAttribute::create($validated);

        return redirect()->route('admin.product-attributes.index')
            ->with('success', 'Product attribute created successfully.');
    }

    public function edit(ProductAttribute $productAttribute)
    {
        return view('admin.product-attributes.edit', compact('productAttribute'));
    }

    public function update(Request $request, ProductAttribute $productAttribute)
    {
        $validated = $request->validate([
            'attribute_value' => 'required|string|max:255|unique:product_attributes,attribute_value,' . $productAttribute->id,
            'attribute_name' => 'required|in:color,size,other',
            'price_adjustment' => 'nullable|numeric',
            'stock_adjustment' => 'nullable|integer',
        ]);

        $productAttribute->update($validated);

        return redirect()->route('admin.product-attributes.index')
            ->with('success', 'Product attribute updated successfully.');
    }

    public function destroy(ProductAttribute $productAttribute)
    {
        $productAttribute->delete();

        return redirect()->route('admin.product-attributes.index')
            ->with('success', 'Product attribute deleted successfully.');
    }
}
