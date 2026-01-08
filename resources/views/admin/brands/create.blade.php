@extends('admin.layouts.app')

@section('title', 'Add Brand')

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Add Brand</h6>
    </div>

    <div class="card-body">
        <form action="{{ route('brands.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Brand Name</label>
                <input type="text" name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    required>

                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn btn-primary">
                <i class="fas fa-save"></i> Save
            </button>
            <a href="{{ route('brands.index') }}" class="btn btn-secondary">
                Back
            </a>
        </form>
    </div>
</div>
@endsection