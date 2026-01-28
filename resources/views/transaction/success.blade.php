@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
        <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full text-center">

            {{-- LOGIKA PENENTU GAMBAR & TEKS --}}
            @if ($transaction->payment_method == 'balance')
                {{-- TAMPILAN 1: POTONG SALDO (HIJAU SEPERTI GAMBAR ANDA) --}}
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-check text-4xl text-green-600"></i>
                </div>

                <h1 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Berhasil!</h1>
                <p class="text-gray-500 mb-6">
                    Terima kasih. Saldo Anda telah terpotong sebesar
                    <span class="font-bold text-gray-800">Rp
                        {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>.
                    Pesanan Anda sedang diproses.
                </p>
            @else
                {{-- TAMPILAN 2: CASH / TUNAI (KUNING / ORANYE) --}}
                {{-- Ini yang akan tampil untuk user Cash --}}
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-receipt text-4xl text-yellow-600"></i>
                </div>

                <h1 class="text-2xl font-bold text-gray-800 mb-2">Pesanan Dibuat!</h1>
                <p class="text-gray-500 mb-6">
                    Silakan tunjukkan kode invoice
                    <span
                        class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded">{{ $transaction->invoice_code }}</span>
                    kepada kasir dan lakukan pembayaran tunai sebesar
                    <span class="font-bold text-gray-800">Rp
                        {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>.
                </p>
            @endif

            {{-- TOMBOL SAMA UNTUK KEDUANYA --}}
            <div class="space-y-3">
                <a href="{{ route('profile.index', ['tab' => 'history']) }}"
                    class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition">
                    Lihat Riwayat Belanja
                </a>
                <a href="{{ route('home') }}"
                    class="block w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 rounded-xl transition">
                    Kembali Belanja
                </a>
            </div>

        </div>
    </div>
@endsection
