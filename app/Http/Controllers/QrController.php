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

        $user = User::with('unitKerja')->where('nip', $nip)->first();

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
            'unit'   => $user->unitKerja?->unit_name ?? '-',
            'saldo'  => (int) ($user->saldo ?? 0)
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

                // ✅ cek saldo
                if ($user->saldo < $request->amount) {
                    throw ValidationException::withMessages([
                        'amount' => 'Saldo tidak mencukupi'
                    ]);
                }

                $saldoAkhir = $user->saldo - $request->amount;

                // ✅ update saldo user
                $user->update([
                    'saldo' => $saldoAkhir
                ]);

                // ✅ simpan transaksi SESUAI table
                Transaction::create([
                    'id_user'        => $user->id,
                    'invoice_code'   => 'QR-' . now()->format('YmdHis'),
                    'grand_total'    => $request->amount,
                    'balance_after'  => $saldoAkhir,
                    'payment_type'   => 'credit', // atau 'cash'
                    'tenure'         => 1,
                    'status'         => 'completed'
                ]);

                $result = [
                    'status'      => 'success',
                    'sisa_saldo' => $saldoAkhir
                ];
            });

            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->errors()['amount'][0]
            ], 422);
        }
    }
}
