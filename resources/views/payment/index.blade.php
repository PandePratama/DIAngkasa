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
                            @forelse($cartItems as $item)
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

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Opsi Cash --}}
                            <label
                                class="relative flex flex-col p-4 border rounded-xl cursor-pointer hover:border-teal-500 transition shadow-sm bg-gray-50">
                                <input type="radio" name="payment_method" value="cash"
                                    class="absolute top-4 right-4 text-teal-600 focus:ring-teal-500" required checked
                                    onclick="toggleTenure(false)">
                                <span class="font-bold text-sm text-gray-800">Bayar Tunai</span>
                                <span class="text-[10px] text-gray-500 mt-1">Potong saldo otomatis</span>
                            </label>

                            {{-- Opsi Kredit (Hanya muncul jika unit bisnis Raditya) --}}
                            @if ($cart->business_unit == 'raditya')
                                <label
                                    class="relative flex flex-col p-4 border rounded-xl cursor-pointer hover:border-teal-500 transition shadow-sm bg-gray-50">
                                    <input type="radio" name="payment_method" value="credit"
                                        class="absolute top-4 right-4 text-teal-600 focus:ring-teal-500"
                                        onclick="toggleTenure(true)">
                                    <span class="font-bold text-sm text-gray-800">Kredit Karyawan</span>
                                    <span class="text-[10px] text-gray-500 mt-1">Cicilan bulanan</span>
                                </label>
                            @endif
                        </div>

                        {{-- Pilihan Tenor (Hidden by default) --}}
                        <div id="tenure-section" class="mt-6 hidden animate-fade-in-down">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Pilih Tenor Cicilan</label>
                            <select name="tenure"
                                class="w-full bg-white border border-gray-300 rounded-lg p-3 text-sm focus:ring-teal-500 focus:border-teal-500">
                                <option value="3">3 Bulan</option>
                                <option value="6">6 Bulan</option>
                                <option value="12">12 Bulan (1 Tahun)</option>
                            </select>
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
                                <span class="font-bold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
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
                                Total Potong Saldo
                            </p>
                            <p class="text-2xl font-black text-teal-700 text-center">
                                @php
                                    $isCredit = $cart->business_unit == 'raditya';
                                    // Hitung total tampilan (Jika kredit: cicilan pertama + admin, Jika cash: full + admin)
                                    $displayTotal = $isCredit ? $subtotal / 3 + $adminFee : $subtotal + $adminFee;
                                @endphp
                                Rp {{ number_format($displayTotal, 0, ',', '.') }}
                            </p>
                            @if ($isCredit)
                                <p class="text-[10px] text-center text-teal-600 mt-1">*Estimasi cicilan pertama</p>
                            @endif
                        </div>

                        {{-- TOMBOL BAYAR (Type Button, bukan Submit) --}}
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
    {{-- Memuat SweetAlert2 dari CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 1. Fungsi Toggle Tampilan Tenor
        function toggleTenure(show) {
            const section = document.getElementById('tenure-section');
            if (section) {
                section.style.display = show ? 'block' : 'none';

                // Animasi kecil (opsional)
                if (show) {
                    section.classList.remove('opacity-0');
                    section.classList.add('opacity-100');
                }
            }
        }

        // 2. Fungsi Konfirmasi Pembayaran (Profesional UX)
        function confirmPayment() {
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                text: "Saldo Anda akan terpotong otomatis untuk transaksi ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d9488', // Warna Teal-600
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Bayar Sekarang!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {

                    // --- EFEK LOADING PROFESIONAL ---
                    Swal.fire({
                        title: 'Sedang Memproses...',
                        html: 'Mohon tunggu, jangan tutup halaman ini.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading(); // Munculkan spinner
                        }
                    });

                    // Submit form secara programmatically
                    // Pastikan ID form sesuai: 'payment-form'
                    document.getElementById('payment-form').submit();
                }
            })
        }

        // 3. Menangkap Pesan Error dari Controller (Misal: Saldo Kurang)
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memproses!',
                text: "{!! session('error') !!}",
                confirmButtonColor: '#d33'
            });
        @endif

        // 4. Menangkap Pesan Sukses
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
