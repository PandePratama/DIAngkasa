<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreditCalculator extends Controller
{
    public function store(Request $request, CreditCalculatorService $creditService)
    {
        $product = Product::findOrFail($request->product_id);

        // 1. Hitung Angka-angka
        try {
            $calc = $creditService->calculate(
                $product,
                $request->tenor,
                $request->dp_amount
            );
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }

        // 2. Simpan Header Transaksi
        $trx = CreditTransaction::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'product_hpp_snapshot' => $product->hpp, // Simpan state HPP saat ini
            'product_price_snapshot' => $product->selling_price, // Simpan state Harga saat ini
            'dp_amount' => $calc['dp_paid'],
            'up_price_percent' => $calc['up_price_percent'],
            'interest_percent' => $calc['interest_percent'],
            'tenor' => $calc['tenor'],
            'retail_price_value' => $calc['retail_price'],
            'monthly_installment_base' => $calc['monthly_installment'],
            'admin_fee' => $calc['admin_fee'],
        ]);

        // 3. Generate & Simpan Jadwal Cicilan (Installments)
        $schedules = $creditService->generateSchedule($calc, now());

        foreach ($schedules as $sch) {
            $trx->installments()->create([
                'month_sequence' => $sch['month_sequence'],
                'due_date' => $sch['due_date'],
                'amount' => $sch['amount'], // Ini sudah otomatis handle +20rb di bulan 1
                'status' => 'unpaid'
            ]);
        }

        return redirect()->route('transactions.show', $trx->id);
    }
}
