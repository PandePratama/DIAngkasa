@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Data Pesanan</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Metode</th>
                <th>Total</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->user->name }}</td>
                <td>{{ strtoupper($order->payment_method) }}</td>
                <td>Rp {{ number_format($order->total_belanja ,0,',','.') }}</td>
                <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.orders.detail', $order->id) }}"
                        class="btn btn-sm btn-success">
                        Detail Order
                    </a>
                </td>

            </tr>
            @endforeach
        </tbody>

    </table>

    {{ $orders->links() }}
</div>
@endsection