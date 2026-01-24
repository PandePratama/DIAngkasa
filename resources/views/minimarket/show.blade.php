@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">

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

                {{-- PRICE --}}
                <p class="text-xl font-bold text-teal-700">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

                <p class="text-sm text-gray-600 mb-4">
                    Stok: {{ $product->stock }}
                </p>

                {{-- SPECIFICATION --}}
                <div class="text-sm text-gray-700 mb-6">
                    <h2 class="font-semibold mb-2">Spesifikasi</h2>
                    <div class="whitespace-pre-line leading-relaxed">
                        {{ $product->specification }}
                    </div>
                </div>

                {{-- FORM --}}
                <form method="POST" action="{{ route('cart.add.diamart', $product->id) }}">
                    @csrf
                    {{-- <input type="hidden" name="purchase_type" value="cash"> --}}

                    <button
                        class="mt-6 w-full md:w-auto bg-teal-600 hover:bg-teal-700 text-white px-8 py-3 rounded-full font-semibold">
                        Tambah ke Keranjang
                    </button>
                </form>
            </div>
        </div>

        {{-- RELATED PRODUCTS --}}
        <h2 class="text-lg font-semibold mt-12 mb-4">
            Produk Lainnya
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
            @foreach ($relatedProducts as $item)
                <a href="{{ route('minimarket.show', $item->id) }}"
                    class="bg-white border rounded-lg p-3 hover:shadow transition">

                    <img src="{{ $item->primaryImage ? asset('storage/' . $item->primaryImage->image_path) : asset('images/placeholder.png') }}"
                        class="mx-auto h-24 object-contain mb-2" alt="{{ $item->name }}">

                    <p class="text-sm font-semibold leading-tight">
                        {{ $item->name }}
                    </p>

                    <p class="text-xs text-gray-600">
                        Rp {{ number_format($item->price, 0, ',', '.') }}
                    </p>
                </a>
            @endforeach
        </div>

    </div>
@endsection
