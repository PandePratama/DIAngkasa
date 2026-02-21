@extends('layouts.app')

@section('content')
<main class="min-h-screen bg-gradient-to-br from-blue-50 via-blue-100 to-cyan-50">

    {{-- ================= HERO SECTION ================= --}}
    <section class="relative pt-10 md:pt-20 pb-16 md:pb-24 overflow-hidden">

        {{-- Background blur --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-24 left-1/4 w-72 h-72 bg-blue-400 rounded-full blur-3xl opacity-20"></div>
            <div class="absolute top-44 right-1/4 w-72 h-72 bg-cyan-400 rounded-full blur-3xl opacity-20"></div>
        </div>

        <div class="container mx-auto px-5 relative z-10">
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

                {{-- IMAGE SLIDER --}}
                <div class="order-1 md:order-2 flex justify-center">
                    <div class="relative w-full max-w-sm sm:max-w-md lg:max-w-lg overflow-hidden rounded-2xl">

                        <div id="heroSlider" class="flex transition-transform duration-700 ease-in-out">
                            <img src="{{ asset('sbadmin/img/hero1.webp') }}" class="w-full flex-shrink-0" alt="">
                            <img src="{{ asset('sbadmin/img/hero2.webp') }}" class="w-full flex-shrink-0" alt="">
                            <img src="{{ asset('sbadmin/img/hero3.webp') }}" class="w-full flex-shrink-0" alt="">
                        </div>

                        {{-- DOTS --}}
                        <div id="heroDots"
                            class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2">
                        </div>
                    </div>
                </div>

                {{-- TEXT --}}
                <div class="order-2 md:order-1 text-center md:text-left">
                    <h1
                        class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight
                        bg-gradient-to-r from-blue-600 to-cyan-600
                        bg-clip-text text-transparent mb-5">
                        Belanja Mudah<br class="hidden md:block">
                        Kebutuhan Karyawan
                    </h1>

                    <p class="text-gray-700 text-sm sm:text-base md:text-lg max-w-xl mx-auto md:mx-0 mb-7">
                        Gadget dan kebutuhan harian dengan sistem kredit internal
                        yang aman, cepat, dan transparan.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="#produk-raditya"
                            class="px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600
                            text-white font-semibold shadow hover:scale-105 transition">
                            Lihat Produk
                        </a>
                        <!-- <a href="#cara-belanja"
                            class="px-6 py-3 rounded-xl bg-white/80 text-blue-600 font-semibold border hover:bg-white">
                            Cara Belanja
                        </a> -->
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= PRODUK RADITYA ================= --}}
    <section id="produk-raditya" class="container mx-auto px-5 py-15">
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800">
                Produk Raditya
            </h2>
            <p class="text-gray-600 mt-1">
                Produk furniture & elektronik terbaik untuk kebutuhan karyawan
            </p>
        </div>

        <div class="max-w-7xl mx-auto mb-4">
            <a href="{{ route('gadget.index') }}"
                class="inline-flex items-center
               text-blue-600 font-semibold
               hover:underline">
                Lihat Semua →
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5 max-w-7xl mx-auto">
            @forelse ($productsGadget ?? [] as $product)
            <a href="{{ route('gadget.show', $product->id) }}"
                class="bg-white/70 rounded-xl shadow-sm border border-white/60
          p-3 md:p-4 transition hover:scale-105 flex flex-col">

                {{-- IMAGE --}}
                <img src="{{ $product->primaryImage
        ? $product->primaryImage->image_path
        : asset('images/placeholder.png') }}"
                    class="mx-auto h-20 md:h-28 object-contain mb-2">

                {{-- NAME --}}
                <p class="text-sm md:text-base font-semibold text-gray-800 line-clamp-2 mb-1 min-h-[2.5rem]">
                    {{ $product->name }}
                </p>

                {{-- PRICE --}}
                <p class="text-cyan-600 font-bold text-sm md:text-base mb-2">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

                {{-- FOOTER ACTION --}}
                <div class="mt-auto flex justify-end">
                    <button
                        onclick="event.preventDefault(); event.stopPropagation();"
                        class="w-9 h-9 md:w-10 md:h-10 rounded-full
                   bg-gradient-to-r from-blue-600 to-cyan-600
                   text-white flex items-center justify-center
                   shadow hover:scale-110 transition"
                        title="Tambah ke Keranjang">
                        <i class="fa-solid fa-cart-plus text-xs md:text-sm"></i>
                    </button>
                </div>
            </a>

            @empty
            <p class="col-span-full text-center text-gray-500">
                Produk gadget belum tersedia
            </p>
            @endforelse

        </div>
    </section>

    {{-- ================= PRODUK DIAMART ================= --}}
    <section class="container mx-auto px-5 py-20">
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800">
                Produk Diamart
            </h2>
            <p class="text-gray-600 mt-1">
                Kebutuhan harian dengan harga terjangkau
            </p>
        </div>

        <div class="max-w-7xl mx-auto mb-4">
            <a href="{{ route('minimarket.index') }}"
                class="inline-flex items-center
               text-blue-600 font-semibold
               hover:underline">
                Lihat Semua →
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5 max-w-7xl mx-auto">
            @forelse ($productsMinimarket ?? [] as $product)
            <a href="{{ route('minimarket.show', $product->id) }}"
                class="bg-white/70 rounded-xl shadow-sm border border-white/60
          p-3 md:p-4 transition hover:scale-105 flex flex-col">

                <img src="{{ $product->primaryImage 
        ? asset('storage/' . $product->primaryImage->image_path) 
        : asset('images/placeholder.png') }}"
                    class="mx-auto h-20 md:h-28 object-contain mb-2">

                <p class="text-sm md:text-base font-semibold text-gray-800 line-clamp-2 mb-1 min-h-[2.5rem]">
                    {{ $product->name }}
                </p>

                <p class="text-cyan-600 font-bold text-sm md:text-base mb-2">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

                {{-- BUTTON CART --}}
                <div class="mt-auto flex justify-end">
                    <button
                        onclick="event.preventDefault(); event.stopPropagation();"
                        class="w-9 h-9 md:w-10 md:h-10 rounded-full
                   bg-gradient-to-r from-blue-600 to-cyan-600
                   text-white flex items-center justify-center
                   shadow hover:scale-110 transition"
                        title="Tambah ke Keranjang">
                        <i class="fa-solid fa-cart-plus text-xs md:text-sm"></i>
                    </button>
                </div>
            </a>

            @empty
            <p class="col-span-full text-center text-gray-500">
                Produk minimarket belum tersedia
            </p>
            @endforelse

        </div>
    </section>

    {{-- ================= KEUNGGULAN ================= --}}
    <section class="container mx-auto px-5 pb-24">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
            <div class="bg-white/70 rounded-2xl p-6 shadow">
                <h3 class="font-bold text-lg mb-2">💳 Kredit Internal</h3>
                <p class="text-gray-600 text-sm">Tanpa kartu kredit, aman & mudah</p>
            </div>
            <div class="bg-white/70 rounded-2xl p-6 shadow">
                <h3 class="font-bold text-lg mb-2">⚡ Proses Cepat</h3>
                <p class="text-gray-600 text-sm">Transaksi real-time & transparan</p>
            </div>
            <div class="bg-white/70 rounded-2xl p-6 shadow">
                <h3 class="font-bold text-lg mb-2">🔒 Terpercaya</h3>
                <p class="text-gray-600 text-sm">Khusus untuk lingkungan internal</p>
            </div>
        </div>
    </section>

</main>

{{-- ================= HERO SLIDER SCRIPT ================= --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const slider = document.getElementById('heroSlider');
        const dotsContainer = document.getElementById('heroDots');
        const slides = slider.children;
        let index = 0;

        for (let i = 0; i < slides.length; i++) {
            const dot = document.createElement('button');
            dot.className = 'w-2.5 h-2.5 rounded-full bg-white/50';
            dot.onclick = () => goSlide(i);
            dotsContainer.appendChild(dot);
        }

        const dots = dotsContainer.children;

        function goSlide(i) {
            index = i;
            slider.style.transform = `translateX(-${i * 100}%)`;
            [...dots].forEach((d, idx) => {
                d.classList.toggle('bg-white', idx === i);
                d.classList.toggle('scale-125', idx === i);
            });
        }

        setInterval(() => {
            index = (index + 1) % slides.length;
            goSlide(index);
        }, 4500);

        goSlide(0);
    });
</script>
@endsection