@extends('admin.layouts.app')

@section('title', 'Add User')

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Add User</h6>
    </div>

    <div class="card-body">

        {{-- ERROR --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Full Name</label>
                <input type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="form-control"
                    required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text"
                    name="username"
                    value="{{ old('username') }}"
                    class="form-control"
                    required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control">
            </div>

            <div class="form-group">
                <label>NIP</label>
                <input type="text"
                    name="nip"
                    value="{{ old('nip') }}"
                    class="form-control"
                    placeholder="Opsional (khusus employee)">
            </div>

            <div class="form-group">
                <label>Unit Kerja</label>
                <input type="text"
                    name="unit_kerja"
                    value="{{ old('unit_kerja') }}"
                    class="form-control">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label>Password</label>
                    <input type="password"
                        name="password"
                        class="form-control"
                        required>
                </div>

                <div class="col-md-6">
                    <label>Confirm Password</label>
                    <input type="password"
                        name="password_confirmation"
                        class="form-control"
                        required>
                </div>
            </div>

            <button class="btn btn-primary mt-4">
                <i class="fas fa-save"></i> Save
            </button>
        </form>
    </div>
</div>
@endsection
