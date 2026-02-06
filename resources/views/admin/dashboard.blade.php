@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Total Users -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Users
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $totalUsers }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Unit Kerja -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Unit Kerja
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $totalUnitKerja }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Produk DIAmart -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Produk DIAmart
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $totalDiamart }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-store fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Produk Raditya -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Produk Raditya
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $totalRaditya }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box-open fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Content Row -->
<div class="row">

    <!-- Welcome Card -->
    <div class="col-lg-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Selamat Datang</h6>
            </div>
            <div class="card-body">
                <p>
                    Selamat datang di <strong>Dashboard Admin</strong>.
                    Dari sini Anda dapat mengelola:
                </p>
                <ul>
                    <li>Produk Raditya</li>
                    <li>Produk Diamart</li>
                    <li>Kategori & Brand</li>
                    <li>Manajemen User</li>
                </ul>
            </div>
        </div>
    </div>

</div>

@endsection