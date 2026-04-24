<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\CommissionSetting;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    public function generateForOrder(Order $order): int
    {
        if (!$order->user_id) {
            return 0;
        }

        if (Commission::where('order_id', $order->id)->exists()) {
            return 0;
        }

        $buyer = User::find($order->user_id);
        if (!$buyer) {
            return 0;
        }

        $base = (float) $order->subtotal;
        if ($base <= 0) {
            return 0;
        }

        $rows = $buyer->isStaff()
            ? $this->selfPurchaseRows($buyer, $base)
            : $this->referredPurchaseRows($buyer, $base);

        if (empty($rows)) {
            return 0;
        }

        $now = now();
        $payload = array_map(fn ($r) => $r + [
            'order_id' => $order->id,
            'status' => 'pending',
            'created_at' => $now,
            'updated_at' => $now,
        ], $rows);

        return (int) DB::table('commissions')->insert($payload) ? count($payload) : 0;
    }

    public function approveForOrder(Order $order): int
    {
        return Commission::where('order_id', $order->id)
            ->where('status', 'pending')
            ->update(['status' => 'approved', 'updated_at' => now()]);
    }

    public function reverseForOrder(Order $order): int
    {
        return Commission::where('order_id', $order->id)
            ->whereIn('status', ['pending', 'approved'])
            ->update(['status' => 'reversed', 'updated_at' => now()]);
    }

    private function selfPurchaseRows(User $buyer, float $base): array
    {
        $rate = CommissionSetting::rateFor($buyer->role);
        if ($rate <= 0) {
            return [];
        }
        return [[
            'beneficiary_user_id' => $buyer->id,
            'beneficiary_role' => $buyer->role,
            'base_amount' => $base,
            'percentage' => $rate,
            'amount' => round($base * $rate / 100, 2),
            'notes' => 'Self-purchase',
        ]];
    }

    private function referredPurchaseRows(User $buyer, float $base): array
    {
        $chain = $buyer->uplineByRole();
        if (empty($chain)) {
            return [];
        }

        $rows = [];
        foreach (['affiliate', 'rm', 'manager'] as $role) {
            if (!isset($chain[$role])) continue;
            $rate = CommissionSetting::rateFor($role);
            if ($rate <= 0) continue;
            $rows[] = [
                'beneficiary_user_id' => $chain[$role]->id,
                'beneficiary_role' => $role,
                'base_amount' => $base,
                'percentage' => $rate,
                'amount' => round($base * $rate / 100, 2),
                'notes' => null,
            ];
        }
        return $rows;
    }
}
