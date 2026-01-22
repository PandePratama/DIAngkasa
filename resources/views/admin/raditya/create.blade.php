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

                {{-- PERBAIKAN 1: Route diganti jadi raditya.store --}}
                <form action="{{ route('raditya.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            {{-- SKU (Dulu Product Code) --}}
                            <div class="form-group">
                                <label>SKU / Kode Produk</label>
                                <input type="text" name="sku" value="{{ old('sku') }}"
                                    class="form-control @error('sku') is-invalid @enderror" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Nama Produk --}}
                            <div class="form-group">
                                <label>Nama Produk</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {{-- Kategori (name: id_category) --}}
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
                            {{-- Brand (name: id_brand) --}}
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

                    <div class="row">
                        <div class="col-md-6">
                            {{-- Stok --}}
                            <div class="form-group">
                                <label>Stok</label>
                                <input type="number" name="stock" value="{{ old('stock', 0) }}"
                                    class="form-control @error('stock') is-invalid @enderror" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Harga</label>

                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="price_display"
                                        class="form-control @error('price') is-invalid @enderror" autocomplete="off">
                                </div>

                                {{-- nilai asli untuk backend --}}
                                <input type="hidden" name="price" id="price">

                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>

                    {{-- Deskripsi (Dulu Specification) --}}
                    <div class="form-group">
                        <label>Deskripsi / Spesifikasi</label>
                        <textarea name="desc" class="form-control @error('desc') is-invalid @enderror" rows="3">{{ old('desc') }}</textarea>
                    </div>

                    {{-- Warranty Info (Khusus Raditya) --}}
                    <div class="form-group">
                        <label>Info Garansi</label>
                        <input type="text" name="warranty_info" value="{{ old('warranty_info') }}"
                            class="form-control @error('warranty_info') is-invalid @enderror"
                            placeholder="Contoh: 1 Tahun Resmi">
                    </div>

                    {{-- Upload Gambar --}}
                    <div class="form-group">
                        <label>Gambar Produk (Bisa lebih dari satu)</label>
                        <input type="file" name="images[]"
                            class="form-control-file @error('images') is-invalid @enderror" multiple required>
                        <small class="text-muted">Format: jpg, jpeg, png, webp. Max: 2MB</small>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary">Simpan Produk</button>
                    <a href="{{ route('raditya.index') }}" class="btn btn-secondary">Batal</a>

                </form>
            </div>
        </div>
    </div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const display = document.getElementById('price_display');
        const hidden = document.getElementById('price');

        function formatRupiah(angka) {
            return angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        display.addEventListener('input', function(e) {
            // ambil hanya angka
            let raw = this.value.replace(/\D/g, '');

            // set ke hidden input (untuk backend)
            hidden.value = raw;

            // format ke tampilan
            this.value = raw ? formatRupiah(raw) : '';
        });
    });
</script>
