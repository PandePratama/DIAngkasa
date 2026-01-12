@extends('layouts.app')

@section('content')
<main class="pt-[10px]">
    <div class="max-w-sm mx-auto my-10 bg-white rounded-xl shadow text-center p-6">

        {{-- QR Code --}}
        <div class="flex justify-center mb-4">
            <div
                id="qrcode"
                class="rounded-lg border p-2 w-[220px] h-[220px] flex items-center justify-center">
            </div>
        </div>

        {{-- User Info --}}
        <h2 class="font-semibold text-lg">{{ $user->name }}</h2>
        <p class="text-sm text-gray-600">{{ $user->nip }}</p>
        <p class="text-sm text-gray-600">{{ $user->unit_kerja }}</p>

        <p class="font-semibold mt-3 text-teal-700">
            Rp {{ number_format($user->credit_limit ?? 0, 0, ',', '.') }}
        </p>

        <button
            class="w-full bg-teal-700 hover:bg-teal-800 text-white py-2 rounded mt-4 transition">
            Ubah Password
        </button>
    </div>
</main>

{{-- QR Code JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $user->nip }}", // ISI QR → NIP USER
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    });
</script>
@endsection