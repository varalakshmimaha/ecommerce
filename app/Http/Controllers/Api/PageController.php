<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'title', 'slug', 'show_in_navbar', 'show_in_footer', 'sort_order']);

        return response()->json(['success' => true, 'data' => $pages]);
    }

    public function show($slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->first();
        if (!$page) {
            return response()->json(['success' => false, 'message' => 'Page not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $page]);
    }
}
