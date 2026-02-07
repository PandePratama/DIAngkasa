@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-6xl">

    {{-- ================= ALERTS ================= --}}
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

    @if ($errors->any())
        <div class="mb-5 rounded-xl bg-red-50 border border-red-200 p-4 text-red-700">
            <p class="font-semibold">⚠️ Error Validasi</p>
            <ul class="text-sm list-disc ml-4 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ================= LOCK INFO ================= --}}
    @if (isset($cartLock) && $cartLock == 'raditya')
        <div class="mb-6 rounded-xl bg-blue-50 border border-blue-200 p-4 text-blue-700 text-sm">
            <strong>Mode Terbatas:</strong>
            Keranjang Anda berisi produk <b>GADGET (RADITYA)</b>.
            Selesaikan atau kosongkan keranjang terlebih dahulu.
        </div>
    @endif

    {{-- ================= PRODUCT DETAIL ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- IMAGE --}}
        <div class="bg-white rounded-2xl shadow p-4 flex items-center justify-center">
            <img src="{{ $product->primaryImage
                ? asset('storage/' . $product->primaryImage->image_path)
                : asset('images/placeholder.png') }}"
                class="h-64 md:h-96 object-contain"
                alt="{{ $product->name }}">
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
                Stok tersedia: <span class="font-semibold">{{ $product->stock }}</span>
            </p>

            {{-- DESKRIPSI --}}
            <div class="bg-gray-50 rounded-xl p-4 mb-6 text-sm text-gray-700">
                <p class="font-semibold mb-2">📋 Deskripsi Produk</p>
                <div class="whitespace-pre-line leading-relaxed">
                    {{ $product->desc }}
                </div>
            </div>

            {{-- FORM CART --}}
            <form method="POST" action="{{ route('cart.add') }}" class="mt-auto">
                @csrf
                <input type="hidden" name="id_product_diamart" value="{{ $product->id }}">

                <div class="flex items-center gap-3 mb-4">
                    <label class="text-sm font-semibold">Jumlah</label>
                    <input type="number"
                        name="qty"
                        value="1"
                        min="1"
                        max="{{ $product->stock }}"
                        class="w-24 text-center border rounded-xl px-3 py-2">
                </div>

                {{-- BUTTON --}}
                @if (isset($cartLock) && $cartLock == 'raditya')
                    <button type="button" disabled
                        class="w-full bg-gray-300 text-gray-500 py-3 rounded-xl font-semibold cursor-not-allowed">
                        🔒 Keranjang Terkunci
                    </button>
                @elseif($product->stock <= 0)
                    <button type="button" disabled
                        class="w-full bg-red-100 text-red-400 py-3 rounded-xl font-semibold cursor-not-allowed">
                        Stok Habis
                    </button>
                @else
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-teal-600 to-cyan-600
                               text-white py-3 rounded-xl font-semibold
                               hover:scale-[1.02] transition">
                        <i class="fa-solid fa-cart-plus mr-2"></i> Tambah ke Keranjang
                    </button>
                @endif
            </form>
        </div>
    </div>

    {{-- ================= RELATED PRODUCTS ================= --}}
    <h2 class="text-xl font-bold mt-14 mb-4">Produk Lainnya</h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
        @foreach ($relatedProducts as $item)
            <a href="{{ route('minimarket.show', $item->id) }}"
               class="bg-white rounded-xl shadow-sm p-3 hover:shadow-lg hover:-translate-y-1 transition">

                <img src="{{ $item->primaryImage
                    ? asset('storage/' . $item->primaryImage->image_path)
                    : asset('images/placeholder.png') }}"
                    class="h-24 mx-auto object-contain mb-2">

                <p class="text-sm font-semibold line-clamp-2">
                    {{ $item->name }}
                </p>

                <p class="text-xs font-bold text-teal-600 mt-1">
                    Rp {{ number_format($item->price, 0, ',', '.') }}
                </p>
            </a>
        @endforeach
    </div>

</div>
@endsection
