<?php

namespace App\Http\Controllers;

use App\Models\Transaction;         // Model Transaksi Tunai
use App\Models\CreditTransaction;   // Model Transaksi Kredit
use App\Models\BalanceMutation;     // Model Mutasi
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
        // 1. Setup Filter Tanggal (Berlaku untuk SEMUA tab)
        $dateRange = null;
        if ($request->filled('from') && $request->filled('to')) {
            $dateRange = [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59'
            ];
        }

        // ==========================================
        // 2. TAB 1: DATA TRANSAKSI TUNAI (CASH)
        // ==========================================
        $cashQuery = Transaction::with(['user', 'purchaseType'])->latest();

        if ($dateRange) {
            $cashQuery->whereBetween('created_at', $dateRange);
        }

        // Hitung Total Omset (Khusus Tunai/Saldo)
        $grandTotalSemua = $cashQuery->sum('grand_total');

        // Pagination Transaksi Biasa (Page name: cash_page)
        $transactions = $cashQuery->paginate(10, ['*'], 'cash_page');


        // ==========================================
        // 3. TAB 2: DATA KREDIT (DIPISAH DUA)
        // ==========================================

        // A. Kredit Sedang Berjalan (Ongoing)
        $ongoingQuery = CreditTransaction::with(['user', 'product'])
            ->where('status', '!=', 'paid_off') // Filter Status Bukan Lunas
            ->latest();

        if ($dateRange) {
            $ongoingQuery->whereBetween('created_at', $dateRange);
        }

        $creditsOngoing = $ongoingQuery->paginate(10, ['*'], 'ongoing_page');


        // B. Kredit Sudah Lunas (Completed)
        $completedQuery = CreditTransaction::with(['user', 'product'])
            ->where('status', 'paid_off') // Filter Status Lunas
            ->latest();

        if ($dateRange) {
            $completedQuery->whereBetween('updated_at', $dateRange); // Gunakan updated_at untuk tgl pelunasan
        }

        $creditsCompleted = $completedQuery->paginate(10, ['*'], 'completed_page');


        // ==========================================
        // 4. TAB 3: DATA LOG MUTASI (AUTODEBET/DP)
        // ==========================================
        $mutationQuery = BalanceMutation::with('user')
            ->where(function ($q) {
                // Filter hanya yang relevan dengan sistem kredit/pembayaran
                $q->where('description', 'like', '%Kredit%')
                    ->orWhere('description', 'like', '%Autodebet%')
                    ->orWhere('description', 'like', '%DP%');
            })
            ->latest();

        if ($dateRange) {
            $mutationQuery->whereBetween('created_at', $dateRange);
        }

        // Pagination Mutasi (Page name: mutations_page)
        $mutations = $mutationQuery->paginate(15, ['*'], 'mutations_page');


        // ==========================================
        // 5. RETURN KE VIEW
        // ==========================================
        return view('admin.transactions.index', compact(
            'transactions',      // Data Tab 1
            'grandTotalSemua',   // Data Alert
            'creditsOngoing',    // Data Tab 2 (Sub A)
            'creditsCompleted',  // Data Tab 2 (Sub B)
            'mutations'          // Data Tab 3
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
    public function printInvoice($id)  // <--- GANTI JADI INI (sebelumnya print_invoice)
    {
        // Pastikan Anda sudah mengimport PDF di bagian atas: use Barryvdh\DomPDF\Facade\Pdf;

        $transaction = Transaction::with(['user.unitKerja', 'items', 'purchaseType'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('admin.transactions.invoice', compact('transaction'));
        $pdf->setPaper('a5', 'portrait');

        return $pdf->stream('Invoice-' . $transaction->invoice_code . '.pdf');
    }
}
