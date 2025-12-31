<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_attribute_id',
        'value',
        'slug',
        'hex_color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    public function variationAttributeValues()
    {
        return $this->hasMany(VariationAttributeValue::class);
    }

    public function variations()
    {
        return $this->belongsToMany(ProductVariation::class, 'variation_attribute_values');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attributeValue) {
            if (empty($attributeValue->slug)) {
                $attributeValue->slug = Str::slug($attributeValue->value);
            }
        });

        static::updating(function ($attributeValue) {
            if (empty($attributeValue->slug)) {
                $attributeValue->slug = Str::slug($attributeValue->value);
            }
        });
    }
}
