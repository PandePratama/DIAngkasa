@extends('admin.layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="container-fluid pt-4">

        <h3 class="mb-4">Riwayat Transaksi</h3>

        {{-- FILTER --}}
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        {{-- TOTAL --}}
        {{-- PERBAIKAN 1: Ganti $total menjadi $grandTotalSemua --}}
        <div class="alert alert-info">
            <b>Total Transaksi:</b> Rp {{ number_format($grandTotalSemua ?? ($total ?? 0), 0, ',', '.') }}
        </div>

        {{-- TABLE --}}
        <div class="card shadow">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Id Transaksi</th>
                            <th>Waktu</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Metode Bayar</th>
                            <th>Total Belanja</th>
                            <th>Saldo Terpotong</th>
                            <th>Sisa Saldo User (Saat Ini)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $trx)
                            <tr>
                                <td>{{ $trx->id }}</td>
                                <td>{{ $trx->created_at->format('d-m-Y H:i') }}</td>
                                <td>{{ $trx->user->nip ?? '-' }}</td>
                                <td>{{ $trx->user->name ?? 'User Terhapus' }}</td>

                                {{-- GANTI BAGIAN <td> METODE BAYAR DENGAN INI --}}
                                <td>
                                    @php
                                        // 1. Cek Data: Prioritaskan ambil dari Relasi (purchaseType), baru kolom biasa
                                        $code = null;

                                        if ($trx->purchaseType) {
                                            $code = $trx->purchaseType->code; // Ambil dari tabel relasi
                                        } elseif ($trx->payment_method) {
                                            $code = $trx->payment_method; // Fallback ke kolom string
                                        }

                                        // 2. Tentukan Label & Warna berdasarkan CODE yang didapat
                                        if ($code == 'balance') {
                                            $label = 'Potong Saldo';
                                            $badgeClass = 'badge bg-primary text-white'; // Bootstrap class
                                            $icon = 'fa-wallet';
                                        } elseif ($code == 'cash') {
                                            $label = 'Cash / Tunai';
                                            $badgeClass = 'badge bg-success text-white';
                                            $icon = 'fa-money-bill-wave';
                                        } else {
                                            // Jika null atau tidak dikenali
                                            $label = $code ? ucfirst($code) : '-';
                                            $badgeClass = 'badge bg-secondary text-white';
                                            $icon = 'fa-circle-question';
                                        }
                                    @endphp

                                    <span class="{{ $badgeClass }} p-2" style="font-size: 0.85rem;">
                                        <i class="fa-solid {{ $icon }} me-1"></i> {{ $label }}
                                    </span>
                                </td>

                                <td>Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>

                                {{-- KOLOM SALDO TERPOTONG --}}
                                <td class="font-bold text-danger">
                                    @php
                                        // 1. Definisikan ulang logic pengambilan kodenya (Sama seperti kolom sebelah)
                                        // Cek Relasi dulu, baru cek kolom biasa
                                        $code = null;
                                        if ($trx->purchaseType) {
                                            $code = $trx->purchaseType->code;
                                        } else {
                                            $code = $trx->payment_method;
                                        }
                                    @endphp

                                    {{-- 2. Gunakan variabel $code yang sudah pasti terisi --}}
                                    @if ($code == 'balance')
                                        - Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                                    @else
                                        {{-- Untuk Cash atau status lainnya --}}
                                        <span class="text-muted" style="font-weight: normal;">0 (Tunai)</span>
                                    @endif
                                </td>

                                {{-- SISA SALDO USER SAAT INI --}}
                                <td>
                                    @if (isset($trx->balance_after))
                                        {{-- Tampilkan saldo yang tersimpan saat transaksi terjadi --}}
                                        Rp {{ number_format($trx->balance_after, 0, ',', '.') }}
                                    @else
                                        {{-- Fallback untuk data lama (sebelum fitur ini ada) atau transaksi Cash --}}
                                        <span class="text-muted small" style="font-size: 0.75rem;">
                                            Tidak tercatat
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center p-4">Belum ada data transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PERBAIKAN 2: Tambahkan Tombol Pagination --}}
            <div class="card-footer">
                {{ $transactions->withQueryString()->links() }}
            </div>
        </div>

    </div>
@endsection
