@extends('layouts.app')

@section('content')

    <main class="pt-[20px] pb-28 max-w-6xl mx-auto px-4">

        <h1 class="text-lg font-bold mb-4">Keranjang Belanja</h1>

        {{-- ALERT STATUS UNIT --}}
        @if (!$carts->isEmpty())
            @php
                $activeUnit = ucfirst($carts->first()->business_unit);
                $alertColor =
                    $activeUnit == 'Diamart'
                        ? 'bg-green-50 border-green-500 text-green-700'
                        : 'bg-blue-50 border-blue-500 text-blue-700';
            @endphp

            <div class="{{ $alertColor }} border-l-4 p-4 mb-6 rounded-r shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">
                            Keranjang aktif untuk unit: <span class="font-bold uppercase">{{ $activeUnit }}</span>.
                        </p>
                        <p class="text-xs mt-1 opacity-80">
                            Anda tidak dapat menambahkan produk dari unit lain sebelum transaksi ini selesai.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- LIST CART --}}
            <div class="md:col-span-2 space-y-6">
                @php $grandTotal = 0; @endphp

                @forelse($carts as $cart)
                    {{--
                        KITA HAPUS HEADER UNIT DISINI
                        (Karena sudah ada di Alert Biru/Hijau di atas)
                    --}}

                    <div class="space-y-4">
                        @foreach ($cart->items as $item)
                            @php
                                $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;
                                $price = $product->price ?? 0;
                                $subtotal = $price * $item->qty;
                                $grandTotal += $subtotal;

                                $imagePath = $product->primaryImage
                                    ? asset('storage/' . $product->primaryImage->image_path)
                                    : asset('images/placeholder.png');
                            @endphp

                            <div class="bg-white rounded-xl shadow p-4 flex gap-4">
                                {{-- IMAGE --}}
                                <img src="{{ $imagePath }}" class="w-20 h-20 object-contain rounded-lg bg-gray-50">

                                {{-- INFO --}}
                                <div class="flex-1">
                                    <p class="font-semibold text-sm leading-tight mb-1">
                                        {{ $product->name }}
                                    </p>

                                    <p class="text-sm font-bold text-teal-700">
                                        Rp {{ number_format($price, 0, ',', '.') }}
                                    </p>

                                    {{-- Info Unit Kecil (Opsional, boleh dihapus juga kalau mau lebih bersih) --}}
                                    <p class="text-[10px] text-gray-400 mt-1 italic">
                                        Unit: {{ ucfirst($cart->business_unit) }}
                                    </p>

                                    {{-- QTY & UPDATE --}}
                                    <div class="mt-4">
                                        @php $stockGudang = $product->stock; @endphp

                                        <form method="POST" action="{{ route('cart.update', $item->id) }}"
                                            class="flex items-center gap-2">
                                            @csrf
                                            <div
                                                class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-gray-50">
                                                <button type="button" onclick="changeQty('input-{{ $item->id }}', -1)"
                                                    class="px-3 py-1 hover:bg-gray-200 text-gray-600 transition"><i
                                                        class="fa-solid fa-minus text-xs"></i></button>

                                                <input type="number" id="input-{{ $item->id }}" name="qty"
                                                    value="{{ $item->qty }}" min="1" max="{{ $stockGudang }}"
                                                    class="w-14 text-center bg-transparent border-none text-sm font-bold focus:ring-0"
                                                    required>

                                                <button type="button" onclick="changeQty('input-{{ $item->id }}', 1)"
                                                    class="px-3 py-1 hover:bg-gray-200 text-gray-600 transition"><i
                                                        class="fa-solid fa-plus text-xs"></i></button>
                                            </div>
                                            <button type="submit"
                                                class="text-[10px] font-bold text-teal-600 hover:underline uppercase tracking-widest">Simpan</button>
                                        </form>
                                        <p class="text-[10px] text-gray-400 mt-1">Stok: <b>{{ $stockGudang }}</b></p>
                                    </div>
                                </div>

                                {{-- REMOVE --}}
                                <div class="text-right flex flex-col justify-between">
                                    <p class="font-bold text-sm">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
                                    <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                        @csrf @method('DELETE')
                                        <button class="text-gray-400 hover:text-red-500 transition-colors"><i
                                                class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow p-12 text-center">
                        <div class="mb-4">
                            <i class="fa-solid fa-cart-shopping text-4xl text-gray-300"></i>
                        </div>
                        <p class="text-gray-500 mb-4 text-sm">Keranjang Anda masih kosong</p>
                        <a href="{{ url('/') }}" class="text-teal-600 font-semibold text-sm underline">Mulai
                            Belanja</a>
                    </div>
                @endforelse
            </div>

            {{-- SUMMARY (SAMA SEPERTI SEBELUMNYA) --}}
            @if (!$carts->isEmpty())
                <div class="hidden md:block">
                    <div class="bg-white rounded-xl shadow p-4 sticky top-[100px] border-t-4 border-teal-600">
                        <h2 class="font-bold text-sm mb-4">Ringkasan Pesanan</h2>
                        <div class="flex justify-between text-sm mb-3">
                            <span class="text-gray-600">Total Harga</span>
                            <span class="font-bold text-teal-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-[10px] text-gray-400 mb-4 leading-tight">*Biaya admin dll dihitung saat pembayaran.
                        </div>
                        <a href="{{ route('payment.index') }}"
                            class="block text-center bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-full text-sm font-bold shadow-md transition">Lanjut
                            Checkout</a>
                    </div>
                </div>
            @endif
        </div>
    </main>

    {{-- MOBILE CHECKOUT --}}
    @if (!$carts->isEmpty())
        <div
            class="fixed bottom-0 left-0 right-0 bg-white shadow-[0_-2px_10px_rgba(0,0,0,0.05)] md:hidden p-4 flex justify-between items-center z-50">
            <div>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-tighter">Total Belanja</p>
                <p class="font-bold text-teal-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
            </div>
            <a href="{{ route('payment.index') }}"
                class="bg-teal-600 text-white px-8 py-2.5 rounded-full font-bold text-sm">Checkout</a>
        </div>
    @endif

@endsection

{{-- SCRIPTS --}}
<script>
    function changeQty(inputId, delta) {
        const input = document.getElementById(inputId);
        const currentValue = parseInt(input.value) || 1;
        const maxStock = parseInt(input.getAttribute('max'));
        let newValue = currentValue + delta;

        if (newValue < 1) newValue = 1;
        if (newValue > maxStock) {
            newValue = maxStock;
            Swal.fire({
                icon: 'warning',
                title: 'Stok Terbatas',
                text: 'Jumlah melebihi stok yang tersedia.',
                confirmButtonColor: '#0d9488'
            });
        }
        input.value = newValue;
    }
</script>

<script>
    // 1. Notifikasi Sukses
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    @endif

    // 2. Notifikasi ERROR Validasi (Yang dikirim dari Controller)
    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Gagal Menambahkan',
            html: @json($errors->first()),
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Oke, Mengerti'
        });
    @endif
</script>
