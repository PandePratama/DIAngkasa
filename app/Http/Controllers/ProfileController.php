<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', [
            'user' => Auth::user(),
            'transactions' => Auth::user()
                // ->transactions()
                ->latest()
                ->get()
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_telp' => 'required|string|digits_between:10,15',
            'nik' => 'required|string|digits:16',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',

            'no_telp.digits_between' => 'No. Telp harus 10–15 digit angka',
            'nik.digits' => 'NIK harus terdiri dari 16 digit angka',
        ]);

        $user = Auth::user();
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'nik' => $request->nik,
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
