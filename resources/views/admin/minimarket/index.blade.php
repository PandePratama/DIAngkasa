@extends('layouts.app')
{{-- Pastikan layouts.app ini layout Public (bukan Admin Dashboard) --}}

@section('content')
    {{-- Filter Bar --}}
    {{-- PERBAIKAN 1: Tambahkan action route agar filter berfungsi --}}
    <form action="{{ route('front.diamart.index') }}" method="GET" class="bg-gray-200 px-6 py-3 mb-4">
        <div class="flex flex-wrap gap-3">

            {{-- Category --}}
            <select name="category" onchange="this.form.submit()" class="px-3 py-1 rounded border text-sm">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>

                        {{-- PERBAIKAN 2: name -> category_name --}}
                        {{ $category->category_name }}

                    </option>
                @endforeach
            </select>

        </div>
    </form>

    {{-- Produk Grid --}}
    <div class="container mx-auto px-6 py-8">

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">

            @forelse ($products as $product)
                <div class="bg-white border rounded-lg p-3 hover:shadow transition">

                    {{-- PERBAIKAN 3: Route ke front.diamart.show --}}
                    <a href="{{ route('front.diamart.show', $product->id) }}" class="block">

                        <div class="h-32 w-full flex items-center justify-center mb-3 bg-gray-50 rounded">
                            @if ($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                    class="h-full object-contain" alt="{{ $product->name }}">
                            @else
                                {{-- Placeholder jika tidak ada gambar --}}
                                <div class="text-gray-400 text-xs">No Image</div>
                            @endif
                        </div>

                        <p class="text-sm font-semibold leading-tight truncate">
                            {{ $product->name }}
                        </p>

                        <p class="text-xs text-gray-600 mb-2">
                            {{ $product->category->category_name ?? 'Umum' }}
                        </p>

                        <p class="text-sm font-bold text-teal-600">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </p>

                        <div class="flex justify-end mt-2">
                            <button class="text-gray-500 hover:text-teal-700">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </button>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-10">
                    <p class="text-gray-500 text-lg">Produk tidak ditemukan</p>
                    <a href="{{ route('front.diamart.index') }}" class="text-teal-600 text-sm hover:underline">Reset
                        Filter</a>
                </div>
            @endforelse

        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $products->withQueryString()->links() }}
        </div>

    </div>
@endsection
