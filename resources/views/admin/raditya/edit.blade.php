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
        <form action="{{ route('products.update', $product->id) }}"
            method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Product Code --}}
            <div class="form-group">
                <label>Product Code</label>
                <input type="text"
                    name="product_code"
                    value="{{ old('product_code', $product->product_code) }}"
                    class="form-control"
                    required>
            </div>

            {{-- Product Name --}}
            <div class="form-group">
                <label>Product Name</label>
                <input type="text"
                    name="name"
                    value="{{ old('name', $product->name) }}"
                    class="form-control"
                    required>
            </div>

            {{-- Specification --}}
            <div class="form-group">
                <label>Specification</label>
                <textarea name="specification"
                    class="form-control">{{ old('specification', $product->specification) }}</textarea>
            </div>

            {{-- Category & Brand --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" class="form-control" required>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Brand</label>
                        <select name="brand_id" class="form-control" required>
                            @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}"
                                {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Stock & Price --}}
            <div class="row">
                <div class="col-md-6">
                    <label>Stock</label>
                    <input type="number"
                        name="stock"
                        min="0"
                        value="{{ old('stock', $product->stock) }}"
                        class="form-control"
                        required>
                </div>

                <div class="col-md-6">
                    <label>Cash Price</label>
                    <input type="number"
                        name="price"
                        step="0.01"
                        value="{{ old('price', $product->price) }}"
                        class="form-control"
                        required>
                </div>
            </div>

            {{-- Upload Images (SAMA SEPERTI CREATE) --}}
            <div class="form-group mt-4">
                <label>Product Images</label>

                <div class="upload-box" onclick="document.getElementById('images').click()">
                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                    <p class="mb-0">Click to upload images</p>
                    <small class="text-muted">
                        Image pertama akan menjadi <strong>Primary Image</strong>
                    </small>
                </div>

                <input type="file"
                    id="images"
                    name="images[]"
                    class="d-none"
                    multiple
                    accept="image/*"
                    onchange="handleImageUpload(event)">

                <input type="hidden" name="deleted_images" id="deleted_images">
            </div>

            {{-- Preview --}}
            <div class="row mt-3" id="image-preview">
                {{-- EXISTING IMAGES --}}
                @foreach ($product->images as $image)
                <div class="col-md-3 mb-3 image-existing" data-id="{{ $image->id }}">
                    <div class="card shadow-sm position-relative">

                        <button type="button"
                            class="btn btn-danger btn-sm position-absolute"
                            style="top:5px;right:5px"
                            onclick="removeExisting({{ $image->id }});"
                            data-image-id="{{ $image->id }}">
                            &times;
                        </button>

                        <img src="{{ asset('storage/'.$image->image_path) }}"
                            class="card-img-top"
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
                <a href="{{ route('products.index') }}" class="btn btn-secondary">
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
    let newFiles = [];
    let deletedImages = [];

    /* upload image baru */
    function handleImageUpload(event) {
        newFiles = Array.from(event.target.files);
        renderNewImages();
    }

    /* render image baru */
    function renderNewImages() {
        const preview = document.getElementById('image-preview');

        // hapus preview NEW saja
        document.querySelectorAll('.image-new').forEach(el => el.remove());

        newFiles.forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-3 image-new';

                col.innerHTML = `
                <div class="card shadow-sm position-relative">

                    <button type="button"
                        class="btn btn-danger btn-sm position-absolute"
                        style="top:5px;right:5px"
                        onclick="removeNew(${index})">
                        &times;
                    </button>

                    <img src="${e.target.result}"
                        class="card-img-top"
                        style="height:160px;object-fit:cover">

                    <div class="card-body text-center p-2">
                        <span class="badge badge-secondary">New</span>
                    </div>
                </div>
            `;
                preview.appendChild(col);
            };

            reader.readAsDataURL(file);
        });

        syncInput();
    }

    /* hapus image baru */
    function removeNew(index) {
        newFiles.splice(index, 1);
        renderNewImages();
    }

    /* hapus image lama */
    function removeExisting(id) {
        deletedImages.push(id);
        document.getElementById('deleted_images').value = deletedImages.join(',');

        const el = document.querySelector('.image-existing[data-id="' + id + '"]');
        if (el) el.remove();
    }

    /* sync input file */
    function syncInput() {
        const dt = new DataTransfer();
        newFiles.forEach(file => dt.items.add(file));
        document.getElementById('images').files = dt.files;
    }
</script>
@endpush