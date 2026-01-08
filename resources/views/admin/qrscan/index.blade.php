@extends('admin.layouts.app')

@section('title', 'Scan QR Karyawan')

@section('content')

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Scan QR Karyawan</h1>

    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="card shadow">
                <div class="card-body text-center">

                    <video id="preview" class="w-100 rounded"></video>

                    <div id="result" class="mt-3 text-left"></div>

                </div>
            </div>

        </div>
    </div>

</div>

{{-- INSTASCAN --}}
<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

<script>
    let scanner = new Instascan.Scanner({
        video: document.getElementById('preview'),
        mirror: false
    });

    scanner.addListener('scan', function(nip) {

        fetch("{{ route('admin.qr.scan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    nip: nip
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    document.getElementById('result').innerHTML = `
                    <strong>NIP:</strong> ${res.data.nip}<br>
                    <strong>Nama:</strong> ${res.data.nama}<br>
                    <strong>Unit Kerja:</strong> ${res.data.unit_kerja}
                `;
                } else {
                    document.getElementById('result').innerHTML =
                        `<span class="text-danger">${res.message}</span>`;
                }
            });
    });

    Instascan.Camera.getCameras().then(function(cameras) {
        if (cameras.length > 0) {
            scanner.start(cameras[0]);
        } else {
            alert('Kamera tidak ditemukan');
        }
    });
</script>

@endsection