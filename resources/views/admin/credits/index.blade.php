{{-- 1. EXTENDS: Sesuaikan 'layouts.admin' dengan nama file layout utama Anda --}}
@extends('admin.layouts.app')

{{-- 2. SECTION: Sesuaikan 'content' dengan nama @yield di layout utama --}}
@section('content')

    <div class="container-fluid"> {{-- Opsional: Agar ada padding kiri-kanan --}}

        {{-- Paste Kode Anda Di Sini --}}
        <div class="card shadow mb-4"> {{-- Tambah shadow mb-4 biar lebih cantik --}}
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Transaksi Kredit</h6>
                <div>
                    <a href="?status=active" class="btn btn-sm btn-primary shadow-sm"><i
                            class="fas fa-check fa-sm text-white-50"></i> Sedang Jalan</a>
                    <a href="?status=bad_debt" class="btn btn-sm btn-danger shadow-sm"><i
                            class="fas fa-exclamation-triangle fa-sm text-white-50"></i> Macet</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead> {{-- Hapus class thead-dark jika pakai style SB Admin default --}}
                            <tr>
                                <th>#ID / Tgl</th>
                                <th>Nasabah & Barang</th>
                                <th>Tenor</th>
                                <th>Tagihan/Bln</th>
                                <th>Progress Bayar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr>
                                    <td>
                                        <strong>#{{ $trx->id }}</strong><br>
                                        <small class="text-muted">{{ $trx->created_at->format('d M Y') }}</small>
                                    </td>

                                    <td>
                                        <div class="font-weight-bold">{{ $trx->user->name ?? 'Guest' }}</div>
                                        <small class="text-primary">{{ $trx->product->name }}</small>
                                    </td>

                                    <td>
                                        <span class="badge badge-info">{{ $trx->tenor }} Bulan</span>
                                    </td>

                                    <td>
                                        Rp {{ number_format($trx->monthly_installment_base, 0, ',', '.') }}
                                    </td>

                                    <td style="min-width: 150px;"> {{-- Kasih min-width biar progress bar tidak gepeng --}}
                                        @php
                                            $percent = ($trx->paid_months / $trx->tenor) * 100;
                                        @endphp
                                        <div class="d-flex justify-content-between mb-1">
                                            <small>Ke-{{ $trx->paid_months }} dari {{ $trx->tenor }}</small>
                                            <small>{{ round($percent) }}%</small>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $percent }}%"></div>
                                        </div>
                                    </td>

                                    <td>
                                        @if ($trx->status == 'paid_off')
                                            <span class="badge badge-success">LUNAS</span>
                                        @elseif($trx->status == 'bad_debt')
                                            <span class="badge badge-dark">MACET (Bad Debt)</span>
                                        @else
                                            @if ($trx->is_overdue)
                                                <span class="badge badge-danger blink">NUNGGAK!</span>
                                                <div class="small text-danger mt-1">Telat Bayar</div>
                                            @else
                                                <span class="badge badge-success">Lancar</span>
                                            @endif
                                        @endif
                                    </td>

                                    <td>
                                        {{-- Pastikan route ini sudah didefinisikan di web.php --}}
                                        <a href="{{ route('credits.show', $trx->id) }}"
                                            class="btn btn-sm btn-info shadow-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center p-5">Belum ada data transaksi kredit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>

    </div>

@endsection

@section('styles')
    <style>
        .blink {
            animation: blinker 1.5s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }
    </style>
@endsection
