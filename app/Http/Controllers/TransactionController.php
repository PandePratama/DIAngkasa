<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Ganti 'transaction' menjadi 'order' (sesuai nama fungsi di Model)
        $query = Transaction::with('order')->latest();

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59'
            ]);
        }

        $transactions = $query->get();

        // Pastikan menggunakan 'grand_total' jika itu nama kolom di DB Anda
        $total = $transactions->sum('grand_total');

        return view('admin.transactions.index', compact('transactions', 'total'));
    }
}
