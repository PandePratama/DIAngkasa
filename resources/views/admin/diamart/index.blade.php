@extends('admin.layouts.app')

@section('title', 'Manajemen Produk Sembako')

@section('content')
    <div class="container-fluid">

        <h1 class="h3 mb-2 text-gray-800">Daftar Produk Sembako (Diamart)</h1>
        <p class="mb-4">Kelola data barang kebutuhan sehari-hari di sini.</p>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-success">Data Produk Diamart</h6>

                {{-- PERBAIKAN: Link ke route diamart.create --}}
                <a href="{{ route('diamart.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Tambah Sembako
                </a>
            </div>
            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                {{-- <th>SKU</th> --}}
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                {{-- Diamart mungkin menampilkan brand atau tidak, tergantung kebutuhan --}}
                                <th>Brand</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($product->primaryImage)
                                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                                alt="{{ $product->name }}" width="50" class="img-thumbnail">
                                        @else
                                            <span class="badge badge-secondary">No Image</span>
                                        @endif
                                    </td>
                                    {{-- <td>{{ $product->sku }}</td> --}}
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->category_name ?? '-' }}</td>
                                    <td>{{ $product->brand->brand_name ?? '-' }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td>
                                        {{-- PERBAIKAN: Link ke route diamart.edit --}}
                                        <a href="{{ route('diamart.edit', $product->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- PERBAIKAN: Form ke route diamart.destroy --}}
                                        <form action="{{ route('diamart.destroy', $product->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus produk sembako ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Belum ada data produk sembako.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
