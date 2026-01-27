<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Support\CreditPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class CreditService extends ServiceProvider
{
    public static function totalUsed(User $user): int
    {
        [$start, $end] = CreditPeriod::current();

        // 🔹 QR
        $qr = Transaction::where('id_user', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        // 🔹 ORDER CREDIT
        $order = Order::join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.id_user', $user->id)
            ->where('orders.payment_method', 'credit')
            ->whereBetween('orders.created_at', [$start, $end])
            ->sum(DB::raw('order_items.qty * products.price'));

        return $qr + $order;
    }

    public static function remaining($user): int
    {
        $today = Carbon::now();

        // 🔥 PERIODE 16 - 15
        if ($today->day <= 15) {
            $start = $today->copy()->subMonth()->day(16)->startOfDay();
            $end   = $today->copy()->day(15)->endOfDay();
        } else {
            $start = $today->copy()->day(16)->startOfDay();
            $end   = $today->copy()->addMonth()->day(15)->endOfDay();
        }

        // ✅ QR USER SAJA
        $totalQr = Transaction::where('id_user', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        // ✅ ORDER CREDIT USER SAJA
        $totalOrderCredit = Order::join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.id_user', $user->id)
            ->where('orders.payment_method', 'credit')
            ->whereBetween('orders.created_at', [$start, $end])
            ->sum(DB::raw('order_items.qty * products.price'));

        // 🔥 SISA LIMIT = credit_limit AKTUAL
        // ❌ JANGAN KURANGI LAGI
        return (int) $user->credit_limit;
    }
}
