<!DOCTYPE html>
<html>

<head>
    <title>Invoice #{{ $transaction->invoice_code }}</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
            font-size: 10pt;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            color: #0f766e;
            /* Teal color */
        }

        .header p {
            margin: 2px 0;
            font-size: 9pt;
            color: #666;
        }

        /* Info Section */
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 3px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            color: #555;
            width: 100px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background-color: #f3f4f6;
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            font-size: 9pt;
        }

        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .text-right {
            text-align: right;
        }

        .total-row td {
            font-weight: bold;
            border-top: 2px solid #333;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 8pt;
            color: #888;
            font-style: italic;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            display: inline-block;
            color: white;
            background-color: #555;
        }
    </style>
</head>

<body>

    <div class="container">
        {{-- HEADER --}}
        <div class="header">
            <h1>DIRADITYA</h1>
            <p>Jalan Raya Dalung No. 123, Kuta Utara, Bali</p>
            <p>Telp: (0361) 123-4567 | Email: admin@diraditya.com</p>
        </div>

        {{-- INFO PELANGGAN & INVOICE --}}
        <table class="info-table">
            <tr>
                <td width="55%">
                    <strong>DITAGIHKAN KEPADA:</strong><br>
                    {{ $transaction->user->name ?? 'Guest' }}<br>
                    NIP: {{ $transaction->user->nip ?? '-' }}<br>
                    {{ $transaction->user->unitKerja->unit_name ?? 'Unit Tidak Diketahui' }}
                </td>
                <td width="45%" class="text-right">
                    <strong>INVOICE INFO:</strong><br>
                    No: <b>{{ $transaction->invoice_code ?? 'INV-' . $transaction->id }}</b><br>
                    Tgl: {{ $transaction->created_at->format('d M Y, H:i') }}<br>
                    Metode: {{ strtoupper($transaction->purchaseType->code ?? $transaction->payment_method) }}
                </td>
            </tr>
        </table>

        {{-- TABEL BARANG --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Deskripsi Item</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->items ?? [] as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-right">{{ $item->qty }}</td>
                        <td class="text-right">Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL TAGIHAN</td>
                    <td class="text-right">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right" style="padding-top:10px;">Saldo Terpotong</td>
                    <td class="text-right" style="padding-top:10px; color: red;">
                        @if (($transaction->purchaseType->code ?? '') == 'balance')
                            - Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}
                        @else
                            0
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- STATUS --}}
        <div style="text-align: right; margin-top: 10px;">
            Status Pembayaran:
            <span class="badge"
                style="background-color: {{ $transaction->status == 'paid' || $transaction->status == 'completed' ? '#1cc88a' : '#f6c23e' }}">
                {{ strtoupper($transaction->status) }}
            </span>
        </div>

        {{-- FOOTER --}}
        <div class="footer">
            Terima kasih telah berbelanja di Diraditya.<br>
            Simpan struk ini sebagai bukti pembayaran yang sah.
        </div>
    </div>

</body>

</html>
