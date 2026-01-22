@extends('admin.layouts.app')

{{-- Tampilkan judul dinamis --}}
@section('title', 'Tambah Kategori ' . ucfirst($group))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Kategori {{ ucfirst($group) }}</h6>
        </div>
        <div class="card-body">

            <form action="{{ route('categories.store') }}" method="POST">
                @csrf

                {{-- ==================================================== --}}
                {{-- BAGIAN PENTING: INPUT HIDDEN GROUP --}}
                {{-- Ini mengirimkan nilai 'diamart' atau 'raditya' ke Controller --}}
                <input type="hidden" name="group" value="{{ $group }}">
                {{-- ==================================================== --}}

                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>

                {{-- Tombol Batal kembali ke group yang benar --}}
                <a href="{{ route('categories.index', ['group' => $group]) }}" class="btn btn-secondary">Batal</a>
            </form>

        </div>
    </div>
@endsection
