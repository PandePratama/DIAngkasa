<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:100',
            'email'          => 'nullable|email|unique:users,email,' . $this->user->id,
            'nip'            => 'nullable|string|unique:users,nip,' . $this->user->id,
            'id_unit_kerja'  => 'required|exists:unit_kerja,id',
            'role'           => 'required|in:super_admin,admin,staff,user',
            'password'       => 'nullable|min:6|confirmed',
            'saldo'     => 'nullable|numeric|min:0',
            'no_telp'       => 'nullable|string|max:20',
            'nik' => [
                'nullable',
                Rule::unique('users', 'nik')->ignore($this->user->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Nama wajib diisi.',
            'email.required'         => 'Email wajib diisi.',
            'email.email'            => 'Format email tidak valid.',
            'email.unique'           => 'Email sudah digunakan.',
            'nip.unique'             => 'NIP sudah digunakan.',
            'id_unit_kerja.required' => 'Unit Kerja wajib dipilih.',
            'id_unit_kerja.exists'   => 'Unit Kerja tidak valid.',
            'role.required'          => 'Role wajib dipilih.',
            'role.in'                => 'Role tidak valid.',
            'password.min'           => 'Password minimal 6 karakter.',
            'password.confirmed'     => 'Konfirmasi password tidak sesuai.',
        ];
    }
}
