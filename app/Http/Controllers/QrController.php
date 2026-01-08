<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class QrController extends Controller
{
    public function scan(Request $request)
    {
        $request->validate([
            'nip' => 'required|string'
        ]);

        $user = User::where('nip', $request->nip)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'nip'  => $user->nip,
                'nama' => $user->name,
                'unit_kerja' => $user->unit_kerja,
            ]
        ]);
    }
}
