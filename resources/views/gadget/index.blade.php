@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50">

    {{-- HEADER --}}
    <div class="bg-white shadow-sm pt-4 md:pt-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
            <h1 class="text-xl md:text-3xl font-bold text-gray-800 mb-1 md:mb-2 flex items-center gap-2">
                <i class="fa-solid fa-mobile-screen-button text-indigo-600"></i>
                Katalog Gadget
            </h1>
            <p class="text-sm md:text-base text-gray-600">
                Temukan gadget impian Anda dengan harga terbaik
            </p>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="bg-white shadow-md sticky top-20 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <form method="GET" class="flex flex-col md:flex-row md:items-center gap-3 md:gap-4">

                {{-- SEARCH --}}
                <div class="flex-1 w-full">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari produk..."
                            class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                </div>

                {{-- CATEGORY --}}
                <div class="relative w-full md:w-auto">
                    <select name="category"
                        onchange="this.form.submit()"
                        class="w-full appearance-none pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 bg-white cursor-pointer">
                        <option value="">📁 Semua Kategori</option>
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                </div>

                {{-- BRAND --}}
                <div class="relative w-full md:w-auto">
                    <select name="brand"
                        onchange="this.form.submit()"
                        class="w-full appearance-none pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 bg-white cursor-pointer">
                        <option value="">🏷️ Semua Brand</option>
                        @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}"
                            {{ request('brand') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->brand_name }}
                        </option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                </div>

                {{-- BUTTON --}}
                <button type="submit"
                    class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 transition-all font-medium shadow-sm">
                    <i class="fa-solid fa-search mr-2"></i>Cari
                </button>

                @if (request()->hasAny(['search','category','brand']))
                <a href="{{ route('gadget.index') }}"
                    class="w-full md:w-auto px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:ring-2 focus:ring-gray-400 transition-all font-medium inline-flex items-center justify-center">
                    <i class="fa-solid fa-rotate-right mr-2"></i>Reset
                </a>
                @endif

            </form>
        </div>
    </div>

    {{-- PRODUCT SECTION --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- RESULT INFO --}}
        <div class="mb-6">
            <p class="text-sm md:text-base text-gray-600">
                <span class="font-semibold text-gray-800">
                    {{ $products->total() }}
                </span> produk ditemukan
            </p>
        </div>

        {{-- GRID --}}
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">

            @forelse ($products as $product)
            <div class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col">

                <a href="{{ route('gadget.show', $product->id) }}" class="flex flex-col h-full">

                    {{-- IMAGE --}}
                    <div class="bg-gray-50 p-3 md:p-4">
                        <div class="aspect-square flex items-center justify-center">
                            <img
                                src="{{ $product->primaryImage ? $product->primaryImage->image_path : asset('images/placeholder.png') }}"
                                alt="{{ $product->name }}"
                                loading="lazy"
                                class="max-h-full max-w-full object-contain transition-transform duration-300 group-hover:scale-105">
                        </div>
                    </div>

                    {{-- INFO --}}
                    <div class="p-3 md:p-4 flex flex-col flex-grow">

                        <h3 class="text-xs sm:text-sm font-semibold text-gray-800 mb-2 line-clamp-2 leading-snug group-hover:text-indigo-600 transition-colors">
                            {{ $product->name }}
                        </h3>

                        <div class="mt-auto">
                            <p class="text-base md:text-lg font-bold text-indigo-600">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>

                    </div>
                </a>

                {{-- ACTION --}}
                <div class="p-3 md:p-4 pt-0 flex gap-2">
                    <button
                        class="flex-1 bg-indigo-600 text-white py-2.5 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-xs sm:text-sm shadow-sm">
                        <i class="fa-solid fa-cart-plus mr-1"></i>
                        Keranjang
                    </button>

                    <button
                        class="px-3 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:ring-2 focus:ring-gray-400 transition-all">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

            </div>
            @empty
            <div class="col-span-full text-center py-16">
                <i class="fa-solid fa-box-open text-5xl text-gray-300 mb-4"></i>
                <p class="text-lg text-gray-500 font-medium">Produk tidak ditemukan</p>
                <p class="text-gray-400 mt-2">Coba ubah filter atau kata kunci pencarian Anda</p>
            </div>
            @endforelse

        </div>

        {{-- PAGINATION --}}
        <div class="mt-10">
            {{ $products->withQueryString()->links() }}
        </div>

    </div>
</div>
@endsection