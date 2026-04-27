<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\CommissionSetting;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletTransaction;
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

    public function backfillForOrder(Order $order): int
    {
        if (!$order->user_id) return 0;

        $buyer = User::find($order->user_id);
        if (!$buyer) return 0;

        $base = (float) $order->subtotal;
        if ($base <= 0) return 0;

        $existingRoles = Commission::where('order_id', $order->id)
            ->pluck('beneficiary_role')->toArray();

        $rows = $buyer->isStaff()
            ? $this->selfPurchaseRows($buyer, $base)
            : $this->referredPurchaseRows($buyer, $base);

        $rows = array_values(array_filter($rows, fn($r) => !in_array($r['beneficiary_role'], $existingRoles)));

        if (empty($rows)) return 0;

        $now = now();
        $payload = array_map(fn($r) => $r + [
            'order_id'   => $order->id,
            'status'     => 'pending',
            'created_at' => $now,
            'updated_at' => $now,
        ], $rows);

        $inserted = DB::table('commissions')->insert($payload) ? count($payload) : 0;

        // If the order is already delivered, immediately approve the backfilled commissions
        if ($inserted > 0 && $order->order_status === 'delivered') {
            $this->approveForOrder($order);
        }

        return $inserted;
    }

    public function approveForOrder(Order $order): int
    {
        $commissions = Commission::where('order_id', $order->id)
            ->where('status', 'pending')
            ->get();

        if ($commissions->isEmpty()) {
            return 0;
        }

        DB::transaction(function () use ($commissions, $order) {
            foreach ($commissions as $commission) {
                WalletTransaction::create([
                    'user_id'    => $commission->beneficiary_user_id,
                    'type'       => 'credit',
                    'amount'     => $commission->amount,
                    'remark'     => 'Commission from order #' . $order->order_number,
                    'created_by' => null,
                    'status'     => 'approved',
                ]);
                $commission->update(['status' => 'approved', 'updated_at' => now()]);
            }
        });

        return $commissions->count();
    }

    public function reverseForOrder(Order $order): int
    {
        $commissions = Commission::where('order_id', $order->id)
            ->whereIn('status', ['pending', 'approved'])
            ->get();

        if ($commissions->isEmpty()) {
            return 0;
        }

        DB::transaction(function () use ($commissions, $order) {
            foreach ($commissions as $commission) {
                if ($commission->status === 'approved') {
                    WalletTransaction::create([
                        'user_id'    => $commission->beneficiary_user_id,
                        'type'       => 'debit',
                        'amount'     => $commission->amount,
                        'remark'     => 'Commission reversed (order #' . $order->order_number . ' cancelled)',
                        'created_by' => null,
                        'status'     => 'approved',
                    ]);
                }
                $commission->update(['status' => 'reversed', 'updated_at' => now()]);
            }
        });

        return $commissions->count();
    }

    private function selfPurchaseRows(User $buyer, float $base): array
    {
        $rows = [];

        // Commission for the staff member on their own purchase
        $rate = CommissionSetting::rateFor($buyer->role);
        if ($rate > 0) {
            $rows[] = [
                'beneficiary_user_id' => $buyer->id,
                'beneficiary_role'    => $buyer->role,
                'base_amount'         => $base,
                'percentage'          => $rate,
                'amount'              => round($base * $rate / 100, 2),
                'notes'               => 'Self-purchase',
            ];
        }

        // Upline RM and manager also earn on self-purchases
        $chain = $buyer->uplineByRole();
        foreach (['rm', 'manager'] as $role) {
            if (!isset($chain[$role])) continue;
            $uplineRate = CommissionSetting::rateFor($role);
            if ($uplineRate <= 0) continue;
            $rows[] = [
                'beneficiary_user_id' => $chain[$role]->id,
                'beneficiary_role'    => $role,
                'base_amount'         => $base,
                'percentage'          => $uplineRate,
                'amount'              => round($base * $uplineRate / 100, 2),
                'notes'               => 'Self-purchase upline',
            ];
        }

        return $rows;
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
