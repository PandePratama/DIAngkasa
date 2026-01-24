@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full text-center">

            {{-- Ikon Centang Animasi --}}
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-check text-4xl text-green-600"></i>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Berhasil!</h1>
            <p class="text-gray-500 mb-6">Terima kasih. Limit Anda telah diperbarui dan pesanan sedang diproses oleh admin.
            </p>

            <div class="space-y-3">
                <a href="{{ route('history.index') }}"
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
