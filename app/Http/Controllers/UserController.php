<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'username'   => 'required|string|unique:users,username',
            'email'      => 'nullable|email|unique:users,email',
            'nip'        => 'nullable|string|unique:users,nip',
            'unit_kerja' => 'nullable|string|max:100',
            'password'   => 'required|min:6|confirmed',
        ]);

        User::create([
            'name'       => $request->name,
            'username'   => $request->username,
            'email'      => $request->email,
            'nip'        => $request->nip,
            'unit_kerja' => $request->unit_kerja,
            'role'       => 'employee', // otomatis
            'password'   => Hash::make($request->password),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User successfully created');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'username'   => 'required|string|unique:users,username,' . $user->id,
            'email'      => 'nullable|email|unique:users,email,' . $user->id,
            'nip'        => 'nullable|string|unique:users,nip,' . $user->id,
            'unit_kerja' => 'nullable|string|max:100',
            'password'   => 'nullable|min:6|confirmed',
        ]);

        $data = $request->only([
            'name',
            'username',
            'email',
            'nip',
            'unit_kerja'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'User successfully updated');
    }

    public function destroy(User $user)
    {
        // Proteksi super admin
        if ($user->role === 'super_admin') {
            return back()->with('failed', 'Super Admin cannot be deleted');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User successfully deleted');
    }
}
