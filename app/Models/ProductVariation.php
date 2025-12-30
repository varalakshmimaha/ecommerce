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
        'stock',
        'weight',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        return $this->hasMany(VariationAttributeValue::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(ProductAttribute::class, 'variation_attribute_values')
            ->withPivot('attribute_value_id')
            ->withTimestamps();
    }

    public function attributeValuesRelation()
    {
        return $this->belongsToMany(ProductAttributeValue::class, 'variation_attribute_values')
            ->withPivot('attribute_id')
            ->withTimestamps();
    }

    /**
     * Get the variation title (combination of attributes)
     */
    public function getVariationTitleAttribute()
    {
        $attributes = $this->attributeValues()
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
        if ($this->image) {
            return $this->image;
        }

        return $this->product->main_image;
    }

    /**
     * Check if variation is in stock
     */
    public function isInStock()
    {
        return $this->stock > 0;
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
                $attributes = $variation->attributeValues()
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
