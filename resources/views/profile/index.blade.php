@extends('layouts.app')

@section('content')

<div class="max-w-sm mx-auto my-10 bg-white rounded-xl shadow text-center p-6">

    <div id="qrcode"
        class="mx-auto mb-4 rounded-lg border p-2 w-[220px] h-[220px] flex items-center justify-center">
    </div>

    <h2 class="font-semibold text-lg">{{ $user->name }}</h2>
    <p class="text-sm text-gray-600">{{ $user->nip }}</p>
    <p class="text-sm text-gray-600">{{ $user->unit_kerja }}</p>

    <p class="font-semibold mt-2">
        Rp {{ number_format($user->saldo ?? 0, 0, ',', '.') }}
    </p>

    <button class="w-full bg-teal-700 text-white py-2 rounded mt-3">
        Ubah Password
    </button>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        let nip = "{{ $user->nip }}";

        if (nip) {
            new QRCode(document.getElementById("qrcode"), {
                text: nip,
                width: 200,
                height: 200,
                correctLevel: QRCode.CorrectLevel.H
            });
        } else {
            document.getElementById("qrcode").innerHTML =
                '<p class="text-sm text-gray-500">NIP belum tersedia</p>';
        }

    });
</script>


@endsection