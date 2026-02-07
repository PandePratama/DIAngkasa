@extends('admin.layouts.app')

@section('title', 'Report Bulanan')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <h4 class="mb-4">
        Rekap Transaksi Bulanan
    </h4>

    {{-- FILTER --}}
    <form method="GET" action="{{ route('reports.monthly') }}" class="mb-4">
        <div class="row g-2 align-items-end">

            {{-- BULAN --}}
            <div class="col-md-3">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-control">
                    @foreach ([
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                    4 => 'April', 5 => 'Mei', 6 => 'Juni',
                    7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                    10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ] as $key => $label)
                    <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- TAHUN --}}
            <div class="col-md-2">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-control">
                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                    @endfor
                </select>
            </div>

            {{-- SEARCH --}}
            <div class="col-md-3">
                <label class="form-label">Cari Nama / NIP</label>
                <input type="text" id="searchInput" class="form-control"
                    placeholder="Ketik nama atau NIP...">
            </div>

            {{-- BUTTON --}}
            <div class="col-md-2">
                <button class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>

        </div>
    </form>

    {{-- TABLE --}}
    <div class="card shadow">
        <div class="card-body">

            <table class="table table-bordered" id="rekapTable">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Unit Kerja</th>
                        <th>Total Transaksi</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekap as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->nip }}</td>
                        <td>{{ $row->unit_name ?? '-' }}</td>
                        <td>
                            Rp {{ number_format($row->total_transaksi, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('report.pdf', [$row->id, $bulan, $tahun]) }}"
                                target="_blank"
                                class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            Tidak ada transaksi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>

{{-- SEARCH SCRIPT --}}
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let keyword = this.value.toLowerCase();
        let rows = document.querySelectorAll('#rekapTable tbody tr');

        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(keyword) ? '' : 'none';
        });
    });
</script>
@endsection