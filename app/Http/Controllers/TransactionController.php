<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // 1. Query Dasar
        $query = Transaction::with(['order', 'user'])->latest();

        // 2. Filter Tanggal (Lebih Rapi)
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59'
            ]);
        }

        // Simpan total seluruh data (sebelum dipaginate) untuk keperluan statistik (Opsional)
        // Hati-hati, sum() pada query besar juga bisa berat.
        // Sebaiknya hanya hitung total halaman ini saja atau gunakan cache.
        $grandTotalSemua = $query->sum('grand_total');

        // 3. Gunakan Pagination (Wajib untuk Admin)
        $transactions = $query->paginate(20);

        // 4. Transformasi Data (Hanya untuk yang tampil di halaman ini)
        // getCollection() digunakan karena kita pakai paginate
        $transactions->getCollection()->transform(function ($trx) {

            // Opsi A: Jika ingin menampilkan Sisa Saldo User SAAT INI (Realtime)
            $trx->user_current_saldo = $trx->user ? $trx->user->saldo : 0;

            // Opsi B: LOGIKA YANG ANDA MINTA (TAPI LOGISNYA SALAH SEPERTI DIJELASKAN DI ATAS)
            // Saya sarankan JANGAN pakai logika pengurangan ini untuk history.
            // $trx->sisa_saldo_kalkulasi = $trx->user_current_saldo - $trx->grand_total;

            return $trx;
        });

        return view('admin.transactions.index', compact('transactions', 'grandTotalSemua'));
    }

    public function success()
    {
        // 1. Ambil transaksi paling baru milik user yang sedang login
        $transaction = Transaction::where('id_user', Auth::id())
            ->latest() // Mengurutkan dari yang terbaru
            ->first(); // Ambil satu saja

        // 2. Cek jika data tidak ditemukan (misal user iseng akses url tanpa belanja)
        if (!$transaction) {
            return redirect()->route('home')->with('error', 'Belum ada transaksi.');
        }

        // 3. Kirim variable $transaction ke view
        return view('transaction.success', compact('transaction'));
    }

    public function printInvoice($id)
    {
        // 1. Ambil data transaksi spesifik berdasarkan ID
        // PENTING: Wajib load relasi 'items' agar tidak error foreach
        $transaction = \App\Models\Transaction::with(['user.unitKerja', 'items', 'purchaseType'])
            ->findOrFail($id);

        // 2. Load View Invoice (Kita buat file baru khusus invoice)
        $pdf = Pdf::loadView('admin.transactions.invoice', compact('transaction'));

        // 3. Set ukuran kertas (A4 atau struk termal, disini kita pakai A4 setengah atau A5 biar rapi)
        $pdf->setPaper('a5', 'portrait');

        // 4. Stream PDF (Tampilkan di browser)
        return $pdf->stream('Invoice-' . $transaction->invoice_code . '.pdf');
    }
}
