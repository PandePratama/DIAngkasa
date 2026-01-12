<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrController extends Controller
{
    public function validateQr(Request $request)
    {
        $nip = strtoupper(trim($request->qr));

        if (!$nip) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'QR kosong'
            ]);
        }

        $user = User::where('nip', $nip)->first();

        if (!$user) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return response()->json([
            'status' => 'valid',
            'nip'    => $user->nip,
            'name'   => $user->name,
            'unit'   => $user->unit_kerja,
            'credit' => $user->credit_limit
        ]);
    }

    // 🔥 TRANSAKSI BELANJA
    public function processTransaction(Request $request)
    {
        $request->validate([
            'nip' => 'required',
            'amount' => 'required|numeric|min:1'
        ]);

        $user = User::where('nip', $request->nip)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ]);
        }

        if ($user->credit_limit < $request->amount) {
            return response()->json([
                'status' => 'error',
                'message' => 'Saldo tidak mencukupi'
            ]);
        }

        $saldoAwal = $user->credit_limit;
        $saldoAkhir = $saldoAwal - $request->amount;

        // 🔻 Update saldo
        $user->update([
            'credit_limit' => $saldoAkhir
        ]);

        // 💾 Simpan transaksi
        Transaction::create([
            'user_id' => $user->id,
            'nip' => $user->nip,
            'admin_name' => Auth::user()->name,
            'amount' => $request->amount,
            'saldo_awal' => $saldoAwal,
            'saldo_akhir' => $saldoAkhir
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi berhasil',
            'sisa_credit' => $saldoAkhir
        ]);
    }
}
