<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'show_in_navbar',
        'show_in_footer',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'show_in_navbar' => 'boolean',
        'show_in_footer' => 'boolean',
        'is_active' => 'boolean',
    ];
}
