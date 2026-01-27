@extends('admin.layouts.app')

@section('title', 'Add Unit Kerja')

@section('content')

<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Add Unit Kerja</h6>
    </div>

    <div class="card-body">
        <form action="{{ route('unit-kerja.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Unit Kerja Name</label>
                <input type="text"
                    name="unit_name"
                    value="{{ old('unit_name') }}"
                    class="form-control"
                    required>
            </div>

            <button class="btn btn-primary">Save</button>
            <a href="{{ route('unit-kerja.index') }}" class="btn btn-secondary">
                Back
            </a>
        </form>

    </div>
</div>
@endsection