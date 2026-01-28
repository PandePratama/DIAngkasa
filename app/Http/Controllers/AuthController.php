<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        // Login via email atau NIP
        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'nip';

        if (Auth::attempt([
            $field     => $request->login,
            'password' => $request->password,
        ], $request->filled('remember'))) {

            $request->session()->regenerate();

            $user = Auth::user();

            // ✅ Redirect berdasarkan role
            if (in_array($user->role, ['super_admin', 'admin'])) {
                return redirect()->route('dashboard');
            }

            if ($user->role === 'user') {
                return redirect()->route('home'); // ✅ BENAR
            }

            // Fallback
            Auth::logout();
            return back()->withErrors([
                'login' => 'Role tidak valid',
            ]);
        }

        return back()
            ->with('failed', 'Email / NIP atau password salah')
            ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home'); // ✅ UX lebih baik
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'nip'           => 'nullable|string',
            'id_unit_kerja' => 'required|exists:unit_kerjas,id', // Sesuaikan nama tabel unit kerja
            'password'      => 'required|min:6|confirmed',
            'role'          => 'required|in:user,admin,super_admin', // <--- Validasi Role
        ]);

        // 2. Simpan User
        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'nip'           => $request->nip,
            'id_unit_kerja' => $request->id_unit_kerja,
            'password'      => Hash::make($request->password), // Jangan lupa Hash password
            'role'          => $request->role, // <--- Simpan Role
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }
}
