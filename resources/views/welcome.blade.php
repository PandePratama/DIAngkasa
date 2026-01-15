@extends('layouts.app')

@section('content')

{{-- MAIN CONTENT --}}
<main class="bg-gray-100">

    {{-- HERO BANNER --}}
    <section class="bg-gradient-to-r from-blue-200 to-white pt-4 pb-12">
        <div class="container mx-auto flex justify-center px-6">
            <img
                src="{{ asset('sbadmin/img/Hero_banner.png') }}"
                alt="Banner Gadget"
                class="rounded-lg shadow-lg max-w-full h-auto
                       max-h-[260px] sm:max-h-[320px] md:max-h-none
                       object-contain">
        </div>
    </section>

    {{-- PRODUK BEST SELLER GADGET --}}
    <section class="container mx-auto px-6 py-12">
        <h1 class="text-xl font-semibold mb-6 text-center">
            PRODUK RADITYA
        </h1>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 mx-auto">
            @forelse ($productsGadget ?? [] as $product)
            <div class="bg-white shadow rounded-lg p-4 text-center">
                <img
                    src="{{ $product->primaryImage
                    ? asset('storage/' . $product->primaryImage->image_path)
                    : asset('images/placeholder.png') }}"
                    alt="{{ $product->name }}"
                    class="mx-auto mb-2 h-24 object-contain">

                <p class="font-semibold">{{ $product->name }}</p>
                <p class="text-gray-700">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>
            </div>
            @empty
            <p class="col-span-full text-center text-gray-500">
                Produk belum tersedia
            </p>
            @endforelse
        </div>
    </section>

    {{-- BANNER PROMO --}}
    <section class="bg-gray-200 py-12">
        <div class="container mx-auto px-6">
            <img
                src="{{ asset('sbadmin/img/december.png') }}"
                alt="Banner Promo"
                class="rounded-lg shadow-lg max-w-full h-auto">
        </div>
    </section>

    {{-- PRODUK BEST SELLER MINIMARKET --}}
    <section class="container mx-auto px-6 py-12">
        <h2 class="text-xl font-semibold mb-6 text-center">
            PRODUK DIAMART
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
            @forelse ($productsMinimarket ?? [] as $product)
            <div class="bg-white shadow rounded-lg p-4 text-center">
                <img
                    src="{{ $product->primaryImage
                    ? asset('storage/' . $product->primaryImage->image_path)
                    : asset('images/placeholder.png') }}"
                    alt="{{ $product->name }}"
                    class="mx-auto mb-2 h-24 object-contain">

                <p class="font-semibold">{{ $product->name }}</p>
                <p class="text-gray-700">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>
            </div>
            @empty
            <p class="col-span-full text-center text-gray-500">
                Produk minimarket belum tersedia
            </p>
            @endforelse
        </div>
    </section>

</main>

@endsection