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
            ->with('variationAttributeValues.attributeValue.attribute')
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
            'price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'variation_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_image_id' => 'nullable|exists:product_images,id',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'exists:product_attribute_values,id',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['product_id'] = $product->id;

        // Handle image upload or gallery selection
        $images = [];
        if ($request->hasFile('variation_image')) {
            $images[] = $request->file('variation_image')->store('product_variations', 'public');
        } elseif ($request->filled('gallery_image_id')) {
            $galleryImage = $product->images()->findOrFail($request->gallery_image_id);
            $images[] = $galleryImage->image_path;
        }

        if (!empty($images)) {
            $validated['images'] = $images;
        }

        // Use product price if variation price not provided
        if (empty($validated['price'])) {
            $validated['price'] = $product->selling_price;
        }

        // Use product stock if variation stock not provided
        if (empty($validated['stock_quantity'])) {
            $validated['stock_quantity'] = $product->stock_quantity;
        }

        // Generate variation title based on selected attributes
        $variationTitle = $this->generateVariationTitle($validated['attribute_values']);

        // Check if this combination of attribute values already exists
        $existingVariation = $this->findVariationByAttributeValues($product, $validated['attribute_values']);

        if ($existingVariation) {
            return back()
                ->withErrors(['attribute_values' => 'A variation with these attribute values already exists.'])
                ->withInput();
        }

        // Remove non-fillable fields before creating variation
        $attributeValues = $validated['attribute_values'];
        unset($validated['attribute_values']);

        $variation = ProductVariation::create($validated);

        // Attach attribute values to the variation
        foreach ($attributeValues as $attributeValueId) {
            $attributeValue = ProductAttributeValue::findOrFail($attributeValueId);

            VariationAttributeValue::create([
                'variation_id' => $variation->id,
                'product_attribute_id' => $attributeValue->product_attribute_id,
                'attribute_value_id' => $attributeValueId,
            ]);
        }

        // Set as default variation if requested
        if ($validated['is_default']) {
            // Remove default flag from other variations
            $product->variations()->where('id', '!=', $variation->id)->update(['is_default' => false]);
        }

        // Automatically enable has_variations flag on the product
        if (!$product->has_variations) {
            $product->update(['has_variations' => true]);
        }

        return redirect()->route('admin.products.variations.index', $product)
            ->with('success', "Product variation '{$variationTitle}' created successfully.");
    }

    /**
     * Generate variation title based on selected attribute values
     */
    private function generateVariationTitle($attributeValueIds)
    {
        $attributeValues = ProductAttributeValue::with('attribute')
            ->whereIn('id', $attributeValueIds)
            ->get()
            ->sortBy(function ($value) {
                return $value->attribute->sort_order;
            });

        $titleParts = [];
        foreach ($attributeValues as $value) {
            $titleParts[] = $value->value;
        }

        return implode(' - ', $titleParts);
    }

    public function edit(Product $product, ProductVariation $variation)
    {
        // Ensure the variation belongs to the product
        if ($variation->product_id !== $product->id) {
            abort(404);
        }

        $variation->load('variationAttributeValues.attributeValue');

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
            'price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'variation_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_image_id' => 'nullable|exists:product_images,id',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'exists:product_attribute_values,id',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Handle image upload or gallery selection
        $images = $variation->images ?? [];
        if ($request->hasFile('variation_image')) {
            $images = [$request->file('variation_image')->store('product_variations', 'public')];
        } elseif ($request->filled('gallery_image_id')) {
            $galleryImage = $product->images()->findOrFail($request->gallery_image_id);
            $images = [$galleryImage->image_path];
        }

        // Remove current image if requested
        if ($request->has('remove_current_image') && $request->input('remove_current_image') == '1') {
            if (!empty($variation->images)) {
                foreach ($variation->images as $image) {
                    \Storage::disk('public')->delete($image);
                }
                $images = [];
            }
        }

        $validated['images'] = $images;

        // Check if this combination of attribute values already exists (excluding current variation)
        $existingVariation = $this->findVariationByAttributeValues($product, $validated['attribute_values'], $variation->id);

        if ($existingVariation) {
            return back()
                ->withErrors(['attribute_values' => 'A variation with these attribute values already exists.'])
                ->withInput();
        }

        // Remove non-fillable fields before updating variation
        $attributeValues = $validated['attribute_values'];
        unset($validated['attribute_values']);

        $variation->update($validated);

        // Remove existing attribute values
        VariationAttributeValue::where('variation_id', $variation->id)->delete();

        // Attach new attribute values to the variation
        foreach ($attributeValues as $attributeValueId) {
            $attributeValue = ProductAttributeValue::findOrFail($attributeValueId);

            VariationAttributeValue::create([
                'variation_id' => $variation->id,
                'product_attribute_id' => $attributeValue->product_attribute_id,
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
     * Set variation as default
     */
    public function setDefault(Product $product, ProductVariation $variation)
    {
        // Ensure the variation belongs to the product
        if ($variation->product_id !== $product->id) {
            abort(404);
        }

        // Remove default flag from all other variations
        $product->variations()->update(['is_default' => false]);

        // Set this variation as default
        $variation->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default variation set successfully.',
            'is_default' => true
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
