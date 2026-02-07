@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6 max-w-6xl">

        {{-- ================= ALERT ================= --}}
        @if (session('success'))
            <div class="mb-5 rounded-xl bg-green-50 border border-green-200 p-4 text-green-700">
                <p class="font-semibold">✅ Berhasil</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 rounded-xl bg-red-50 border border-red-200 p-4 text-red-700">
                <p class="font-semibold">❌ Gagal</p>
                <p class="text-sm">{!! session('error') !!}</p>
            </div>
        @endif

        {{-- ================= LOCK ALERT ================= --}}
        @if (isset($cartLock) && $cartLock == 'diamart')
            <div class="mb-6 rounded-xl bg-yellow-50 border border-yellow-200 p-4 text-yellow-800">
                <div class="flex gap-3">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                    <div class="text-sm">
                        <p>
                            <b>Mode Terbatas:</b> Keranjang Anda berisi produk
                            <b class="uppercase">Sembako (Diamart)</b>.
                        </p>
                        <p class="mt-1">
                            Selesaikan transaksi atau kosongkan keranjang terlebih dahulu.
                        </p>
                        <a href="{{ route('cart.index') }}" class="inline-block mt-2 font-semibold underline">
                            Lihat Keranjang
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- ================= PRODUCT DETAIL ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- IMAGE --}}
            <div class="bg-white rounded-2xl shadow p-4 flex items-center justify-center">
                <img src="{{ $product->primaryImage
                    ? asset('storage/' . $product->primaryImage->image_path)
                    : asset('images/placeholder.png') }}"
                    class="h-64 md:h-96 object-contain" alt="{{ $product->name }}">
            </div>

            {{-- INFO --}}
            <div class="flex flex-col">

                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 mb-2">
                    {{ $product->name }}
                </h1>

                <p class="text-2xl font-bold text-teal-600 mb-1">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

                <p class="text-sm text-gray-500 mb-4">
                    Stok tersedia:
                    <span class="font-semibold">{{ $product->stock }}</span>
                </p>

                {{-- DESKRIPSI --}}
                <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700 mb-6">
                    <p class="font-semibold mb-2">📋 Deskripsi Produk</p>
                    <div class="whitespace-pre-line leading-relaxed">
                        {{ $product->desc }}
                    </div>
                </div>

                {{-- FORM ADD TO CART --}}
                <form method="POST" action="{{ route('cart.add') }}" class="mt-auto">
                    @csrf

                    {{-- ID PRODUCT DIRADITYA --}}
                    <input type="hidden" name="id_product_diraditya" value="{{ $product->id }}">

                    {{-- ========================================== --}}
                    {{-- FITUR BARU: SIMULASI KREDIT --}}
                    {{-- ========================================== --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
                        <h3 class="font-bold text-blue-800 mb-3 flex items-center">
                            <i class="fa-solid fa-calculator mr-2"></i> Simulasi Cicilan
                        </h3>

                        {{-- Input DP --}}
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">
                                Rencana Uang Muka (DP)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                                <input type="number" id="input_dp" value="0" min="0"
                                    class="w-full pl-8 pr-4 py-2 border rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Isi 0 jika tanpa DP">
                            </div>
                            <small class="text-xs text-gray-500 mt-1">*DP akan mengurangi cicilan bulanan</small>
                        </div>

                        {{-- Loading State --}}
                        <div id="loading_sim" class="text-center py-4 hidden">
                            <i class="fas fa-spinner fa-spin text-blue-600"></i> Menghitung...
                        </div>

                        {{-- Hasil Simulasi (Grid) --}}
                        <div id="result_sim" class="grid grid-cols-2 gap-3">
                            {{-- Data akan diisi oleh Javascript --}}
                        </div>
                    </div>
                    {{-- ========================================== --}}

                    {{-- FORM ADD TO CART --}}
                    <form method="POST" action="{{ route('cart.add') }}">
                        @csrf

                        {{-- PENTING: Input Hidden untuk ID Produk Raditya --}}
                        <input type="hidden" name="id_product_diraditya" value="{{ $product->id }}">

                        {{-- Input Qty --}}
                        <div class="flex items-center mb-4">
                            <label class="mr-3 font-semibold text-sm">Jumlah:</label>
                            <input type="number" name="qty" value="1" min="1" max="{{ $product->stock }}"
                                class="border rounded px-3 py-2 w-20 text-center focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- LOGIKA TOMBOL --}}
                        @if (isset($cartLock) && $cartLock == 'diamart')
                            {{-- STATE 1: TERKUNCI OLEH DIAMART --}}
                            <button type="button" disabled
                                class="w-full bg-gray-300 text-gray-500 px-6 py-3 rounded-lg font-bold cursor-not-allowed">
                                <i class="fa-solid fa-lock mr-2"></i> Selesaikan Sembako Dulu
                            </button>
                        @elseif($product->stock <= 0)
                            {{-- STATE 2: STOK HABIS --}}
                            <button type="button" disabled
                                class="w-full bg-red-100 text-red-500 px-6 py-3 rounded-lg font-bold cursor-not-allowed">
                                Stok Habis
                            </button>
                        @else
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-teal-600 to-cyan-600
                               text-white py-3 rounded-xl font-semibold
                               hover:scale-[1.02] transition shadow-lg">
                                <i class="fa-solid fa-cart-plus mr-2"></i>
                                Tambah ke Keranjang
                            </button>
                        @endif
                    </form>
            </div>
        </div>

        {{-- SCRIPT KHUSUS HALAMAN INI --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const inputDp = document.getElementById('input_dp');
                const resultContainer = document.getElementById('result_sim');
                const loading = document.getElementById('loading_sim');
                const productId = "{{ $product->id }}"; // Ambil ID Produk dari Blade

                // Fungsi ambil data ke server
                function fetchSimulation() {
                    const dpVal = inputDp.value || 0;

                    // Tampilkan loading, sembunyikan hasil
                    loading.classList.remove('hidden');
                    resultContainer.innerHTML = '';

                    fetch("{{ route('raditya.simulation_schemes') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                product_id: productId,
                                dp_amount: dpVal
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            loading.classList.add('hidden');

                            if (data.status === 'success') {
                                // Loop hasil skema
                                data.schemes.forEach(scheme => {
                                    const html = `
                                <div class="bg-white p-3 rounded-lg border border-blue-100 shadow-sm text-center">
                                    <p class="text-xs font-bold text-gray-500 uppercase">${scheme.tenor} Bulan</p>
                                    <p class="text-blue-700 font-bold text-lg">Rp ${scheme.monthly}</p>
                                    <p class="text-[10px] text-gray-400">/bulan</p>
                                </div>
                            `;
                                    resultContainer.insertAdjacentHTML('beforeend', html);
                                });
                            }
                        })
                        .catch(err => {
                            loading.classList.add('hidden');
                            console.error(err);
                        });
                }

                // Hitung otomatis saat halaman dimuat (DP 0)
                fetchSimulation();

                // Hitung ulang saat user mengetik DP (Debounce sedikit biar ga spam)
                let timeoutId;
                inputDp.addEventListener('input', function() {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(fetchSimulation, 500); // Tunggu 0.5 detik setelah ngetik
                });
            });
        </script>
    @endsection
