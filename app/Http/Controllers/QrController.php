<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
            'nip'     => 'required',
            'amount'  => 'required|numeric|min:1'
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

        // 🔻 POTONG CREDIT
        $user->credit_limit -= $request->amount;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi berhasil',
            'sisa_credit' => $user->credit_limit
        ]);
    }
}
