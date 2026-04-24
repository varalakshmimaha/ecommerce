<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionSetting;
use Illuminate\Http\Request;

class CommissionSettingController extends Controller
{
    public function index()
    {
        $rows = CommissionSetting::orderByRaw("FIELD(role,'affiliate','rm','manager')")->get()->keyBy('role');
        foreach (['affiliate', 'rm', 'manager'] as $role) {
            if (!$rows->has($role)) {
                $rows[$role] = CommissionSetting::create([
                    'role' => $role, 'percentage' => 0, 'is_active' => true,
                ]);
            }
        }
        return view('admin.commission-settings.index', compact('rows'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.percentage' => 'required|numeric|min:0|max:100',
            'settings.*.is_active' => 'nullable|boolean',
        ]);

        foreach ($validated['settings'] as $role => $data) {
            if (!in_array($role, ['affiliate', 'rm', 'manager'], true)) continue;
            CommissionSetting::updateOrCreate(
                ['role' => $role],
                [
                    'percentage' => $data['percentage'],
                    'is_active' => (bool) ($data['is_active'] ?? false),
                    'updated_by' => $request->user()?->id,
                ]
            );
        }

        return redirect()->route('admin.commission-settings.index')->with('success', 'Commission rates updated.');
    }
}
