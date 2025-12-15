<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::orderBy('sort_order')->paginate(20);
        return view('admin.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon')->store('brands', 'public');
        }

        Brand::create($validated);
        return redirect()->back()->with('success', 'Brand created successfully');
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('icon')) {
            if ($brand->icon) {
                Storage::disk('public')->delete($brand->icon);
            }
            $validated['icon'] = $request->file('icon')->store('brands', 'public');
        }

        $brand->update($validated);
        return redirect()->back()->with('success', 'Brand updated successfully');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->icon) {
            Storage::disk('public')->delete($brand->icon);
        }
        $brand->delete();
        return redirect()->back()->with('success', 'Brand deleted successfully');
    }
}
