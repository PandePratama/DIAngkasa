<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
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
        $transaction = Transaction::where('user_id', Auth::id())
            ->latest() // Mengurutkan dari yang terbaru
            ->first(); // Ambil satu saja

        // 2. Cek jika data tidak ditemukan (misal user iseng akses url tanpa belanja)
        if (!$transaction) {
            return redirect()->route('home')->with('error', 'Belum ada transaksi.');
        }

        // 3. Kirim variable $transaction ke view
        return view('transaction.success', compact('transaction'));
    }
}
