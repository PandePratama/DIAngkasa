@extends('layouts.app')

@section('content')
    <main class="pt-[20px] max-w-6xl mx-auto px-4 pb-24">
        <h1 class="text-xl font-bold mb-6 text-gray-800">Konfirmasi Pembayaran</h1>

        {{-- FORM PEMBAYARAN --}}
        <form id="payment-form" method="POST" action="{{ route('payment.process') }}">
            @csrf

            {{-- 1. VALIDASI ERROR --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl mb-6">
                    <div class="font-bold mb-1">Terjadi Kesalahan:</div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                {{-- SISI KIRI: ITEM & METODE --}}
                <div class="md:col-span-2 space-y-6">

                    {{-- 2. RINCIAN BARANG (LOGIKA NULL SAFE) --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Rincian Barang</h2>

                        <div class="space-y-4">
                            @forelse($cart->items as $item)
                                @php
                                    // A. Tentukan Produk (Diamart atau Raditya)
                                    $product = null;
                                    if ($item->id_product_diamart) {
                                        $product = $item->productDiamart;
                                    } elseif ($item->id_product_diraditya) {
                                        $product = $item->productDiraditya;
                                    }

                                    // B. Cek Ketersediaan Produk (Safety)
                                    if (!$product) {
                                        continue;
                                    }

                                    // C. Ambil Harga & Gambar
                                    $price = $product->price ?? 0;
                                    $imagePath = optional($product->primaryImage)->image_path;
                                    $image = $imagePath ? asset('storage/' . $imagePath) : asset('images/no-image.png');
                                @endphp

                                <div
                                    class="flex items-center justify-between border-b border-gray-50 pb-4 last:border-0 last:pb-0">
                                    <div class="flex items-center gap-4">
                                        {{-- Gambar Produk --}}
                                        <div
                                            class="w-16 h-16 bg-gray-50 rounded-lg flex items-center justify-center p-2 border border-gray-100 overflow-hidden">
                                            <img src="{{ $product->primaryImage->image_url ?? asset('images/placeholder.png') }}" alt="{{ $product->name }}"
                                                class="w-full h-full object-contain">
                                        </div>

                                        {{-- Nama & Qty --}}
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 line-clamp-1">
                                                {{ $product->name }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $item->qty }} x Rp {{ number_format($price, 0, ',', '.') }}
                                            </p>
                                            <span
                                                class="text-[10px] px-2 py-0.5 rounded-full {{ $item->id_product_diamart ? 'bg-blue-100 text-blue-600' : 'bg-orange-100 text-orange-600' }}">
                                                {{ $item->id_product_diamart ? 'Diamart' : 'Raditya' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Subtotal per Item --}}
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-gray-900">
                                            Rp {{ number_format($price * $item->qty, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <p class="text-gray-400 text-sm">Keranjang Anda kosong.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- 3. METODE PEMBAYARAN --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Metode Pembayaran</h2>

                        <div class="space-y-3">
                            {{-- OPSI A: POTONG SALDO --}}
                            <label
                                class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:border-teal-500 transition shadow-sm bg-gray-50 has-[:checked]:border-teal-50 has-[:checked]:bg-teal-50">
                                <input type="radio" name="payment_method" value="balance"
                                    class="payment-radio h-4 w-4 text-teal-600 focus:ring-teal-500" checked>
                                <div class="ml-3 block">
                                    <span class="font-bold text-sm text-gray-800 block">Potong Saldo (Otomatis)</span>
                                    <span class="text-xs text-gray-500">Sisa Saldo: Rp
                                        {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</span>
                                </div>
                            </label>

                            {{-- OPSI B: CASH --}}
                            <label
                                class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:border-teal-500 transition shadow-sm bg-gray-50 has-[:checked]:border-teal-50 has-[:checked]:bg-teal-50">
                                <input type="radio" name="payment_method" value="cash"
                                    class="payment-radio h-4 w-4 text-teal-600 focus:ring-teal-500">
                                <div class="ml-3 block">
                                    <span class="font-bold text-sm text-gray-800 block">Cash / Tunai</span>
                                    <span class="text-xs text-gray-500">Bayar di kasir toko</span>
                                </div>
                            </label>

                            {{-- OPSI C: KREDIT (HANYA UNTUK RADITYA) --}}
                            {{-- OPSI C: KREDIT (HANYA UNTUK RADITYA) --}}
                            {{-- OPSI C: KREDIT (HANYA UNTUK RADITYA) --}}
                            @if ($cart->business_unit == 'raditya')
                                @php
                                    // Ambil harga produk untuk acuan kalkulasi di JS
                                    // Asumsi: Credit hanya 1 item, ambil item pertama
                                    $creditItem = $cart->items->first();
                                    $productPrice = $creditItem->productDiraditya->price ?? 0;
                                    // HPP dibutuhkan untuk logic margin (UpPrice), jika tidak ada pakai price
                                    $productHpp =
                                        $creditItem->productDiraditya->hpp > 0
                                            ? $creditItem->productDiraditya->hpp
                                            : $productPrice;
                                @endphp

                                <label
                                    class="group relative flex flex-col p-6 border rounded-3xl cursor-pointer transition bg-white shadow-sm hover:shadow-md hover:border-teal-400 has-[:checked]:border-teal-600 has-[:checked]:shadow-lg">

                                    {{-- HEADER RADIO --}}
                                    <div class="flex items-center gap-4 mb-4">
                                        {{-- PENTING: Saya tambahkan data-price dan data-hpp disini --}}
                                        <input type="radio" name="payment_method" value="credit"
                                            data-price="{{ $productPrice }}" data-hpp="{{ $productHpp }}"
                                            class="payment-radio h-5 w-5 text-teal-600 focus:ring-teal-600">

                                        <div>
                                            <span class="font-semibold text-base text-gray-900 block">
                                                Ajukan Kredit / Cicilan
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                Tenor 3, 6, 9, hingga 12 bulan
                                            </span>
                                        </div>
                                    </div>

                                    {{-- FORM INPUT KREDIT --}}
                                    <div id="credit-options"
                                        class="hidden mt-5 pt-5 border-t border-gray-200 animate-fade-in">

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                                            {{-- Tenor --}}
                                            <div>
                                                <label class="text-xs font-semibold text-gray-700 mb-2 block">
                                                    Pilih Tenor
                                                </label>
                                                <div
                                                    class="relative bg-gray-50 border border-gray-200 rounded-2xl shadow-inner transition focus-within:border-teal-500 focus-within:ring-1 focus-within:ring-teal-400">
                                                    <select name="tenor" id="input-tenor"
                                                        class="w-full text-sm bg-transparent px-4 py-3 rounded-2xl focus:outline-none">
                                                        <option value="3">3 Bulan</option>
                                                        <option value="6">6 Bulan</option>
                                                        <option value="9">9 Bulan</option>
                                                        <option value="12">12 Bulan</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- DP --}}
                                            <div>
                                                <label class="text-xs font-semibold text-gray-700 mb-2 block">
                                                    Uang Muka (DP)
                                                </label>
                                                <div
                                                    class="relative bg-gray-50 border border-gray-200 rounded-2xl shadow-inner transition focus-within:border-teal-500 focus-within:ring-1 focus-within:ring-teal-400">
                                                    <span class="absolute left-4 top-3 text-gray-400 text-sm">Rp</span>
                                                    <input type="text" id="dp_amount" name="dp_amount" placeholder="0"
                                                        min="0" value="0"
                                                        class="w-full bg-transparent pl-9 pr-4 py-3 text-sm rounded-2xl focus:outline-none font-semibold text-gray-800">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- HASIL ESTIMASI (UI BARU) --}}
                                        <div id="estimation-box"
                                            class="bg-teal-50 border border-teal-200 rounded-xl p-4 flex justify-between items-center hidden">
                                            <div>
                                                <p
                                                    class="text-[10px] font-bold text-teal-600 uppercase tracking-wider mb-1">
                                                    Estimasi Cicilan
                                                </p>
                                                {{-- <p class="text-xs text-teal-700">
                                                    Sudah termasuk bunga & margin
                                                </p> --}}
                                            </div>
                                            <div class="text-right">
                                                <p id="est-monthly" class="text-xl font-black text-teal-800">Rp 0</p>
                                                <p class="text-[10px] text-teal-600 font-bold">/ bulan</p>
                                            </div>
                                        </div>

                                    </div>
                                </label>
                            @endif

                        </div>
                    </div>
                </div>

                {{-- SISI KANAN: RINGKASAN BIAYA --}}
                <div class="md:col-span-1">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sticky top-[100px]">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 border-b pb-4 text-center">Ringkasan Biaya</h2>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 font-medium">Subtotal Belanja</span>
                                <span class="font-bold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>

                            @if ($adminFee > 0)
                                <div class="flex justify-between text-sm text-orange-600 font-bold">
                                    <span>{{ $adminLabel }}</span>
                                    <span>Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="bg-teal-50 rounded-xl p-4 mb-6 border border-teal-100">
                            <p class="text-[10px] text-teal-600 font-bold uppercase text-center mb-1 tracking-widest">
                                Total Tagihan
                            </p>
                            <p class="text-2xl font-black text-teal-700 text-center">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- TOMBOL SUBMIT --}}
                        <button type="button" onclick="confirmPayment()"
                            class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:-translate-y-1">
                            Proses Transaksi
                        </button>

                        <a href="{{ route('cart.index') }}"
                            class="block text-center text-xs font-bold text-gray-400 mt-4 hover:text-gray-600 transition">
                            Batal & Kembali
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </main>

    {{-- SCRIPT AREA --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- 1. DATA & SELECTORS ---
            const userSaldo = {{ auth()->user()->saldo ?? 0 }};
            const grandTotal = {{ $total ?? 0 }};

            // Elements
            const radios = document.querySelectorAll('.payment-radio');
            const creditRadio = document.querySelector('input[value="credit"]');
            const creditOptions = document.getElementById('credit-options');

            // Input Form Kredit
            const dpInput = document.getElementById('dp_amount');
            const inputTenor = document.getElementById('input-tenor');

            // Box Estimasi
            const estBox = document.getElementById('estimation-box');
            const estText = document.getElementById('est-monthly');

            // --- 2. FUNGSI KALKULATOR ESTIMASI (LOGIC DARI SERVICE PHP) ---
            function calculateInstallment() {
                // Cek apakah radio credit ada (berarti produk Raditya) & sedang dipilih
                if (!creditRadio || !creditRadio.checked) return;

                // Ambil Data Price & HPP dari atribut data- di radio button
                let price = parseFloat(creditRadio.dataset.price);
                let hpp = parseFloat(creditRadio.dataset.hpp);

                // Ambil DP (Bersihkan titik ribuan)
                let rawDp = dpInput.value.replace(/\./g, "") || "0";
                let dp = parseFloat(rawDp);
                let tenor = parseInt(inputTenor.value);

                // Safety Logic: DP tidak boleh minus
                if (dp < 0) dp = 0;
                // DP tidak boleh melebihi harga barang (dikurangi 1000 perak)
                if (dp >= price) dp = price - 1000;

                // --- MULAI HITUNGAN ---

                // A. Sisa Pokok
                let sisaPokok = price - dp;

                // B. Margin / Up Price (Sesuai Logic PHP)
                let upPricePercent = 17.5;
                if (hpp <= 3000000) {
                    upPricePercent = 17.5;
                } else if (hpp <= 8000000) {
                    upPricePercent = 22.5;
                } else {
                    upPricePercent = 27.5;
                }

                // C. Harga Retail
                let hargaRetail = sisaPokok * (1 + (upPricePercent / 100));

                // D. Bunga / Interest (Sesuai Logic PHP)
                let interestPercent = 0;
                switch (tenor) {
                    case 3:
                        interestPercent = 9;
                        break;
                    case 6:
                        interestPercent = 15;
                        break;
                    case 9:
                        interestPercent = 23;
                        break;
                    case 12:
                        interestPercent = 24;
                        break;
                }

                // E. Total Pinjaman
                let totalLoan = hargaRetail * (1 + (interestPercent / 100));

                // F. Angsuran Bulanan (Bulatkan ke atas kelipatan 1000)
                let rawMonthly = totalLoan / tenor;
                let monthlyInstallment = Math.ceil(rawMonthly / 1000) * 1000;

                // --- UPDATE TAMPILAN ---
                if (estText) {
                    estText.innerText = "Rp " + new Intl.NumberFormat('id-ID').format(monthlyInstallment);
                }
            }

            // --- 3. EVENT LISTENERS ---

            // A. Input DP (Format Rupiah + Trigger Hitung)
            if (dpInput) {
                dpInput.addEventListener("input", function(e) {
                    let cursorPos = this.selectionStart;
                    let raw = this.value.replace(/\D/g, ""); // Hapus non-angka

                    if (!raw) {
                        this.value = "";
                        calculateInstallment(); // Recalculate saat kosong
                        return;
                    }

                    // Format Ribuan
                    let formatted = new Intl.NumberFormat("id-ID").format(raw);
                    this.value = formatted;

                    // Kembalikan posisi cursor
                    let diff = this.value.length - raw.length;
                    this.selectionStart = this.selectionEnd = cursorPos +
                        diff; // Logic cursor kasar tapi cukup

                    // PANGGIL KALKULATOR
                    calculateInstallment();
                });
            }

            // B. Ubah Tenor (Trigger Hitung)
            if (inputTenor) {
                inputTenor.addEventListener('change', calculateInstallment);
            }

            // C. Ganti Metode Pembayaran (Radio)
            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'credit') {
                        // Tampilkan Form Kredit
                        if (creditOptions) {
                            creditOptions.classList.remove('hidden');
                            creditOptions.classList.add('animate-fade-in');
                        }
                        // Tampilkan Box Estimasi
                        if (estBox) estBox.classList.remove('hidden');

                        // Hitung Awal
                        calculateInstallment();

                        if (dpInput) setTimeout(() => dpInput.focus(), 200);

                    } else {
                        // Sembunyikan Form Kredit
                        if (creditOptions) creditOptions.classList.add('hidden');
                        // Sembunyikan Box Estimasi
                        if (estBox) estBox.classList.add('hidden');

                        // Reset DP
                        if (dpInput) dpInput.value = '0';
                    }
                });
            });

            // --- 4. LOGIC SUBMIT / KONFIRMASI (Global Function) ---
            window.confirmPayment = function() {
                const form = document.getElementById('payment-form');
                const methodElement = document.querySelector('input[name="payment_method"]:checked');

                if (!methodElement) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Metode',
                        text: 'Pilih metode pembayaran dulu.'
                    });
                    return;
                }

                const method = methodElement.value;
                let title = 'Konfirmasi Pembayaran';
                let text = "";
                let btnText = 'Ya, Bayar!';
                let iconType = 'question';

                // VALIDASI SALDO (UNTUK BAYAR FULL)
                if (method === 'balance') {
                    if (userSaldo < grandTotal) {
                        const kekurangan = grandTotal - userSaldo;
                        Swal.fire({
                            icon: 'error',
                            title: 'Saldo Tidak Cukup!',
                            html: `Saldo: <b>Rp ${new Intl.NumberFormat('id-ID').format(userSaldo)}</b><br>Tagihan: <b>Rp ${new Intl.NumberFormat('id-ID').format(grandTotal)}</b><br><span class="text-red-600 font-bold">Kurang: Rp ${new Intl.NumberFormat('id-ID').format(kekurangan)}</span>`,
                            confirmButtonColor: '#d33'
                        });
                        return;
                    }
                    title = 'Potong Saldo?';
                    text = "Saldo akan dipotong otomatis.";
                }

                // VALIDASI KREDIT
                else if (method === 'credit') {
                    let rawDP = dpInput ? dpInput.value.replace(/\./g, "") : "0";
                    let dpVal = parseFloat(rawDP) || 0;

                    // Cek Saldo untuk DP
                    if (dpVal > 0 && userSaldo < dpVal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Saldo Kurang untuk DP',
                            text: 'Saldo tidak cukup untuk membayar Uang Muka.'
                        });
                        return;
                    }

                    // Cek Saldo untuk Cicilan Pertama (Jika DP 0)
                    // Logic ini agak tricky di JS karena harus hitung ulang, tapi backend akan validasi juga.
                    // Untuk UX, kita beri peringatan umum saja.

                    title = 'Ajukan Kredit?';
                    if (dpVal > 0) {
                        text =
                            `DP <b>Rp ${new Intl.NumberFormat('id-ID').format(dpVal)}</b> dipotong dari Saldo.`;
                    } else {
                        text =
                            `<b>Tanpa DP.</b><br>Pembayaran angsuran pertama + Admin akan dipotong saldo sekarang.`;
                    }
                    btnText = 'Ya, Ajukan!';
                    iconType = 'info';
                }

                // CASH
                else if (method === 'cash') {
                    title = 'Pesanan Cash';
                    text = "Bayar tunai di kasir.";
                    btnText = 'Ya, Pesan!';
                }

                // EKSEKUSI
                Swal.fire({
                    title: title,
                    html: text,
                    icon: iconType,
                    showCancelButton: true,
                    confirmButtonColor: '#0d9488',
                    cancelButtonColor: '#d33',
                    confirmButtonText: btnText,
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // BERSIHKAN FORMAT RUPIAH SEBELUM SUBMIT
                        if (method === 'credit' && dpInput) {
                            dpInput.value = dpInput.value.replace(/\./g, "");
                        }

                        Swal.fire({
                            title: 'Memproses...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        form.submit();
                    }
                });
            };

            // --- 5. ERROR HANDLING ---
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: "{!! session('error') !!}",
                    confirmButtonColor: '#d33'
                });
            @endif
        });
    </script>

    <style>
        /* Animasi halus untuk munculnya form kredit */
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
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
    </style>
@endsection
