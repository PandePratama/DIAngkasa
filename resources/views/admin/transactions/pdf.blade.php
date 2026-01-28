<!DOCTYPE html>
<html>

<head>
    <title>Laporan Riwayat Transaksi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        p {
            text-align: center;
            margin-top: 0;
            font-size: 9pt;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .total-row td {
            border-top: 2px solid #000;
            font-weight: bold;
            background-color: #e9ecef;
        }

        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8pt;
            color: #000;
            border: 1px solid #ccc;
        }

        ul {
            margin: 0;
            padding-left: 15px;
        }
    </style>
</head>

<body>

    <h2>Laporan Riwayat Transaksi</h2>
    <p>
        Periode:
        {{ request('from') ? \Carbon\Carbon::parse(request('from'))->format('d-m-Y') : 'Awal' }}
        s/d
        {{ request('to') ? \Carbon\Carbon::parse(request('to'))->format('d-m-Y') : 'Sekarang' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Waktu</th>
                <th>NIP</th>
                <th>Nama Customer</th>
                <th>Metode Bayar</th>
                <th>Detail Order Barang</th>
                <th class="text-right">Total Belanja</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach ($transactions as $trx)
                @php $grandTotal += $trx->grand_total; @endphp
                <tr>
                    <td>{{ $trx->id }}</td>
                    <td>{{ $trx->created_at->format('d-m-Y H:i') }}</td>
                    <td>{{ $trx->user->nip ?? '-' }}</td>
                    <td>{{ $trx->user->name ?? 'User Terhapus' }}</td>
                    <td>
                        @php
                            $code = $trx->purchaseType ? $trx->purchaseType->code : $trx->payment_method ?? null;
                            $label = match ($code) {
                                'balance' => 'Potong Saldo',
                                'cash' => 'Cash / Tunai',
                                default => '-',
                            };
                        @endphp
                        {{ $label }}
                    </td>
                    <td>
                        <ul>
                            @foreach ($trx->items as $item)
                                <li>{{ $item->product_name }} ({{ $item->qty }})</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-right">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL KESELURUHAN</td>
                <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
