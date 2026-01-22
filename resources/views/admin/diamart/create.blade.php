@extends('admin.layouts.app')

@section('title', 'Tambah Produk Sembako')

@section('content')
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-success">Tambah Produk Diamart</h6>
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

            <form action="{{ route('diamart.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Nama Produk --}}
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                        placeholder="Contoh: Beras Ramos 5kg" required autocomplete="off">
                </div>

                {{-- Deskripsi --}}
                <div class="form-group">
                    <label>Deskripsi (Opsional)</label>
                    <textarea name="desc" class="form-control" rows="3">{{ old('desc') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        {{-- Kategori --}}
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="id_category" class="form-control" required>
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
                        {{-- Brand --}}
                        <div class="form-group">
                            <label>Brand</label>
                            <select name="id_brand" class="form-control" required>
                                <option value="">-- Pilih Brand --</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ old('id_brand') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->brand_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Jika tidak ada brand, pilih 'No Brand' atau buat brand baru.</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        {{-- Stok --}}
                        <div class="form-group">
                            <label>Stok</label>
                            <input type="number" name="stock" value="{{ old('stock', 0) }}" class="form-control"
                                min="0" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        {{-- Harga --}}
                        <div class="form-group">
                            <label>Harga Jual (Rp)</label>
                            <input type="number" name="price" value="{{ old('price') }}" class="form-control"
                                min="0" required>
                        </div>
                    </div>
                </div>

                {{-- Upload Gambar --}}
                <div class="form-group mt-3">
                    <label class="font-weight-bold">Foto Produk</label>

                    {{-- Container Upload Box --}}
                    <div id="upload-box"
                        class="position-relative border rounded d-flex justify-content-center align-items-center overflow-hidden"
                        style="height: 300px; cursor: pointer; border: 2px dashed #ccc !important; background-color: #f8f9fa;"
                        onclick="document.getElementById('images').click()">

                        {{-- 1. Tampilan Awal (Placeholder Ikon & Teks) --}}
                        <div id="placeholder-content" class="text-center p-4">
                            <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-primary"></i>
                            <h6 class="font-weight-bold text-dark">Klik untuk upload gambar</h6>
                            <p class="text-muted small mb-0">Bisa pilih lebih dari 1 gambar</p>
                        </div>

                        {{-- 2. Tampilan Preview (Akan diisi gambar lewat JS) --}}
                        {{-- Kita sembunyikan dulu pakai d-none --}}
                        <div id="preview-container" class="d-none w-100 h-100 row no-gutters">
                            {{-- Gambar akan disuntikkan ke sini oleh Javascript --}}
                        </div>

                        {{-- Overlay Edit (Opsional: Agar user tau bisa diklik lagi) --}}
                        <div id="hover-overlay"
                            class="position-absolute w-100 h-100 d-none align-items-center justify-content-center"
                            style="background: rgba(0,0,0,0.5); top:0; left:0; z-index: 10;">
                            <span class="text-white font-weight-bold"><i class="fas fa-edit"></i> Ganti Gambar</span>
                        </div>
                    </div>

                    {{-- Input File (Hidden) --}}
                    <input type="file" id="images" name="images[]" class="d-none" multiple accept="image/*" required
                        onchange="previewInsideBox()">

                    <small class="text-muted mt-2 d-block">*Disarankan rasio gambar persegi atau landscape.</small>
                </div>


                <div class="text-right mt-4">
                    <a href="{{ route('diamart.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Produk
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Efek Hover agar user tahu bisa ganti gambar
        const uploadBox = document.getElementById('upload-box');
        const hoverOverlay = document.getElementById('hover-overlay');

        uploadBox.addEventListener('mouseenter', () => {
            // Hanya munculkan overlay jika sudah ada gambar (preview container tidak hidden)
            if (!document.getElementById('preview-container').classList.contains('d-none')) {
                hoverOverlay.classList.remove('d-none');
                hoverOverlay.classList.add('d-flex');
            }
        });

        uploadBox.addEventListener('mouseleave', () => {
            hoverOverlay.classList.add('d-none');
            hoverOverlay.classList.remove('d-flex');
        });

        function previewInsideBox() {
            const input = document.getElementById('images');
            const placeholder = document.getElementById('placeholder-content');
            const previewContainer = document.getElementById('preview-container');

            // Reset
            previewContainer.innerHTML = '';

            if (input.files && input.files.length > 0) {
                // 1. Sembunyikan Placeholder (Ikon & Teks)
                placeholder.classList.add('d-none');

                // 2. Tampilkan Container Preview
                previewContainer.classList.remove('d-none');

                // Hitung lebar kolom berdasarkan jumlah gambar
                // Jika 1 gambar: full, Jika 2: bagi 2, dst (max 4 biar rapi)
                let colClass = 'col-12';
                if (input.files.length === 2) colClass = 'col-6';
                else if (input.files.length === 3) colClass = 'col-4';
                else if (input.files.length >= 4) colClass = 'col-6'; // Grid 2x2 jika banyak

                Array.from(input.files).forEach((file, index) => {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const imgDiv = document.createElement('div');

                        // Logika Grid agar gambar memenuhi kotak
                        if (input.files.length >= 4) {
                            imgDiv.classList.add('col-6', 'h-50'); // Jadi grid kotak-kotak
                        } else {
                            imgDiv.classList.add(colClass, 'h-100'); // Full height
                        }

                        imgDiv.style.border = '1px solid #fff'; // Pemisah antar gambar

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover'; // KUNCI: Gambar mengisi penuh tanpa gepeng

                        imgDiv.appendChild(img);
                        previewContainer.appendChild(imgDiv);
                    }
                    reader.readAsDataURL(file);
                });

            } else {
                // Jika batal pilih, kembalikan ke tampilan awal
                placeholder.classList.remove('d-none');
                previewContainer.classList.add('d-none');
            }
        }
    </script>
@endpush
