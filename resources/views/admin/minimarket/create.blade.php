@extends('admin.layouts.app')

@section('title', 'Add Minimarket Product')

@section('content')
<div class="card shadow">

    {{-- Header --}}
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Add Minimarket Product</h6>
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

        <form action="{{ route('minimarket-products.store') }}"
              method="POST"
              enctype="multipart/form-data">
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

            {{-- Specifications --}}
            <div class="form-group">
                <label>Specifications</label>
                <textarea name="specification"
                          class="form-control">{{ old('specification') }}</textarea>
            </div>

            {{-- Category --}}
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

            {{-- Stock & Price --}}
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
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Credit 3 Months</label>
                        <input type="number"
                               name="price_3_months"
                               value="{{ old('price_3_months') }}"
                               class="form-control"
                               step="0.01">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Credit 6 Months</label>
                        <input type="number"
                               name="price_6_months"
                               value="{{ old('price_6_months') }}"
                               class="form-control"
                               step="0.01">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Credit 9 Months</label>
                        <input type="number"
                               name="price_9_months"
                               value="{{ old('price_9_months') }}"
                               class="form-control"
                               step="0.01">
                    </div>
                </div>

                <div class="col-md-3">
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

            {{-- IMAGES (DISAMAKAN DENGAN products/create) --}}
            <div class="form-group mt-3">
                <label>Product Images</label>

                <div class="upload-box"
                     onclick="document.getElementById('images').click()">
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
                       onchange="handleImageUpload(event)"
                       required>
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

{{-- STYLE --}}
@push('styles')
<style>
.upload-box {
    border: 2px dashed #ced4da;
    border-radius: 10px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    background: #f8f9fa;
    transition: .3s;
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

{{-- SCRIPT --}}
@push('scripts')
<script>
let selectedFiles = [];

function handleImageUpload(event){
    selectedFiles = Array.from(event.target.files);
    renderPreview();
}

function renderPreview(){
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';

    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();

        reader.onload = function(e){
            const col = document.createElement('div');
            col.className = 'col-md-3 mb-3';

            col.innerHTML = `
                <div class="card shadow-sm position-relative">

                    <button type="button"
                            class="btn btn-danger btn-sm position-absolute"
                            style="top:5px;right:5px"
                            onclick="removeImage(${index})">
                        &times;
                    </button>

                    <img src="${e.target.result}"
                         class="card-img-top"
                         style="height:160px;object-fit:cover">

                    <div class="card-body text-center p-2">
                        ${index === 0
                            ? '<span class="badge badge-success">Primary</span>'
                            : '<span class="badge badge-secondary">Image</span>'}
                    </div>

                </div>
            `;
            preview.appendChild(col);
        };

        reader.readAsDataURL(file);
    });

    syncFileInput();
}

function removeImage(index){
    selectedFiles.splice(index, 1);
    renderPreview();
}

function syncFileInput(){
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    document.getElementById('images').files = dt.files;
}
</script>
@endpush
