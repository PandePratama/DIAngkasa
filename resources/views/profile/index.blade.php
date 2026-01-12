@extends('admin.layouts.app')

@section('title', 'Scan QR Karyawan')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Scan QR Karyawan</h1>

    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="card shadow">
                <div class="card-body text-center">

                    <div id="reader" style="width:100%;"></div>

                    <div id="result" class="mt-4"></div>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const html5QrCode = new Html5Qrcode("reader");

        function onScanSuccess(decodedText) {

            html5QrCode.stop();

            fetch("{{ route('admin.qr.validate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nip: decodedText
                    })
                })
                .then(res => res.json())
                .then(res => {

                    if (res.status) {
                        document.getElementById('result').innerHTML = `
                    <div class="alert alert-success text-left">
                        <strong>✅ QR VALID</strong><br><br>
                        <b>NIP:</b> ${res.data.nip}<br>
                        <b>Nama:</b> ${res.data.name}<br>
                        <b>Unit Kerja:</b> ${res.data.unit_kerja}<br>
                        <b>Limit Kredit:</b> Rp ${new Intl.NumberFormat('id-ID').format(res.data.limit)}
                    </div>
                `;
                    } else {
                        document.getElementById('result').innerHTML = `
                    <div class="alert alert-danger">
                        ❌ ${res.message}
                    </div>
                `;
                    }
                })
                .catch(() => {
                    alert('Terjadi kesalahan server');
                });
        }

        Html5Qrcode.getCameras().then(cameras => {

            if (!cameras.length) {
                alert("Kamera tidak ditemukan");
                return;
            }

            let cameraId =
                cameras.find(cam => cam.label.toLowerCase().includes('back'))?.id ||
                cameras[0].id;

            html5QrCode.start(
                cameraId, {
                    fps: 10,
                    qrbox: 250
                },
                onScanSuccess
            );

        });
    });
</script>
@endpush