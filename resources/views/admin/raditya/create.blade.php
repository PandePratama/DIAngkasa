@extends('admin.layouts.app')

@section('title', 'Tambah Produk Gadget')

@section('content')
    <div class="container-fluid">

        <h1 class="h3 mb-4 text-gray-800">Tambah Gadget Baru (Raditya)</h1>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Input Produk</h6>
            </div>
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('raditya.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- BARIS 1: SKU & NAMA --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>SKU / Kode Produk</label>
                                <input type="text" name="sku" value="{{ old('sku') }}"
                                    class="form-control @error('sku') is-invalid @enderror" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror" required>
                            </div>
                        </div>
                    </div>

                    {{-- BARIS 2: KATEGORI & BRAND --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="id_category" class="form-control @error('id_category') is-invalid @enderror"
                                    required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('id_category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Brand</label>
                                <select name="id_brand" class="form-control @error('id_brand') is-invalid @enderror"
                                    required>
                                    <option value="">-- Pilih Brand --</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ old('id_brand') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->brand_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- BARIS 3: STOK & GARANSI --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Stok</label>
                                <input type="number" name="stock" value="{{ old('stock', 0) }}"
                                    class="form-control @error('stock') is-invalid @enderror" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Info Garansi</label>
                                <input type="text" name="warranty_info" value="{{ old('warranty_info') }}"
                                    class="form-control @error('warranty_info') is-invalid @enderror"
                                    placeholder="Contoh: 1 Tahun Resmi">
                            </div>
                        </div>
                    </div>

                    {{-- BARIS 4: HPP & HARGA JUAL (Dibuat Sejajar & Format Rupiah) --}}
                    <div class="row bg-light p-3 rounded mb-3 border">
                        <div class="col-md-6">
                            {{-- HPP (Harga Modal) --}}
                            <div class="form-group">
                                <label class="text-danger font-weight-bold">HPP (Harga Modal)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-danger text-white">Rp</span>
                                    <input type="text" id="hpp_display"
                                        class="form-control @error('hpp') is-invalid @enderror" autocomplete="off"
                                        placeholder="0">
                                </div>
                                {{-- Input hidden untuk backend --}}
                                <input type="hidden" name="hpp" id="hpp" value="{{ old('hpp') }}">
                                @error('hpp')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            {{-- Harga Jual --}}
                            <div class="form-group">
                                <label class="text-success font-weight-bold">Harga Jual</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white">Rp</span>
                                    <input type="text" id="price_display"
                                        class="form-control @error('price') is-invalid @enderror" autocomplete="off"
                                        placeholder="0">
                                </div>
                                {{-- Input hidden untuk backend --}}
                                <input type="hidden" name="price" id="price" value="{{ old('price') }}">
                                @error('price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="form-group">
                        <label>Deskripsi / Spesifikasi</label>
                        <textarea name="desc" class="form-control @error('desc') is-invalid @enderror" rows="3">{{ old('desc') }}</textarea>
                    </div>

                    {{-- Upload Gambar --}}
                    <div class="form-group">
                        <label>Gambar Produk (Bisa lebih dari satu)</label>
                        <input type="file" name="images[]"
                            class="form-control-file @error('images') is-invalid @enderror">
                        <small class="text-muted">Format: jpg, jpeg, png, webp. Max: 2MB</small>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        <i class="fas fa-save mr-2"></i> Simpan Produk
                    </button>
                    <a href="{{ route('raditya.index') }}" class="btn btn-secondary btn-block">Batal</a>

                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT FORMAT RUPIAH (Berlaku untuk HPP dan Harga Jual) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            function formatRupiah(angka) {
                if (!angka) return '';
                return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function setupCurrencyInput(displayId, hiddenId) {
                const display = document.getElementById(displayId);
                const hidden = document.getElementById(hiddenId);

                // Jika ada old value (saat validasi gagal), format ulang saat page load
                if (hidden.value) {
                    display.value = formatRupiah(hidden.value);
                }

                display.addEventListener('input', function(e) {
                    // Hanya ambil angka
                    let raw = this.value.replace(/\D/g, '');

                    // Simpan ke hidden input
                    hidden.value = raw;

                    // Tampilkan format rupiah
                    this.value = raw ? formatRupiah(raw) : '';
                });
            }

            // Terapkan ke HPP
            setupCurrencyInput('hpp_display', 'hpp');

            // Terapkan ke Harga Jual
            setupCurrencyInput('price_display', 'price');
        });
    </script>
@endsection
