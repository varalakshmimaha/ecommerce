<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    public function index()
    {
        $attributes = ProductAttribute::withCount('activeValues')
            ->orderBy('sort_order')
            ->paginate(20);
        
        return view('admin.product-attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.product-attributes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        ProductAttribute::create($validated);

        return redirect()->route('admin.product-attributes.index')
            ->with('success', 'Product attribute created successfully.');
    }

    public function show(ProductAttribute $productAttribute)
    {
        $productAttribute->load('activeValues');
        return view('admin.product-attributes.show', compact('productAttribute'));
    }

    public function edit(ProductAttribute $productAttribute)
    {
        return view('admin.product-attributes.edit', compact('productAttribute'));
    }

    public function update(Request $request, ProductAttribute $productAttribute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name,' . $productAttribute->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $productAttribute->update($validated);

        return redirect()->route('admin.product-attributes.index')
            ->with('success', 'Product attribute updated successfully.');
    }

    public function destroy(ProductAttribute $productAttribute)
    {
        // Check if attribute is used by any variations
        if ($productAttribute->variationAttributeValues()->exists()) {
            return redirect()->route('admin.product-attributes.index')
                ->with('error', 'Cannot delete attribute that is used by product variations.');
        }

        $productAttribute->delete();

        return redirect()->route('admin.product-attributes.index')
            ->with('success', 'Product attribute deleted successfully.');
    }

    /**
     * Toggle attribute status
     */
    public function toggleStatus(ProductAttribute $productAttribute)
    {
        $productAttribute->update([
            'is_active' => !$productAttribute->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attribute status updated successfully.',
            'is_active' => $productAttribute->is_active
        ]);
    }
}
