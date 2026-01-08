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

        @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
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

            <!-- Specifications -->
            <div class="form-group">
                <label>Specification</label>
                <textarea name="specification" class="form-control">{{ old('specification', $product->specification) }}</textarea>
            </div>

            {{-- Category & Brand --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id"
                            class="form-control @error('category_id') is-invalid @enderror">
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
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Brand</label>
                        <select name="brand_id"
                            class="form-control @error('brand_id') is-invalid @enderror">
                            <option value="">-- Select Brand --</option>
                            @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}"
                                {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
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

            {{-- Credit Prices --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Credit 6 Months</label>
                        <input type="number"
                            name="price_6_months"
                            step="0.01"
                            value="{{ old('price_6_months', $product->price_6_months) }}"
                            class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Credit 9 Months</label>
                        <input type="number"
                            name="price_9_months"
                            step="0.01"
                            value="{{ old('price_9_months', $product->price_9_months) }}"
                            class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Credit 12 Months</label>
                        <input type="number"
                            name="price_12_months"
                            step="0.01"
                            value="{{ old('price_12_months', $product->price_12_months) }}"
                            class="form-control">
                    </div>
                </div>
            </div>

            {{-- Upload Images --}}
            <div class="form-group mt-3">
                <label>Add New Images</label>
                <input type="file"
                    name="images[]"
                    class="form-control-file @error('images.*') is-invalid @enderror"
                    multiple>
                <small class="text-muted">
                    Upload gambar tambahan (gambar lama tidak dihapus)
                </small>
                @error('images.*')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="mt-4 text-right">
                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Product
                </button>

                <a href="{{ route('products.index') }}"
                    class="btn btn-secondary">
                    Back
                </a>
            </div>
        </form>

        {{-- IMAGE LIST --}}
        <hr>
        <h6 class="font-weight-bold mb-3">Product Images</h6>

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

                        <form action="{{ route('products.images.destroy', $image->id) }}"
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