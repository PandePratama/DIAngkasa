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

                    <div class="mt-3 text-muted">
                        Arahkan QR ke kamera
                    </div>

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

        function onScanSuccess(decodedText, decodedResult) {

            // Stop scan setelah berhasil
            html5QrCode.stop();

            // POPUP VALID
            alert(
                "✅ QR VALID\n\n" +
                "Isi QR:\n" + decodedText
            );
        }

        function onScanFailure(error) {
            // Abaikan error scan agar tidak spam console
        }

        Html5Qrcode.getCameras().then(cameras => {

            if (!cameras || cameras.length === 0) {
                alert("Kamera tidak ditemukan");
                return;
            }

            // Prioritas kamera belakang
            let cameraId =
                cameras.find(cam => cam.label.toLowerCase().includes('back'))?.id ||
                cameras[0].id;

            html5QrCode.start(
                cameraId, {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    }
                },
                onScanSuccess,
                onScanFailure
            );

        }).catch(err => {
            console.error(err);
            alert("Gagal mengakses kamera");
        });

    });
</script>
@endpush