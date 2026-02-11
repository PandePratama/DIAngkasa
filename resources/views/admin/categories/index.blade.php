@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kategori Produk</h6>
            <a href="{{ route('categories.create', ['group' => $group]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Category
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

            {{-- FORM PEMBUNGKUS BULK DELETE --}}
            <form id="bulkActionForm" action="{{ route('categories.bulk-action') }}" method="POST">
                @csrf

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                {{-- Checkbox Master --}}
                                <th width="10">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th width="50">#</th>
                                <th>Name</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    {{-- Checkbox Item --}}
                                    <td>
                                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                            class="select-item">
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $category->category_name }}</td>
                                    <td>
                                        <a href="{{ route('categories.edit', $category->id) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Tombol Delete Satuan (Panggil JS, BUKAN Form) --}}
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="deleteSingle({{ $category->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No categories found
                                    </td>
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
                title: 'Hapus Kategori Terpilih?',
                text: "Semua data yang terkait dengan kategori ini mungkin akan terpengaruh!",
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
        function deleteSingle(categoryId) {
            Swal.fire({
                title: 'Yakin hapus kategori ini?',
                text: "Data tidak bisa kembali!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('delete-form-single');

                    // Sesuaikan route destroy untuk kategori
                    let url = "{{ route('categories.destroy', ':id') }}";
                    url = url.replace(':id', categoryId);

                    form.action = url;
                    form.submit();
                }
            });
        }
    </script>
@endpush
