<?php

namespace App\Http\Controllers;

use App\Models\CreditTransaction; // Pastikan Model ini di-use
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // ==========================================
        // 1. DATA TRANSAKSI REGULER (TUNAI/SALDO)
        // ==========================================

        // Eager load 'purchaseType' juga agar efisien di view
        $query = Transaction::with(['order', 'user', 'purchaseType'])->latest();

        // Filter Tanggal (Berlaku untuk kedua query)
        $dateRange = null;
        if ($request->filled('from') && $request->filled('to')) {
            $dateRange = [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59'
            ];
            $query->whereBetween('created_at', $dateRange);
        }

        // Hitung Total Omset (Hanya dari transaksi reguler/cash)
        $grandTotalSemua = $query->sum('grand_total');

        // Pagination Transaksi Biasa
        // PENTING: Gunakan page name 'trx_page' agar tidak bentrok dengan tab sebelah
        $transactions = $query->paginate(10, ['*'], 'trx_page');

        // Transformasi Data (Opsional, untuk menampilkan saldo saat ini)
        $transactions->getCollection()->transform(function ($trx) {
            $trx->user_current_saldo = $trx->user ? $trx->user->saldo : 0;
            return $trx;
        });


        // ==========================================
        // 2. DATA TRANSAKSI KREDIT (BARU)
        // ==========================================

        // Load relasi user dan product (karena di view kita panggil $credit->product->name)
        $creditQuery = CreditTransaction::with(['user', 'product'])->latest();

        // Terapkan Filter Tanggal yang sama
        if ($dateRange) {
            $creditQuery->whereBetween('created_at', $dateRange);
        }

        // Pagination Transaksi Kredit
        // PENTING: Gunakan page name 'credit_page'
        $creditTransactions = $creditQuery->paginate(10, ['*'], 'credit_page');


        // ==========================================
        // 3. RETURN VIEW
        // ==========================================
        return view('admin.transactions.index', compact(
            'transactions',
            'creditTransactions',
            'grandTotalSemua'
        ));
    }

    public function success()
    {
        // 1. CEK SESSION KREDIT (Prioritas cek session dari PaymentController)
        if (session()->has('credit_trx_id')) {
            $transaction = \App\Models\CreditTransaction::with('product')->find(session('credit_trx_id'));
            $type = 'credit'; // <--- INI PENTING
        }
        // 2. JIKA TIDAK ADA SESSION, AMBIL TRANSAKSI BIASA TERBARU
        else {
            $transaction = \App\Models\Transaction::where('id_user', \Illuminate\Support\Facades\Auth::id())
                ->latest()
                ->first();
            $type = 'regular'; // <--- INI PENTING
        }

        // 3. Validasi jika data kosong (User belum pernah belanja)
        if (!$transaction) {
            return redirect()->route('home')->with('error', 'Belum ada transaksi.');
        }

        // 4. KIRIM DATA KE VIEW
        return view('transaction.success', compact('transaction', 'type'));
    }
    public function printInvoice($id)
    {
        // Method ini khusus cetak Invoice Transaksi Biasa (Cash/Saldo)
        // Untuk Kredit, biasanya ada method terpisah (printContract misalnya)

        $transaction = \App\Models\Transaction::with(['user.unitKerja', 'items', 'purchaseType'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('admin.transactions.invoice', compact('transaction'));
        $pdf->setPaper('a5', 'portrait');

        return $pdf->stream('Invoice-' . $transaction->invoice_code . '.pdf');
    }
}
