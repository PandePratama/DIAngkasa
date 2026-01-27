@extends('layouts.app')

@section('content')
    {{-- MAIN CONTENT --}}
    <main class="min-h-screen bg-gradient-to-br from-blue-50 via-blue-100 to-cyan-50">

        {{-- HERO BANNER --}}
        <section class="relative pt-8 pb-16 overflow-hidden">
            {{-- Decorative background elements --}}
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div
                    class="absolute top-20 left-10 w-72 h-72 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse">
                </div>
                <div class="absolute top-40 right-10 w-72 h-72 bg-cyan-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"
                    style="animation-delay: 1s;"></div>
                <div
                    class="absolute -bottom-20 left-1/2 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10">
                </div>
            </div>

            <div class="container mx-auto px-6 relative z-10">
                {{-- Glass card for banner --}}
                <div
                    class="backdrop-blur-md bg-white/30 rounded-3xl shadow-2xl border border-white/50 p-8 max-w-6xl mx-auto hover:shadow-blue-200/50 transition-all duration-300 hover:scale-[1.02]">
                    <img src="{{ asset('sbadmin/img/Hero_banner.png') }}" alt="Banner Gadget"
                        class="rounded-2xl shadow-lg max-w-full h-auto max-h-[260px] sm:max-h-[320px] md:max-h-none object-contain mx-auto">
                </div>
            </div>
        </section>

        {{-- PRODUK BEST SELLER GADGET --}}
        <section class="container mx-auto px-6 py-16 relative">
            <div class="text-center mb-12">
                <h1
                    class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent mb-3">
                    PRODUK RADITYA
                </h1>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-cyan-500 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 max-w-7xl mx-auto">
                @forelse ($productsGadget ?? [] as $product)
                    <div
                        class="group relative backdrop-blur-lg bg-white/40 rounded-2xl shadow-lg border border-white/60 p-6 text-center hover:bg-white/60 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-blue-200/50">
                        {{-- Shine effect on hover --}}
                        <div
                            class="absolute inset-0 rounded-2xl bg-gradient-to-r from-transparent via-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 transform -skew-x-12">
                        </div>

                        <div class="relative z-10">
                            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-4 mb-4">
                                <img src="{{ $product->primaryImage
                                    ? asset('storage/' . $product->primaryImage->image_path)
                                    : asset('images/placeholder.png') }}"
                                    alt="{{ $product->name }}"
                                    class="mx-auto h-32 object-contain group-hover:scale-110 transition-transform duration-300">
                            </div>

                            <p class="font-bold text-gray-800 mb-2 line-clamp-2">{{ $product->name }}</p>
                            <p
                                class="text-lg font-semibold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full backdrop-blur-lg bg-white/40 rounded-2xl shadow-lg border border-white/60 p-12 text-center">
                        <p class="text-gray-600 text-lg">Produk belum tersedia</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- BANNER PROMO --}}
        <section class="py-16 relative">
            <div class="container mx-auto px-6">
                <div
                    class="backdrop-blur-md bg-gradient-to-r from-blue-400/30 to-cyan-400/30 rounded-3xl shadow-2xl border border-white/50 p-8 max-w-6xl mx-auto hover:shadow-blue-300/50 transition-all duration-300">
                    <img src="{{ asset('sbadmin/img/december.png') }}" alt="Banner Promo"
                        class="rounded-2xl shadow-lg max-w-full h-auto mx-auto">
                </div>
            </div>
        </section>

        {{-- PRODUK BEST SELLER MINIMARKET --}}
        <section class="container mx-auto px-6 py-16 pb-24">
            <div class="text-center mb-12">
                <h2
                    class="text-4xl font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent mb-3">
                    PRODUK DIAMART
                </h2>
                <div class="w-24 h-1 bg-gradient-to-r from-cyan-500 to-blue-500 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 max-w-7xl mx-auto">
                @forelse ($productsMinimarket ?? [] as $product)
                    <div
                        class="group relative backdrop-blur-lg bg-white/40 rounded-2xl shadow-lg border border-white/60 p-6 text-center hover:bg-white/60 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-cyan-200/50">
                        {{-- Shine effect on hover --}}
                        <div
                            class="absolute inset-0 rounded-2xl bg-gradient-to-r from-transparent via-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 transform -skew-x-12">
                        </div>

                        <div class="relative z-10">
                            <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl p-4 mb-4">
                                <img src="{{ $product->primaryImage
                                    ? asset('storage/' . $product->primaryImage->image_path)
                                    : asset('images/placeholder.png') }}"
                                    alt="{{ $product->name }}"
                                    class="mx-auto h-32 object-contain group-hover:scale-110 transition-transform duration-300">
                            </div>

                            <p class="font-bold text-gray-800 mb-2 line-clamp-2">{{ $product->name }}</p>
                            <p
                                class="text-lg font-semibold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full backdrop-blur-lg bg-white/40 rounded-2xl shadow-lg border border-white/60 p-12 text-center">
                        <p class="text-gray-600 text-lg">Produk minimarket belum tersedia</p>
                    </div>
                @endforelse
            </div>
        </section>

    </main>
@endsection
