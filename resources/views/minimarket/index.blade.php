@extends('layouts.app')

@section('content')
    {{-- Filter Bar --}}
    <form method="GET" class="bg-gray-200 px-6 py-3 mb-4">
        <div class="flex flex-wrap gap-3">
            <select name="category" onchange="this.form.submit()" class="px-3 py-1 rounded border text-sm">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->category_name ?? $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="container mx-auto px-6 py-8">

        {{-- 1. FITUR BARU: ALERT JIKA TERKUNCI --}}
        {{-- Variabel $cartLock dikirim dari MinimarketController --}}
        @if (isset($cartLock) && $cartLock == 'raditya')
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-8 rounded-r shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-info text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <span class="font-bold">Mode Terbatas:</span>
                            Keranjang belanja Anda saat ini berisi produk <span class="font-bold uppercase">GADGET
                                (RADITYA)</span>.
                            Anda tidak dapat membeli Sembako sebelum menyelesaikan transaksi Gadget atau mengosongkan
                            keranjang.
                        </p>
                        <a href="{{ route('cart.add') }}" class="text-sm font-bold text-blue-700 underline mt-1 block">
                            Lihat Keranjang
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Produk Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">

            @forelse ($products as $product)
                <div class="bg-white border rounded-lg p-3 hover:shadow transition relative group">

                    {{-- Link Detail Produk (Hanya pada Gambar & Nama) --}}
                    <a href="{{ route('minimarket.show', $product->id) }}" class="block">
                        {{-- Image Logic --}}
                        <img src="{{ $product->primaryImage
                            ? asset('storage/' . $product->primaryImage->image_path)
                            : asset('images/placeholder.png') }}"
                            class="mx-auto h-32 object-contain mb-3 group-hover:scale-105 transition-transform duration-300"
                            alt="{{ $product->name }}">

                        {{-- Product Name --}}
                        <p class="text-sm font-semibold leading-tight line-clamp-2 h-10 mb-1">
                            {{ $product->name }}
                        </p>
                    </a>

                    {{-- Bagian Bawah: Harga & Tombol Action --}}
                    <div class="flex justify-between items-end mt-2">
                        {{-- Price --}}
                        <p class="text-xs text-gray-600 font-bold">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </p>

                        {{-- 2. TOMBOL ADD TO CART DENGAN LOGIKA LOCK --}}
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            {{-- Input Hidden ID Produk Diamart --}}
                            <input type="hidden" name="id_product_diamart" value="{{ $product->id }}">

                            @if (isset($cartLock) && $cartLock == 'raditya')
                                {{-- JIKA TERKUNCI: Tombol Disabled (Abu-abu & Icon Gembok) --}}
                                <button type="button" disabled
                                    class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center cursor-not-allowed"
                                    title="Terkunci: Selesaikan keranjang Raditya dulu">
                                    <i class="fa-solid fa-lock text-xs"></i>
                                </button>
                            @else
                                {{-- JIKA AMAN: Tombol Submit (Hijau/Teal & Icon Keranjang) --}}
                                <button type="submit"
                                    class="w-8 h-8 rounded-full bg-teal-50 text-teal-600 hover:bg-teal-600 hover:text-white flex items-center justify-center transition shadow-sm"
                                    title="Tambah ke Keranjang">
                                    <i class="fa-solid fa-cart-plus text-xs"></i>
                                </button>
                            @endif
                        </form>
                    </div>

                </div>
            @empty
                <div class="col-span-full text-center py-12 text-gray-500 bg-gray-50 rounded-lg border border-dashed">
                    <i class="fa-solid fa-box-open text-4xl mb-3 text-gray-300"></i>
                    <p>Produk tidak ditemukan</p>
                </div>
            @endforelse

        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $products->withQueryString()->links() }}
        </div>

    </div>
@endsection
