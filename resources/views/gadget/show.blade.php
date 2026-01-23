@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- IMAGE --}}
        <div class="border rounded-lg p-2 bg-white">
            <img
                src="{{ $product->primaryImage
                    ? asset('storage/'.$product->primaryImage->image_path)
                    : asset('images/placeholder.png') }}"
                class="mx-auto h-64 md:h-80 object-contain">
        </div>

        {{-- INFO --}}
        <div
            x-data="{
                type: 'cash',
                tenor: null,
                cashPrice: {{ $product->price }},
                prices: {
                    3: {{ $product->price_3_months ?? 'null' }},
                    6: {{ $product->price_6_months ?? 'null' }},
                    9: {{ $product->price_9_months ?? 'null' }},
                    12: {{ $product->price_12_months ?? 'null' }},
                },
                get displayPrice() {
                    if (this.type === 'credit' && this.tenor && this.prices[this.tenor]) {
                        return this.prices[this.tenor];
                    }
                    return this.cashPrice;
                }
            }">

            <h1 class="text-xl md:text-2xl font-semibold mb-2">
                {{ $product->name }}
            </h1>

            {{-- PRICE --}}
            <p class="text-xl font-bold text-teal-700">
                Rp <span x-text="displayPrice.toLocaleString('id-ID')"></span>
            </p>

            <p class="text-xs text-gray-500 mb-3"
                x-show="type === 'credit' && tenor">
                Harga per bulan • Tenor <span x-text="tenor"></span> bulan
            </p>

            <p class="text-sm text-gray-600 mb-4">
                Stok: {{ $product->stock }}
            </p>

            {{-- SPECIFICATION --}}
            <div class="text-sm text-gray-700 mb-6">
                <h2 class="font-semibold mb-2">Spesifikasi</h2>
                <div class="whitespace-pre-line leading-relaxed">
                    {{ $product->desc }}
                </div>
            </div>

            {{-- FORM --}}
            <form method="POST" action="{{ route('cart.add',$product->id) }}">
                @csrf

                {{-- PURCHASE TYPE --}}
                <p class="font-semibold mb-2">Metode Pembelian</p>

                <div class="flex gap-4 mb-4 text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio"
                            name="purchase_type"
                            value="cash"
                            x-model="type">
                        Cash
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio"
                            name="purchase_type"
                            value="credit"
                            x-model="type">
                        Kredit
                    </label>
                </div>

                {{-- TENOR --}}
                <div x-show="type === 'credit'" x-transition>
                    <p class="font-semibold mb-2">Pilih Tenor</p>

                    <div class="space-y-2 text-sm">

                        @if($product->price_3_months)
                        <label class="flex items-center gap-2 border rounded-lg p-3 cursor-pointer">
                            <input type="radio"
                                name="tenor"
                                value="3"
                                x-model="tenor"
                                required>
                            <span>
                                3 Bulan —
                                Rp {{ number_format($product->price_3_months,0,',','.') }}/bulan
                            </span>
                        </label>
                        @endif

                        @if($product->price_6_months)
                        <label class="flex items-center gap-2 border rounded-lg p-3 cursor-pointer">
                            <input type="radio"
                                name="tenor"
                                value="6"
                                x-model="tenor"
                                required>
                            <span>
                                6 Bulan —
                                Rp {{ number_format($product->price_6_months,0,',','.') }}/bulan
                            </span>
                        </label>
                        @endif

                        @if($product->price_9_months)
                        <label class="flex items-center gap-2 border rounded-lg p-3 cursor-pointer">
                            <input type="radio"
                                name="tenor"
                                value="9"
                                x-model="tenor">
                            <span>
                                9 Bulan —
                                Rp {{ number_format($product->price_9_months,0,',','.') }}/bulan
                            </span>
                        </label>
                        @endif

                        @if($product->price_12_months)
                        <label class="flex items-center gap-2 border rounded-lg p-3 cursor-pointer">
                            <input type="radio"
                                name="tenor"
                                value="12"
                                x-model="tenor">
                            <span>
                                12 Bulan —
                                Rp {{ number_format($product->price_12_months,0,',','.') }}/bulan
                            </span>
                        </label>
                        @endif

                    </div>
                </div>

                {{-- SUBMIT --}}
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
        <a href="{{ route('gadget.show', $item->id) }}"
            class="bg-white border rounded-lg p-3 hover:shadow transition">

            <img
                src="{{ $item->primaryImage
                    ? asset('storage/'.$item->primaryImage->image_path)
                    : asset('images/placeholder.png') }}"
                class="mx-auto h-24 object-contain mb-2">

            <p class="text-sm font-semibold leading-tight">
                {{ $item->name }}
            </p>

            <p class="text-xs text-gray-600">
                Rp {{ number_format($item->price,0,',','.') }}
            </p>
        </a>
        @endforeach
    </div>

</div>
@endsection