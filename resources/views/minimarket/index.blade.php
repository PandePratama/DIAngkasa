@extends('layouts.app')

@section('content')
{{-- Filter Bar --}}
<form method="GET" class="bg-gray-200 px-6 py-3 mb-4">
    <div class="flex flex-wrap gap-3">

        {{-- Category --}}
        <select name="category"
            onchange="this.form.submit()"
            class="px-3 py-1 rounded border text-sm">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $category)
            <option value="{{ $category->id }}"
                {{ request('category') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
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

            <a href="{{ route('minimarket.show', $product->id) }}" class="block">

                <img src="{{ $product->primaryImage
                            ? asset('storage/' . $product->primaryImage->image_path)
                            : asset('images/placeholder.png') }}"
                    class="mx-auto h-32 object-contain mb-3" alt="{{ $product->name }}">

                <p class="text-sm font-semibold leading-tight">
                    {{ $product->name }}
                </p>

                <p class="text-xs text-gray-600 mb-2">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

                <div class="flex justify-end">
                    <button class="text-gray-500 hover:text-teal-700">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </button>
                </div>
            </a>
        </div>
        @empty
        <p class="col-span-full text-center text-gray-500">
            Produk tidak ditemukan
        </p>
        @endforelse

    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $products->withQueryString()->links() }}
    </div>

</div>
@endsection