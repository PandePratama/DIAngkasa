@extends('layouts.app')

@section('content')

<main class="pt-[20px] pb-28 max-w-6xl mx-auto px-4">

    <h1 class="text-lg font-bold mb-6">Keranjang Belanja</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- LIST CART --}}
        <div class="md:col-span-2 space-y-4">
            @forelse($items as $item)
            <div class="bg-white rounded-xl shadow p-4 flex gap-4">

                {{-- IMAGE --}}
                <img
                    src="{{ $item->product->primaryImage
            ? asset('storage/'.$item->product->primaryImage->image_path)
            : asset('images/placeholder.png') }}"
                    class="w-20 h-20 object-contain">

                {{-- INFO --}}
                <div class="flex-1">
                    <p class="font-semibold text-sm">
                        {{ $item['name'] }}
                    </p>

                    <p class="text-sm text-gray-600">
                        Rp {{ number_format($item['price'],0,',','.') }}
                    </p>

                    {{-- PURCHASE TYPE --}}
                    <p class="text-xs mt-1">
                        @if(($item['purchase_type'] ?? 'cash') === 'credit')
                        <span class="text-orange-600 font-semibold">
                            Kredit
                            @if(!empty($item['tenor']))
                            • {{ $item['tenor'] }} bulan
                            @endif
                        </span>
                        @else
                        <span class="text-green-600 font-semibold">
                            Cash
                        </span>
                        @endif
                    </p>

                    {{-- UPDATE QTY --}}
                    <form method="POST"
                        action="{{ route('cart.update', $item['id']) }}"
                        class="flex items-center gap-2 mt-2">
                        @csrf
                        <input type="number"
                            name="qty"
                            value="{{ $item['qty'] }}"
                            min="1"
                            class="w-16 border rounded text-center text-sm">
                        <button class="text-sm text-teal-600">
                            Update
                        </button>
                    </form>
                </div>

                {{-- SUBTOTAL --}}
                <div class="text-right">
                    <p class="font-semibold text-sm">
                        Rp {{ number_format($item['price'] * $item['qty'],0,',','.') }}
                    </p>

                    <form method="POST"
                        action="{{ route('cart.remove',$item['id']) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-500 text-sm mt-4">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </div>

            </div>
            @empty
            <p class="text-center text-gray-500">
                Cart kosong
            </p>
            @endforelse
        </div>

        {{-- SUMMARY (DESKTOP) --}}
        <div class="hidden md:block">
            <div class="bg-white rounded-xl shadow p-4 sticky top-[100px]">
                <h2 class="font-semibold mb-4">Ringkasan</h2>

                <div class="flex justify-between text-sm mb-3">
                    <span>Total</span>
                    <span class="font-semibold">
                        Rp {{ number_format($total,0,',','.') }}
                    </span>
                </div>

                <a href="{{ route('payment.index') }}"
                    class="block text-center bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-full">
                    Checkout
                </a>
            </div>
        </div>

    </div>
</main>

{{-- MOBILE CHECKOUT --}}
<div class="fixed bottom-0 left-0 right-0 bg-white shadow md:hidden p-4 flex justify-between">
    <div>
        <p class="text-xs text-gray-500">Total</p>
        <p class="font-bold">
            Rp {{ number_format($total,0,',','.') }}
        </p>
    </div>
    <a href="{{ route('payment.index') }}"
        class="bg-teal-600 text-white px-6 py-2 rounded-full">
        Checkout
    </a>
</div>

@endsection