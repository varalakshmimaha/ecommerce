<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        return view('frontend.categories.show', [
            'slug' => $category->slug
        ]);
    }
}
