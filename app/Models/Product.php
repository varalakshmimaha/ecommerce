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

    public function relatedProducts()
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFinalPriceAttribute()
    {
        return $this->discounted_price ?? $this->selling_price;
    }
}

