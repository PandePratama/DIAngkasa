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

        {{-- NAVIGASI TAB (SAYA AKTIFKAN KEMBALI & TAMBAH TAB MUTASI) --}}
        <ul class="nav nav-tabs mb-3" id="trxTabs" role="tablist">
            {{-- Tab 1: Penjualan Tunai --}}
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="cash-tab" data-bs-toggle="tab" data-bs-target="#cash" type="button"
                    role="tab">
                    <i class="fas fa-shopping-cart text-success me-1"></i> Penjualan (Tunai)
                </button>
            </li>

            {{-- Tab 2: Data Kredit (Untuk lihat progres tenor) --}}
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="credit-tab" data-bs-toggle="tab" data-bs-target="#credit" type="button"
                    role="tab">
                    <i class="fas fa-file-contract text-primary me-1"></i> Data Kredit (Kontrak)
                </button>
            </li>

            {{-- Tab 3: Mutasi Saldo (Untuk lihat autodebet bulanan) --}}
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="mutation-tab" data-bs-toggle="tab" data-bs-target="#mutation" type="button"
                    role="tab">
                    <i class="fas fa-history text-danger me-1"></i> Log Keuangan (Autodebet)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="trxTabsContent">

            {{-- ==================================================== --}}
            {{-- TAB 1: TRANSAKSI BELANJA (INVOICE) --}}
            {{-- ==================================================== --}}
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
                                            <span class="badge bg-success">Tunai / Saldo</span>
                                        </td>
                                        <td>Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                                        <td>
                                            <a href="{{ route('transactions.print_invoice', $trx->id) }}" target="_blank"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-print"></i>
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
            {{-- TAB 2: DATA KREDIT (LIHAT PROGRES TENOR DISINI) --}}
            {{-- ==================================================== --}}
            <div class="tab-pane fade" id="credit" role="tabpanel">
                <div class="card shadow border-0">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>#ID</th>
                                    <th>Nasabah</th>
                                    <th>Barang</th>
                                    <th>Tenor</th>
                                    <th>Progres Bayar</th> {{-- KOLOM PENTING --}}
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($creditTransactions as $credit)
                                    {{-- HITUNG PROGRES (Sudah bayar berapa bulan?) --}}
                                    @php
                                        // Menghitung jumlah cicilan yang statusnya 'paid'
                                        $sudahBayar = $credit->installments->where('status', 'paid')->count();
                                        $persen = ($sudahBayar / $credit->tenor) * 100;
                                    @endphp

                                    <tr>
                                        <td>#{{ $credit->id }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $credit->user->name ?? '-' }}</div>
                                        </td>
                                        <td>{{ $credit->product->name ?? 'Produk Dihapus' }}</td>
                                        <td>
                                            <span class="badge bg-info text-dark">{{ $credit->tenor }} Bulan</span>
                                        </td>

                                        {{-- LOGIC TAMPILAN PROGRES --}}
                                        <td style="min-width: 150px;">
                                            <div class="d-flex justify-content-between small mb-1">
                                                <span>Bulan ke-{{ $sudahBayar }}</span>
                                                <span>dari {{ $credit->tenor }}</span>
                                            </div>
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ $persen }}%"
                                                    aria-valuenow="{{ $persen }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            @php
                                                $statusClass = match ($credit->status) {
                                                    'progress' => 'bg-warning text-dark',
                                                    'paid_off' => 'bg-success',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">
                                                {{ ucfirst($credit->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Tidak ada data kredit.</td>
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

            {{-- ==================================================== --}}
            {{-- TAB 3: MUTASI SALDO (LIHAT BUKTI POTONG DISINI) --}}
            {{-- ==================================================== --}}
            <div class="tab-pane fade" id="mutation" role="tabpanel">
                <div class="alert alert-warning py-2 small">
                    <i class="fas fa-info-circle me-1"></i>
                    Tab ini menampilkan setiap uang yang keluar dari saldo user (Termasuk Autodebet Bulan ke-2, ke-3, dst).
                </div>

                <div class="card shadow border-0">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Keterangan Transaksi</th>
                                    <th>Nominal</th>
                                    <th>Sisa Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- PASTIKAN CONTROLLER MENGIRIM $mutations --}}
                                @forelse ($mutations as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $log->user->name ?? '-' }}</td>
                                        <td>
                                            {{-- LOGIC TAMPILAN DESKRIPSI (YANG ANDA MAKSUD) --}}
                                            @if (str_contains($log->description, 'Autodebet'))
                                                {{-- Jika Autodebet Cicilan --}}
                                                <span class="badge bg-primary mb-1">
                                                    <i class="fas fa-sync-alt me-1"></i> Cicilan Otomatis
                                                </span>
                                                <div class="fw-bold text-dark">{{ $log->description }}</div>
                                            @elseif(str_contains($log->description, 'DP'))
                                                {{-- Jika Pembayaran DP --}}
                                                <span class="badge bg-success mb-1">Uang Muka (DP)</span>
                                                <div class="fw-bold text-dark">{{ $log->description }}</div>
                                            @else
                                                {{-- Transaksi Lain --}}
                                                {{ $log->description }}
                                            @endif

                                            <div class="small text-muted mt-1">Ref: {{ $log->reference_id }}</div>
                                        </td>
                                        <td class="text-danger fw-bold">
                                            - Rp {{ number_format($log->amount, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            Rp {{ number_format($log->current_balance, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Belum ada riwayat mutasi saldo.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white">
                        {{ $mutations->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ambil semua tombol tab
            const tabButtons = document.querySelectorAll('#trxTabs button');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // 1. Hapus class 'active' dari semua tombol tab
                    tabButtons.forEach(btn => btn.classList.remove('active'));

                    // 2. Tambahkan class 'active' ke tombol yang diklik
                    this.classList.add('active');

                    // 3. Sembunyikan semua konten tab
                    const tabContents = document.querySelectorAll('.tab-pane');
                    tabContents.forEach(content => {
                        content.classList.remove('show', 'active');
                    });

                    // 4. Munculkan konten tab yang sesuai target
                    const targetId = this.getAttribute('data-bs-target');
                    const targetContent = document.querySelector(targetId);
                    if (targetContent) {
                        targetContent.classList.add('show', 'active');
                    }
                });
            });
        });
    </script>
@endpush
