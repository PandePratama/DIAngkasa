<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Providers\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QrController extends Controller
{
    public function validateQr(Request $request)
    {
        $nip = strtoupper(trim($request->qr));

        if (!$nip) {
            return response()->json([
                'status'  => 'invalid',
                'message' => 'QR kosong'
            ]);
        }

        $user = User::where('nip', $nip)->first();

        if (!$user) {
            return response()->json([
                'status'  => 'invalid',
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

    // 🔥 TRANSAKSI QR
    public function processTransaction(Request $request)
    {
        $request->validate([
            'nip'    => 'required',
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            DB::transaction(function () use ($request, &$result) {

                $user = User::where('nip', $request->nip)
                    ->lockForUpdate()
                    ->firstOrFail();

                $sisa = CreditService::remaining($user);

                if ($sisa < $request->amount) {
                    throw ValidationException::withMessages([
                        'amount' => 'Limit kredit tidak mencukupi'
                    ]);
                }

                $saldoAwal  = $user->credit_limit;
                $saldoAkhir = $saldoAwal - $request->amount;

                $user->update(['credit_limit' => $saldoAkhir]);

                Transaction::create([
                    'user_id'     => $user->id,
                    'nip'         => $user->nip,
                    'admin_name'  => auth()->user()->name,
                    'amount'      => $request->amount,
                    'saldo_awal'  => $saldoAwal,
                    'saldo_akhir' => $saldoAkhir
                ]);

                $result = [
                    'status'      => 'success',
                    'sisa_credit' => $saldoAkhir
                ];
            });

            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()['amount'][0]
            ], 422);
        }
    }
}
