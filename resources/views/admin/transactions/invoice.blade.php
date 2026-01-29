<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $transaction->invoice_code }}</title>
    <style>
        /* Setup Halaman */
        @page {
            margin: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #334155;
            margin: 0;
            padding: 40px;
            line-height: 1.5;
            background-color: #ffffff;
        }

        /* Tipografi & Warna */
        .text-teal {
            color: #0d9488;
        }

        .text-gray-500 {
            color: #64748b;
        }

        .text-sm {
            font-size: 9pt;
        }

        .text-xs {
            font-size: 8pt;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        /* Header Layout */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .brand-name {
            font-size: 24pt;
            letter-spacing: -1px;
            margin: 0;
            color: #0d9488;
        }

        /* Info Section */
        .billing-table {
            width: 100%;
            margin-bottom: 40px;
        }

        .billing-table td {
            vertical-align: top;
            width: 50%;
        }

        .section-title {
            text-transform: uppercase;
            font-size: 8pt;
            font-weight: bold;
            color: #94a3b8;
            letter-spacing: 1px;
            margin-bottom: 8px;
            display: block;
        }

        /* Table Design */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #f8fafc;
            color: #475569;
            text-transform: uppercase;
            font-size: 8pt;
            padding: 12px 10px;
            border-bottom: 2px solid #cbd5e1;
            text-align: left;
        }

        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Summary Section */
        .summary-wrapper {
            width: 100%;
        }

        .summary-table {
            width: 40%;
            margin-left: auto;
        }

        .summary-table td {
            padding: 5px 0;
        }

        .grand-total-box {
            background-color: #0d9488;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }

        /* Status Badge */
        .badge {
            padding: 5px 12px;
            border-radius: 99px;
            font-size: 8pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        .bg-paid {
            background-color: #dcfce7;
            color: #166534;
        }

        .bg-pending {
            background-color: #fef9c3;
            color: #854d0e;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 40px;
            left: 40px;
            right: 40px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    {{-- 1. HEADER & BRANDING --}}
    <table class="header-table">
        <tr>
            <td>
                <h1 class="brand-name">DIRADITYA</h1>
                <p class="text-gray-500 text-sm" style="margin-top: 5px;">
                    Jalan Raya Dalung No. 123, Kuta Utara, Bali<br>
                    (0361) 123-4567 | admin@diraditya.com
                </p>
            </td>
            <td class="text-right">
                <div style="font-size: 20pt; font-weight: bold; margin-bottom: 5px;">INVOICE</div>
                <div class="text-gray-500">#{{ $transaction->invoice_code ?? 'INV-' . $transaction->id }}</div>
                <div style="margin-top: 10px;">
                    <span
                        class="badge {{ $transaction->status == 'paid' || $transaction->status == 'completed' ? 'bg-paid' : 'bg-pending' }}">
                        {{ $transaction->status }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    {{-- 2. BILLING & DATE INFO --}}
    <table class="billing-table">
        <tr>
            <td>
                <span class="section-title">Ditagihkan Kepada</span>
                <div class="font-bold" style="font-size: 11pt; color: #1e293b;">
                    {{ $transaction->user->name ?? 'Guest' }}
                </div>
                <div class="text-gray-500 text-sm">
                    NIP: {{ $transaction->user->nip ?? '-' }}<br>
                    {{ $transaction->user->unitKerja->unit_name ?? 'Unit Tidak Diketahui' }}
                </div>
            </td>
            <td class="text-right">
                <span class="section-title">Detail Transaksi</span>
                <div class="text-sm">
                    <span class="text-gray-500">Tanggal Terbit:</span>
                    <span class="font-bold">{{ $transaction->created_at->format('d M Y') }}</span><br>
                    <span class="text-gray-500">Waktu:</span>
                    <span class="font-bold">{{ $transaction->created_at->format('H:i') }} WITA</span><br>
                    <span class="text-gray-500">Metode:</span>
                    <span
                        class="font-bold">{{ strtoupper($transaction->purchaseType->code ?? $transaction->payment_method) }}</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- 3. ITEMS TABLE --}}
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="50%">Deskripsi Item</th>
                <th width="15%" class="text-right">Harga</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="20%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction->items ?? [] as $item)
                <tr>
                    <td class="text-gray-500 text-sm">{{ $loop->iteration }}</td>
                    <td>
                        <div class="font-bold" style="color: #1e293b;">{{ $item->product_name }}</div>
                    </td>
                    <td class="text-right text-sm">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-center text-sm">{{ $item->qty }}</td>
                    <td class="text-right font-bold" style="color: #1e293b;">
                        Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 4. SUMMARY SECTION --}}
    <div class="summary-wrapper">
        <table class="summary-table">
            <tr>
                <td class="text-gray-500 text-sm">Subtotal</td>
                <td class="text-right font-bold">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-gray-500 text-sm">Potongan Saldo</td>
                <td class="text-right font-bold" style="color: #ef4444;">
                    @if (($transaction->purchaseType->code ?? '') == 'balance')
                        - Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}
                    @else
                        Rp 0
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="grand-total-box">
                        <table width="100%">
                            <tr>
                                <td style="font-size: 9pt; text-transform: uppercase;">Total Tagihan</td>
                                <td class="text-right" style="font-size: 14pt; font-weight: bold;">
                                    Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- 5. FOOTER --}}
    <div class="footer text-gray-500">
        <p class="text-sm font-bold" style="color: #0d9488; margin-bottom: 5px;">Terima kasih atas kunjungan Anda!</p>
        <p class="text-xs">
            Struk ini dihasilkan secara otomatis oleh sistem Diraditya dan merupakan bukti pembayaran yang sah.<br>
            Harap simpan salinan ini untuk keperluan klaim atau referensi di masa mendatang.
        </p>
    </div>
</body>

</html>
