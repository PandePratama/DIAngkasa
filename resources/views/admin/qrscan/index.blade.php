@extends('admin.layouts.app')

@section('title', 'Scan QR Karyawan')

@section('content')
<div class="container-fluid pt-4">

    {{-- TITLE --}}
    <h2 class="text-center mb-4 text-xl font-semibold">
        Scan QR Employee
    </h2>

    {{-- CAMERA --}}
    <div class="d-flex justify-content-center">
        <div class="camera-wrapper">
            <div id="reader"></div>
        </div>
    </div>

</div>

{{-- STYLE --}}
<style>
    .camera-wrapper {
        width: 340px;
        height: 260px;
        border-radius: 16px;
        overflow: hidden;
        border: 3px solid #e5e7eb;
        background: #000;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .15);
    }

    #reader,
    #reader video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover;
    }
</style>

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const html5QrCode = new Html5Qrcode("reader");

    function startScanner() {
        html5QrCode.start({
                facingMode: "environment"
            }, {
                fps: 10,
                qrbox: {
                    width: 180,
                    height: 180
                },
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                }
            },
            onScanSuccess
        );
    }

    function onScanSuccess(decodedText) {
        html5QrCode.stop();

        fetch("{{ route('qr.validate') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    qr: decodedText.trim()
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'valid') {
                    Swal.fire('GAGAL', data.message, 'error');
                    return startScanner();
                }

                // 🔥 FORM INPUT BELANJA
                Swal.fire({
                    title: 'Transaksi Belanja',
                    html: `
                    <b>${data.name}</b><br>
                    ${data.nip} - ${data.unit}<br><br>
                    <b>Saldo:</b> Rp ${data.credit.toLocaleString()}<br><br>
                    <input id="amount" class="swal2-input"
                        placeholder="Nominal belanja"
                        type="number" min="1">
                `,
                    showCancelButton: true,
                    confirmButtonText: 'Proses',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        const amount = document.getElementById('amount').value;
                        if (!amount || amount <= 0) {
                            Swal.showValidationMessage('Nominal tidak valid');
                        }
                        return amount;
                    }
                }).then(result => {
                    if (!result.isConfirmed) {
                        startScanner();
                        return;
                    }

                    // 🔥 KIRIM TRANSAKSI
                    fetch("{{ route('qr.transaction') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                nip: data.nip,
                                amount: result.value
                            })
                        })
                        .then(res => res.json())
                        .then(resp => {
                            if (resp.status === 'success') {
                                Swal.fire(
                                    'BERHASIL',
                                    `Sisa saldo: Rp ${resp.sisa_credit.toLocaleString()}`,
                                    'success'
                                );
                            } else {
                                Swal.fire('GAGAL', resp.message, 'error');
                            }
                            setTimeout(startScanner, 2500);
                        });
                });
            });
    }

    startScanner();
</script>

@endsection