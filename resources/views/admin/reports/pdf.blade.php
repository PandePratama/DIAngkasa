<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice Transaksi</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            width: 100%;
            margin-bottom: 15px;
        }

        .logo-wrapper {
            width: 40%;
            float: left;
        }

        .logo-wrapper img {
            height: 45px;
            margin-right: 10px;
        }

        .title-wrapper {
            width: 60%;
            float: right;
            text-align: right;
        }

        .title-wrapper h2 {
            margin: 0;
            font-size: 18px;
        }

        .clear {
            clear: both;
        }

        hr {
            border: 0;
            border-top: 2px solid #000;
            margin: 10px 0 20px;
        }

        .info table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info td {
            padding: 4px 0;
            vertical-align: top;
        }

        table.transaksi {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.transaksi th,
        table.transaksi td {
            border: 1px solid #000;
            padding: 6px;
        }

        table.transaksi th {
            background-color: #f2f2f2;
            text-align: center;
        }

        table.transaksi td {
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-box {
            margin-top: 15px;
            width: 100%;
        }

        .total-box table {
            width: 40%;
            float: right;
            border-collapse: collapse;
        }

        .total-box td {
            border: 1px solid #000;
            padding: 8px;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            font-size: 11px;
            text-align: center;
            color: #666;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="logo-wrapper">
            <img src="{{ public_path('images/logo1.png') }}" alt="Logo 1">
            <img src="{{ public_path('images/logo2.png') }}" alt="Logo 2">
        </div>

        <div class="title-wrapper">
            <h2>LAPORAN TRANSAKSI BULANAN</h2>
            <small>Bulan {{ $bulan }} / {{ $tahun }}</small>
        </div>

        <div class="clear"></div>
    </div>

    <hr>

    {{-- INFO USER --}}
    <div class="info">
        <table>
            <tr>
                <td width="20%">Nama</td>
                <td width="2%">:</td>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>:</td>
                <td>{{ $user->nip }}</td>
            </tr>
            <tr>
                <td>Unit Kerja</td>
                <td>:</td>
                <td>{{ $user->unitKerja->unit_name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- TABEL TRANSAKSI --}}
    <table class="transaksi">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Tanggal</th>
                <th width="25%">Invoice</th>
                <th width="20%">Status</th>
                <th width="30%">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $trx)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ $trx->created_at->format('d-m-Y') }}</td>
                <td>{{ $trx->invoice_code ?? '-' }}</td>
                <td class="text-center">{{ ucfirst($trx->status) }}</td>
                <td class="text-right">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada transaksi</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TOTAL --}}
    <div class="total-box">
        <table>
            <tr>
                <td>Total Nominal</td>
                <td class="text-right">
                    Rp {{ number_format($totalNominal, 0, ',', '.') }}
                </td>
            </tr>
        </table>
        <div class="clear"></div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Dicetak pada {{ now()->format('d-m-Y H:i') }}
    </div>

</body>

</html>