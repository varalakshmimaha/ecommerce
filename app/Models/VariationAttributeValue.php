<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariation;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;

class VariationAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'variation_id',
        'product_attribute_id',
        'attribute_value_id',
    ];

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    public function attributeValue()
    {
        return $this->belongsTo(ProductAttributeValue::class, 'attribute_value_id');
    }
}
