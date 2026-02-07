@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
        <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full text-center">

            {{-- SKENARIO 1: TRANSAKSI KREDIT (BARU DITAMBAHKAN) --}}
            @if (isset($type) && $type == 'credit')
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-file-signature text-4xl text-blue-600"></i>
                </div>

                <h1 class="text-2xl font-bold text-gray-800 mb-2">Pengajuan Kredit Berhasil!</h1>

                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 text-sm text-left">
                    <p class="mb-2">
                        <span class="text-gray-500">Barang:</span><br>
                        <span class="font-bold text-gray-800">{{ $transaction->product->name ?? 'Gadget' }}</span>
                    </p>
                    <div class="flex justify-between mb-2">
                        <span>Tenor:</span>
                        <span class="font-bold">{{ $transaction->tenor }} Bulan</span>
                    </div>
                    <div class="flex justify-between border-t border-blue-200 pt-2">
                        <span>DP Terbayar (Saldo):</span>
                        <span class="font-bold text-blue-700">Rp
                            {{ number_format($transaction->dp_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <p class="text-gray-500 text-sm mb-6">
                    Cicilan pertama akan dimulai bulan depan. Cek menu <b>Tanggungan Tenor</b> untuk jadwal lengkap.
                </p>

                {{-- SKENARIO 2: TRANSAKSI REGULER (SALDO/CASH) --}}
            @else
                @if ($transaction->payment_method == 'balance')
                    {{-- POTONG SALDO --}}
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-check text-4xl text-green-600"></i>
                    </div>

                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Lunas!</h1>
                    <p class="text-gray-500 mb-6">
                        Saldo terpotong sebesar
                        <span class="font-bold text-gray-800">Rp
                            {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>.
                    </p>
                @else
                    {{-- CASH / TUNAI --}}
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-receipt text-4xl text-yellow-600"></i>
                    </div>

                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Pesanan Dibuat!</h1>
                    <p class="text-gray-500 mb-6">
                        Tunjukkan Invoice <span
                            class="font-mono font-bold bg-gray-100 px-1">{{ $transaction->invoice_code }}</span>
                        ke kasir dan bayar tunai sebesar
                        <span class="font-bold text-gray-800">Rp
                            {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>.
                    </p>
                @endif
            @endif

            {{-- TOMBOL NAVIGASI --}}
            <div class="space-y-3">
                @if (isset($type) && $type == 'credit')
                    <a href="{{ route('profile.index', ['tab' => 'credit']) }}#credit-{{ $transaction->id }}"
                        class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition text-center">

                        Lihat Jadwal Cicilan
                    </a>
                @else
                    {{-- Jika Reguler --}}
                    <a href="{{ route('transactions.print_invoice', $transaction->id) }}" target="_blank"
                        class="block w-full bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 rounded-xl transition">
                        <i class="fas fa-print mr-2"></i> Cetak Invoice
                    </a>
                @endif

                <a href="{{ route('home') }}"
                    class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition">
                    Kembali Belanja
                </a>
            </div>

        </div>
    </div>
@endsection
