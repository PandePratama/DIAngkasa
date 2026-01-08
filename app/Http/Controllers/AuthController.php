<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            if ($user->role === 'employee') {
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
}
