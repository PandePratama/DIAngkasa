@extends('admin.layouts.app')

@section('title', 'Manajemen Kredit & Cicilan')

@section('content')
    <div class="container-fluid">

        <h1 class="h3 mb-4 text-gray-800">Manajemen Kredit & Cicilan</h1>

        {{-- BAGIAN 1: DATA TRANSAKSI KREDIT (DENGAN TAB STATUS) --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-file-contract mr-2"></i> Data Transaksi Kredit</h6>
            </div>

            <div class="card-body">

                {{-- NAVIGASI TAB (Pills) --}}
                <ul class="nav nav-pills mb-4" id="creditStatusTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="ongoing-tab" data-toggle="pill" href="#ongoing"
                            role="tab">
                            <i class="fas fa-clock mr-1"></i> Sedang Berjalan
                            <span class="badge badge-light text-dark ml-1">{{ $creditsOngoing->total() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="completed-tab" data-toggle="pill" href="#completed"
                            role="tab">
                            <i class="fas fa-check-circle mr-1"></i> Sudah Lunas (Selesai)
                            <span class="badge badge-light text-dark ml-1">{{ $creditsCompleted->total() }}</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="creditStatusTabContent">

                    {{-- TAB 1: KREDIT BERJALAN (ON PROGRESS) --}}
                    <div class="tab-pane fade show active" id="ongoing" role="tabpanel">
                        <div class="alert alert-warning py-2 border-left-warning small">
                            <i class="fas fa-info-circle mr-1"></i> Daftar cicilan yang masih aktif berjalan.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#ID</th>
                                        <th>Nasabah & Barang</th>
                                        <th>Tenor</th>
                                        <th>Progres Bayar</th>
                                        <th>Sisa Tagihan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($creditsOngoing as $trx)
                                        @php
                                            // HITUNG MANUAL DARI RELASI INSTALLMENTS
                                            // Ambil jumlah cicilan yang statusnya 'paid'
                                            $sudahBayar = $trx->installments->where('status', 'paid')->count();

                                            // Logic Sisa & Persen
                                            $sisaBulan = $trx->tenor - $sudahBayar;

                                            // Safety check biar tidak minus (jika ada kesalahan data)
                                            if ($sisaBulan < 0) {
                                                $sisaBulan = 0;
                                            }

                                            $persen = $trx->tenor > 0 ? ($sudahBayar / $trx->tenor) * 100 : 0;
                                            $sisaRupiah = $sisaBulan * $trx->monthly_amount;
                                        @endphp
                                        <tr>
                                            <td>#{{ $trx->id }}</td>
                                            <td>
                                                <strong>{{ $trx->user->name }}</strong><br>
                                                <small class="text-muted">{{ $trx->product->name }}</small>
                                            </td>
                                            <td><span class="badge badge-info">{{ $trx->tenor }} Bulan</span></td>

                                            <td style="min-width: 160px">
                                                <div class="d-flex justify-content-between small mb-1">
                                                    <span>Bulan ke-{{ $sudahBayar }}</span>
                                                    <span>{{ number_format($persen, 0) }}%</span>
                                                </div>
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar bg-warning" role="progressbar"
                                                        style="width: {{ $persen }}%"></div>
                                                </div>
                                                <small class="text-muted text-xs">dari {{ $trx->tenor }} bulan</small>
                                            </td>

                                            <td>
                                                <small class="text-muted">Sisa {{ $sisaBulan }}x lagi</small><br>
                                                <strong class="text-danger">Rp
                                                    {{ number_format($sisaRupiah, 0, ',', '.') }}</strong>
                                            </td>
                                            <td>
                                                <a href="{{ route('credits.show', $trx->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada kredit
                                                berjalan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $creditsOngoing->appends(['ongoing_page' => request('ongoing_page')])->links() }}
                        </div>
                    </div>

                    {{-- TAB 2: KREDIT LUNAS (COMPLETED) --}}
                    <div class="tab-pane fade" id="completed" role="tabpanel">
                        <div class="alert alert-success py-2 border-left-success small">
                            <i class="fas fa-check mr-1"></i> Daftar kredit yang sudah lunas sepenuhnya.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#ID / Tgl Lunas</th>
                                        <th>Nasabah & Barang</th>
                                        <th>Total Nilai Kredit</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($creditsCompleted as $trx)
                                        <tr>
                                            <td>
                                                #{{ $trx->id }}<br>
                                                <small class="text-success font-weight-bold">
                                                    <i class="fas fa-calendar-check mr-1"></i>
                                                    {{ $trx->updated_at->format('d M Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <strong>{{ $trx->user->name }}</strong><br>
                                                {{ $trx->product->name }}
                                            </td>
                                            <td>
                                                @php $totalMasuk = $trx->dp_amount + ($trx->monthly_amount * $trx->tenor); @endphp
                                                <span class="text-success font-weight-bold">Rp
                                                    {{ number_format($totalMasuk, 0, ',', '.') }}</span>
                                                <br>
                                                <small class="text-muted">Tenor: {{ $trx->tenor }} Bulan</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-success px-3 py-2"><i
                                                        class="fas fa-check-circle"></i> LUNAS</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('credits.show', $trx->id) }}"
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-file-invoice"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data kredit
                                                lunas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $creditsCompleted->appends(['completed_page' => request('completed_page')])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGIAN 2: LOG PEMOTONGAN SALDO --}}
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
                                    <td class="align-middle">
                                        {{ $log->created_at->format('d M Y') }}<br>
                                        <small class="text-muted">{{ $log->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="align-middle">{{ $log->user->name ?? '-' }}</td>
                                    <td class="align-middle">
                                        @if (str_contains($log->description, 'DP 0'))
                                            <span class="badge badge-info mb-1"><i class="fas fa-bolt mr-1"></i> Angsuran
                                                Awal (Tanpa DP)</span>
                                            <div class="small text-dark">{{ $log->description }}</div>
                                        @elseif(str_contains($log->description, 'Pembayaran DP') || str_contains($log->description, 'DP Kredit'))
                                            <span class="badge badge-success mb-1"><i
                                                    class="fas fa-money-bill-wave mr-1"></i> Uang Muka (DP)</span>
                                            <div class="small text-dark">{{ $log->description }}</div>
                                        @elseif(str_contains($log->description, 'Autodebet'))
                                            <span class="badge badge-primary mb-1"><i class="fas fa-sync-alt mr-1"></i>
                                                Autodebet Berkala</span>
                                            <div class="small text-dark">{{ $log->description }}</div>
                                        @else
                                            <span class="badge badge-secondary mb-1">Transaksi Lain</span>
                                            <div class="small text-dark">{{ $log->description }}</div>
                                        @endif
                                    </td>
                                    <td class="align-middle text-danger font-weight-bold">- Rp
                                        {{ number_format($log->amount, 0, ',', '.') }}</td>
                                    <td class="align-middle">Rp {{ number_format($log->current_balance, 0, ',', '.') }}
                                    </td>
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
                <div class="mt-2">
                    {{ $mutations->appends(['mutations_page' => request('mutations_page')])->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection
