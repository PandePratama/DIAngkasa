@extends('admin.layouts.app')

@section('title', 'Add Product')

@section('content')
<div class="card shadow">

    {{-- Header --}}
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Add Product</h6>
    </div>

    <div class="card-body">

        {{-- Error --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Product Code --}}
            <div class="form-group">
                <label>Product Code</label>
                <input type="text"
                    name="product_code"
                    value="{{ old('product_code') }}"
                    class="form-control"
                    required>
            </div>

            {{-- Product Name --}}
            <div class="form-group">
                <label>Product Name</label>
                <input type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="form-control"
                    required>
            </div>
            <!-- Specifications -->
            <div class="form-group">
                <label>Specifications</label>
                <textarea name="specifications" class="form-control">{{ old('specifications') }}</textarea>
            </div>

            {{-- Category & Brand --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                            <option value="">-- Select Brand --</option>
                            @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}"
                                {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                            @endforeach
                        </select>
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
                            value="{{ old('stock', 0) }}"
                            class="form-control"
                            min="0"
                            required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Cash Price</label>
                        <input type="number"
                            name="price"
                            value="{{ old('price') }}"
                            class="form-control"
                            step="0.01"
                            required>
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
                            value="{{ old('price_6_months') }}"
                            class="form-control"
                            step="0.01">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Credit 9 Months</label>
                        <input type="number"
                            name="price_9_months"
                            value="{{ old('price_9_months') }}"
                            class="form-control"
                            step="0.01">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Credit 12 Months</label>
                        <input type="number"
                            name="price_12_months"
                            value="{{ old('price_12_months') }}"
                            class="form-control"
                            step="0.01">
                    </div>
                </div>
            </div>

            {{-- Images --}}
            <div class="form-group mt-3">
                <label>Product Images</label>
                <input type="file"
                    name="images[]"
                    class="form-control-file"
                    multiple
                    accept="image/*"
                    onchange="previewImages(event)"
                    required>
                <small class="text-muted">
                    Image pertama akan otomatis menjadi <strong>Primary Image</strong>
                </small>
            </div>

            {{-- Preview --}}
            <div class="row mt-3" id="image-preview"></div>

            {{-- Submit --}}
            <div class="text-right mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Product
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImages(event) {
        const preview = document.getElementById('image-preview');
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
                        ${index === 0
                            ? '<span class="badge badge-success">Primary</span>'
                            : ''}
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