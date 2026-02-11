@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-50">

        {{-- Header Section --}}
        {{-- pt-20 ditambahkan tepat di div putih ini (disesuaikan dengan gaya Gadget) --}}
        <div class="bg-white shadow-sm pt-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class="fa-solid fa-shopping-basket text-emerald-600 mr-2"></i>
                    Minimarket Sembako
                </h1>
                <p class="text-gray-600">Belanja kebutuhan sehari-hari dengan mudah dan praktis</p>
            </div>
        </div>

        {{-- Filter Bar --}}
        {{-- sticky class diperbaiki agar form menempel presisi --}}
        <div class="sticky bg-white shadow-md top-20 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <form method="GET" action="{{ route('minimarket.index') }}" class="flex flex-wrap items-center gap-4">

                    {{-- Search Bar --}}
                    <div class="flex-1 min-w-[250px]">
                        <div class="relative">
                            <i
                                class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari produk sembako..."
                                class="w-full pl-11 pr-10 py-2.5 border border-gray-300 rounded-lg outline-none focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 transition-all">

                            {{-- Clear Search Button (Fungsi Asli Dipertahankan) --}}
                            @if (request('search'))
                                <button type="button"
                                    onclick="document.querySelector('input[name=search]').value=''; this.closest('form').submit();"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
                                    <i class="fa-solid fa-times-circle"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Category Filter --}}
                    <div class="relative">
                        <select name="category" onchange="this.form.submit()"
                            class="appearance-none pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg outline-none focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 bg-white transition-all cursor-pointer hover:border-emerald-400">
                            <option value="">📁 Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name ?? $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <i
                            class="fa-solid fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>

                    {{-- Search Button --}}
                    <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 outline-none focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all font-medium shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-search mr-2"></i>Cari
                    </button>

                    {{-- Reset Filter --}}
                    @if (request()->hasAny(['search', 'category']))
                        <a href="{{ route('minimarket.index') }}"
                            class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 outline-none focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all font-medium inline-flex items-center">
                            <i class="fa-solid fa-rotate-right mr-2"></i>Reset
                        </a>
                    @endif

                </form>
            </div>
        </div>

        {{-- Product Grid Section --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            {{-- Alert Notification - Cart Lock (Desain disesuaikan agar lebih rapi) --}}
            @if (isset($cartLock) && $cartLock == 'raditya')
                <div
                    class="mb-6 bg-blue-50 border border-blue-200 p-5 rounded-xl shadow-sm flex flex-col md:flex-row items-start md:items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-lock text-blue-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-blue-900 mb-1">Mode Terbatas Aktif</h3>
                        <p class="text-sm text-blue-800">
                            Keranjang Anda saat ini berisi produk <span
                                class="font-bold bg-blue-200 px-2 py-0.5 rounded text-xs uppercase tracking-wide">Gadget
                                (Raditya)</span>.<br class="hidden md:block">
                            Selesaikan transaksi Gadget terlebih dahulu untuk membeli Sembako.
                        </p>
                    </div>
                    <a href="{{ route('cart.index') }}"
                        class="w-full md:w-auto px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm shadow-sm flex items-center justify-center whitespace-nowrap">
                        <i class="fa-solid fa-shopping-cart mr-2"></i>Lihat Keranjang
                    </a>
                </div>
            @endif

            {{-- Results Info --}}
            <div class="mb-6 flex items-center justify-between">
                <p class="text-gray-600">
                    <span class="font-semibold text-gray-800">{{ $products->total() }}</span> produk ditemukan
                </p>
            </div>

            {{-- Product Grid (Grid persis dengan gaya Gadget) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @forelse ($products as $product)
                    <div
                        class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col">
                        <a href="{{ route('minimarket.show', $product->id) }}" class="block flex-grow focus:outline-none">

                            {{-- Image Container --}}
                            <div class="relative bg-gray-50 p-4 overflow-hidden border-b border-gray-100">
                                <div class="aspect-square flex items-center justify-center">
                                    <img src="{{ $product->primaryImage ? asset('storage/' . $product->primaryImage->image_path) : asset('images/placeholder.png') }}"
                                        class="max-h-full max-w-full object-contain group-hover:scale-110 transition-transform duration-300"
                                        alt="{{ $product->name }}">
                                </div>

                                {{-- Lock Badge --}}
                                @if (isset($cartLock) && $cartLock == 'raditya')
                                    <div
                                        class="absolute top-3 left-3 bg-gray-900/80 backdrop-blur text-white px-2 py-1 rounded text-[10px] font-bold tracking-wide flex items-center gap-1 shadow-sm">
                                        <i class="fa-solid fa-lock"></i> Terkunci
                                    </div>
                                @endif

                                {{-- Wishlist Badge --}}
                                <div
                                    class="absolute top-3 right-3 bg-white rounded-full p-2 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-regular fa-heart text-gray-600 hover:text-red-500 transition-colors"></i>
                                </div>
                            </div>

                            {{-- Product Info --}}
                            <div class="p-4 flex flex-col h-full">
                                <h3
                                    class="text-sm font-semibold text-gray-800 mb-2 line-clamp-2 leading-tight group-hover:text-emerald-600 transition-colors">
                                    {{ $product->name }}
                                </h3>

                                {{-- Price --}}
                                <div class="mb-3 mt-auto">
                                    <p class="text-lg font-bold text-emerald-600">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </a>

                        {{-- Action Buttons (Berupa Form sesuai fungsi minimarket asli) --}}
                        <div class="p-4 pt-0 mt-auto">
                            <form action="{{ route('cart.add') }}" method="POST" class="flex gap-2 w-full">
                                @csrf
                                <input type="hidden" name="id_product_diamart" value="{{ $product->id }}">

                                @if (isset($cartLock) && $cartLock == 'raditya')
                                    <button type="button" disabled
                                        class="flex-1 bg-gray-100 text-gray-400 py-2 rounded-lg cursor-not-allowed font-medium text-sm border border-gray-200 shadow-sm flex justify-center items-center"
                                        title="Selesaikan keranjang Gadget terlebih dahulu">
                                        <i class="fa-solid fa-lock mr-1"></i> Terkunci
                                    </button>
                                @else
                                    <button type="submit"
                                        class="flex-1 bg-emerald-600 text-white py-2 rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-medium text-sm shadow-sm hover:shadow-md flex justify-center items-center">
                                        <i class="fa-solid fa-cart-plus mr-1"></i> Beli
                                    </button>
                                @endif

                                <a href="{{ route('minimarket.show', $product->id) }}"
                                    class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </form>
                        </div>
                    </div>
                @empty
                    {{-- Empty State (Desain Gadget) --}}
                    <div class="col-span-full text-center py-16">
                        <i class="fa-solid fa-basket-shopping text-6xl text-gray-300 mb-4"></i>
                        <p class="text-xl text-gray-500 font-medium">Produk tidak ditemukan</p>
                        <p class="text-gray-400 mt-2">Coba ubah filter atau kata kunci pencarian Anda</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-10">
                {{ $products->withQueryString()->links() }}
            </div>
        </div>
    </div>

    {{-- Script untuk Auto-Search (Fungsi Asli Dipertahankan) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const searchForm = searchInput.closest('form');
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    if (searchInput.value.length >= 3 || searchInput.value.length === 0) {
                        searchForm.submit();
                    }
                }, 600);
            });

            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchTimeout);
                    searchForm.submit();
                }
            });
        });
    </script>
@endsection
