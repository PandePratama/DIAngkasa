@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="card shadow">

    {{-- Header --}}
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Edit Product</h6>
    </div>

    <div class="card-body">

        {{-- Alerts --}}
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- FORM --}}
        <form action="{{ route('raditya.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- SKU & Name --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                            class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                            class="form-control" required>
                    </div>
                </div>
            </div>

            {{-- Category & Brand --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="id_category" class="form-control @error('id_category') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih Kategori --</option>
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
                    <div class="form-group">
                        <label>Brand</label>
                        <select name="id_brand" class="form-control @error('id_brand') is-invalid @enderror" required>
                            <option value="">-- Pilih Brand --</option>
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

            {{-- Stock & Warranty --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" min="0" value="{{ old('stock', $product->stock) }}"
                            class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Info Garansi</label>
                        <input type="text" name="warranty_info"
                            value="{{ old('warranty_info', $product->warranty) }}" class="form-control"
                            placeholder="Contoh: 1 Tahun Resmi">
                    </div>
                </div>
            </div>

            {{-- BARIS 4: HPP & HARGA JUAL (NEW) --}}
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
                        {{-- Input hidden --}}
                        <input type="hidden" name="hpp" id="hpp" value="{{ old('hpp', $product->hpp) }}">
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
                        {{-- Input hidden --}}
                        <input type="hidden" name="price" id="price"
                            value="{{ old('price', $product->price) }}">
                        @error('price')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Specification --}}
            <div class="form-group">
                <label>Specification</label>
                <textarea name="desc" class="form-control" rows="3">{{ old('desc', $product->desc) }}</textarea>
            </div>

            {{-- Upload Images --}}
            <div class="form-group mt-4">
                <label>Product Images</label>

                <div class="upload-box" onclick="document.getElementById('images').click()">
                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                    <p class="mb-0">Click to upload images</p>
                    <small class="text-muted">
                        Image pertama akan menjadi <strong>Primary Image</strong>
                    </small>
                </div>

                <input type="file" id="images" name="images[]" class="d-none" multiple accept="image/*"
                    onchange="handleImageUpload(event)">

                <input type="hidden" name="deleted_images" id="deleted_images">
            </div>

            {{-- Preview --}}
            <div class="row mt-3" id="image-preview">
                {{-- EXISTING IMAGES --}}
                @foreach ($product->images as $image)
                <div class="col-md-3 mb-3 image-existing" data-id="{{ $image->id }}">
                    <div class="card shadow-sm position-relative">

                        <button type="button" class="btn btn-danger btn-sm position-absolute"
                            style="top:5px;right:5px" onclick="removeExisting({{ $image->id }});"
                            data-image-id="{{ $image->id }}">
                            &times;
                        </button>

                        <img src="{{ asset('storage/' . $image->image_path) }}" class="card-img-top"
                            style="height:160px;object-fit:cover">

                        <div class="card-body text-center p-2">
                            @if ($loop->first)
                            <span class="badge badge-success">Primary</span>
                            @endif
                        </div>

                    </div>
                </div>
                @endforeach

            </div>

            {{-- Submit --}}
            <div class="text-right mt-4">
                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Product
                </button>
                <a href="{{ route('raditya.index') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>
        </form>

    </div>
</div>
@endsection

@push('styles')
<style>
    .upload-box {
        border: 2px dashed #ced4da;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        background: #f8f9fa;
        transition: 0.3s;
    }

    .upload-box:hover {
        background: #e9ecef;
        border-color: #4e73df;
    }

    .upload-box i {
        color: #4e73df;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        /* FORMAT RUPIAH */
        function formatRupiah(angka) {
            if (!angka) return '';
            // Hapus titik desimal .00 jika ada (dari database float)
            let clean = parseFloat(angka).toString();
            return clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function setupCurrencyInput(displayId, hiddenId) {
            const display = document.getElementById(displayId);
            const hidden = document.getElementById(hiddenId);

            // Set Initial Value (Saat Edit, ambil dari hidden value)
            if (hidden.value) {
                display.value = formatRupiah(hidden.value);
            }

            display.addEventListener('input', function(e) {
                let raw = this.value.replace(/\D/g, ''); // Ambil angka saja
                hidden.value = raw; // Simpan ke hidden input
                this.value = raw ? formatRupiah(raw) : ''; // Format tampilan
            });
        }

        // Terapkan ke HPP & Price
        setupCurrencyInput('hpp_display', 'hpp');
        setupCurrencyInput('price_display', 'price');
    });

    /* IMAGE UPLOAD LOGIC */
    let newFiles = [];
    let deletedImages = [];

    function handleImageUpload(event) {
        newFiles = Array.from(event.target.files);
        renderNewImages();
    }

    function renderNewImages() {
        const preview = document.getElementById('image-preview');
        document.querySelectorAll('.image-new').forEach(el => el.remove());

        newFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-3 image-new';
                col.innerHTML = `
                <div class="card shadow-sm position-relative">
                    <button type="button" class="btn btn-danger btn-sm position-absolute" style="top:5px;right:5px" onclick="removeNew(${index})">&times;</button>
                    <img src="${e.target.result}" class="card-img-top" style="height:160px;object-fit:cover">
                    <div class="card-body text-center p-2"><span class="badge badge-secondary">New</span></div>
                </div>`;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
        syncInput();
    }

    function removeNew(index) {
        newFiles.splice(index, 1);
        renderNewImages();
    }

    function removeExisting(id) {
        if (!confirm('Hapus gambar ini permanen?')) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('raditya.images.destroy', ':id') }}".replace(':id', id);

        form.innerHTML = `
        @csrf
        <input type="hidden" name="_method" value="DELETE">
    `;

        document.body.appendChild(form);
        form.submit();
    }


    function syncInput() {
        const dt = new DataTransfer();
        newFiles.forEach(file => dt.items.add(file));
        document.getElementById('images').files = dt.files;
    }
</script>
@endpush