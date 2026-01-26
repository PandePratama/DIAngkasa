@extends('admin.layouts.app')

@section('title', 'Add User')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Add User</h6>
        </div>

        <div class="card-body">

            {{-- ERROR HANDLING --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                {{-- SECTION 1: IDENTITAS --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                                placeholder="Nama Lengkap" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip') }}" class="form-control"
                                placeholder="Nomor Induk Pegawai">
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: KONTAK & UNIT --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                                placeholder="email@example.com" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Unit Kerja <span class="text-danger">*</span></label>
                            <select name="id_unit_kerja" class="form-control" required>
                                <option value="">-- Pilih Unit Kerja --</option>
                                @foreach ($unitKerja as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ old('id_unit_kerja') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->unit_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: ROLE & LIMIT --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-control" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super
                                    Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Limit Anggaran</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" id="price_display"
                                    class="form-control @error('price') is-invalid @enderror" placeholder="0"
                                    autocomplete="off">

                                {{-- Hidden Input untuk Backend --}}
                                <input type="hidden" name="price" id="price">
                            </div>
                            <small class="text-muted">Kosongkan jika tidak ada limit.</small>
                            @error('price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- SECTION 4: SECURITY --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-secondary mr-2" type="button" onclick="history.back()">Cancel</button>
                    <button class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save User
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

{{-- Script tetap sama --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const display = document.getElementById('price_display');
        const hidden = document.getElementById('price');

        function formatRupiah(angka) {
            return angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        if (display) {
            display.addEventListener('input', function(e) {
                let raw = this.value.replace(/\D/g, '');
                hidden.value = raw;
                this.value = raw ? formatRupiah(raw) : '';
            });
        }
    });
</script>
