@extends('layouts.app')

@section('content')
<main class="pt-10 px-4 pb-12">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- ================= LEFT : PROFILE CARD ================= --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow p-6 text-center">
                {{-- QR CODE CONTAINER --}}
                <div class="flex justify-center mb-4">
                    <div id="qrcode" class="p-2 border rounded-lg bg-white">
                        {{-- QR Code akan muncul di sini --}}
                    </div>
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
                <div class="border-b mb-6 flex gap-6">
                    <button class="tab-btn font-semibold text-teal-600 border-b-2 border-teal-600 pb-2 transition"
                        data-tab="profile">
                        Profile & Password
                    </button>
                    <button class="tab-btn text-gray-500 pb-2 hover:text-teal-500 transition" data-tab="history">
                        History Transaksi
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
                    {{-- UPDATE PROFILE --}}
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="text-sm font-medium">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full border rounded px-3 py-2">
                            @error('name')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full border rounded px-3 py-2">
                            @error('email')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium">No. Telepon</label>
                            <input type="text" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}"
                                class="w-full border rounded px-3 py-2">
                            @error('no_telp')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium">NIK</label>
                            <input type="text" name="nik" value="{{ old('nik', $user->nik) }}"
                                class="w-full border rounded px-3 py-2">
                            @error('nik')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium">Alamat</label>
                            <input type="text" name="address" value="{{ old('address', $user->address) }}"
                                class="w-full border rounded px-3 py-2">
                            @error('address')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="bg-teal-600 text-white px-6 py-2 rounded">
                            Simpan Perubahan
                        </button>
                    </form>
                    <hr>

                    {{-- UPDATE PASSWORD --}}
                    <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <h3 class="font-semibold text-gray-800">Ganti Password</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Password Lama</label>
                                <input type="password" name="current_password"
                                    class="w-full border rounded-lg px-3 py-2 text-sm @error('current_password') border-red-500 @enderror"
                                    required>
                                @error('current_password')
                                <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Password Baru</label>
                                <input type="password" name="password"
                                    class="w-full border rounded-lg px-3 py-2 text-sm @error('password') border-red-500 @enderror"
                                    required>
                                @error('password')
                                <p class="text-red-600 text-[10px] mt-1">{{ $message }}</p>
                                @enderror
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

                {{-- ================= TAB CONTENT : HISTORY ================= --}}
                <div id="tab-history" class="tab-content hidden">

                    {{-- 1. FORM FILTER (Versi Tailwind) --}}
                    <form method="GET" action="{{ route('profile.index') }}" class="mb-6">
                        {{-- PENTING: Input hidden ini agar saat filter disubmit, tab history otomatis aktif --}}
                        <input type="hidden" name="tab" value="history">

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                            {{-- Input Dari Tanggal --}}
                            <div class="md:col-span-4">
                                <label class="block text-xs font-bold text-gray-500 mb-1">Dari Tanggal</label>
                                <input type="date" name="from" value="{{ request('from') }}"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                            </div>

                            {{-- Input Sampai Tanggal --}}
                            <div class="md:col-span-4">
                                <label class="block text-xs font-bold text-gray-500 mb-1">Sampai Tanggal</label>
                                <input type="date" name="to" value="{{ request('to') }}"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                            </div>

                            {{-- Tombol Filter --}}
                            <div class="md:col-span-2">
                                <button type="submit"
                                    class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition shadow">
                                    Filter
                                </button>
                            </div>

                            {{-- Tombol Reset --}}
                            <div class="md:col-span-2">
                                <a href="{{ route('profile.index', ['tab' => 'history']) }}"
                                    class="block text-center w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm transition">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- 2. TOTAL SUMMARY (Versi Tailwind) --}}
                    {{-- Menggunakan $grandTotalSemua sesuai perbaikan controller --}}
                    <div
                        class="bg-teal-50 border border-teal-200 rounded-lg p-4 mb-6 flex justify-between items-center shadow-sm">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-teal-100 rounded-full text-teal-600">
                                {{-- Icon Calculator (FontAwesome) --}}
                                <i class="fa-solid fa-calculator"></i>
                            </div>
                            <div>
                                <p class="text-xs text-teal-600 font-bold uppercase tracking-wide">Total Transaksi
                                    (Filtered)</p>
                                <p class="text-xs text-gray-500">Berdasarkan periode yang dipilih</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block text-lg font-bold text-teal-800">
                                Rp {{ number_format($grandTotalSemua ?? ($total ?? 0), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- 3. TABEL DATA --}}
                    <div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                                <tr>
                                    <th class="p-3 border-b">Tanggal</th>
                                    <th class="p-3 border-b">Invoice</th>
                                    <th class="p-3 border-b">Total Belanja</th>
                                    <th class="p-3 border-b text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($transactions as $trx)
                                <tr class="hover:bg-gray-50 transition">
                                    {{-- TANGGAL --}}
                                    <td class="p-3 whitespace-nowrap text-gray-600">
                                        {{ $trx->created_at->format('d M Y H:i') }}
                                    </td>

                                    {{-- INVOICE --}}
                                    <td class="p-3 font-mono text-xs text-teal-600">
                                        {{ $trx->invoice ?? '#TRX-' . str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}
                                    </td>

                                    {{-- NOMINAL --}}
                                    <td class="p-3 font-bold text-gray-700">
                                        Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="p-3 text-center">
                                        @php
                                        $status = $trx->status ?? 'success';
                                        $colorClass = match ($status) {
                                        'paid',
                                        'success'
                                        => 'bg-green-100 text-green-700 ring-1 ring-green-200',
                                        'pending'
                                        => 'bg-yellow-100 text-yellow-700 ring-1 ring-yellow-200',
                                        'failed',
                                        'cancelled'
                                        => 'bg-red-100 text-red-700 ring-1 ring-red-200',
                                        default => 'bg-gray-100 text-gray-700 ring-1 ring-gray-200',
                                        };
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $colorClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            {{-- Icon Empty State --}}
                                            <svg class="w-12 h-12 mb-3 text-gray-300" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            <span class="font-medium">Tidak ada transaksi ditemukan.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- 4. PAGINATION --}}
                    @if ($transactions->hasPages())
                    <div class="mt-4">
                        {{ $transactions->appends(['tab' => 'history'])->withQueryString()->links() }}
                    </div>
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

        // Pastikan nipValue tidak kosong agar QR Code valid
        if (nipValue) {
            const qrcode = new QRCode(qrContainer, {
                text: nipValue,
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        // 2. Tab Logic & Auto Switch
        const tabs = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');

        function activateTab(target) {
            // Reset tabs visual
            tabs.forEach(t => {
                t.classList.remove('text-teal-600', 'border-teal-600', 'border-b-2');
                t.classList.add('text-gray-500');
            });

            // Activate specific tab visual
            const activeBtn = document.querySelector(`button[data-tab="${target}"]`);
            if (activeBtn) {
                activeBtn.classList.add('text-teal-600', 'border-teal-600', 'border-b-2');
                activeBtn.classList.remove('text-gray-500');
            }

            // Show content
            contents.forEach(c => c.classList.add('hidden'));
            const contentDiv = document.getElementById('tab-' + target);
            if (contentDiv) contentDiv.classList.remove('hidden');
        }

        // Event Listener Click
        tabs.forEach(btn => {
            btn.addEventListener('click', () => {
                activateTab(btn.dataset.tab);
            });
        });

        // 3. Auto Switch Tab Logic (Jika ada parameter filter/page di URL)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('tab')) {
            activateTab(urlParams.get('tab'));
        } else if (urlParams.has('from') || urlParams.has('to') || urlParams.has('page')) {
            // Fallback jika tidak ada param 'tab' tapi ada indikasi sedang filter/paging history
            activateTab('history');
        }

    });

    // 4. Download QR logic
    function downloadQR(bgColor = '#ffffff') {
        const qrCanvas = document.querySelector('#qrcode canvas');
        if (!qrCanvas) return alert("QR Code belum siap.");

        const canvas = document.createElement('canvas');
        const padding = 40;
        const size = qrCanvas.width + (padding * 2);

        canvas.width = size;
        canvas.height = size;

        const ctx = canvas.getContext('2d');
        // Fill background
        ctx.fillStyle = bgColor;
        ctx.fillRect(0, 0, size, size);
        // Draw original QR onto new canvas
        ctx.drawImage(qrCanvas, padding, padding);

        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = "QR-{{ $user->nip }}.png";
        link.click();
    }
</script>
@endsection