<?php

namespace App\Http\Controllers;

use App\Models\Transaction;         // Model Transaksi Tunai
use App\Models\CreditTransaction;   // Model Transaksi Kredit
use App\Models\BalanceMutation;     // <--- TAMBAHKAN INI (Model Mutasi)
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Menampilkan Riwayat Transaksi (Gabungan Tunai, Kredit, & Mutasi)
     */
    public function index(Request $request)
    {
        // Setup Filter Tanggal (Berlaku untuk SEMUA tab)
        $dateRange = null;
        if ($request->filled('from') && $request->filled('to')) {
            $dateRange = [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59'
            ];
        }

        // ==========================================
        // 1. DATA TRANSAKSI REGULER (TUNAI/SALDO)
        // ==========================================
        $query = Transaction::with(['user', 'purchaseType'])->latest();

        if ($dateRange) {
            $query->whereBetween('created_at', $dateRange);
        }

        // Hitung Total Omset (Khusus Tunai/Saldo)
        $grandTotalSemua = $query->sum('grand_total');

        // Pagination Transaksi Biasa (Page name: trx_page)
        $transactions = $query->paginate(10, ['*'], 'trx_page');


        // ==========================================
        // 2. DATA TRANSAKSI KREDIT
        // ==========================================
        $creditQuery = CreditTransaction::with(['user', 'product'])->latest();

        if ($dateRange) {
            $creditQuery->whereBetween('created_at', $dateRange);
        }

        // Pagination Transaksi Kredit (Page name: credit_page)
        $creditTransactions = $creditQuery->paginate(10, ['*'], 'credit_page');


        // ==========================================
        // 3. DATA LOG MUTASI SALDO (BARU)
        // ==========================================
        $mutationQuery = BalanceMutation::with('user')->latest();

        if ($dateRange) {
            $mutationQuery->whereBetween('created_at', $dateRange);
        }

        // Pagination Mutasi (Page name: mutation_page)
        $mutations = $mutationQuery->paginate(15, ['*'], 'mutation_page');


        // ==========================================
        // 4. RETURN KE VIEW
        // ==========================================
        return view('admin.transactions.index', compact(
            'transactions',
            'creditTransactions',
            'mutations', // <--- KIRIM DATA MUTASI KE VIEW
            'grandTotalSemua'
        ));
    }

    /**
     * Halaman Sukses setelah Checkout
     */
    public function success()
    {
        // 1. Cek Session Kredit
        if (session()->has('credit_trx_id')) {
            $transaction = CreditTransaction::with('product')->find(session('credit_trx_id'));
            $type = 'credit';
        }
        // 2. Jika tidak, ambil Transaksi Reguler terbaru
        else {
            $transaction = Transaction::where('id_user', Auth::id())
                ->latest()
                ->first();
            $type = 'regular';
        }

        if (!$transaction) {
            return redirect()->route('home')->with('error', 'Belum ada transaksi yang ditemukan.');
        }

        return view('transaction.success', compact('transaction', 'type'));
    }

    /**
     * Cetak Invoice PDF (Khusus Transaksi Reguler)
     */
    public function printInvoice($id)
    {
        $transaction = Transaction::with(['user.unitKerja', 'items', 'purchaseType'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('admin.transactions.invoice', compact('transaction'));
        $pdf->setPaper('a5', 'portrait');

        return $pdf->stream('Invoice-' . $transaction->invoice_code . '.pdf');
    }
}
