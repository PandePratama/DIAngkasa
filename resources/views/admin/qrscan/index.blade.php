@extends('admin.layouts.app')

@section('title', 'Scan QR Karyawan')

@section('content')
<div class="container-fluid pt-4">

    {{-- TITLE --}}
    <h2 class="text-center mb-4 font-weight-bold text-dark">
        Scan QR Employee
    </h2>

    {{-- CAMERA WRAPPER --}}
    <div class="row justify-content-center">
        <div class="col-md-6 d-flex justify-content-center">
            <div class="camera-wrapper">
                <div id="reader"></div>
            </div>
        </div>
    </div>

    <div class="text-center mt-3">
        <small class="text-muted" id="status-text">Pastikan izin kamera aktif...</small>
    </div>

</div>

{{-- STYLE --}}
<style>
    .camera-wrapper {
        width: 100%;
        max-width: 400px;
        border-radius: 16px;
        overflow: hidden;
        border: 4px solid #4e73df;
        /* Warna Primary Bootstrap */
        background: #000;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .25);
        position: relative;
    }

    #reader {
        width: 100%;
        min-height: 300px;
    }

    /* Hilangkan tombol bawaan library jika mengganggu */
    #reader__dashboard_section_csr span {
        display: none !important;
    }
</style>

{{-- SCRIPTS --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Inisialisasi Scanner
    const html5QrCode = new Html5Qrcode("reader");
    const statusText = document.getElementById('status-text');

    function startScanner() {
        statusText.innerText = "Memulai kamera...";

        html5QrCode.start({
                facingMode: "environment"
            }, // Pakai kamera belakang
            {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                },
                aspectRatio: 1.0
            },
            onScanSuccess, // Callback jika sukses scan
            onScanFailure // Callback jika frame gagal (opsional, biasanya dikosongkan agar tidak spam console)
        ).catch(err => {
            // 🔥 TANGKAP ERROR KAMERA DISINI
            console.error("Error start camera: ", err);
            statusText.innerText = "Gagal: " + err;
            statusText.classList.add('text-danger');

            Swal.fire({
                icon: 'error',
                title: 'Kamera Gagal Dibuka',
                text: 'Pastikan Anda menggunakan HTTPS atau localhost, dan izinkan akses kamera di browser.',
                footer: `<small>${err}</small>`
            });
        });
    }

    function onScanFailure(error) {
        // Biarkan kosong agar console tidak penuh warning saat mencari QR
    }

    function onScanSuccess(decodedText, decodedResult) {
        // 1. Matikan scanner & mainkan suara beep (opsional)
        html5QrCode.stop().then(() => {
            console.log("QR Stopped. Data:", decodedText);
            statusText.innerText = "Memproses data...";

            // 2. Kirim ke Server
            validateQr(decodedText);
        }).catch(err => {
            console.error("Failed to stop", err);
        });
    }

    function validateQr(qrCode) {
        fetch("{{ route('admin.qr.validate') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    qr: qrCode.trim()
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'valid') {
                    Swal.fire('Data Tidak Ditemukan', data.message, 'error').then(() => startScanner());
                    return;
                }

                // 3. Tampilkan Form Input Nominal
                showTransactionForm(data);
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error Sistem', 'Gagal menghubungi server', 'error').then(() => startScanner());
            });
    }

    function showTransactionForm(data) {
        const saldo = Number(data.saldo || 0);

        Swal.fire({
            title: 'Transaksi Belanja',
            html: `
        <div class="text-left" style="font-size: 0.9rem;">
            <strong>Nama:</strong> ${data.name}<br>
            <strong>NIP:</strong> ${data.nip}<br>
            <strong>Unit:</strong> ${data.unit}<br>
            <hr>
            <strong>Sisa Saldo:</strong>
            <span class="text-success font-weight-bold">
                Rp ${saldo.toLocaleString('id-ID')}
            </span>
        </div>
        <div class="mt-3">
            <label>Masukkan Nominal Belanja:</label>
            <input id="amount" class="swal2-input" type="number"
                placeholder="Contoh: 50000" min="1" autofocus>
        </div>
    `,
            showCancelButton: true,
            confirmButtonText: 'Bayar',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#4e73df',
            preConfirm: () => {
                const amount = Number(document.getElementById('amount').value);

                if (!amount || amount <= 0) {
                    Swal.showValidationMessage('Nominal harus diisi!');
                    return false;
                }

                if (amount > saldo) {
                    Swal.showValidationMessage('Saldo tidak mencukupi!');
                    return false;
                }

                return amount;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                processTransaction(data.nip, result.value);
            } else {
                startScanner();
            }
        });
    }


    function processTransaction(nip, amount) {
        Swal.fire({
            title: 'Memproses...',
            didOpen: () => Swal.showLoading()
        });

        fetch("{{ route('admin.qr.transaction') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    nip: nip,
                    amount: amount
                })
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: `Sisa saldo: Rp ${Number(resp.sisa_saldo).toLocaleString('id-ID')}`,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4e73df'
                    }).then(() => startScanner());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: resp.message,
                        confirmButtonText: 'OK'
                    }).then(() => startScanner());
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan koneksi',
                    confirmButtonText: 'OK'
                }).then(() => startScanner());
            });
    }

    // Jalankan Scanner saat halaman dimuat
    document.addEventListener('DOMContentLoaded', startScanner);
</script>
@endsection