@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50">

        {{-- Header Section --}}
        {{-- pt-20 ditambahkan tepat di div putih ini.
             Fungsinya agar warna putih memanjang ke atas di balik Navbar, sehingga celah abu-abu hilang. --}}
        <div class="bg-white shadow-sm pt-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class="fa-solid fa-mobile-screen-button text-indigo-600 mr-2"></i>
                    Katalog Gadget
                </h1>
                <p class="text-gray-600">Temukan gadget impian Anda dengan harga terbaik</p>
            </div>
        </div>

        {{-- Filter Bar --}}
        {{-- sticky top-20 memastikan form ini menempel presisi di bawah navbar (h-20) saat Anda scroll ke bawah --}}
        <div class="bg-white shadow-md top-20 z-40 ">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <form method="GET" class="flex flex-wrap items-center gap-4">

                    {{-- Search Bar --}}
                    <div class="flex-1 min-w-[250px]">
                        <div class="relative">
                            <i
                                class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari produk..." {{-- outline-none & focus:outline-none mematikan paksa garis hitam bawaan browser --}}
                                class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>

                    {{-- Category Filter --}}
                    <div class="relative">
                        <select name="category" onchange="this.form.submit()"
                            class="appearance-none pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg outline-none focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 bg-white transition-all cursor-pointer hover:border-indigo-400">
                            <option value="">📁 Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                        <i
                            class="fa-solid fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>

                    {{-- Brand Filter --}}
                    <div class="relative">
                        <select name="brand" onchange="this.form.submit()"
                            class="appearance-none pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg outline-none focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 bg-white transition-all cursor-pointer hover:border-indigo-400">
                            <option value="">🏷️ Semua Brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->brand_name }}
                                </option>
                            @endforeach
                        </select>
                        <i
                            class="fa-solid fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>

                    {{-- Search Button --}}
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 outline-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all font-medium shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-search mr-2"></i>Cari
                    </button>

                    {{-- Reset Filter --}}
                    @if (request()->hasAny(['search', 'category', 'brand']))
                        <a href="{{ route('gadget.index') }}"
                            class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 outline-none focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all font-medium inline-flex items-center">
                            <i class="fa-solid fa-rotate-right mr-2"></i>Reset
                        </a>
                    @endif

                </form>
            </div>
        </div>
        {{-- Product Grid Section --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- Results Info --}}
            <div class="mb-6 flex items-center justify-between">
                <p class="text-gray-600">
                    <span class="font-semibold text-gray-800">{{ $products->total() }}</span> produk ditemukan
                </p>
            </div>

            {{-- Product Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @forelse ($products as $product)
                    <div
                        class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col">
                        <a href="{{ route('gadget.show', $product->id) }}" class="block flex-grow focus:outline-none">
                            {{-- Image Container --}}
                            <div class="relative bg-gray-50 p-4 overflow-hidden border-gray-100">
                                <div class="aspect-square flex items-center justify-center">
                                    <img src="{{ $product->primaryImage ? asset('storage/' . $product->primaryImage->image_path) : asset('images/placeholder.png') }}"
                                        class="max-h-full max-w-full object-contain group-hover:scale-110 transition-transform duration-300"
                                        alt="{{ $product->name }}">
                                </div>
                                {{-- Wishlist Badge --}}
                                <div
                                    class="absolute top-3 right-3 bg-white rounded-full p-2 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-regular fa-heart text-gray-600 hover:text-red-500 transition-colors"></i>
                                </div>
                            </div>

                            {{-- Product Info --}}
                            <div class="p-4 flex flex-col h-full">
                                <h3
                                    class="text-sm font-semibold text-gray-800 mb-2 line-clamp-2 leading-tight group-hover:text-indigo-600 transition-colors">
                                    {{ $product->name }}
                                </h3>

                                {{-- Price --}}
                                <div class="mb-3 mt-auto">
                                    <p class="text-lg font-bold text-indigo-600">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </a>

                        {{-- Action Buttons --}}
                        <div class="p-4 pt-0 mt-auto flex gap-2">
                            <button onclick="event.preventDefault();"
                                class="flex-1 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-sm shadow-sm hover:shadow-md">
                                <i class="fa-solid fa-cart-plus mr-1"></i>
                                Keranjang
                            </button>
                            <button onclick="event.preventDefault();"
                                class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-16">
                        <i class="fa-solid fa-box-open text-6xl text-gray-300 mb-4"></i>
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
@endsection
