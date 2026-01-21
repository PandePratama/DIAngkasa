@extends('admin.layouts.app')

@section('title', 'Edit Unit Kerja')

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Edit Unit Kerja</h6>
    </div>

    <div class="card-body">
        <form action="{{ route('unit-kerja.update', $unitKerja->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group
">
                <label>Unit Kerja Name</label>
                <input type="text" name="unit_name"
                    value="{{ $unitKerja->unit_name }}"
                    class="form-control @error('unit_name') is-invalid @enderror"
                    required>

                @error('unit_name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('unit-kerja.index') }}" class="btn btn-secondary">
                Back
            </a>
        </form>
    </div>
</div>
@endsection