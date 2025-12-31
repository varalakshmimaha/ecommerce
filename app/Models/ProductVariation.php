<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'compare_price',
        'stock_quantity',
        'weight',
        'attributes',
        'images',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'attributes' => 'array',
        'images' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variationAttributeValues()
    {
        return $this->hasMany(VariationAttributeValue::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(ProductAttribute::class, 'variation_attribute_values')
            ->withPivot('attribute_value_id')
            ->withTimestamps();
    }

    public function attributeValues()
    {
        return $this->belongsToMany(ProductAttributeValue::class, 'variation_attribute_values')
            ->withPivot('product_attribute_id')
            ->withTimestamps();
    }

    /**
     * Get the variation title (combination of attributes)
     */
    public function getVariationTitleAttribute()
    {
        $attributes = $this->variationAttributeValues()
            ->with('attributeValue')
            ->get()
            ->map(function ($variationAttributeValue) {
                return $variationAttributeValue->attributeValue->value;
            })
            ->toArray();

        return empty($attributes) ? 'Default' : implode(' / ', $attributes);
    }

    /**
     * Get the variation image
     * If variation has specific image, use it, otherwise use product main image
     */
    public function getVariationImageAttribute()
    {
        $images = $this->images;
        if (!empty($images) && isset($images[0])) {
            return $images[0];
        }

        return $this->product->main_image;
    }

    /**
     * Check if variation is in stock
     */
    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Generate SKU if not set
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variation) {
            if (empty($variation->sku)) {
                $baseSku = $variation->product->sku ?? strtoupper(substr($variation->product->name, 0, 6));
                $attributes = $variation->variationAttributeValues()
                    ->with('attributeValue')
                    ->get()
                    ->take(2)
                    ->map(function ($variationAttributeValue) {
                        return $variationAttributeValue->attributeValue->value;
                    })
                    ->implode('-');
                $variation->sku = $baseSku . ($attributes ? '-' . strtoupper(str_replace(' ', '-', $attributes)) : '');
            }
        });
    }
}
