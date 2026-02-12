@extends('admin.layouts.app')

@section('title', 'Unit Kerja')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Unit Kerja</h6>
            <a href="{{ route('unit-kerja.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Unit Kerja
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
            {{-- TAMBAHKAN ALERT ERROR INI --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- Search and Per Page Controls --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="{{ route('unit-kerja.index') }}" method="GET" class="form-inline">
                        <label class="mr-2">Show</label>
                        <select name="per_page" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                        <label>entries</label>
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    </form>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('unit-kerja.index') }}" method="GET" class="form-inline float-right">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama unit kerja..."
                                value="{{ request('search') }}" aria-label="Search">
                            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if (request('search'))
                                    <a href="{{ route('unit-kerja.index', ['per_page' => request('per_page', 10)]) }}"
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
            <form id="bulkActionForm" action="{{ route('unit-kerja.bulk-action') }}" method="POST">
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
                            @forelse ($unitKerja as $unit_kerja)
                                <tr>
                                    {{-- Checkbox Item --}}
                                    <td>
                                        <input type="checkbox" name="unit_kerja_ids[]" value="{{ $unit_kerja->id }}"
                                            class="select-item">
                                    </td>
                                    <td>{{ $unitKerja->firstItem() + $loop->index }}</td>
                                    <td>{{ $unit_kerja->unit_name }}</td>
                                    <td>
                                        <a href="{{ route('unit-kerja.edit', $unit_kerja->id) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Tombol Delete Satuan (Panggil JS, BUKAN Form) --}}
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="deleteSingle({{ $unit_kerja->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No unit kerja found
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

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $unitKerja->firstItem() ?? 0 }} to {{ $unitKerja->lastItem() ?? 0 }} of
                    {{ $unitKerja->total() }} entries
                    @if (request('search'))
                        <span class="text-muted">(filtered from search: "{{ request('search') }}")</span>
                    @endif
                </div>
                <div>
                    {{ $unitKerja->appends(['per_page' => request('per_page', 10), 'search' => request('search')])->links() }}
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
                title: 'Hapus Unit Kerja Terpilih?',
                text: "Karyawan atau data yang terhubung dengan unit kerja ini mungkin akan terpengaruh!",
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
        function deleteSingle(unitId) {
            Swal.fire({
                title: 'Yakin hapus unit kerja ini?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('delete-form-single');

                    // Sesuaikan route destroy untuk unit-kerja
                    let url = "{{ route('unit-kerja.destroy', ':id') }}";
                    url = url.replace(':id', unitId);

                    form.action = url;
                    form.submit();
                }
            });
        }
    </script>
@endpush
