<?php

namespace App\Http\Controllers\Frontend\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless(in_array($user->role, ['manager', 'rm'], true), 403, 'Only managers and RMs can manage a team.');

        $childRole = $user->role === 'manager' ? 'rm' : 'affiliate';
        $childRoleLabel = $user->role === 'manager' ? 'Relationship Managers' : 'Affiliates';

        $team = User::where('parent_id', $user->id)
            ->where('role', $childRole)
            ->withCount([
                'children as referrals_count',
                'commissions as total_commissions',
            ])
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('frontend.team.index', compact('user', 'team', 'childRole', 'childRoleLabel'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_unless(in_array($user->role, ['manager', 'rm'], true), 403);

        $childRole = $user->role === 'manager' ? 'rm' : 'affiliate';

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:users,mobile',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $data = [
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $childRole,
            'parent_id' => $user->id,
            'is_verified' => true,
        ];

        if ($childRole === 'affiliate') {
            $data['affiliate_status'] = 'approved';
            $data['approved_at'] = now();
            $data['referral_code'] = $this->generateReferralCode($validated['name']);
        } else {
            $data['affiliate_status'] = 'none';
        }

        User::create($data);

        return redirect()->route('team.index')->with('success', ucfirst($childRole) . ' created.');
    }

    public function destroy(Request $request, User $member)
    {
        $user = $request->user();
        abort_unless(in_array($user->role, ['manager', 'rm'], true), 403);
        abort_unless($member->parent_id === $user->id, 403, 'You can only remove your own team members.');

        $member->update(['role' => 'customer', 'parent_id' => null]);

        return redirect()->route('team.index')->with('success', 'Team member removed.');
    }

    private function generateReferralCode(string $name): string
    {
        $base = strtoupper(Str::of($name)->slug('')->substr(0, 3)->padLeft(3, 'X'));
        do {
            $code = $base . strtoupper(Str::random(5));
        } while (User::where('referral_code', $code)->exists());
        return $code;
    }
}
