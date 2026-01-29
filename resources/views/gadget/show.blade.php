@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">

        {{-- 1. ALERT ERROR / SUKSES --}}
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

        {{-- 2. ALERT LOCK (Jika keranjang terkunci oleh Diamart) --}}
        @if (isset($cartLock) && $cartLock == 'diamart')
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-r shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        {{-- Icon Peringatan --}}
                        <i class="fa-solid fa-triangle-exclamation text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <span class="font-bold">Mode Terbatas:</span>
                            Keranjang Anda saat ini berisi produk <span class="font-bold uppercase">SEMBAKO
                                (DIAMART)</span>.
                            <br>
                            Anda tidak dapat membeli Gadget sebelum menyelesaikan transaksi Sembako atau mengosongkan
                            keranjang.
                        </p>
                        <a href="{{ route('cart.index') }}" class="text-sm font-bold text-yellow-700 underline mt-2 block">
                            Lihat Keranjang Sembako
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- IMAGE PRODUCT --}}
            <div class="border rounded-lg p-2 bg-white">
                <img src="{{ $product->primaryImage
                    ? asset('storage/' . $product->primaryImage->image_path)
                    : asset('images/placeholder.png') }}"
                    class="mx-auto h-64 md:h-80 object-contain" alt="{{ $product->name }}">
            </div>

            {{-- INFO PRODUCT --}}
            <div>
                <h1 class="text-2xl font-bold mb-2">{{ $product->name }}</h1>

                <p class="text-2xl font-bold text-blue-700 mb-4">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

                <p class="text-gray-600 mb-6">Stok: <b>{{ $product->stock }}</b></p>

                <div class="prose max-w-none text-gray-700 mb-6">
                    {!! nl2br(e($product->desc)) !!}
                </div>

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
                        {{-- STATE 3: AMAN (BISA BELI) --}}
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-cart-plus mr-2"></i> Tambah ke Keranjang
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection
