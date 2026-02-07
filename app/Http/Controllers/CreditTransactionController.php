<?php

namespace App\Http\Controllers;

use App\Models\BalanceMutation;
use App\Models\CreditTransaction;
use App\Models\CreditInstallment;
use App\Models\ProductRaditya;
use App\Services\CreditCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreditTransactionController extends Controller
{
    /**
     * MENAMPILKAN HALAMAN MANAJEMEN KREDIT
     */
    public function index(Request $request)
    {
        // -----------------------------------------------------------
        // 1. DATA KREDIT: SEDANG BERJALAN
        // Filter: Status 'progress'
        // -----------------------------------------------------------
        $creditsOngoing = CreditTransaction::with(['user', 'product', 'installments'])
            ->where('status', 'progress')
            ->latest()
            ->paginate(10, ['*'], 'ongoing_page');

        // -----------------------------------------------------------
        // 2. DATA KREDIT: SUDAH LUNAS
        // Filter: Status 'paid' atau 'complete'
        // -----------------------------------------------------------
        $creditsCompleted = CreditTransaction::with(['user', 'product'])
            ->whereIn('status', ['paid', 'complete'])
            ->latest()
            ->paginate(10, ['*'], 'completed_page');

        // -----------------------------------------------------------
        // 3. DATA MUTASI (LOG)
        // -----------------------------------------------------------
        $mutations = BalanceMutation::with('user')
            ->where(function ($query) {
                $query->where('description', 'like', '%Kredit%')
                    ->orWhere('description', 'like', '%Autodebet%')
                    ->orWhere('description', 'like', '%DP%')
                    ->orWhere('description', 'like', '%Angsuran%');
            })
            ->latest()
            ->paginate(15, ['*'], 'mutations_page');

        return view('admin.credits.index', compact(
            'creditsOngoing',
            'creditsCompleted',
            'mutations'
        ));
    }

    /**
     * MENAMPILKAN DETAIL
     */
    public function show($id)
    {
        // Opsional: Redirect ke Profile User seperti request sebelumnya
        // return redirect()->route('profile.index', ['tab' => 'credit']) . '#credit-' . $id;

        // Atau tetap tampilkan view admin detail
        $creditTransaction = CreditTransaction::with(['product', 'user', 'installments'])->findOrFail($id);
        return view('admin.credits.show', compact('creditTransaction'));
    }

    /**
     * MEMBUAT KREDIT BARU (MANUAL OLEH ADMIN)
     * Logic ini disesuaikan agar sama dengan PaymentController (DP 0 = Bulan 1 Lunas)
     */
    public function store(Request $request, CreditCalculatorService $service)
    {
        $request->validate([
            'product_id' => 'required|exists:product_diraditya,id',
            'dp_amount'  => 'required', // Nanti dibersihkan dari titik
            'tenor'      => 'required|in:3,6,9,12',
        ]);

        $product = ProductRaditya::findOrFail($request->product_id);

        // Bersihkan DP
        $rawDP = str_replace('.', '', $request->dp_amount);
        $dpAmount = is_numeric($rawDP) ? (float)$rawDP : 0;

        return DB::transaction(function () use ($product, $request, $service, $dpAmount) {
            // 1. Hitung Kalkulasi
            $calc = $service->calculate($product, $request->tenor, $dpAmount);
            $isNoDp = ($calc['dp_amount'] <= 0);

            // 2. Simpan Header Transaksi
            $trx = CreditTransaction::create([
                'id_product'        => $product->id,
                'id_user'           => auth()->id(), // Atau ambil dari request jika admin input untuk user lain
                'tenor'             => $calc['tenor'],
                'dp_amount'         => $calc['dp_amount'],
                'up_price'          => $calc['up_price_percent'],
                'monthly_amount'    => $calc['monthly_installment'],
                'admin_fee'         => 20000,
                'status'            => 'progress',
                'total_paid_month'  => $isNoDp ? 1 : 0, // LOGIC BARU: DP 0 = Bulan 1 Lunas
            ]);

            // 3. Generate Jadwal
            $schedules = $service->generateSchedule($calc);

            foreach ($schedules as $sch) {
                $bulanKe = $sch['month_sequence'];
                $statusCicilan = 'unpaid';
                $jatuhTempo = null;
                $updatedAt = null;

                // LOGIC STATUS & JATUH TEMPO (SAMA DENGAN PAYMENT CONTROLLER)
                if ($isNoDp) {
                    if ($bulanKe == 1) {
                        $statusCicilan = 'paid';
                        $jatuhTempo = Carbon::now(); // Lunas hari ini
                        $updatedAt = now();
                    } else {
                        // Bulan 2 maju 1 langkah
                        $jatuhTempo = Carbon::now()->addMonths($bulanKe - 1)->setDay(25);
                    }
                } else {
                    // Normal (Ada DP)
                    $jatuhTempo = Carbon::now()->addMonths($bulanKe)->setDay(25);
                }

                $trx->installments()->create([
                    'id_user'           => auth()->id(),
                    'installment_month' => $bulanKe,
                    'due_date'          => $jatuhTempo,
                    'amount'            => abs($sch['amount']),
                    'status'            => $statusCicilan,
                    'updated_at'        => $updatedAt,
                    'admin_fee'         => 0,
                    'balance_before'    => 0,
                    'balance_after'     => 0
                ]);
            }

            return redirect()->route('credits.index')
                ->with('success', 'Pengajuan Kredit Manual Berhasil!');
        });
    }
}
