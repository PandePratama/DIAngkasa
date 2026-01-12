@extends('admin.layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="container-fluid pt-4">

    <h3 class="mb-4">Riwayat Transaksi</h3>

    {{-- FILTER --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    {{-- TOTAL --}}
    <div class="alert alert-info">
        <b>Total Transaksi:</b> Rp {{ number_format($total, 0, ',', '.') }}
    </div>

    {{-- TABLE --}}
    <div class="card shadow">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Id Transaksi</th>
                        <th>Waktu</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Admin</th>
                        <th>Nominal</th>
                        <th>Saldo Awal</th>
                        <th>Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $trx)
                    <tr>
                        <td>{{ $trx->id }}</td>
                        <td>{{ $trx->created_at->format('d-m-Y H:i') }}</td>
                        <td>{{ $trx->nip }}</td>
                        <td>{{ $trx->user->name }}</td>
                        <td>{{ $trx->admin_name }}</td>
                        <td>Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($trx->saldo_awal, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($trx->saldo_akhir, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection