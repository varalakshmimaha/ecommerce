<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\VariationAttributeValue;
use Illuminate\Http\Request;

class ProductVariationController extends Controller
{
    public function index(Product $product)
    {
        $variations = $product->variations()
            ->with('attributeValues.attributeValue')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.products.variations.index', compact('product', 'variations'));
    }

    public function create(Product $product)
    {
        $attributes = ProductAttribute::where('is_active', true)
            ->with('activeValues')
            ->orderBy('sort_order')
            ->get();

        return view('admin.products.variations.create', compact('product', 'attributes'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'nullable|string|max:255|unique:product_variations,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'exists:product_attribute_values,id',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['product_id'] = $product->id;

        // Check if this combination of attribute values already exists
        $existingVariation = $this->findVariationByAttributeValues($product, $validated['attribute_values']);
        
        if ($existingVariation) {
            return back()
                ->withErrors(['attribute_values' => 'A variation with these attribute values already exists.'])
                ->withInput();
        }

        $variation = ProductVariation::create($validated);

        // Attach attribute values to the variation
        foreach ($validated['attribute_values'] as $attributeValueId) {
            $attributeValue = ProductAttributeValue::findOrFail($attributeValueId);
            
            VariationAttributeValue::create([
                'variation_id' => $variation->id,
                'attribute_id' => $attributeValue->attribute_id,
                'attribute_value_id' => $attributeValueId,
            ]);
        }

        return redirect()->route('admin.products.variations.index', $product)
            ->with('success', 'Product variation created successfully.');
    }

    public function edit(Product $product, ProductVariation $variation)
    {
        // Ensure the variation belongs to the product
        if ($variation->product_id !== $product->id) {
            abort(404);
        }

        $variation->load('attributeValues.attributeValue');
        
        $attributes = ProductAttribute::where('is_active', true)
            ->with('activeValues')
            ->orderBy('sort_order')
            ->get();

        return view('admin.products.variations.edit', compact('product', 'variation', 'attributes'));
    }

    public function update(Request $request, Product $product, ProductVariation $variation)
    {
        // Ensure the variation belongs to the product
        if ($variation->product_id !== $product->id) {
            abort(404);
        }

        $validated = $request->validate([
            'sku' => 'nullable|string|max:255|unique:product_variations,sku,' . $variation->id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'exists:product_attribute_values,id',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Check if this combination of attribute values already exists (excluding current variation)
        $existingVariation = $this->findVariationByAttributeValues($product, $validated['attribute_values'], $variation->id);
        
        if ($existingVariation) {
            return back()
                ->withErrors(['attribute_values' => 'A variation with these attribute values already exists.'])
                ->withInput();
        }

        $variation->update($validated);

        // Remove existing attribute values
        VariationAttributeValue::where('variation_id', $variation->id)->delete();

        // Attach new attribute values to the variation
        foreach ($validated['attribute_values'] as $attributeValueId) {
            $attributeValue = ProductAttributeValue::findOrFail($attributeValueId);
            
            VariationAttributeValue::create([
                'variation_id' => $variation->id,
                'attribute_id' => $attributeValue->attribute_id,
                'attribute_value_id' => $attributeValueId,
            ]);
        }

        return redirect()->route('admin.products.variations.index', $product)
            ->with('success', 'Product variation updated successfully.');
    }

    public function destroy(Product $product, ProductVariation $variation)
    {
        // Ensure the variation belongs to the product
        if ($variation->product_id !== $product->id) {
            abort(404);
        }

        $variation->delete();

        return redirect()->route('admin.products.variations.index', $product)
            ->with('success', 'Product variation deleted successfully.');
    }

    /**
     * Toggle variation status
     */
    public function toggleStatus(Product $product, ProductVariation $variation)
    {
        // Ensure the variation belongs to the product
        if ($variation->product_id !== $product->id) {
            abort(404);
        }

        $variation->update([
            'is_active' => !$variation->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Variation status updated successfully.',
            'is_active' => $variation->is_active
        ]);
    }

    /**
     * Bulk update variations
     */
    public function bulkUpdate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'variations' => 'required|array',
            'variations.*.id' => 'required|exists:product_variations,id',
            'variations.*.price' => 'nullable|numeric|min:0',
            'variations.*.stock' => 'nullable|integer|min:0',
            'variations.*.is_active' => 'boolean',
        ]);

        foreach ($validated['variations'] as $variationData) {
            $variation = ProductVariation::findOrFail($variationData['id']);
            
            // Ensure the variation belongs to the product
            if ($variation->product_id !== $product->id) {
                continue;
            }

            $updateData = [];
            
            if (isset($variationData['price'])) {
                $updateData['price'] = $variationData['price'];
            }
            
            if (isset($variationData['stock'])) {
                $updateData['stock'] = $variationData['stock'];
            }
            
            if (isset($variationData['is_active'])) {
                $updateData['is_active'] = $variationData['is_active'];
            }

            if (!empty($updateData)) {
                $variation->update($updateData);
            }
        }

        return redirect()->route('admin.products.variations.index', $product)
            ->with('success', 'Variations updated successfully.');
    }

    /**
     * Find variation by attribute values
     */
    private function findVariationByAttributeValues(Product $product, array $attributeValueIds, $excludeVariationId = null)
    {
        $query = $product->variations();

        if ($excludeVariationId) {
            $query->where('id', '!=', $excludeVariationId);
        }

        return $query->whereHas('attributeValues', function ($q) use ($attributeValueIds) {
            $q->whereIn('attribute_value_id', $attributeValueIds);
        }, '=', count($attributeValueIds))->first();
    }
}
