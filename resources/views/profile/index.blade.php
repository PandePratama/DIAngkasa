@extends('layouts.app')

@section('content')
<main class="pt-6 px-4">
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        {{-- ================= LEFT : PROFILE CARD ================= --}}
        <div class="bg-white rounded-xl shadow p-6 text-center">
            {{-- QR --}}
            <div class="flex justify-center mb-4">
                <div id="qrcode"
                    class="w-[200px] h-[200px] border rounded-lg flex items-center justify-center">
                </div>
            </div>

            <button
                onclick="downloadQR('#ffffff')"
                class="w-full bg-teal-600 hover:bg-teal-700 text-white text-sm py-2 rounded-lg">
                Download QR Code
            </button>

            <h2 class="font-semibold text-lg mt-4">{{ $user->name }}</h2>
            <p class="text-sm text-gray-600">{{ $user->nip }}</p>
            <p class="text-sm text-gray-600">{{ $user->unitKerja->unit_name ?? '-' }}</p>

            <div class="mt-4 bg-teal-50 rounded-lg py-3">
                <p class="text-xs text-gray-500">Credit Limit</p>
                <p class="font-semibold text-teal-700 text-lg">
                    Rp {{ number_format($user->credit_limit ?? 0, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- ================= RIGHT : CONTENT ================= --}}
        <div class="md:col-span-2 bg-white rounded-xl shadow p-6">

            {{-- TAB NAV --}}
            <div class="border-b mb-4 flex gap-6">
                <button
                    class="tab-btn font-semibold text-teal-600 border-b-2 border-teal-600 pb-2"
                    data-tab="profile">
                    Profile
                </button>
                <button
                    class="tab-btn text-gray-500 pb-2"
                    data-tab="history">
                    History Transaksi
                </button>
            </div>

            {{-- ================= TAB : UPDATE PROFILE ================= --}}
            <div id="tab-profile" class="tab-content">
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-sm font-medium">NIK</label>
                        <input type="text"
                            name="nik"
                            id="nik"
                            maxlength="16"
                            inputmode="numeric"
                            autocomplete="off"
                            value="{{ $user->nik }}"
                            class="form-input w-full border rounded-lg px-3 py-2 text-sm"
                            placeholder="Masukkan NIK">
                        @error('nik')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Nama</label>
                        <input type="text"
                            name="name"
                            value="{{ $user->name }}"
                            class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Email</label>
                        <input type="email"
                            name="email"
                            value="{{ $user->email }}"
                            class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="text-sm font-medium">No. Telp</label>
                        <input type="text"
                            name="no_telp"
                            id="no_telp"
                            maxlength="15"
                            inputmode="numeric"
                            autocomplete="off"
                            value="{{ $user->no_telp }}"
                            class="form-input w-full border rounded-lg px-3 py-2 text-sm"
                            placeholder="Masukkan No. Telp">
                        @error('no_telp')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- UNIT KERJA (READ ONLY) --}}
                    <div>
                        <label class="text-sm font-medium">Unit Kerja</label>
                        <input type="text"
                            value="{{ $user->unitKerja->unit_name ?? '-' }}"
                            disabled
                            class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-100 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">
                            Unit kerja hanya dapat diubah oleh admin
                        </p>
                    </div>

                    <button
                        class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm">
                        Update Profile
                    </button>
                    @if (session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm flex justify-between items-center">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="font-bold text-green-700">×</button>
                    </div>
                    @endif

                </form>

                {{-- FORM GANTI PASSWORD --}}
                <hr class="my-6">

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- PASSWORD LAMA --}}
                    <div>
                        <label class="text-sm font-medium">Password Lama</label>
                        <input type="password" name="current_password"
                            class="w-full border rounded-lg px-3 py-2 text-sm @error('current_password') border-red-500 @enderror"
                            required>

                        @error('current_password')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PASSWORD BARU --}}
                    <div>
                        <label class="text-sm font-medium">Password Baru</label>
                        <input type="password" name="password"
                            class="w-full border rounded-lg px-3 py-2 text-sm @error('password') border-red-500 @enderror"
                            required>

                        @error('password')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- KONFIRMASI --}}
                    <div>
                        <label class="text-sm font-medium">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation"
                            class="w-full border rounded-lg px-3 py-2 text-sm"
                            required>
                    </div>

                    <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm">
                        Update Password
                    </button>
                </form>

            </div>

            {{-- ================= TAB : HISTORY TRANSAKSI ================= --}}
            <div id="tab-history" class="tab-content hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 border">Tanggal</th>
                                <th class="p-2 border">Invoice</th>
                                <th class="p-2 border">Total</th>
                                <th class="p-2 border">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions ?? [] as $trx)
                            <tr class="text-center">
                                <td class="p-2 border">{{ $trx->created_at->format('d M Y') }}</td>
                                <td class="p-2 border">{{ $trx->invoice }}</td>
                                <td class="p-2 border">
                                    Rp {{ number_format($trx->total, 0, ',', '.') }}
                                </td>
                                <td class="p-2 border">
                                    <span class="px-2 py-1 rounded text-xs
                                        {{ $trx->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($trx->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-500">
                                    Belum ada transaksi
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</main>

{{-- QR LIB --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ trim($user->nip) }}",
            width: 200,
            height: 200,
            correctLevel: QRCode.CorrectLevel.H
        });

        // Tab Logic
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('text-teal-600', 'border-teal-600', 'border-b-2');
                    b.classList.add('text-gray-500');
                });

                btn.classList.add('text-teal-600', 'border-teal-600', 'border-b-2');
                btn.classList.remove('text-gray-500');

                document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
                document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
            });
        });
    });

    function downloadQR(bgColor = '#ffffff') {
        const qrCanvas = document.querySelector('#qrcode canvas');
        const canvas = document.createElement('canvas');
        const size = 300;

        canvas.width = size;
        canvas.height = size;

        const ctx = canvas.getContext('2d');
        ctx.fillStyle = bgColor;
        ctx.fillRect(0, 0, size, size);
        ctx.drawImage(qrCanvas, 40, 40, size - 80, size - 80);

        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = "QR-{{ $user->nip }}.png";
        link.click();
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        function numericOnly(input, maxLength) {

            // ⌨️ ketik & paste
            input.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, maxLength);
            });

            // 📋 paste handler
            input.addEventListener('paste', function(e) {
                e.preventDefault();

                const text = (e.clipboardData || window.clipboardData).getData('text');
                const digits = text.replace(/\D/g, '').slice(0, maxLength);

                document.execCommand('insertText', false, digits);
            });

            // 🚫 drag & drop
            input.addEventListener('drop', e => e.preventDefault());
        }

        numericOnly(document.getElementById('nik'), 16);
        numericOnly(document.getElementById('no_telp'), 15);
    });
</script>


@endsection