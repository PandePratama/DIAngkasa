@extends('admin.layouts.app')

@section('title', 'Riwayat Transaksi & Kredit')

@section('content')
    <div class="container-fluid pt-4">

        <h3 class="mb-4">Riwayat Transaksi & Cicilan</h3>

        {{-- FILTER TANGGAL --}}
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

        {{-- ALERT RINGKASAN --}}
        <div class="alert alert-info py-2 mb-4">
            <i class="fas fa-money-bill-wave mr-2"></i>
            <b>Total Omset Tunai:</b> Rp {{ number_format($grandTotalSemua ?? 0, 0, ',', '.') }}
        </div>

        {{-- NAVIGASI TAB UTAMA --}}
        <ul class="nav nav-tabs mb-3" id="trxTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="cash-tab" data-bs-toggle="tab" data-bs-target="#cash" type="button"
                    role="tab">
                    <i class="fas fa-shopping-cart text-success me-1"></i> Penjualan (Tunai)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="credit-tab" data-bs-toggle="tab" data-bs-target="#credit" type="button"
                    role="tab">
                    <i class="fas fa-file-contract text-primary me-1"></i> Data Kredit
                </button>
            </li>
            {{-- <li class="nav-item" role="presentation">
            <button class="nav-link" id="mutation-tab" data-bs-toggle="tab" data-bs-target="#mutation" type="button"
                role="tab">
                <i class="fas fa-history text-danger me-1"></i> Log Keuangan
            </button>
        </li> --}}
        </ul>

        <div class="tab-content" id="trxTabsContent">

            {{-- ==================================================== --}}
            {{-- TAB 1: TRANSAKSI TUNAI (CASH) --}}
            {{-- ==================================================== --}}
            <div class="tab-pane fade show active" id="cash" role="tabpanel">
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
                                        <td><span class="badge bg-success">Tunai / Saldo</span></td>
                                        <td>Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                                        <td>
                                            <a href="{{ route('admin.transactions.invoice', $trx->id) }}"
                                                class="btn btn-sm btn-outline-danger" title="Download Invoice"
                                                target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Tidak ada data transaksi tunai.</td>
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

            {{-- ==================================================== --}}
            {{-- TAB 2: DATA KREDIT (DENGAN SUB-TAB STATUS) --}}
            {{-- ==================================================== --}}
            <div class="tab-pane fade" id="credit" role="tabpanel">

                {{-- SUB NAVIGASI (Pills) --}}
                <ul class="nav nav-pills mb-3" id="creditStatusTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="ongoing-tab" data-toggle="pill" href="#ongoing"
                            role="tab">
                            <i class="fas fa-clock mr-1"></i> Sedang Berjalan
                            <span class="badge bg-dark text-white ms-1">{{ $creditsOngoing->total() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="completed-tab" data-toggle="pill" href="#completed"
                            role="tab">
                            <i class="fas fa-check-circle mr-1"></i> Sudah Lunas
                            <span class="badge bg-dark text-white ms-1">{{ $creditsCompleted->total() }}</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{-- SUB-TAB: BERJALAN --}}
                    <div class="tab-pane fade show active" id="ongoing" role="tabpanel">
                        <div class="card shadow border-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="bg-light">
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
                                                $sudahBayar = $trx->total_paid_month;
                                                $persen = $trx->tenor > 0 ? ($sudahBayar / $trx->tenor) * 100 : 0;
                                                $sisaBulan = $trx->tenor - $sudahBayar;
                                                $sisaRupiah = $sisaBulan * $trx->monthly_amount;
                                            @endphp
                                            <tr>
                                                <td>#{{ $trx->id }}</td>
                                                <td>
                                                    <strong>{{ $trx->user->name }}</strong><br>
                                                    <small class="text-muted">{{ $trx->product->name }}</small>
                                                </td>
                                                <td><span class="badge bg-info text-dark">{{ $trx->tenor }} Bulan</span>
                                                </td>
                                                <td style="min-width: 160px">
                                                    <div class="d-flex justify-content-between small mb-1">
                                                        <span>Bulan ke-{{ $sudahBayar }}</span>
                                                        <span>{{ number_format($persen, 0) }}%</span>
                                                    </div>
                                                    <div class="progress" style="height: 10px;">
                                                        <div class="progress-bar bg-warning"
                                                            style="width: {{ $persen }}%"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small>Sisa {{ $sisaBulan }}x</small><br>
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
                            </div>
                            <div class="card-footer bg-white">
                                {{ $creditsOngoing->appends(['ongoing_page' => request('ongoing_page')])->links() }}
                            </div>
                        </div>
                    </div>

                    {{-- SUB-TAB: LUNAS --}}
                    <div class="tab-pane fade" id="completed" role="tabpanel">
                        <div class="card shadow border-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#ID / Tgl Lunas</th>
                                            <th>Nasabah & Barang</th>
                                            <th>Total Nilai</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($creditsCompleted as $trx)
                                            <tr>
                                                <td>
                                                    #{{ $trx->id }}<br>
                                                    <small
                                                        class="text-success fw-bold">{{ $trx->updated_at->format('d M Y') }}</small>
                                                </td>
                                                <td>
                                                    <strong>{{ $trx->user->name }}</strong><br>
                                                    {{ $trx->product->name }}
                                                </td>
                                                <td>
                                                    @php $totalMasuk = $trx->dp_amount + ($trx->monthly_amount * $trx->tenor); @endphp
                                                    <span class="text-success fw-bold">Rp
                                                        {{ number_format($totalMasuk, 0, ',', '.') }}</span>
                                                </td>
                                                <td><span class="badge bg-success px-3">LUNAS</span></td>
                                                <td>
                                                    <a href="{{ route('credits.show', $trx->id) }}"
                                                        class="btn btn-sm btn-outline-success">Detail</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Belum ada kredit
                                                    lunas.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer bg-white">
                                {{ $creditsCompleted->appends(['completed_page' => request('completed_page')])->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================================================== --}}
            {{-- TAB 3: LOG KEUANGAN (MUTASI) --}}
            {{-- ==================================================== --}}
            <div class="tab-pane fade" id="mutation" role="tabpanel">
                <div class="alert alert-warning py-2 small">
                    <i class="fas fa-info-circle me-1"></i>
                    Tab ini menampilkan uang keluar dari saldo user (Autodebet, DP, dll).
                </div>

                <div class="card shadow border-0">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Deskripsi Pemotongan</th>
                                    <th>Jumlah</th>
                                    <th>Sisa Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mutations as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                        <td>{{ $log->user->name ?? '-' }}</td>
                                        <td>
                                            @if (str_contains($log->description, 'DP 0'))
                                                <span class="badge bg-info text-dark mb-1">Angsuran Awal (Tanpa DP)</span>
                                            @elseif(str_contains($log->description, 'DP') || str_contains($log->description, 'Uang Muka'))
                                                <span class="badge bg-success mb-1">Uang Muka (DP)</span>
                                            @elseif(str_contains($log->description, 'Autodebet'))
                                                <span class="badge bg-primary mb-1">Autodebet</span>
                                            @else
                                                <span class="badge bg-secondary mb-1">Lainnya</span>
                                            @endif
                                            <div class="small">{{ $log->description }}</div>
                                            <div class="small text-muted">Ref: {{ $log->reference_id }}</div>
                                        </td>
                                        <td class="text-danger fw-bold">- Rp
                                            {{ number_format($log->amount, 0, ',', '.') }}
                                        </td>
                                        <td>Rp {{ number_format($log->current_balance, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Belum ada riwayat mutasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white">
                        {{ $mutations->appends(['mutations_page' => request('mutations_page')])->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    {{-- SCRIPT MANUAL UNTUK TAB NAVIGASI (VANILLA JS) --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Fungsi helper untuk handle tab click
            function handleTabClick(containerId) {
                const buttons = document.querySelectorAll(containerId + ' a[data-toggle="pill"], ' + containerId +
                    ' button[data-bs-toggle="tab"]');

                buttons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();

                        // 1. Remove active class from buttons
                        buttons.forEach(btn => btn.classList.remove('active'));
                        // 2. Add active class to clicked button
                        this.classList.add('active');

                        // 3. Hide all tab panes in this context
                        const targetId = this.getAttribute('data-bs-target') || this.getAttribute(
                            'href');
                        const parentContent = document.querySelector(targetId).parentElement;
                        const panes = parentContent.children;

                        for (let pane of panes) {
                            pane.classList.remove('show', 'active');
                        }

                        // 4. Show target pane
                        const targetPane = document.querySelector(targetId);
                        if (targetPane) {
                            targetPane.classList.add('show', 'active');
                        }
                    });
                });
            }

            // Inisialisasi Tab Utama
            handleTabClick('#trxTabs');

            // Inisialisasi Sub-Tab Kredit (Pills)
            handleTabClick('#creditStatusTab');
        });
    </script>
@endpush
