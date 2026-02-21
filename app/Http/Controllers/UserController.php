<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Tambahkan ini untuk hashing password

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $users = User::with('unitKerja')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhereHas('unitKerja', function ($q) use ($search) {
                            $q->where('unit_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate($perPage)
            ->appends($request->query());

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $unitKerja = UnitKerja::orderBy('unit_name')->get();
        return view('admin.users.create', compact('unitKerja'));
    }

    public function store(StoreUserRequest $request)
    {
        // 1. Ambil data yang sudah divalidasi
        $data = $request->validated();

        // 2. Set Default Saldo ke 0 jika kosong
        // (Input 'saldo' didapat dari hidden input hasil JS formatting)
        $data['saldo'] = $request->saldo ?? 0;

        // 3. Hash Password (Default 'password123' jika tidak diisi admin)
        $rawPassword = $request->password ?? 'password123';
        $data['password'] = Hash::make($rawPassword);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat');
    }

    public function edit(User $user)
    {
        $unitKerja = UnitKerja::orderBy('unit_name')->get();
        return view('admin.users.edit', compact('user', 'unitKerja'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // 1. Ambil data validasi
        $data = $request->validated();

        // 2. Handle Password
        // Jika kosong, hapus dari array agar password lama tidak tertimpa null/kosong
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            // Jika diisi, hash password baru
            $data['password'] = Hash::make($data['password']);
        }

        // 3. Handle Saldo
        // Pastikan saldo terupdate (jika null dianggap 0)
        $data['saldo'] = $request->saldo ?? 0;

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'Saldo berhasil diupdate!');
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
