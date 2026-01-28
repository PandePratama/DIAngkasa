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
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control"
                        required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
                </div>

                <div class="form-group">
                    <label>No. Telp</label>
                    <input type="text" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}" class="form-control">
                </div>

                <div class="form-group">
                    <label>NIK</label>
                    <input type="text" name="nik" value="{{ old('nik', $user->nik) }}" class="form-control">
                </div>

                <div class="form-group">
                    <label>NIP</label>
                    <input type="text" name="nip" value="{{ old('nip', $user->nip) }}" class="form-control">
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

                {{-- ==================== TAMBAHAN: INPUT SALDO ==================== --}}
                <div class="form-group">
                    <label class="font-weight-bold text-primary">Limit Anggaran / Saldo</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>

                        {{-- 1. Input Visual --}}
                        <input type="text" id="saldo_display" class="form-control @error('saldo') is-invalid @enderror"
                            placeholder="0" autocomplete="off">

                        {{-- 2. Input Hidden (PERBAIKAN DISINI) --}}
                        {{-- Kita cast ke (int) agar 200000.00 menjadi 200000 --}}
                        <input type="hidden" name="saldo" id="saldo" value="{{ old('saldo', (int) $user->saldo) }}">
                    </div>
                    <small class="text-muted">Format otomatis (Contoh: 1.000.000). Kosongkan jika 0.</small>
                    @error('saldo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                {{-- ==================== END TAMBAHAN ==================== --}}

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <label>Password Baru</label>
                        <input type="password" name="password" class="form-control"
                            placeholder="Kosongkan jika tidak diubah">
                    </div>

                    <div class="col-md-6">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control"
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

    {{-- SCRIPT FORMATTER RUPIAH --}}
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
                // Ambil hanya angka untuk input hidden
                let cleanValue = this.value.replace(/\D/g, '');
                hiddenInput.value = cleanValue;

                // Format tampilan visual
                this.value = formatRupiah(this.value);
            });

            // 2. Inisialisasi Nilai Awal (PENTING UNTUK HALAMAN EDIT)
            // Ambil nilai dari hidden input (data dari database) lalu format ke visual
            if (hiddenInput.value) {
                displayInput.value = formatRupiah(hiddenInput.value);
            }
        });
    </script>

@endsection
