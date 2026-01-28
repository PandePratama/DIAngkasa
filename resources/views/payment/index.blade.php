@extends('layouts.app')

@section('content')
    <main class="pt-[20px] max-w-6xl mx-auto px-4 pb-24">
        <h1 class="text-xl font-bold mb-6 text-gray-800">Konfirmasi Pembayaran</h1>

        {{-- FORM PEMBAYARAN --}}
        {{-- PENTING: Tambahkan id="payment-form" untuk dikenali JavaScript --}}
        <form id="payment-form" method="POST" action="{{ route('payment.process') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                {{-- SISI KIRI: ITEM & METODE --}}
                <div class="md:col-span-2 space-y-6">

                    {{-- 1. DAFTAR ITEM --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Rincian Barang</h2>
                        <div class="space-y-4">
                            {{-- PERBAIKAN: Gunakan $cart->items, bukan $cartItems --}}
                            @forelse($cart->items as $item)
                                @php
                                    // Logika menentukan produk Diamart atau Raditya
                                    $product = $item->id_product_diamart
                                        ? $item->productDiamart
                                        : $item->productDiraditya;

                                    $price = $product->price ?? 0;

                                    $image = $product->primaryImage
                                        ? asset('storage/' . $product->primaryImage->image_path)
                                        : asset('images/placeholder.png');
                                @endphp

                                <div
                                    class="flex items-center justify-between border-b border-gray-50 pb-4 last:border-0 last:pb-0">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-16 h-16 bg-gray-50 rounded-lg flex items-center justify-center p-2 border border-gray-100">
                                            <img src="{{ $image }}" class="max-h-full max-w-full object-contain">
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">
                                                {{ $product->name ?? 'Produk Tidak Ditemukan' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Rp {{ number_format($price, 0, ',', '.') }} x {{ $item->qty }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-gray-900">
                                            Rp {{ number_format($price * $item->qty, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 text-sm py-4">Keranjang Anda kosong</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- 2. METODE PEMBAYARAN --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Metode Pembayaran</h2>

                        <div class="space-y-3">

                            {{-- OPSI 1: POTONG SALDO --}}
                            <label
                                class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:border-teal-500 transition shadow-sm bg-gray-50 has-[:checked]:border-teal-50 has-[:checked]:bg-teal-50">
                                <input type="radio" name="payment_method" value="balance"
                                    class="h-4 w-4 text-teal-600 focus:ring-teal-500" required checked>
                                <div class="ml-3 block">
                                    <span class="font-bold text-sm text-gray-800 block">Potong Saldo</span>
                                    <span class="text-xs text-gray-500">Sisa Saldo: Rp
                                        {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</span>
                                </div>
                            </label>

                            {{-- OPSI 2: CASH / TUNAI --}}
                            <label
                                class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:border-teal-500 transition shadow-sm bg-gray-50 has-[:checked]:border-teal-50 has-[:checked]:bg-teal-50">
                                <input type="radio" name="payment_method" value="cash"
                                    class="h-4 w-4 text-teal-600 focus:ring-teal-500">
                                <div class="ml-3 block">
                                    <span class="font-bold text-sm text-gray-800 block">Cash / Tunai</span>
                                    <span class="text-xs text-gray-500">Bayar langsung di kasir toko</span>
                                </div>
                            </label>

                        </div>
                    </div>
                </div>

                {{-- SISI KANAN: RINGKASAN BIAYA --}}
                <div class="md:col-span-1">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sticky top-[100px]">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 border-b pb-4 text-center">Ringkasan Biaya</h2>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 font-medium">Subtotal Belanja</span>
                                <span class="font-bold text-gray-900">Rp
                                    {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>

                            {{-- Tampilkan Admin Fee Jika Ada --}}
                            @if ($adminFee > 0)
                                <div class="flex justify-between text-sm text-orange-600 font-bold">
                                    <span>{{ $adminLabel }}</span>
                                    <span>Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="bg-teal-50 rounded-xl p-4 mb-6 border border-teal-100">
                            <p class="text-[10px] text-teal-600 font-bold uppercase text-center mb-1 tracking-widest">
                                Total Tagihan
                            </p>
                            <p class="text-2xl font-black text-teal-700 text-center">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- TOMBOL BAYAR --}}
                        <button type="button" onclick="confirmPayment()"
                            class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:-translate-y-1">
                            Konfirmasi & Bayar
                        </button>

                        <a href="{{ route('cart.index') }}"
                            class="block text-center text-xs font-bold text-gray-400 mt-4 hover:text-gray-600 transition">
                            Batal & Kembali
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </main>

    {{-- SCRIPT AREA --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Fungsi Konfirmasi Pembayaran (Dinamis: Cash vs Saldo)
        function confirmPayment() {
            // 1. Cek Metode Pembayaran yang dipilih user
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            let titleText = 'Konfirmasi Pembayaran';
            let bodyText = "Saldo Anda akan terpotong otomatis.";
            let btnText = 'Ya, Potong Saldo!';

            // 2. Ubah teks jika memilih Cash
            if (paymentMethod === 'cash') {
                bodyText = "Pesanan akan dibuat. Silakan lakukan pembayaran tunai di kasir.";
                btnText = 'Ya, Buat Pesanan!';
            }

            // 3. Tampilkan SweetAlert
            Swal.fire({
                title: titleText,
                text: bodyText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d9488', // Warna Teal-600
                cancelButtonColor: '#d33',
                confirmButtonText: btnText,
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {

                    // --- EFEK LOADING ---
                    Swal.fire({
                        title: 'Sedang Memproses...',
                        html: 'Mohon tunggu, jangan tutup halaman ini.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form
                    document.getElementById('payment-form').submit();
                }
            })
        }

        // Menangkap Pesan Error
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memproses!',
                text: "{!! session('error') !!}",
                confirmButtonColor: '#d33'
            });
        @endif

        // Menangkap Pesan Sukses
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
@endsection
