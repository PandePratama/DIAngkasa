<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Mulai Query Transaksi milik User ini
        // Asumsi: Anda sudah membuat relasi 'transactions' di model User (lihat langkah 2 di bawah)
        $query = $user->transactions()->latest();

        // Alternatif jika belum ada relasi di model User:
        // $query = \App\Models\Transaction::where('user_id', $user->id)->latest();

        // 2. Logika Filter Tanggal (Sama seperti Admin tapi aman untuk User)
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59'
            ]);
        }

        // 3. Eksekusi Query
        $transactions = $query->get();

        // 4. Hitung Total (Hanya dari data yang difilter/ditampilkan)
        // Pastikan nama kolomnya sesuai database ('grand_total' atau 'amount')
        $total = $transactions->sum('grand_total');

        return view('profile.index', [
            'user' => $user,
            'transactions' => $transactions,
            'total' => $total, // Kirim variabel total ke view
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $user = Auth::user();
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profile berhasil diperbarui');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password lama wajib diisi',
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password baru minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user = Auth::user();

        // ❌ Password lama salah
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password lama tidak sesuai',
            ]);
        }

        // ❌ Password baru sama dengan password lama
        if (Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Password baru tidak boleh sama dengan password lama',
            ]);
        }

        // ✅ Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // OPTIONAL (AMAN): logout semua session lain
        Auth::logoutOtherDevices($request->password);

        return back()->with('success', 'Password berhasil diperbarui 🔐');
    }
}
