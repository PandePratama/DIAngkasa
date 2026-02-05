@extends('layouts.app')

@section('content')
    <main class="pt-10 px-4 pb-12">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- ================= LEFT : PROFILE CARD ================= --}}
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow p-6 text-center">
                    {{-- QR CODE --}}
                    <div class="flex justify-center mb-4">
                        <div id="qrcode" class="p-2 border rounded-lg bg-white"></div>
                    </div>

                    <button onclick="downloadQR()"
                        class="w-full bg-teal-600 hover:bg-teal-700 text-white text-sm py-2 rounded-lg transition">
                        Download QR Code
                    </button>

                    <div class="mt-6 border-t pt-4">
                        <h2 class="font-bold text-xl text-gray-800">{{ $user->name }}</h2>
                        <p class="text-sm text-gray-500">NIP: {{ $user->nip }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $user->unitKerja->unit_name ?? '-' }}</p>
                    </div>

                    <div class="mt-4 bg-teal-50 rounded-lg py-4">
                        <p class="text-xs text-teal-600 uppercase tracking-wider font-semibold">Saldo / Credit Limit</p>
                        <p class="font-bold text-teal-700 text-xl">
                            Rp {{ number_format($user->saldo ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- ================= RIGHT : CONTENT TABS ================= --}}
            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow p-6">

                    {{-- TAB NAVIGATION --}}
                    <div class="border-b mb-6 flex gap-6 overflow-x-auto">
                        <button
                            class="tab-btn font-semibold text-teal-600 border-b-2 border-teal-600 pb-2 transition whitespace-nowrap"
                            data-tab="profile">
                            Profile & Password
                        </button>
                        {{-- TAB BARU: KREDIT --}}
                        <button class="tab-btn text-gray-500 pb-2 hover:text-teal-500 transition whitespace-nowrap"
                            data-tab="credit">
                            Riwayat Kredit & Cicilan
                        </button>
                        <button class="tab-btn text-gray-500 pb-2 hover:text-teal-500 transition whitespace-nowrap"
                            data-tab="history">
                            History Belanja
                        </button>
                    </div>

                    {{-- NOTIFICATION --}}
                    @if (session('success'))
                        <div
                            class="mb-4 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm flex justify-between items-center">
                            <span>{{ session('success') }}</span>
                            <button onclick="this.parentElement.remove()" class="font-bold text-green-700">×</button>
                        </div>
                    @endif

                    {{-- ================= TAB CONTENT : PROFILE ================= --}}
                    <div id="tab-profile" class="tab-content space-y-8">
                        {{-- Form Update Profile & Password (Kode sama seperti sebelumnya) --}}
                        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="text-sm font-medium">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                    class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="text-sm font-medium">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="text-sm font-medium">No. Telepon</label>
                                <input type="text" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}"
                                    class="w-full border rounded px-3 py-2">
                            </div>
                            <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded">Simpan
                                Perubahan</button>
                        </form>

                        <hr>

                        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <h3 class="font-semibold text-gray-800">Ganti Password</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Password Lama</label>
                                    <input type="password" name="current_password"
                                        class="w-full border rounded-lg px-3 py-2 text-sm" required>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Password Baru</label>
                                    <input type="password" name="password"
                                        class="w-full border rounded-lg px-3 py-2 text-sm" required>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation"
                                        class="w-full border rounded-lg px-3 py-2 text-sm" required>
                                </div>
                            </div>
                            <button type="submit"
                                class="bg-gray-800 hover:bg-black text-white px-6 py-2 rounded-lg text-sm transition">Update
                                Password</button>
                        </form>
                    </div>

                    {{-- ================= TAB CONTENT : CREDIT (BARU) ================= --}}
                    <div id="tab-credit" class="tab-content hidden space-y-6">
                        @forelse ($credits as $credit)
                            @php
                                // Hitung Progress
                                $totalInstallments = $credit->tenor;
                                $paidInstallments = $credit->installments->where('status', 'paid')->count();
                                $progressPercent =
                                    $totalInstallments > 0 ? ($paidInstallments / $totalInstallments) * 100 : 0;

                                // Hitung Sisa Tagihan
                                $remainingMonths = $totalInstallments - $paidInstallments;
                                $remainingAmount = $remainingMonths * $credit->monthly_amount;
                            @endphp

                            <div class="border rounded-xl p-5 hover:shadow-md transition bg-white">
                                {{-- Header Kartu --}}
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex gap-4">
                                        <div
                                            class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                                            <i class="fa-solid fa-mobile-screen text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800">
                                                {{ $credit->product->name ?? 'Produk Dihapus' }}</h4>
                                            <p class="text-xs text-gray-500">
                                                Diajukan: {{ $credit->created_at->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        @if ($credit->status == 'paid_off' || $credit->status == 'paid')
                                            <span
                                                class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">LUNAS</span>
                                        @else
                                            <span
                                                class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">BERJALAN</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Progress Bar --}}
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="font-semibold text-gray-600">Progres Pembayaran</span>
                                        <span class="font-bold text-teal-600">{{ round($progressPercent) }}%
                                            ({{ $paidInstallments }}/{{ $totalInstallments }} Bulan)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-teal-600 h-2.5 rounded-full transition-all duration-500"
                                            style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                </div>

                                {{-- Info Grid --}}
                                <div class="grid grid-cols-2 gap-4 mb-4 text-sm bg-gray-50 p-3 rounded-lg">
                                    <div>
                                        <p class="text-xs text-gray-500">Cicilan per Bulan</p>
                                        <p class="font-bold text-gray-700">Rp
                                            {{ number_format($credit->monthly_amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Sisa Tagihan</p>
                                        <p class="font-bold text-red-600">Rp
                                            {{ number_format($remainingAmount, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                {{-- Accordion Detail Cicilan Bulanan --}}
                                <details class="group">
                                    <summary
                                        class="flex justify-between items-center font-medium cursor-pointer list-none text-sm text-teal-600 hover:text-teal-800">
                                        <span>Lihat Rincian Pemotongan Bulanan</span>
                                        <span class="transition group-open:rotate-180">
                                            <svg fill="none" height="24" shape-rendering="geometricPrecision"
                                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="1.5" viewBox="0 0 24 24" width="24">
                                                <path d="M6 9l6 6 6-6"></path>
                                            </svg>
                                        </span>
                                    </summary>
                                    <div class="text-neutral-600 mt-3 group-open:animate-fadeIn">
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-xs text-left">
                                                <thead class="bg-gray-100 text-gray-600 font-bold">
                                                    <tr>
                                                        <th class="p-2 rounded-l-lg">Bulan Ke</th>
                                                        <th class="p-2">Jatuh Tempo</th>
                                                        <th class="p-2">Nominal</th>
                                                        <th class="p-2 rounded-r-lg text-center">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    @foreach ($credit->installments as $ins)
                                                        <tr>
                                                            <td class="p-2">Bulan {{ $ins->installment_month }}</td>
                                                            <td class="p-2">
                                                                {{ \Carbon\Carbon::parse($ins->due_date)->format('d M Y') }}
                                                            </td>
                                                            <td class="p-2">Rp
                                                                {{ number_format($ins->amount, 0, ',', '.') }}</td>
                                                            <td class="p-2 text-center">
                                                                @if ($ins->status == 'paid')
                                                                    <span
                                                                        class="text-green-600 font-bold flex items-center justify-center gap-1">
                                                                        <i class="fa-solid fa-check-circle"></i> Lunas
                                                                    </span>
                                                                    @if ($ins->updated_at)
                                                                        <span
                                                                            class="text-[10px] text-gray-400 block">{{ $ins->updated_at->format('d/m/y') }}</span>
                                                                    @endif
                                                                @else
                                                                    <span class="text-gray-400 font-semibold">Belum</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </details>
                            </div>
                        @empty
                            <div class="text-center py-10 text-gray-400 border-2 border-dashed rounded-xl">
                                <i class="fa-solid fa-file-invoice text-4xl mb-3 text-gray-300"></i>
                                <p>Anda belum memiliki riwayat pengajuan kredit.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- ================= TAB CONTENT : HISTORY BELANJA ================= --}}
                    <div id="tab-history" class="tab-content hidden">
                        {{-- Form Filter & Table History Belanja (Biarkan kode lama Anda disini) --}}
                        <form method="GET" action="{{ route('profile.index') }}" class="mb-6">
                            <input type="hidden" name="tab" value="history">
                            {{-- ... isi form filter ... --}}
                            {{-- COPY PASTE FORM FILTER DARI KODE SEBELUMNYA --}}
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Dari Tanggal</label>
                                    <input type="date" name="from" value="{{ request('from') }}"
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Sampai Tanggal</label>
                                    <input type="date" name="to" value="{{ request('to') }}"
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <button type="submit"
                                        class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition shadow">Filter</button>
                                </div>
                                <div class="md:col-span-2">
                                    <a href="{{ route('profile.index', ['tab' => 'history']) }}"
                                        class="block text-center w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm transition">Reset</a>
                                </div>
                            </div>
                        </form>

                        {{-- Summary & Tabel --}}
                        <div
                            class="bg-teal-50 border border-teal-200 rounded-lg p-4 mb-6 flex justify-between items-center shadow-sm">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-teal-100 rounded-full text-teal-600"><i
                                        class="fa-solid fa-calculator"></i></div>
                                <div>
                                    <p class="text-xs text-teal-600 font-bold uppercase tracking-wide">Total Transaksi</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="block text-lg font-bold text-teal-800">Rp
                                    {{ number_format($grandTotalSemua ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm">
                            <table class="w-full text-sm text-left border-collapse">
                                <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                                    <tr>
                                        <th class="p-3 border-b">Tanggal</th>
                                        <th class="p-3 border-b">Invoice</th>
                                        <th class="p-3 border-b">Total</th>
                                        <th class="p-3 border-b text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($transactions as $trx)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="p-3 whitespace-nowrap text-gray-600">
                                                {{ $trx->created_at->format('d M Y H:i') }}</td>
                                            <td class="p-3 font-mono text-xs text-teal-600">
                                                {{ $trx->invoice_code ?? '#TRX-' . $trx->id }}</td>
                                            <td class="p-3 font-bold text-gray-700">Rp
                                                {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                                            <td class="p-3 text-center">
                                                <span
                                                    class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-green-100 text-green-700 ring-1 ring-green-200">Success</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-8 text-center text-gray-400">Tidak ada riwayat
                                                belanja.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($transactions->hasPages())
                            <div class="mt-4">{{ $transactions->appends(['tab' => 'history'])->links() }}</div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </main>

    {{-- SCRIPTS (QR & Tabs) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Initialize QR Code
            const qrContainer = document.getElementById("qrcode");
            const nipValue = "{{ trim($user->nip) }}";
            if (nipValue) {
                new QRCode(qrContainer, {
                    text: nipValue,
                    width: 200,
                    height: 200
                });
            }

            // 2. Tab Logic
            const tabs = document.querySelectorAll('.tab-btn');
            const contents = document.querySelectorAll('.tab-content');

            function activateTab(target) {
                tabs.forEach(t => {
                    t.classList.remove('text-teal-600', 'border-teal-600', 'border-b-2');
                    t.classList.add('text-gray-500');
                });
                const activeBtn = document.querySelector(`button[data-tab="${target}"]`);
                if (activeBtn) {
                    activeBtn.classList.add('text-teal-600', 'border-teal-600', 'border-b-2');
                    activeBtn.classList.remove('text-gray-500');
                }
                contents.forEach(c => c.classList.add('hidden'));
                document.getElementById('tab-' + target).classList.remove('hidden');
            }

            tabs.forEach(btn => {
                btn.addEventListener('click', () => activateTab(btn.dataset.tab));
            });

            // Auto Switch based on URL param
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('tab')) {
                activateTab(urlParams.get('tab'));
            } else if (urlParams.has('from') || urlParams.has('to')) {
                activateTab('history'); // Default ke history jika filter belanja
            }
        });

        function downloadQR() {
            // ... (Kode download QR sama) ...
            const qrCanvas = document.querySelector('#qrcode canvas');
            if (!qrCanvas) return alert("QR Code belum siap.");
            const canvas = document.createElement('canvas');
            const padding = 40;
            const size = qrCanvas.width + (padding * 2);
            canvas.width = size;
            canvas.height = size;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, size, size);
            ctx.drawImage(qrCanvas, padding, padding);
            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = "QR-{{ $user->nip }}.png";
            link.click();
        }
    </script>

    <style>
        /* Animasi sederhana untuk Accordion */
        details>summary {
            list-style: none;
        }

        details>summary::-webkit-details-marker {
            display: none;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .group-open\:animate-fadeIn[open] {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
@endsection
