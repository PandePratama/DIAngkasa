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
                            <label class="font-weight-bold text-primary">Limit Anggaran / Saldo Awal</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>

                                {{-- 1. Input Visual (Untuk Tampilan User) --}}
                                <input type="text" id="saldo_display"
                                    class="form-control @error('saldo') is-invalid @enderror" placeholder="0"
                                    autocomplete="off">

                                {{-- 2. Input Hidden (Untuk dikirim ke Database) --}}
                                {{-- Value diambil dari old('saldo') jika user gagal validasi form sebelumnya --}}
                                <input type="hidden" name="saldo" id="saldo" value="{{ old('saldo') }}">
                            </div>

                            <small class="text-muted">Format otomatis (Contoh: 1.000.000). Kosongkan jika 0.</small>

                            @error('saldo')
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

{{-- SCRIPT FORMATTER (Wajib ada di file create.blade.php) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const displayInput = document.getElementById('saldo_display');
        const hiddenInput = document.getElementById('saldo');

        // Fungsi Format Rupiah
        function formatRupiah(angka) {
            if (!angka) return '';
            let number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }

        // 1. Event Listener saat mengetik
        displayInput.addEventListener('keyup', function(e) {
            // Ambil angka murni untuk input hidden
            let cleanValue = this.value.replace(/\D/g, '');
            hiddenInput.value = cleanValue;

            // Update tampilan visual dengan titik
            this.value = formatRupiah(this.value);
        });

        // 2. Cek Old Data (Penting saat Create gagal validasi)
        // Jika user submit -> error -> kembali ke form, angka yang tadi diketik muncul lagi
        if (hiddenInput.value) {
            displayInput.value = formatRupiah(hiddenInput.value);
        }
    });
</script>
