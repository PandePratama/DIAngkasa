<?php

namespace App\Http\Controllers;

use App\Models\CreditTransaction;
use App\Models\ProductRaditya; // Pastikan Model ini di-use
use App\Services\CreditCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditTransactionController extends Controller
{
    public function index()
    {
        // Pastikan relasi 'product' di model CreditTransaction mengarah ke ProductRaditya
        $transactions = CreditTransaction::with(['user', 'product', 'installments'])
            ->latest()
            ->paginate(10);

        return view('admin.credits.index', compact('transactions'));
    }

    public function show(CreditTransaction $creditTransaction)
    {
        // Load relasi installments agar bisa ditampilkan di detail
        $creditTransaction->load(['product', 'installments']);
        return view('admin.credits.show', compact('creditTransaction'));
    }

    public function store(Request $request, CreditCalculatorService $service)
    {
        // 1. Validasi Input Khusus ProductRaditya
        $request->validate([
            // PENTING: Ganti 'products' dengan nama tabel asli dari ProductRaditya
            // Biasanya Laravel menamai tabelnya 'product_radityas' (jamak + snake_case)
            'product_id' => 'required|exists:product_radityas,id',
            'dp_amount' => 'required|numeric',
            'tenor' => 'required|in:3,6,9,12',
        ]);

        // Mengambil data spesifik dari Model ProductRaditya
        $product = ProductRaditya::findOrFail($request->product_id);

        return DB::transaction(function () use ($product, $request, $service) {

            // 2. Hitung (Service harus support object ProductRaditya)
            $calc = $service->calculate($product, $request->tenor, $request->dp_amount);

            // 3. Simpan Header Transaksi
            $trx = CreditTransaction::create([
                // Pastikan kolom ini foreign key ke tabel product_radityas
                'product_id' => $product->id,
                'user_id' => auth()->id(),

                // Snapshot Data
                'product_hpp_snapshot' => $calc['product_hpp_snapshot'],
                'product_price_snapshot' => $calc['product_price_snapshot'],
                'dp_amount' => $calc['dp_amount'],
                'tenor' => $calc['tenor'],
                'up_price_percent' => $calc['up_price_percent'],
                'interest_percent' => $calc['interest_percent'],
                'retail_price_value' => $calc['retail_price'],
                'monthly_installment_base' => $calc['monthly_installment'],
                'admin_fee' => 20000,
                'status' => 'active'
            ]);

            // 4. Generate & Simpan Jadwal Cicilan
            $schedules = $service->generateSchedule($calc);

            foreach ($schedules as $sch) {
                $trx->installments()->create([
                    'month_sequence' => $sch['month_sequence'],
                    'due_date' => $sch['due_date'],
                    'amount' => $sch['amount'],
                    'status' => 'unpaid'
                ]);
            }

            return redirect()->route('transactions.show', $trx->id)
                ->with('success', 'Pengajuan Kredit Product Raditya Berhasil!');
        });
    }
}
