<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;

class ProductVariationController extends Controller
{
    /**
     * Get product variations with attributes
     */
    public function index(Product $product)
    {
        if (!$product->has_variations) {
            return response()->json([
                'success' => false,
                'message' => 'This product does not have variations'
            ], 404);
        }

        $variations = $product->activeVariations()
            ->with('attributeValues.attributeValue')
            ->get()
            ->map(function ($variation) {
                return [
                    'id' => $variation->id,
                    'sku' => $variation->sku,
                    'price' => $variation->price,
                    'stock' => $variation->stock,
                    'weight' => $variation->weight,
                    'image' => $variation->variation_image,
                    'is_active' => $variation->is_active,
                    'is_in_stock' => $variation->isInStock(),
                    'title' => $variation->variation_title,
                    'attributes' => $variation->attributeValues->map(function ($attributeValue) {
                        return [
                            'attribute_id' => $attributeValue->attribute_id,
                            'attribute_name' => $attributeValue->attribute->name,
                            'attribute_value_id' => $attributeValue->attribute_value_id,
                            'attribute_value' => $attributeValue->attributeValue->value,
                            'hex_code' => $attributeValue->attributeValue->hex_code,
                        ];
                    }),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'has_variations' => $product->has_variations,
                    'effective_price' => $product->effective_price,
                    'effective_stock' => $product->effective_stock,
                    'price_range' => $product->price_range,
                ],
                'variations' => $variations,
                'variation_options' => $product->variation_options,
            ]
        ]);
    }

    /**
     * Find variation by selected attributes
     */
    public function findByAttributes(Request $request, Product $product)
    {
        if (!$product->has_variations) {
            return response()->json([
                'success' => false,
                'message' => 'This product does not have variations'
            ], 404);
        }

        $validated = $request->validate([
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'exists:product_attribute_values,id',
        ]);

        $variation = $product->findVariationByAttributes($validated['attribute_values']);

        if (!$variation) {
            return response()->json([
                'success' => false,
                'message' => 'No variation found with selected attributes',
                'available_combinations' => $this->getAvailableVariationCombinations($product, $validated['attribute_values'])
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'variation' => [
                    'id' => $variation->id,
                    'sku' => $variation->sku,
                    'price' => $variation->price,
                    'stock' => $variation->stock,
                    'weight' => $variation->weight,
                    'image' => $variation->variation_image,
                    'is_active' => $variation->is_active,
                    'is_in_stock' => $variation->isInStock(),
                    'title' => $variation->variation_title,
                ],
                'product' => [
                    'effective_price' => $product->effective_price,
                    'effective_stock' => $product->effective_stock,
                ]
            ]
        ]);
    }

    /**
     * Get available attribute combinations
     */
    public function getAvailableCombinations(Product $product, Request $request)
    {
        if (!$product->has_variations) {
            return response()->json([
                'success' => false,
                'message' => 'This product does not have variations'
            ], 404);
        }

        $selectedAttributes = $request->get('selected_attributes', []);
        
        $availableOptions = $product->variation_options;
        $availableCombinations = [];

        // Get all active variations with their attributes
        $variations = $product->activeVariations()
            ->with('attributeValues.attributeValue')
            ->get();

        foreach ($variations as $variation) {
            $attributeValues = $variation->attributeValues->pluck('attribute_value_id')->toArray();
            $isAvailable = true;

            // Check if this variation matches selected attributes
            foreach ($selectedAttributes as $selectedAttributeId) {
                if (!in_array($selectedAttributeId, $attributeValues)) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $availableCombinations[] = [
                    'variation_id' => $variation->id,
                    'attribute_values' => $attributeValues,
                    'price' => $variation->price,
                    'stock' => $variation->stock,
                    'is_in_stock' => $variation->isInStock(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'available_options' => $availableOptions,
                'available_combinations' => $availableCombinations,
                'selected_attributes' => $selectedAttributes,
            ]
        ]);
    }

    /**
     * Get product attributes and values
     */
    public function getAttributes(Product $product)
    {
        if (!$product->has_variations) {
            return response()->json([
                'success' => false,
                'message' => 'This product does not have variations'
            ], 404);
        }

        $attributes = ProductAttribute::where('is_active', true)
            ->whereHas('values', function ($query) use ($product) {
                $query->whereHas('variationAttributeValues', function ($q) use ($product) {
                    $q->whereHas('variation', function ($q2) use ($product) {
                        $q2->where('product_id', $product->id)->where('is_active', true);
                    });
                });
            })
            ->with(['activeValues' => function ($query) use ($product) {
                $query->whereHas('variationAttributeValues', function ($q) use ($product) {
                    $q->whereHas('variation', function ($q2) use ($product) {
                        $q2->where('product_id', $product->id)->where('is_active', true);
                    });
                });
            }])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($attribute) {
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'slug' => $attribute->slug,
                    'description' => $attribute->description,
                    'values' => $attribute->activeValues->map(function ($value) {
                        return [
                            'id' => $value->id,
                            'value' => $value->value,
                            'slug' => $value->slug,
                            'hex_code' => $value->hex_code,
                        ];
                    }),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $attributes
        ]);
    }

    /**
     * Check stock for specific variation
     */
    public function checkStock(Product $product, ProductVariation $variation)
    {
        // Ensure variation belongs to product
        if ($variation->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Variation does not belong to this product'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'variation_id' => $variation->id,
                'stock' => $variation->stock,
                'is_in_stock' => $variation->isInStock(),
                'is_active' => $variation->is_active,
            ]
        ]);
    }

    /**
     * Get price range for variations
     */
    public function getPriceRange(Product $product)
    {
        if (!$product->has_variations) {
            return response()->json([
                'success' => false,
                'message' => 'This product does not have variations'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'price_range' => $product->price_range,
                'effective_price' => $product->effective_price,
                'effective_stock' => $product->effective_stock,
            ]
        ]);
    }

    /**
     * Helper method to get available combinations
     */
    private function getAvailableVariationCombinations(Product $product, array $selectedAttributeIds)
    {
        $variations = $product->activeVariations()
            ->with('attributeValues')
            ->get();

        $combinations = [];
        
        foreach ($variations as $variation) {
            $attributeValueIds = $variation->attributeValues->pluck('attribute_value_id')->toArray();
            
            // Check if this variation contains any of the selected attributes
            $hasSelectedAttribute = false;
            foreach ($selectedAttributeIds as $selectedId) {
                if (in_array($selectedId, $attributeValueIds)) {
                    $hasSelectedAttribute = true;
                    break;
                }
            }
            
            if ($hasSelectedAttribute) {
                $combinations[] = [
                    'variation_id' => $variation->id,
                    'attribute_values' => $attributeValueIds,
                    'title' => $variation->variation_title,
                    'price' => $variation->price,
                    'stock' => $variation->stock,
                    'is_in_stock' => $variation->isInStock(),
                ];
            }
        }
        
        return $combinations;
    }
}
