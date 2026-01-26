<?php

namespace App\Http\Controllers;

use App\Models\Transaction; // Ubah dari Order ke Transaction
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        // PERBAIKAN:
        // 1. Tidak perlu JOIN manual ke tabel products yang hilang.
        // 2. Gunakan tabel 'transactions' yang sudah punya kolom 'grand_total'.

        $orders = Transaction::with('user') // Eager load User
            ->latest()
            ->paginate(10);

        // Kita tetap lempar variabel '$orders' agar Anda tidak perlu ubah banyak di View
        return view('admin.orders.index', compact('orders'));
    }

    public function detailOrder($id)
    {
        // Ambil detail transaksi beserta jadwal cicilannya
        $order = Transaction::with(['user', 'installments'])
            ->findOrFail($id);

        return view('admin.orders.detail', compact('order'));
    }
}
