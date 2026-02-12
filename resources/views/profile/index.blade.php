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
                            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
                            <span>{{ session('success') }}</span>
                            <button onclick="this.parentElement.remove()" class="font-bold">×</button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ================= TAB CONTENT : PROFILE ================= --}}
                    <div id="tab-profile" class="tab-content space-y-8">
                        {{-- Form Update Profile --}}
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
                                    class="w-full border rounded px-3 py-2" placeholder="masukkan email">
                            </div>
                            <div>
                                <label class="text-sm font-medium">No. Telepon</label>
                                <input type="text" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}"
                                    class="w-full border rounded px-3 py-2" placeholder="08111...">
                            </div>
                            {{-- Jika ada field NIK dan Address --}}
                            <div>
                                <label class="text-sm font-medium">NIK</label>
                                <input type="text" name="nik" value="{{ old('nik', $user->nik) }}"
                                    class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="text-sm font-medium">Alamat</label>
                                <textarea name="address" rows="3" class="w-full border rounded px-3 py-2">{{ old('address', $user->address) }}</textarea>
                            </div>

                            <button type="submit"
                                class="bg-teal-600 text-white px-6 py-2 rounded hover:bg-teal-700 transition">
                                Simpan Perubahan
                            </button>
                        </form>

                        <hr>

                        {{-- Form Ganti Password --}}
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
                                class="bg-gray-800 hover:bg-black text-white px-6 py-2 rounded-lg text-sm transition">
                                Update Password
                            </button>
                        </form>
                    </div>

                    {{-- ================= TAB CONTENT : CREDIT (REFINED) ================= --}}
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

                                // Hitung Total Bayar Awal (DP Logic)
                                if ($credit->dp_amount > 0) {
                                    $totalInitialPay = $credit->dp_amount + $credit->admin_fee;
                                } else {
                                    $totalInitialPay = $credit->monthly_amount + $credit->admin_fee;
                                }
                            @endphp

                            <div
                                class="border border-gray-200 rounded-2xl p-6 hover:shadow-lg transition-shadow duration-300 bg-white relative overflow-hidden">

                                {{-- Dekorasi --}}
                                <div
                                    class="absolute top-0 right-0 w-24 h-24 bg-teal-50 rounded-bl-full -mr-4 -mt-4 opacity-50 pointer-events-none">
                                </div>

                                {{-- 1. HEADER --}}
                                <div
                                    class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 relative z-10">
                                    <div class="flex gap-4 items-center">
                                        <div
                                            class="w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm border border-blue-100 shrink-0">
                                            <i class="fa-solid fa-mobile-screen text-2xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-lg leading-tight">
                                                {{ $credit->product->name ?? 'Produk Dihapus' }}</h4>
                                            <div class="flex items-center gap-2 text-xs text-gray-500 mt-1.5">
                                                <span class="flex items-center"><i
                                                        class="fa-regular fa-calendar mr-1.5"></i>
                                                    {{ $credit->created_at->format('d M Y') }}</span>
                                                <span class="text-gray-300">|</span>
                                                <span>Tenor <b>{{ $credit->tenor }} Bulan</b></span>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($credit->status == 'paid_off' || $credit->status == 'paid')
                                        <span
                                            class="bg-green-100 text-green-700 px-4 py-1.5 rounded-full text-xs font-bold border border-green-200 shadow-sm flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check"></i> LUNAS
                                        </span>
                                    @else
                                        <span
                                            class="bg-blue-50 text-blue-700 px-4 py-1.5 rounded-full text-xs font-bold border border-blue-200 shadow-sm flex items-center gap-1">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div> BERJALAN
                                        </span>
                                    @endif
                                </div>

                                {{-- 2. PROGRESS BAR --}}
                                <div class="mb-6 relative z-10">
                                    <div class="flex justify-between text-xs mb-2 font-medium text-gray-600">
                                        <span>Progres: <b class="text-gray-900">{{ $paidInstallments }}</b> dari
                                            {{ $totalInstallments }} Bulan</span>
                                        <span class="text-teal-600 font-bold">{{ round($progressPercent) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-teal-500 h-2.5 rounded-full transition-all duration-700 ease-out shadow-[0_0_10px_rgba(20,184,166,0.5)]"
                                            style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                </div>

                                {{-- 3. GRID UTAMA (PORSI INFO) --}}
                                <div
                                    class="grid grid-cols-2 md:grid-cols-5 gap-y-6 gap-x-4 bg-gray-50 rounded-xl p-5 border border-gray-100 mb-5 relative z-10">
                                    {{-- Cicilan --}}
                                    <div class="border-r border-gray-200 pr-2 border-r-none-mobile">
                                        <p class="text-[10px] uppercase tracking-wider text-gray-500 font-bold mb-1">
                                            Cicilan / Bulan</p>
                                        <p class="font-bold text-gray-800 text-base">Rp
                                            {{ number_format($credit->monthly_amount, 0, ',', '.') }}</p>
                                    </div>
                                    {{-- Uang Muka --}}
                                    <div
                                        class="border-r border-gray-200 pr-2 border-r-none-mobile md:px-2 pt-2 md:pt-0 border-t md:border-t-0 border-gray-200">
                                        <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Uang
                                            Muka (DP)</p>
                                        <p class="font-medium text-gray-600 text-sm">
                                            @if ($credit->dp_amount > 0)
                                                Rp {{ number_format($credit->dp_amount, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                    {{-- Admin Fee --}}
                                    <div
                                        class="md:border-r border-gray-200 pl-0 md:pl-2 md:pr-2 pt-2 md:pt-0 border-t md:border-t-0 border-gray-200">
                                        <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Biaya
                                            Admin</p>
                                        <p class="font-medium text-gray-600 text-sm">Rp
                                            {{ number_format($credit->admin_fee, 0, ',', '.') }}</p>
                                    </div>
                                    {{-- Total Bayar Awal --}}
                                    <div
                                        class="col-span-2 md:col-span-1 pl-0 md:pl-2 pt-2 md:pt-0 border-t md:border-t-0 border-gray-200 bg-teal-50 md:bg-transparent -mx-5 md:mx-0 px-5 md:px-0 py-2 md:py-0 rounded-b-xl md:rounded-none mt-2 md:mt-0">
                                        <p
                                            class="text-[10px] uppercase tracking-wider text-teal-600 md:text-gray-500 font-bold mb-1">
                                            <i class="fa-solid fa-wallet mr-1 md:hidden"></i> Total Bayar Awal
                                        </p>
                                        <p class="font-bold text-teal-700 text-base">
                                            Rp {{ number_format($totalInitialPay, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    {{-- Sisa Tagihan --}}
                                    <div class="md:border-r border-gray-200 px-0 md:px-2">
                                        <p class="text-[10px] uppercase tracking-wider text-gray-500 font-bold mb-1">Sisa
                                            Tagihan</p>
                                        @if ($remainingAmount > 0)
                                            <p class="font-bold text-red-600 text-base">Rp
                                                {{ number_format($remainingAmount, 0, ',', '.') }}</p>
                                        @else
                                            <p class="font-bold text-green-600 text-base">Rp 0</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- 4. ACCORDION DETAIL --}}
                                <details class="group relative z-10">
                                    <summary
                                        class="flex justify-between items-center font-semibold cursor-pointer list-none text-sm text-teal-600 hover:text-teal-800 transition py-2 select-none">
                                        <span class="flex items-center gap-2">
                                            <i class="fa-solid fa-list-check"></i> Lihat Jadwal & Status Pembayaran
                                        </span>
                                        <span class="transition transform group-open:rotate-180 duration-300">
                                            <i class="fa-solid fa-chevron-down"></i>
                                        </span>
                                    </summary>

                                    <div class="mt-4 border-t border-gray-100 pt-4 animate-fadeIn">
                                        <div class="overflow-hidden rounded-lg border border-gray-200">
                                            <table class="w-full text-xs text-left">
                                                <thead class="bg-gray-50 text-gray-600 font-bold uppercase tracking-wide">
                                                    <tr>
                                                        <th class="p-3">Bulan</th>
                                                        <th class="p-3">Jatuh Tempo</th>
                                                        <th class="p-3">Nominal</th>
                                                        <th class="p-3 text-center">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100 bg-white">
                                                    @foreach ($credit->installments as $ins)
                                                        @php
                                                            // Logic Visual Nominal (Termasuk Admin jika DP 0 & Bulan 1)
                                                            $nominalShow = $ins->amount;
                                                            if (
                                                                $credit->dp_amount == 0 &&
                                                                $ins->installment_month == 1
                                                            ) {
                                                                $nominalShow += $credit->admin_fee;
                                                            }
                                                        @endphp
                                                        <tr class="hover:bg-gray-50 transition">
                                                            <td class="p-3 font-medium text-gray-700">
                                                                Ke-{{ $ins->installment_month }}</td>
                                                            <td class="p-3 text-gray-500">
                                                                {{ \Carbon\Carbon::parse($ins->due_date)->format('d M Y') }}
                                                            </td>
                                                            <td class="p-3 font-mono text-gray-600">
                                                                Rp {{ number_format($nominalShow, 0, ',', '.') }}
                                                                @if ($credit->dp_amount == 0 && $ins->installment_month == 1)
                                                                    <span
                                                                        class="text-[9px] text-gray-400 block leading-tight">(Termasuk
                                                                        Admin)</span>
                                                                @endif
                                                            </td>
                                                            <td class="p-3 text-center">
                                                                @if ($ins->status == 'paid')
                                                                    <div class="inline-flex flex-col items-center">
                                                                        <span
                                                                            class="text-green-600 font-bold flex items-center gap-1 bg-green-50 px-2 py-0.5 rounded-full border border-green-100">
                                                                            <i class="fa-solid fa-check text-[10px]"></i>
                                                                            Lunas
                                                                        </span>
                                                                        @if ($ins->updated_at)
                                                                            <span
                                                                                class="text-[9px] text-gray-400 mt-0.5">{{ $ins->updated_at->format('d/m/y') }}</span>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    <span
                                                                        class="text-gray-400 font-medium bg-gray-100 px-2 py-0.5 rounded-full">Belum</span>
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
                            <div
                                class="flex flex-col items-center justify-center py-16 px-4 text-center border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50">
                                <div
                                    class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm mb-4">
                                    <i class="fa-solid fa-file-invoice text-3xl text-gray-300"></i>
                                </div>
                                <h3 class="text-gray-900 font-bold text-lg">Belum Ada Cicilan</h3>
                                <p class="text-gray-500 text-sm max-w-xs mt-1">Anda belum memiliki riwayat pengajuan kredit
                                    gadget saat ini.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- ================= TAB CONTENT : HISTORY BELANJA ================= --}}
                    <div id="tab-history" class="tab-content hidden">
                        {{-- Form Filter --}}
                        <form method="GET" action="{{ route('profile.index') }}" class="mb-6">
                            <input type="hidden" name="tab" value="history">
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

                        {{-- Summary --}}
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

                        {{-- Tabel History --}}
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
                                                @php
                                                    $methodRaw = $trx->payment_method;
                                                    if (empty($methodRaw) && $trx->purchaseType) {
                                                        $methodRaw = $trx->purchaseType->code;
                                                    }
                                                    $method = strtolower($methodRaw ?? '');
                                                    $payStatus = strtolower($trx->payment_status ?? 'unpaid');
                                                @endphp

                                                @if ($method == 'balance')
                                                    <span
                                                        class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-green-100 text-green-700 border border-green-200 inline-flex items-center gap-1">
                                                        <i class="fa-solid fa-check-circle"></i> Lunas (Saldo)
                                                    </span>
                                                @elseif ($method == 'cash')
                                                    @if ($payStatus == 'paid')
                                                        <span
                                                            class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-green-100 text-green-700 border border-green-200 inline-flex items-center gap-1">
                                                            <i class="fa-solid fa-money-bill-wave"></i> Lunas (Cash)
                                                        </span>
                                                    @else
                                                        <span
                                                            class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-orange-100 text-orange-700 border border-orange-200 inline-flex items-center gap-1">
                                                            <i class="fa-solid fa-cash-register"></i> Bayar di Kasir
                                                        </span>
                                                    @endif
                                                @elseif ($method == 'credit')
                                                    <span
                                                        class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-blue-100 text-blue-700 border border-blue-200 inline-flex items-center gap-1">
                                                        <i class="fa-solid fa-clock-rotate-left"></i> Cicilan
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-gray-100 text-gray-600 border border-gray-200">
                                                        {{ $method ?: '-' }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-8 text-center text-gray-400">
                                                <div class="flex flex-col items-center justify-center gap-2">
                                                    <i class="fa-solid fa-basket-shopping text-2xl text-gray-300"></i>
                                                    <span>Tidak ada riwayat belanja.</span>
                                                </div>
                                            </td>
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

    {{-- SCRIPTS --}}
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
                activateTab('history');
            }
        });

        function downloadQR() {
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
        @media (max-width: 768px) {
            .border-r-none-mobile {
                border-right: none;
            }
        }

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
