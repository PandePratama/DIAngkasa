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

        // 1. Mulai Query
        // Pastikan relasi 'transactions' ada di Model User.
        // Jika error, ganti jadi: \App\Models\Transaction::where('id_user', $user->id)->latest();
        $query = $user->transactions()->latest();

        // 2. Filter Tanggal
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59'
            ]);
        }

        // 3. HITUNG TOTAL (Lakukan INI SEBELUM Pagination)
        // Kita ingin total uang dari SEMUA data yang difilter, bukan cuma 10 data di halaman 1.
        $total = $query->sum('grand_total');

        // 4. EKSEKUSI PAGINATION (SOLUSI ERROR ANDA)
        // Ganti get() menjadi paginate(10)
        $transactions = $query->paginate(10)->withQueryString();
        // withQueryString() penting agar saat klik Halaman 2, filter tanggal tidak hilang.

        return view('profile.index', [
            'user' => $user,
            'transactions' => $transactions,
            'total' => $total,
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
