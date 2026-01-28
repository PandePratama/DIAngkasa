<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * ======================
     * HALAMAN PROFILE
     * ======================
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $baseQuery = $user->transactions()->latest();

        // Filter tanggal
        if ($request->filled('from') && $request->filled('to')) {
            $baseQuery->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59'
            ]);
        }

        $total = (clone $baseQuery)->sum('grand_total');

        $transactions = (clone $baseQuery)
            ->paginate(10)
            ->withQueryString();

        return view('profile.index', compact(
            'user',
            'transactions',
            'total'
        ));
    }

    /**
     * ======================
     * UPDATE PROFILE USER
     * ======================
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore(Auth::id()),
            ],
            'no_telp' => 'required|string|digits_between:10,15',
            'nik' => 'required|string|digits:16',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'no_telp.digits_between' => 'No. Telp harus 10–15 digit',
            'nik.digits' => 'NIK harus 16 digit',
        ]);

        Auth::user()->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'no_telp' => $request->no_telp,
            'nik'     => $request->nik,
        ]);

        return back()->with('success', 'Profile berhasil diperbarui ✅');
    }

    /**
     * ======================
     * UPDATE PASSWORD
     * ======================
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password lama wajib diisi',
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user = Auth::user();

        // Password lama salah
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password lama tidak sesuai',
            ]);
        }

        // Password baru sama
        if (Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Password baru tidak boleh sama',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        Auth::logoutOtherDevices($request->password);

        return back()->with('success', 'Password berhasil diperbarui 🔐');
    }
}
