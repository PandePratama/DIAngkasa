@extends('admin.layouts.app')

@section('title', 'Manajemen Produk Raditya')

@section('content')
    <div class="container-fluid">

        <h1 class="h3 mb-2 text-gray-800">Daftar Produk Raditya</h1>
        <p class="mb-4">Kelola data barang elektronik, gadget, dan furniture di sini.</p>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Produk</h6>

                <div>
                    <a href="{{ route('raditya.export.excel') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('raditya.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Gadget
                    </a>
                </div>
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

                {{-- Search and Per Page Controls --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form action="{{ route('raditya.index') }}" method="GET" class="form-inline">
                            <label class="mr-2">Show</label>
                            <select name="per_page" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <label>entries</label>
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('raditya.index') }}" method="GET" class="form-inline float-right">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari nama, SKU, kategori, brand..." value="{{ request('search') }}"
                                    aria-label="Search" style="min-width: 250px;">
                                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    @if (request('search'))
                                        <a href="{{ route('raditya.index', ['per_page' => request('per_page', 10)]) }}"
                                            class="btn btn-secondary">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- FORM PEMBUNGKUS BULK DELETE --}}
                <form id="bulkActionForm" action="{{ route('raditya.bulk-action') }}" method="POST">
                    @csrf

                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    {{-- Checkbox Master --}}
                                    <th style="width: 10px;">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>SKU</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Brand</th>
                                    <th>Stok</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        {{-- Checkbox Item --}}
                                        <td>
                                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                                class="select-item">
                                        </td>
                                        <td>{{ $products->firstItem() + $loop->index }}</td>
                                        <td>
                                            @if ($product->primaryImage)
                                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                                    alt="{{ $product->name }}" width="50" class="img-thumbnail">
                                            @else
                                                <span class="badge badge-secondary">No Image</span>
                                            @endif
                                        </td>
                                        <td>{{ $product->sku }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->category_name ?? '-' }}</td>
                                        <td>{{ $product->brand->brand_name ?? '-' }}</td>
                                        <td>{{ $product->stock }}</td>
                                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                        <td>
                                            {{-- Tombol Edit --}}
                                            <a href="{{ route('raditya.edit', $product->id) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            {{-- Tombol Delete Satuan (Panggil JS, BUKAN Form) --}}
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="deleteSingle({{ $product->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Belum ada data produk gadget.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Tombol Bulk Delete (Muncul di luar tabel jika checkbox dipilih) --}}
                    <button type="button" id="bulk-delete-btn" onclick="confirmBulkDelete()"
                        class="btn btn-danger mt-3 d-none">
                        <i class="fas fa-trash"></i> Hapus Terpilih
                    </button>
                </form>

                {{-- FORM RAHASIA UNTUK DELETE SATUAN --}}
                <form id="delete-form-single" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of
                        {{ $products->total() }} entries
                        @if (request('search'))
                            <span class="text-muted">(filtered from search: "{{ request('search') }}")</span>
                        @endif
                    </div>
                    <div>
                        {{ $products->appends(['per_page' => request('per_page', 10), 'search' => request('search')])->links() }}
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.select-item');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

            // 1. Fungsi Toggle Tombol Bulk Delete
            function toggleBulkButton() {
                const checkedCount = document.querySelectorAll('.select-item:checked').length;
                if (checkedCount > 0) {
                    bulkDeleteBtn.classList.remove('d-none');
                    bulkDeleteBtn.innerHTML = `<i class="fas fa-trash"></i> Hapus (${checkedCount}) Terpilih`;
                } else {
                    bulkDeleteBtn.classList.add('d-none');
                }
            }

            // 2. Event Listener Select All
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    toggleBulkButton();
                });
            }

            // 3. Event Listener Checkbox Item
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    selectAll.checked = [...checkboxes].every(c => c.checked);
                    toggleBulkButton();
                });
            });
        });

        // 4. Fungsi Konfirmasi SweetAlert untuk Bulk Delete
        function confirmBulkDelete() {
            Swal.fire({
                title: 'Hapus Gadget Terpilih?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('bulkActionForm').submit();
                }
            });
        }

        // 5. Fungsi Delete Satuan
        function deleteSingle(productId) {
            Swal.fire({
                title: 'Yakin hapus gadget ini?',
                text: "Data tidak bisa kembali!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('delete-form-single');
                    let url = "{{ route('raditya.destroy', ':id') }}";
                    url = url.replace(':id', productId);
                    form.action = url;
                    form.submit();
                }
            });
        }
    </script>
@endpush
