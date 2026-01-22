<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\UnitKerja;

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

    public function store(StoreUserRequest $request)
    {
        User::create($request->validated());
        return redirect()->route('users.index')->with('success', 'User berhasil dibuat');
    }

    public function edit(User $user)
    {
        $unitKerja = UnitKerja::orderBy('unit_name')->get();
        return view('admin.users.edit', compact('user', 'unitKerja'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());
        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');
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
