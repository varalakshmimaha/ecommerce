<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('mobile', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'mobile'      => 'required|string|max:15|unique:users,mobile',
            'email'       => 'nullable|email|unique:users,email',
            'password'    => 'required|string|min:6',
            'role'        => 'required|string|max:50',
            'permissions' => 'nullable|array',
        ]);

        $isAdmin = strtolower($validated['role']) === 'admin';

        User::create([
            'name'        => $validated['name'],
            'mobile'      => $validated['mobile'],
            'email'       => $validated['email'] ?? null,
            'password'    => Hash::make($validated['password']),
            'role'        => $isAdmin ? 'admin' : strtolower($validated['role']),
            'is_admin'    => $isAdmin,
            'permissions' => $isAdmin ? null : ($validated['permissions'] ?? []),
            'is_verified' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User created. They can log in with mobile: {$validated['mobile']} and the password you set.");
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'mobile'      => 'required|string|max:15|unique:users,mobile,' . $user->id,
            'email'       => 'nullable|email|unique:users,email,' . $user->id,
            'password'    => 'nullable|string|min:6',
            'role'        => 'required|string|max:50',
            'permissions' => 'nullable|array',
        ]);

        $isAdmin = strtolower($validated['role']) === 'admin';

        $data = [
            'name'        => $validated['name'],
            'mobile'      => $validated['mobile'],
            'email'       => $validated['email'] ?? null,
            'role'        => $isAdmin ? 'admin' : strtolower($validated['role']),
            'is_admin'    => $isAdmin,
            'permissions' => $isAdmin ? null : ($validated['permissions'] ?? []),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', "User updated successfully.");
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', "User deleted.");
    }
}
