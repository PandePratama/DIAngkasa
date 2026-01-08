@extends('admin.layouts.app')

@section('title', 'Add Category')

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Add Category</h6>
    </div>

    <div class="card-body">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Category Name</label>
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
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                Back
            </a>
        </form>
    </div>
</div>
@endsection