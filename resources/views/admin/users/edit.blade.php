@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Edit User</h6>
    </div>

    <div class="card-body">

        {{-- ERROR --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Full Name</label>
                <input type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    class="form-control"
                    required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="form-control"
                    required>
            </div>

            <div class="form-group">
                <label>NIP</label>
                <input type="text"
                    name="nip"
                    value="{{ old('nip', $user->nip) }}"
                    class="form-control">
            </div>

            <div class="form-group">
                <label>Unit Kerja</label>
                <select name="id_unit_kerja" class="form-control" required>
                    <option value="">-- Pilih Unit Kerja --</option>
                    @foreach ($unitKerja as $unit)
                    <option value="{{ $unit->id }}"
                        {{ old('id_unit_kerja', $user->id_unit_kerja) == $unit->id ? 'selected' : '' }}>
                        {{ $unit->unit_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <label>Password Baru</label>
                    <input type="password"
                        name="password"
                        class="form-control"
                        placeholder="Kosongkan jika tidak diubah">
                </div>

                <div class="col-md-6">
                    <label>Konfirmasi Password</label>
                    <input type="password"
                        name="password_confirmation"
                        class="form-control"
                        placeholder="Kosongkan jika tidak diubah">
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection