@extends('layouts.app')

@section('content')
<main class="min-h-screen bg-gradient-to-br from-blue-50 via-blue-100 to-cyan-50">

    {{-- ================= HERO SECTION ================= --}}
    <section class="relative pt-16 pb-28 overflow-hidden">

        {{-- Background blur --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-24 left-1/4 w-80 h-80 bg-blue-400 rounded-full blur-3xl opacity-20"></div>
            <div class="absolute top-44 right-1/4 w-80 h-80 bg-cyan-400 rounded-full blur-3xl opacity-20"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10">
            {{-- 👇 pusatkan isi hero --}}
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-14 items-center">

                {{-- IMAGE --}}
                <div class="order-1 md:order-2 flex justify-center">
                    <img src="{{ asset('sbadmin/img/hero1.webp') }}"
                        alt="Hero Image"
                        class="w-full max-w-md lg:max-w-lg drop-shadow-xl">
                </div>

                {{-- TEXT --}}
                <div class="order-2 md:order-1 text-center md:text-left">
                    <h1
                        class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight
                           bg-gradient-to-r from-blue-600 to-cyan-600
                           bg-clip-text text-transparent mb-6 pb-4">
                        Belanja Mudah<br class="hidden md:block">
                        Kebutuhan Karyawan
                    </h1>

                    <p class="text-gray-700 text-base md:text-lg max-w-xl mb-8">
                        Gadget dan kebutuhan harian dengan sistem kredit internal
                        yang aman, cepat, dan transparan.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="#produk-gadget"
                            class="px-7 py-3 rounded-xl
                               bg-gradient-to-r from-blue-600 to-cyan-600
                               text-white font-semibold shadow-lg
                               hover:scale-105 transition">
                            Lihat Produk
                        </a>

                        <a href="#cara-belanja"
                            class="px-7 py-3 rounded-xl
                               bg-white/80 text-blue-600 font-semibold border
                               hover:bg-white transition">
                            Cara Belanja
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ================= PRODUK GADGET ================= --}}
    <section id="produk-gadget" class="container mx-auto px-6 py-20">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-extrabold text-gray-800 mb-2">
                Rekomendasi Gadget
            </h2>
            <p class="text-gray-600">
                Produk pilihan terbaik untuk kebutuhan karyawan
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 max-w-7xl mx-auto">
            @forelse ($productsGadget ?? [] as $product)
            <div
                class="group relative backdrop-blur-lg bg-white/50 rounded-2xl shadow-lg border border-white/60 p-5 text-center hover:bg-white transition-all duration-300 hover:scale-105">

                {{-- BADGE --}}
                <span
                    class="absolute top-3 left-3 text-xs font-semibold px-3 py-1 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 text-white shadow">
                    Gadget
                </span>

                {{-- IMAGE --}}
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-4 mb-4">
                    <img src="{{ $product->primaryImage
                            ? asset('storage/' . $product->primaryImage->image_path)
                            : asset('images/placeholder.png') }}"
                        class="mx-auto h-32 object-contain group-hover:scale-110 transition">
                </div>

                {{-- NAME --}}
                <p class="font-semibold text-gray-800 mb-1 line-clamp-2">
                    {{ $product->name }}
                </p>

                {{-- PRICE --}}
                <p
                    class="text-lg font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent mb-4">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

                {{-- ACTION --}}
                <button
                    class="opacity-0 group-hover:opacity-100 transition-all duration-300 px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-semibold shadow hover:scale-105">
                    + Keranjang
                </button>
            </div>
            @empty
            <div class="col-span-full text-center bg-white/60 rounded-2xl p-12">
                <p class="text-gray-600">Produk gadget belum tersedia</p>
            </div>
            @endforelse
        </div>
    </section>

    {{-- ================= PRODUK MINIMARKET ================= --}}
    <section class="container mx-auto px-6 py-20">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-extrabold text-gray-800 mb-2">
                Produk Diamart
            </h2>
            <p class="text-gray-600">
                Kebutuhan harian dengan harga terjangkau
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 max-w-7xl mx-auto">
            @forelse ($productsMinimarket ?? [] as $product)
            <div
                class="group relative backdrop-blur-lg bg-white/50 rounded-2xl shadow-lg border border-white/60 p-5 text-center hover:bg-white transition-all duration-300 hover:scale-105">

                <span
                    class="absolute top-3 left-3 text-xs font-semibold px-3 py-1 rounded-full bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow">
                    Diamart
                </span>

                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl p-4 mb-4">
                    <img src="{{ $product->primaryImage
                            ? asset('storage/' . $product->primaryImage->image_path)
                            : asset('images/placeholder.png') }}"
                        class="mx-auto h-32 object-contain group-hover:scale-110 transition">
                </div>

                <p class="font-semibold text-gray-800 mb-1 line-clamp-2">
                    {{ $product->name }}
                </p>

                <p
                    class="text-lg font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent mb-4">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

                <button
                    class="opacity-0 group-hover:opacity-100 transition-all duration-300 px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold shadow hover:scale-105">
                    + Keranjang
                </button>
            </div>
            @empty
            <div class="col-span-full text-center bg-white/60 rounded-2xl p-12">
                <p class="text-gray-600">Produk minimarket belum tersedia</p>
            </div>
            @endforelse
        </div>
    </section>

    {{-- ================= KEUNGGULAN ================= --}}
    <section class="container mx-auto px-6 pb-24">
        <div class="grid md:grid-cols-3 gap-6 text-center">
            <div class="bg-white/60 backdrop-blur rounded-2xl p-6 shadow">
                <h3 class="font-bold text-lg mb-2">💳 Kredit Internal</h3>
                <p class="text-gray-600">Tanpa kartu kredit, aman & mudah</p>
            </div>
            <div class="bg-white/60 backdrop-blur rounded-2xl p-6 shadow">
                <h3 class="font-bold text-lg mb-2">⚡ Proses Cepat</h3>
                <p class="text-gray-600">Transaksi real-time & transparan</p>
            </div>
            <div class="bg-white/60 backdrop-blur rounded-2xl p-6 shadow">
                <h3 class="font-bold text-lg mb-2">🔒 Terpercaya</h3>
                <p class="text-gray-600">Khusus untuk lingkungan internal</p>
            </div>
        </div>
    </section>

</main>
@endsection