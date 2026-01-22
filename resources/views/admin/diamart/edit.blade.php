@extends('admin.layouts.app')

@section('title', 'Edit Produk Diamart')

@section('content')
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Edit Produk Sembako</h6>
        </div>

        <div class="card-body">

            {{-- PENTING: Route ke diamart.update --}}
            <form action="{{ route('diamart.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Nama Produk --}}
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control"
                        required>
                </div>

                {{-- Deskripsi --}}
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="desc" class="form-control" rows="3">{{ old('desc', $product->desc) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        {{-- Kategori --}}
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="id_category" class="form-control" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('id_category', $product->id_category) == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        {{-- Brand --}}
                        <div class="form-group">
                            <label>Brand</label>
                            <select name="id_brand" class="form-control" required>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ old('id_brand', $product->id_brand) == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->brand_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        {{-- Stock --}}
                        <div class="form-group">
                            <label>Stok</label>
                            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                                class="form-control" min="0" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        {{-- Price --}}
                        <div class="form-group">
                            <label>Harga Jual</label>
                            <input type="number" name="price" value="{{ old('price', $product->price) }}"
                                class="form-control" min="0" required>
                        </div>
                    </div>
                </div>

                {{-- Upload Gambar (Logic Tambahan untuk Edit) --}}
                <div class="form-group mt-4">
                    <label class="font-weight-bold">Foto Produk</label>

                    {{-- Area Upload Baru --}}
                    <div class="p-3 bg-light border rounded text-center mb-3"
                        style="cursor: pointer; border-style: dashed !important;"
                        onclick="document.getElementById('images').click()">
                        <i class="fas fa-plus fa-2x text-muted"></i>
                        <p class="mb-0 text-muted">Tambah Gambar Baru</p>
                    </div>
                    <input type="file" id="images" name="images[]" class="d-none" multiple accept="image/*"
                        onchange="previewNewImages(event)">
                    <input type="hidden" name="deleted_images" id="deleted_images">

                    {{-- Container Preview Gabungan --}}
                    <div class="row" id="edit-preview-container">

                        {{-- 1. Gambar Lama (Existing) --}}
                        @foreach ($product->images as $image)
                            <div class="col-6 col-md-3 mb-3 existing-image" id="img-{{ $image->id }}">
                                <div class="card h-100 shadow-sm border-0 position-relative">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute"
                                        style="top:5px; right:5px; z-index:10"
                                        onclick="markAsDeleted({{ $image->id }})">
                                        &times;
                                    </button>
                                    <img src="{{ asset('storage/' . $image->image_path) }}" class="card-img-top h-100"
                                        style="object-fit: cover; height: 150px;">
                                    <div class="card-footer p-1 text-center bg-white"><small
                                            class="text-muted">Tersimpan</small></div>
                                </div>
                            </div>
                        @endforeach

                        {{-- 2. Gambar Baru (Akan muncul lewat JS) --}}
                    </div>
                </div>

                <div class="text-right mt-4">
                    <a href="{{ route('diamart.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let deletedIds = [];

        // Fungsi menandai gambar lama untuk dihapus
        function markAsDeleted(id) {
            deletedIds.push(id);
            document.getElementById('deleted_images').value = deletedIds.join(',');

            // Sembunyikan element visualnya
            document.getElementById('img-' + id).style.display = 'none';
        }

        // Fungsi preview gambar baru upload
        function previewNewImages(event) {
            const input = event.target;
            const container = document.getElementById('edit-preview-container');

            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const colDiv = document.createElement('div');
                        colDiv.className = 'col-6 col-md-3 mb-3';
                        colDiv.innerHTML = `
                        <div class="card h-100 shadow-sm border-0">
                            <img src="${e.target.result}" class="card-img-top" style="object-fit: cover; height: 150px;">
                            <div class="card-footer p-1 text-center bg-success text-white"><small>Baru</small></div>
                        </div>
                    `;
                        container.appendChild(colDiv);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }
    </script>
@endpush
