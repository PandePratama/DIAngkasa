@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- TABEL 1: DATA TRANSAKSI KREDIT (YANG SUDAH ADA) --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-file-contract mr-2"></i> Data Transaksi Kredit</h6>
            </div>
            <div class="card-body">
                {{-- ... (Isi tabel kredit Anda yang sudah benar tadi) ... --}}
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#ID / Tgl</th>
                                <th>Nasabah & Barang</th>
                                <th>DP Awal</th> {{-- KOLOM BARU --}}
                                <th>Tagihan/Bln</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr>
                                    <td>#{{ $trx->id }} <br> <small>{{ $trx->created_at->format('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $trx->user->name }}</strong><br>
                                        {{ $trx->product->name }}
                                    </td>
                                    <td>
                                        {{-- MENAMPILKAN DP --}}
                                        <span class="text-success">Rp
                                            {{ number_format($trx->dp_amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        {{-- MENAMPILKAN TAGIHAN BULANAN --}}
                                        Rp {{ number_format($trx->monthly_amount, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        <span class="badge badge-{{ $trx->status == 'paid_off' ? 'success' : 'warning' }}">
                                            {{ $trx->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('credits.show', $trx->id) }}"
                                            class="btn btn-sm btn-info">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Kosong</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $transactions->appends(['mutations_page' => request('mutations_page')])->links() }}
                </div>
            </div>
        </div>

        {{-- TABEL 2: RIWAYAT PEMOTONGAN SALDO (BARU) --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-danger text-white d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-history mr-2"></i> Log Pemotongan Saldo (Autodebet & DP)
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Deskripsi Pemotongan</th>
                                <th>Jumlah Dipotong</th>
                                <th>Sisa Saldo User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mutations as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $log->user->name ?? '-' }}</td>
                                    <td>{{ $log->description }}</td>
                                    <td class="text-danger font-weight-bold">
                                        - Rp {{ number_format($log->amount, 0, ',', '.') }}
                                    </td>
                                    <td>Rp {{ number_format($log->current_balance, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">Belum ada aktivitas pemotongan
                                        saldo.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Pagination Tabel Bawah --}}
                <div class="mt-2">
                    {{ $mutations->appends(['credits_page' => request('credits_page')])->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection
