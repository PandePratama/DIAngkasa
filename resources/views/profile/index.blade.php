@extends('layouts.app')

@section('content')
<main class="pt-[10px]">
    <div class="max-w-sm mx-auto my-10 bg-white rounded-xl shadow text-center p-6">

        {{-- QR CODE --}}
        <div class="flex justify-center mb-4">
            <div id="qrcode"
                class="w-[220px] h-[220px] border rounded-lg flex items-center justify-center">
            </div>
        </div>

        {{-- DOWNLOAD BUTTON --}}
        <button
            onclick="downloadQR('#ffffff')"
            class="w-full bg-teal-600 hover:bg-teal-700 text-white text-sm py-2 rounded-lg">
            Download QR Code
        </button>

        {{-- USER INFO --}}
        <h2 class="font-semibold text-lg mt-4">{{ $user->name }}</h2>
        <p class="text-sm text-gray-600">{{ $user->nip }}</p>
        <p class="text-sm text-gray-600">{{ $user->unit_kerja }}</p>

        <p class="font-semibold mt-3 text-teal-700">
            Rp {{ number_format($user->credit_limit ?? 0, 0, ',', '.') }}
        </p>
    </div>
</main>

{{-- QR LIB --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ trim($user->nip) }}",
        width: 220,
        height: 220,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H // 🔥 WAJIB BIAR MUDAH DISCAN
    });
});

/**
 * Download QR dengan background custom
 * @param {string} bgColor contoh: '#ffffff', '#fef3c7'
 */
function downloadQR(bgColor = '#ffffff') {
    const qrCanvas = document.querySelector('#qrcode canvas');

    // Buat canvas baru (agar background solid)
    const canvas = document.createElement('canvas');
    const size = 300; // bikin lebih besar biar tajam
    canvas.width = size;
    canvas.height = size;

    const ctx = canvas.getContext('2d');

    // Background
    ctx.fillStyle = bgColor;
    ctx.fillRect(0, 0, size, size);

    // Gambar QR ke canvas baru
    ctx.drawImage(qrCanvas, 40, 40, size - 80, size - 80);

    // Download
    const link = document.createElement('a');
    link.href = canvas.toDataURL('image/png');
    link.download = "QR-{{ $user->nip }}.png";
    link.click();
}
</script>
@endsection
