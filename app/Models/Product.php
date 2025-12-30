<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'sub_category_id',
        'brand_id',
        'mrp',
        'selling_price',
        'discounted_price',
        'gst',
        'min_order_quantity',
        'stock_quantity',
        'main_image',
        'short_description',
        'full_description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_new_arrival',
        'is_featured',
        'is_trending',
        'is_top_rated',
        'status',
        'has_variations',
    ];

    protected $casts = [
        'mrp' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'gst' => 'decimal:2',
        'is_new_arrival' => 'boolean',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'is_top_rated' => 'boolean',
        'has_variations' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class)->orderBy('sort_order');
    }

    public function activeVariations()
    {
        return $this->variations()->where('is_active', true);
    }

    public function variationAttributeValues()
    {
        return $this->hasManyThrough(
            VariationAttributeValue::class,
            ProductVariation::class,
            'product_id',
            'variation_id'
        );
    }

    // Product.php model
    public function relatedProducts()
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_product_id')
                    ->withTimestamps();
    }

    // And the inverse relationship if needed
    public function relatedTo()
    {
        return $this->belongsToMany(Product::class, 'related_products', 'related_product_id', 'product_id')
                    ->withTimestamps();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFinalPriceAttribute()
    {
        return $this->discounted_price ?? $this->selling_price;
    }

    /**
     * Check if product has variations
     */
    public function hasVariations()
    {
        return $this->has_variations && $this->activeVariations()->exists();
    }

    /**
     * Get effective price considering variations
     */
    public function getEffectivePriceAttribute()
    {
        if ($this->hasVariations()) {
            $minPriceVariation = $this->activeVariations()
                ->orderBy('price', 'asc')
                ->first();
                
            return $minPriceVariation ? $minPriceVariation->price : $this->final_price;
        }
        
        return $this->final_price;
    }

    /**
     * Get effective stock considering variations
     */
    public function getEffectiveStockAttribute()
    {
        if ($this->hasVariations()) {
            return $this->activeVariations()->sum('stock');
        }
        
        return $this->stock_quantity;
    }

    /**
     * Get variation options for frontend
     */
    public function getVariationOptionsAttribute()
    {
        if (!$this->hasVariations()) {
            return [];
        }

        $options = [];
        
        // Get all attributes used by this product's variations
        $attributeValues = $this->variationAttributeValues()
            ->with('attribute', 'attributeValue')
            ->get();

        foreach ($attributeValues as $attributeValue) {
            $attributeName = $attributeValue->attribute->name;
            $attributeValueData = [
                'id' => $attributeValue->attribute_value_id,
                'value' => $attributeValue->attributeValue->value,
                'slug' => $attributeValue->attributeValue->slug,
            ];
            
            // Add hex code for colors if available
            if ($attributeValue->attributeValue->hex_code) {
                $attributeValueData['hex_code'] = $attributeValue->attributeValue->hex_code;
            }
            
            if (!isset($options[$attributeName])) {
                $options[$attributeName] = [];
            }
            
            if (!in_array($attributeValueData, $options[$attributeName])) {
                $options[$attributeName][] = $attributeValueData;
            }
        }

        return $options;
    }

    /**
     * Find variation by attribute values
     */
    public function findVariationByAttributes(array $attributeValueIds)
    {
        return $this->activeVariations()
            ->whereHas('attributeValues', function ($query) use ($attributeValueIds) {
                $query->whereIn('attribute_value_id', $attributeValueIds);
            }, '=', count($attributeValueIds))
            ->first();
    }

    /**
     * Get price range for variations
     */
    public function getPriceRangeAttribute()
    {
        if (!$this->hasVariations()) {
            return null;
        }

        $prices = $this->activeVariations()->pluck('price');
        
        if ($prices->isEmpty()) {
            return null;
        }

        $minPrice = $prices->min();
        $maxPrice = $prices->max();
        
        if ($minPrice == $maxPrice) {
            return [
                'min' => $minPrice,
                'max' => $maxPrice,
                'display' => '₹' . number_format($minPrice, 2)
            ];
        }
        
        return [
            'min' => $minPrice,
            'max' => $maxPrice,
            'display' => '₹' . number_format($minPrice, 2) . ' – ₹' . number_format($maxPrice, 2)
        ];
    }
}

