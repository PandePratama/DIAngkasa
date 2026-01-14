@extends('admin.layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">Detail Order #{{ $order->id }}</h2>

    {{-- INFORMASI ORDER --}}
    <div class="card mb-4">
        <div class="card-header">
            Informasi Pesanan
        </div>
        <div class="card-body">
            <table class="table table-borderless mb-0">
                <tr>
                    <th width="200">User</th>
                    <td>{{ $order->user->name }}</td>
                </tr>
                <tr>
                    <th>Metode Pembayaran</th>
                    <td>{{ strtoupper($order->payment_method) }}</td>
                </tr>
                <tr>
                    <th>Metode Pengiriman</th>
                    <td>{{ $order->shipping_method }}</td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ITEM ORDER --}}
    <div class="card">
        <div class="card-header">
            Daftar Produk
        </div>
        <div class="card-body">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp

                    @foreach($order->items as $item)
                        @php
                            $subtotal = $item->qty * ($item->product->price ?? 0);
                            $grandTotal += $subtotal;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product->name ?? '-' }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>Rp {{ number_format($item->product->price ?? 0,0,',','.') }}</td>
                            <td>Rp {{ number_format($subtotal,0,',','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total</th>
                        <th>Rp {{ number_format($grandTotal,0,',','.') }}</th>
                    </tr>
                </tfoot>
            </table>

        </div>
    </div>

    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mt-3">
        ← Kembali
    </a>

</div>
@endsection
