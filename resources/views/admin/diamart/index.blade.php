@extends('admin.layouts.app')

@section('title', 'Manajemen Produk Sembako')

@section('content')
    <div class="container-fluid">

        <h1 class="h3 mb-2 text-gray-800">Daftar Produk Sembako (Diamart)</h1>
        <p class="mb-4">Kelola data barang kebutuhan sehari-hari di sini.</p>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-success">Data Produk Diamart</h6>

                <a href="{{ route('diamart.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Tambah Sembako
                </a>
            </div>
            <div class="card-body">

                {{-- FORM PEMBUNGKUS (BULK DELETE) --}}
                <form id="bulkActionForm" action="{{ route('diamart.bulk-action') }}" method="POST">
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
                                        <td>{{ $loop->iteration }}</td>
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
                                        <td>{{ $product->stock }}</td>
                                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                        <td>
                                            {{-- Tombol Edit --}}
                                            <a href="{{ route('diamart.edit', $product->id) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            {{-- Tombol Delete Satuan (AMAN DARI NESTED FORM) --}}
                                            {{-- Kita panggil fungsi JS deleteSingle dengan ID produk --}}
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="deleteSingle({{ $product->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
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

                    {{-- Tombol Bulk Delete (Muncul jika ada checkbox dipilih) --}}
                    <button type="button" id="bulk-delete-btn" onclick="confirmBulkDelete()"
                        class="btn btn-danger mt-3 d-none">
                        <i class="fas fa-trash"></i> Hapus Terpilih
                    </button>

                </form>

                {{-- FORM RAHASIA UNTUK DELETE SATUAN --}}
                {{-- Form ini akan ditembak oleh Javascript deleteSingle() --}}
                <form id="delete-form-single" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>

            </div>
        </div>

    </div>

    {{-- Script Khusus Halaman Ini --}}
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

        // 4. FUNGSI EKSEKUSI BULK DELETE (YANG SEBELUMNYA HILANG)
        function confirmBulkDelete() {
            Swal.fire({
                title: 'Hapus Produk Terpilih?',
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

        // 5. FUNGSI DELETE SATUAN (Aman dari bentrok Form)
        // 5. FUNGSI DELETE SATUAN (Aman dari bentrok Form)
        function deleteSingle(productId) {
            Swal.fire({
                title: 'Yakin hapus produk ini?',
                text: "Data tidak bisa kembali!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Ambil form rahasia
                    let form = document.getElementById('delete-form-single');

                    // --- PERBAIKAN DI SINI ---

                    // 1. Buat URL template dengan placeholder ':id'
                    // Laravel akan menganggap ':id' sebagai string biasa, jadi tidak error.
                    let url = "{{ route('diamart.destroy', ':id') }}";

                    // 2. Ganti teks ':id' dengan productId yang sebenarnya menggunakan JS
                    url = url.replace(':id', productId);

                    // 3. Set action form
                    form.action = url;

                    // Submit
                    form.submit();
                }
            });
        }
    </script>
@endsection
