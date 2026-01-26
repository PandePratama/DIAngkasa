<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tambahkan 'user' ke dalam with() (Eager Loading)
        // Kita butuh data user untuk mengambil saldo-nya.
        $query = Transaction::with(['order', 'user'])->latest();

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59'
            ]);
        }

        $transactions = $query->get();

        // 2. LOGIKA MATEMATIKA DISINI (Transform)
        // Kita memodifikasi data sebelum dikirim ke View
        $transactions->transform(function ($trx) {
            // Cek apakah ada data user (untuk menghindari error jika user terhapus)
            $currentSaldo = $trx->user ? $trx->user->saldo : 0;

            // Rumus: Saldo User Saat Ini - Grand Total Transaksi
            // Hasilnya disimpan dalam atribut baru bernama 'sisa_saldo_kalkulasi'
            $trx->sisa_saldo_kalkulasi = $currentSaldo - $trx->grand_total;

            return $trx;
        });

        // Hitung total semua transaksi
        $total = $transactions->sum('grand_total');

        return view('admin.transactions.index', compact('transactions', 'total'));
    }
}
