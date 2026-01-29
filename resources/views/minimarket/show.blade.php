@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">

        {{-- 1. NOTIFICATION ALERTS (CRITICAL FOR DEBUGGING) --}}
        {{-- Ini penting agar kita tahu kenapa produk gagal masuk --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                <p class="font-bold">Berhasil</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <p class="font-bold">Gagal</p>
                <p>{!! session('error') !!}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <p class="font-bold">Error Validasi</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 2. LOCK ALERT (Jika sedang terkunci oleh unit lain) --}}
        @if (isset($cartLock) && $cartLock == 'raditya')
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0"><i class="fa-solid fa-circle-info text-blue-500"></i></div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <span class="font-bold">Mode Terbatas:</span> Keranjang Anda berisi produk <span
                                class="font-bold uppercase">GADGET (RADITYA)</span>.
                            Selesaikan atau kosongkan keranjang sebelum membeli Sembako.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- IMAGE --}}
            <div class="border rounded-lg p-2 bg-white">
                <img src="{{ $product->primaryImage
                    ? asset('storage/' . $product->primaryImage->image_path)
                    : asset('images/placeholder.png') }}"
                    class="mx-auto h-64 md:h-80 object-contain" alt="{{ $product->name }}">
            </div>

            {{-- INFO --}}
            <div>
                <h1 class="text-xl md:text-2xl font-semibold mb-2">
                    {{ $product->name }}
                </h1>

                <p class="text-xl font-bold text-teal-700">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

                <p class="text-sm text-gray-600 mb-4">
                    Stok: {{ $product->stock }}
                </p>

                <div class="text-sm text-gray-700 mb-6">
                    <h2 class="font-semibold mb-2">Spesifikasi</h2>
                    <div class="whitespace-pre-line leading-relaxed">
                        {{ $product->desc }}
                    </div>
                </div>

                {{-- FORM ADD TO CART --}}
                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf

                    <input type="hidden" name="id_product_diamart" value="{{ $product->id }}">

                    {{-- Input Qty Manual (Agar user bisa beli lebih dari 1) --}}
                    <div class="flex items-center mb-4">
                        <label class="mr-3 font-semibold text-sm">Jumlah:</label>
                        <input type="number" name="qty" value="1" min="1" max="{{ $product->stock }}"
                            class="border rounded px-3 py-2 w-20 text-center">
                    </div>

                    {{-- 3. LOGIKA TOMBOL (DISABLE JIKA LOCKED / STOK HABIS) --}}
                    @if (isset($cartLock) && $cartLock == 'raditya')
                        <button type="button" disabled
                            class="w-full md:w-auto bg-gray-300 text-gray-500 px-8 py-3 rounded-full font-bold cursor-not-allowed">
                            <i class="fa-solid fa-lock mr-2"></i> Terkunci (Selesaikan Raditya Dulu)
                        </button>
                    @elseif($product->stock <= 0)
                        <button type="button" disabled
                            class="w-full md:w-auto bg-red-100 text-red-400 px-8 py-3 rounded-full font-bold cursor-not-allowed">
                            Stok Habis
                        </button>
                    @else
                        <button type="submit"
                            class="w-full md:w-auto bg-teal-600 hover:bg-teal-700 text-white px-8 py-3 rounded-full font-semibold">
                            Tambah ke Keranjang
                        </button>
                    @endif
                </form>
            </div>
        </div>

        {{-- RELATED PRODUCTS --}}
        <h2 class="text-lg font-semibold mt-12 mb-4">Produk Lainnya</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
            @foreach ($relatedProducts as $item)
                <a href="{{ route('minimarket.show', $item->id) }}"
                    class="bg-white border rounded-lg p-3 hover:shadow transition">
                    <img src="{{ $item->primaryImage ? asset('storage/' . $item->primaryImage->image_path) : asset('images/placeholder.png') }}"
                        class="mx-auto h-24 object-contain mb-2" alt="{{ $item->name }}">
                    <p class="text-sm font-semibold leading-tight">{{ $item->name }}</p>
                    <p class="text-xs text-gray-600">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                </a>
            @endforeach
        </div>

    </div>
@endsection
