<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\FooterSection;
use App\Models\FooterLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'company_name' => Setting::get('company_name'),
            'company_logo' => Setting::get('company_logo'),
            'company_description' => Setting::get('company_description'),
            'whatsapp_number' => Setting::get('whatsapp_number'),
            'phone_number' => Setting::get('phone_number'),
            'email' => Setting::get('email'),
            'address' => Setting::get('address'),
            'qr_code' => Setting::get('qr_code'),
            'bank_name' => Setting::get('bank_name'),
            'bank_account_number' => Setting::get('bank_account_number'),
            'bank_ifsc' => Setting::get('bank_ifsc'),
            'bank_account_holder' => Setting::get('bank_account_holder'),
            'enable_favourites' => Setting::get('enable_favourites', 'false'),
            'enable_compare' => Setting::get('enable_compare', 'false'),
        ];

        $footerSections = FooterSection::with('links')->orderBy('sort_order')->get();

        return view('admin.settings.index', compact('settings', 'footerSections'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_description' => 'nullable|string',
            'whatsapp_number' => 'nullable|string|max:20',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:20',
            'bank_account_holder' => 'nullable|string|max:255',
            'enable_favourites' => 'nullable|in:true,false',
            'enable_compare' => 'nullable|in:true,false',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'company_logo' || $key === 'qr_code') {
                if ($request->hasFile($key)) {
                    $oldValue = Setting::get($key);
                    if ($oldValue) {
                        Storage::disk('public')->delete($oldValue);
                    }
                    Setting::set($key, $request->file($key)->store('settings', 'public'));
                }
            } else {
                Setting::set($key, $value);
            }
        }

        // Checkboxes not submitted = unchecked = false
        if (!$request->has('enable_favourites')) {
            Setting::set('enable_favourites', 'false');
        }
        if (!$request->has('enable_compare')) {
            Setting::set('enable_compare', 'false');
        }

        return redirect()->back()->with('success', 'Settings updated successfully');
    }

    public function storeFooterSection(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        FooterSection::create($validated);
        return redirect()->back()->with('success', 'Footer section created successfully');
    }

    public function updateFooterSection(Request $request, FooterSection $footerSection)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $footerSection->update($validated);
        return redirect()->back()->with('success', 'Footer section updated successfully');
    }

    public function destroyFooterSection(FooterSection $footerSection)
    {
        $footerSection->delete();
        return redirect()->back()->with('success', 'Footer section deleted successfully');
    }

    public function storeFooterLink(Request $request)
    {
        $validated = $request->validate([
            'footer_section_id' => 'required|exists:footer_sections,id',
            'title' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        FooterLink::create($validated);
        return redirect()->back()->with('success', 'Footer link created successfully');
    }

    public function updateFooterLink(Request $request, FooterLink $footerLink)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $footerLink->update($validated);
        return redirect()->back()->with('success', 'Footer link updated successfully');
    }

    public function destroyFooterLink(FooterLink $footerLink)
    {
        $footerLink->delete();
        return redirect()->back()->with('success', 'Footer link deleted successfully');
    }
}

