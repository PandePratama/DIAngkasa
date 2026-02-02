@extends('admin.layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="container-fluid pt-4">

        <h3 class="mb-4">Riwayat Transaksi</h3>

        {{-- FILTER TANGGAL (Berlaku untuk kedua tab) --}}
        <form method="GET" class="row g-2 mb-4 bg-white p-3 rounded shadow-sm">
            <div class="col-md-3">
                <label class="small text-muted">Dari Tanggal</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label class="small text-muted">Sampai Tanggal</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
            </div>
        </form>

        {{-- NAVIGASI TAB --}}
        <ul class="nav nav-tabs mb-3" id="trxTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="cash-tab" data-bs-toggle="tab" data-bs-target="#cash" type="button"
                    role="tab">
                    <i class="fas fa-money-bill-wave text-success me-1"></i> Tunai / Saldo
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="credit-tab" data-bs-toggle="tab" data-bs-target="#credit" type="button"
                    role="tab">
                    <i class="fas fa-file-contract text-primary me-1"></i> Kredit / Cicilan
                </button>
            </li>
        </ul>

        <div class="tab-content" id="trxTabsContent">

            {{-- TAB 1: TRANSAKSI BIASA (Existing Code) --}}
            <div class="tab-pane fade show active" id="cash" role="tabpanel">
                <div class="alert alert-info py-2">
                    <b>Total Omset Tunai:</b> Rp {{ number_format($grandTotalSemua, 0, ',', '.') }}
                </div>

                <div class="card shadow border-0">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>#ID</th>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Metode</th>
                                    <th>Total</th>
                                    <th>Saldo User</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $trx)
                                    <tr>
                                        <td>#{{ $trx->id }}</td>
                                        <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $trx->user->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $trx->user->nip ?? '' }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $code = $trx->purchaseType
                                                    ? $trx->purchaseType->code
                                                    : $trx->payment_method;
                                                $badge = $code == 'balance' ? 'bg-primary' : 'bg-success';
                                                $label = $code == 'balance' ? 'Potong Saldo' : 'Tunai';
                                            @endphp
                                            <span class="badge {{ $badge }}">{{ $label }}</span>
                                        </td>
                                        <td>Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                                        <td>
                                            @if ($trx->balance_after)
                                                Rp {{ number_format($trx->balance_after, 0, ',', '.') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('transactions.print_invoice', $trx->id) }}" target="_blank"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Tidak ada data transaksi tunai.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white">
                        {{ $transactions->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

            {{-- TAB 2: TRANSAKSI KREDIT (NEW - Sesuai Schema) --}}
            <div class="tab-pane fade" id="credit" role="tabpanel">
                <div class="card shadow border-0">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>#ID</th>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Barang</th> {{-- id_product --}}
                                    <th>Tenor</th> {{-- tenor --}}
                                    <th>Cicilan/Bln</th> {{-- monthly_amount --}}
                                    <th>Status</th> {{-- status --}}
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($creditTransactions as $credit)
                                    <tr>
                                        <td>#{{ $credit->id }}</td>
                                        <td>{{ $credit->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $credit->user->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $credit->user->nip ?? '' }}</small>
                                        </td>
                                        <td>
                                            {{-- Relasi ke Product Raditya --}}
                                            {{ $credit->product->name ?? 'Produk Dihapus' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark">{{ $credit->tenor }} Bulan</span>
                                        </td>
                                        <td class="fw-bold">
                                            Rp {{ number_format($credit->monthly_amount, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match ($credit->status) {
                                                    'progress' => 'bg-warning text-dark',
                                                    'paid' => 'bg-success',
                                                    'complete' => 'bg-success',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">
                                                {{ ucfirst($credit->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{-- Tombol ke detail cicilan (Kartu Piutang) --}}
                                            {{-- Pastikan route 'credits.show' atau sejenisnya ada --}}
                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">Tidak ada data pengajuan kredit.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white">
                        {{ $creditTransactions->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
