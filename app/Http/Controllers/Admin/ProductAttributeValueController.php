<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;

class ProductAttributeValueController extends Controller
{
    public function index(Request $request)
    {
        $attributeId = $request->get('attribute_id');
        
        if ($attributeId) {
            $attribute = ProductAttribute::findOrFail($attributeId);
            $attributeValues = $attribute->values()->orderBy('sort_order')->paginate(20);
        } else {
            $attributeValues = ProductAttributeValue::with('attribute')
                ->orderBy('product_attribute_id')
                ->orderBy('sort_order')
                ->paginate(20);
        }
        
        $attributes = ProductAttribute::where('is_active', true)->get();

        dd($attributeValues);
        
        return view('admin.product-attribute-values.index', compact('attributeValues', 'attributes', 'attributeId'));
    }

    public function create()
    {
        $attributes = ProductAttribute::where('is_active', true)->get();
        return view('admin.product-attribute-values.create', compact('attributes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_attribute_id' => 'required|exists:product_attributes,id',
            'value' => 'required|string|max:255',
            'hex_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Check for duplicate value within the same attribute
        $existing = ProductAttributeValue::where('product_attribute_id', $validated['product_attribute_id'])
            ->where('value', $validated['value'])
            ->first();

        if ($existing) {
            return back()->withErrors(['value' => 'This value already exists for this attribute.'])->withInput();
        }

        ProductAttributeValue::create($validated);

        return redirect()->route('admin.product-attribute-values.index')
            ->with('success', 'Product attribute value created successfully.');
    }

    public function edit(ProductAttributeValue $productAttributeValue)
    {
        $attributes = ProductAttribute::where('is_active', true)->get();
        return view('admin.product-attribute-values.edit', compact('productAttributeValue', 'attributes'));
    }

    public function update(Request $request, ProductAttributeValue $productAttributeValue)
    {
        $validated = $request->validate([
            'product_attribute_id' => 'required|exists:product_attributes,id',
            'value' => 'required|string|max:255',
            'hex_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Check for duplicate value within the same attribute (excluding current record)
        $existing = ProductAttributeValue::where('product_attribute_id', $validated['product_attribute_id'])
            ->where('value', $validated['value'])
            ->where('id', '!=', $productAttributeValue->id)
            ->first();

        if ($existing) {
            return back()->withErrors(['value' => 'This value already exists for this attribute.'])->withInput();
        }

        $productAttributeValue->update($validated);

        return redirect()->route('admin.product-attribute-values.index')
            ->with('success', 'Attribute value updated successfully.');
    }

    public function destroy(ProductAttributeValue $productAttributeValue)
    {
        // Check if value is used by any variations
        if ($productAttributeValue->variationAttributeValues()->exists()) {
            return back()->with('error', 'Cannot delete value that is used by product variations.');
        }

        $attributeId = $productAttributeValue->product_attribute_id;
        $productAttributeValue->delete();

        return redirect()->route('admin.product-attribute-values.index', ['attribute_id' => $attributeId])
            ->with('success', 'Attribute value deleted successfully.');
    }

    /**
     * Toggle value status
     */
    public function toggleStatus(ProductAttributeValue $productAttributeValue)
    {
        $productAttributeValue->update([
            'is_active' => !$productAttributeValue->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Value status updated successfully.',
            'is_active' => $productAttributeValue->is_active
        ]);
    }
}
