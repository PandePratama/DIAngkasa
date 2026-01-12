@extends('admin.layouts.app')

@section('title', 'Edit Minimarket Product')

@section('content')
<div class="card shadow">

    {{-- Header --}}
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Edit Minimarket Product</h6>
    </div>

    <div class="card-body">

        {{-- Alerts --}}
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- FORM --}}
        <form action="{{ route('minimarket-products.update', $product->id) }}"
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
                    class="form-control @error('product_code') is-invalid @enderror">
                @error('product_code')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Product Name --}}
            <div class="form-group">
                <label>Product Name</label>
                <input type="text"
                    name="name"
                    value="{{ old('name', $product->name) }}"
                    class="form-control @error('name') is-invalid @enderror">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Specifications --}}
            <div class="form-group">
                <label>Specification</label>
                <textarea name="specification" class="form-control">{{ old('specification', $product->specification) }}</textarea>
            </div>

            {{-- Category --}}
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                    <option value="">-- Select Category --</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Stock & Cash Price --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number"
                            name="stock"
                            min="0"
                            value="{{ old('stock', $product->stock) }}"
                            class="form-control @error('stock') is-invalid @enderror">
                        @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Cash Price</label>
                        <input type="number"
                            name="price"
                            step="0.01"
                            value="{{ old('price', $product->price) }}"
                            class="form-control @error('price') is-invalid @enderror">
                        @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Upload New Images --}}
            <div class="form-group mt-3">
                <label>Add New Images</label>
                <input type="file"
                    name="images[]"
                    class="form-control-file @error('images.*') is-invalid @enderror"
                    multiple
                    accept="image/*"
                    onchange="previewNewImages(event)">
                <small class="text-muted">
                    Upload gambar tambahan (gambar lama tidak dihapus)
                </small>
                @error('images.*')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            {{-- Preview New Images --}}
            <div class="row mt-3" id="new-image-preview"></div>

            {{-- Submit Buttons --}}
            <div class="mt-4 text-right">
                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Product
                </button>
                <a href="{{ route('minimarket-products.index') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>

        </form>

        {{-- Existing Images --}}
        <hr>
        <h6 class="font-weight-bold mb-3">Existing Product Images</h6>
        <div class="row">
            @forelse ($product->images as $image)
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="{{ asset('storage/' . $image->image_path) }}"
                        class="card-img-top"
                        style="height:150px;object-fit:cover">

                    <div class="card-body text-center p-2">
                        @if ($image->is_primary)
                        <span class="badge badge-success mb-2">Primary</span>
                        @endif

                        <form action=""
                            method="POST"
                            onsubmit="return confirm('Delete this image?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm btn-block">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-muted text-center">No images uploaded</p>
            </div>
            @endforelse
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewNewImages(event) {
        const preview = document.getElementById('new-image-preview');
        preview.innerHTML = '';

        Array.from(event.target.files).forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-3';

                col.innerHTML = `
                <div class="card">
                    <img src="${e.target.result}" 
                         class="card-img-top" 
                         style="height:150px;object-fit:cover;">
                    <div class="card-body text-center p-2">
                        ${index === 0 ? '<span class="badge badge-success">Primary</span>' : ''}
                    </div>
                </div>
            `;

                preview.appendChild(col);
            };

            reader.readAsDataURL(file);
        });
    }
</script>
@endpush