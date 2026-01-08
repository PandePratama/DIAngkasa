@extends('layouts.app')

@section('content')
<main class="pt-[20px] max-w-6xl mx-auto px-4 pb-24">

    <h1 class="text-lg font-semibold mb-6">Pembayaran</h1>

    <form method="POST" action="{{ route('payment.process') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- LEFT --}}
            <div class="md:col-span-2 space-y-6">

                {{-- ITEM LIST --}}
                <div class="bg-gray-200 rounded-xl p-4">
                    @forelse($cartItems as $item)
                    <div class="flex items-center justify-between mb-3">

                        <div class="flex items-center gap-3">
                            <img
                                src="{{ $item->product->primaryImage
                ? asset('storage/'.$item->product->primaryImage->image_path)
                : asset('images/placeholder.png') }}"
                                class="w-10 h-10 object-contain">

                            <div>
                                <p class="text-sm font-semibold">
                                    {{ $item->product->name }}
                                </p>

                                <p class="text-xs text-gray-600">
                                    Rp {{ number_format($item->price,0,',','.') }}
                                </p>

                                <p class="text-xs">
                                    @if($item->purchase_type === 'credit')
                                    <span class="text-orange-600 font-semibold">
                                        Kredit • {{ $item->tenor }} bulan
                                    </span>
                                    @else
                                    <span class="text-green-600 font-semibold">
                                        Cash
                                    </span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <span class="text-sm">
                            Qty: {{ $item->qty }}
                        </span>
                    </div>
                    @empty
                    <p class="text-sm text-center text-gray-500">
                        Keranjang kosong
                    </p>
                    @endforelse
                </div>

                {{-- PAYMENT METHOD --}}
                <div class="bg-gray-200 rounded-xl p-4">
                    <h2 class="text-sm font-semibold mb-3">Metode Pembayaran</h2>

                    <label class="flex gap-3 bg-white rounded p-3 mb-3 cursor-pointer">
                        <input type="radio" name="payment_method" value="cash" required>
                        <div>
                            <p class="font-semibold text-sm">Cash</p>
                            <p class="text-xs text-gray-500">
                                Dengan pembayaran cash limit tidak terpotong
                            </p>
                        </div>
                    </label>

                    <label class="flex gap-3 bg-white rounded p-3 cursor-pointer">
                        <input type="radio" name="payment_method" value="credit">
                        <div>
                            <p class="font-semibold text-sm">Kredit</p>
                            <p class="text-xs text-gray-500">
                                Limit akan terpotong otomatis
                            </p>
                        </div>
                    </label>
                </div>

                {{-- SHIPPING --}}
                <div class="bg-gray-200 rounded-xl p-4">
                    <h2 class="text-sm font-semibold mb-3">Pengiriman</h2>

                    <label class="flex items-center gap-3 bg-white rounded p-3 cursor-pointer">
                        <input type="radio" name="shipping_method" value="pickup" required>
                        <span class="text-sm">Ambil di Toko</span>
                    </label>
                </div>

            </div>

            {{-- RIGHT --}}
            <div>
                <div class="bg-gray-200 rounded-xl p-6 text-center sticky top-[110px]">
                    <p class="text-sm mb-2">Total Belanja</p>
                    <p class="text-xl font-bold mb-4">
                        Rp {{ number_format($total,0,',','.') }}
                    </p>

                    <button
                        class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-full w-full">
                        Bayar
                    </button>
                </div>
            </div>

        </div>
    </form>
</main>
@endsection