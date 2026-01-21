<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('unitKerja')->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $unitKerja = UnitKerja::orderBy('unit_name')->get();
        return view('admin.users.create', compact('unitKerja'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'nip'            => 'nullable|string|unique:users,nip',
            'id_unit_kerja'  => 'required|exists:unit_kerja,id',
            'password'       => 'required|min:6|confirmed',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'nip'           => $request->nip,
            'id_unit_kerja' => $request->id_unit_kerja,
            'role'          => 'user',
            'password'      => Hash::make($request->password),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dibuat');
    }

    public function edit(User $user)
    {
        $unitKerja = UnitKerja::orderBy('unit_name')->get();
        return view('admin.users.edit', compact('user', 'unitKerja'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email,' . $user->id,
            'nip'            => 'nullable|string|unique:users,nip,' . $user->id,
            'id_unit_kerja'  => 'required|exists:unit_kerja,id',
            'password'       => 'nullable|min:6|confirmed',
        ]);

        $data = $request->only([
            'name',
            'email',
            'nip',
            'id_unit_kerja',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'super_admin') {
            return back()->with('failed', 'Super Admin tidak bisa dihapus');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}
