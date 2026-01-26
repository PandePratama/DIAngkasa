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
                    {{-- FORM FILTER --}}
                    <form method="GET" action="" class="mb-4 flex gap-2 items-end">
                        <div>
                            <label class="text-xs font-bold text-gray-500">Dari</label>
                            <input type="date" name="from" value="{{ request('from') }}"
                                class="border rounded p-1 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500">Sampai</label>
                            <input type="date" name="to" value="{{ request('to') }}"
                                class="border rounded p-1 text-sm">
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">
                            Filter
                        </button>
                    </form>

                    {{-- INFORMASI TOTAL --}}
                    <div class="mb-4 p-3 bg-blue-50 text-blue-800 rounded-lg text-sm">
                        Total Belanja : <span class="font-bold">Rp
                            {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    {{-- NOTIFICATION --}}
                    @if (session('success'))
                        <div
                            class="mb-4 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm flex justify-between items-center">
                            <span>{{ session('success') }}</span>
                            <button onclick="this.parentElement.remove()" class="font-bold text-green-700">×</button>
                        </div>
                    @endif

                    {{-- TAB CONTENT : PROFILE --}}
                    <div id="tab-profile" class="tab-content space-y-8">
                        {{-- UPDATE PROFILE --}}
                        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ $user->name }}"
                                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-teal-500 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" value="{{ $user->email }}"
                                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-teal-500 mt-1">
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Unit Kerja</label>
                                <input type="text" value="{{ $user->unitKerja->unit_name ?? '-' }}" disabled
                                    class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 cursor-not-allowed mt-1">
                                <p class="text-[10px] text-gray-400 mt-1 italic">* Unit kerja hanya dapat diubah oleh Admin.
                                </p>
                            </div>
                            <button type="submit"
                                class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg text-sm transition shadow-sm">
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

                    {{-- TAB CONTENT : HISTORY --}}
                    <div id="tab-history" class="tab-content hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left border-collapse">
                                <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                                    <tr>
                                        <th class="p-3 border-b">Tanggal</th>
                                        <th class="p-3 border-b">Invoice / ID</th>
                                        <th class="p-3 border-b">Nominal</th>
                                        <th class="p-3 border-b text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transactions as $trx)
                                        <tr class="hover:bg-gray-50 transition">
                                            {{-- 1. TANGGAL --}}
                                            <td class="p-3 border-b">
                                                {{ $trx->created_at->format('d M Y H:i') }}
                                            </td>

                                            {{-- 2. INVOICE (Menggunakan ID karena di Admin pakai ID) --}}
                                            <td class="p-3 border-b font-mono text-xs">
                                                @if (isset($trx->invoice))
                                                    {{ $trx->invoice }}
                                                @else
                                                    {{-- Jika tidak ada kolom invoice, generate dari ID --}}
                                                    #TRX-{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}
                                                @endif
                                            </td>

                                            {{-- 3. TOTAL/NOMINAL (Menggunakan 'amount' sesuai kode Admin) --}}
                                            <td class="p-3 border-b font-semibold text-gray-700">
                                                Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                            </td>

                                            {{-- 4. STATUS --}}
                                            <td class="p-3 border-b text-center">
                                                {{-- Cek apakah kolom status ada, jika tidak anggap 'success' --}}
                                                @php
                                                    $status = $trx->status ?? 'success';
                                                    $colorClass = match ($status) {
                                                        'paid', 'success' => 'bg-green-100 text-green-700',
                                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                                        'failed', 'cancelled' => 'bg-red-100 text-red-700',
                                                        default => 'bg-gray-100 text-gray-700',
                                                    };
                                                @endphp

                                                <span
                                                    class="px-2 py-1 rounded-full text-[10px] font-bold uppercase {{ $colorClass }}">
                                                    {{ $status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-8 text-center text-gray-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <svg class="w-10 h-10 mb-2 text-gray-300" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                        </path>
                                                    </svg>
                                                    <span>Belum ada riwayat transaksi.</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
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

            const qrcode = new QRCode(qrContainer, {
                text: nipValue,
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            // 2. Tab Logic
            const tabs = document.querySelectorAll('.tab-btn');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(btn => {
                btn.addEventListener('click', () => {
                    const target = btn.dataset.tab;

                    // Reset tabs
                    tabs.forEach(t => t.classList.remove('text-teal-600', 'border-teal-600',
                        'border-b-2'));
                    tabs.forEach(t => t.classList.add('text-gray-500'));

                    // Active tab
                    btn.classList.add('text-teal-600', 'border-teal-600', 'border-b-2');
                    btn.classList.remove('text-gray-500');

                    // Show content
                    contents.forEach(c => c.classList.add('hidden'));
                    document.getElementById('tab-' + target).classList.remove('hidden');
                });
            });
        });

        // 3. Download QR logic
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
