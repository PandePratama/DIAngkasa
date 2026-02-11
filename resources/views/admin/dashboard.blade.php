@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

{{-- Tambahan CSS Custom untuk Desain Modern --}}
@push('styles')
    <style>
        /* Card Modern Style */
        .card-modern {
            border: none;
            border-radius: 15px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow: hidden;
            position: relative;
            background: #fff;
        }

        /* Efek melayang saat di-hover */
        .card-modern:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1), 0 10px 10px rgba(0, 0, 0, 0.05) !important;
        }

        /* Garis atas gradien */
        .card-modern::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
        }

        .top-primary::before {
            background: linear-gradient(to right, #4e73df, #224abe);
        }

        .top-info::before {
            background: linear-gradient(to right, #36b9cc, #258391);
        }

        .top-success::before {
            background: linear-gradient(to right, #1cc88a, #13855c);
        }

        .top-warning::before {
            background: linear-gradient(to right, #f6c23e, #dda20a);
        }

        .top-danger::before {
            background: linear-gradient(to right, #e74a3b, #be2617);
        }

        .top-dark::before {
            background: linear-gradient(to right, #5a5c69, #373840);
        }

        /* Ikon Watermark Latar Belakang */
        .icon-bg {
            position: absolute;
            right: -15px;
            bottom: -20px;
            font-size: 6rem;
            opacity: 0.08;
            transform: rotate(-15deg);
            transition: all 0.4s ease;
        }

        /* Ikon bergerak sedikit saat di-hover */
        .card-modern:hover .icon-bg {
            transform: rotate(0deg) scale(1.1);
            opacity: 0.15;
        }

        /* Styling Teks Angka agar lebih besar */
        .number-stat {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -1px;
        }
    </style>
@endpush

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Dashboard Overview</h1>
        <span class="text-muted small"><i class="fas fa-calendar-alt"></i>
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
    </div>

    <div class="row">

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card card-modern shadow-sm h-100 py-3 top-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center position-relative" style="z-index: 2;">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                            <div class="number-stat text-gray-800">{{ number_format($totalUsers) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                    <i class="fas fa-users icon-bg text-primary"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card card-modern shadow-sm h-100 py-3 top-info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center position-relative" style="z-index: 2;">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Unit Kerja</div>
                            <div class="number-stat text-gray-800">{{ number_format($totalUnitKerja) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-info"></i>
                        </div>
                    </div>
                    <i class="fas fa-building icon-bg text-info"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card card-modern shadow-sm h-100 py-3 top-danger">
                <div class="card-body">
                    <div class="row no-gutters align-items-center position-relative" style="z-index: 2;">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Kategori Produk</div>
                            <div class="number-stat text-gray-800">{{ number_format($totalCategories) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-danger"></i>
                        </div>
                    </div>
                    <i class="fas fa-tags icon-bg text-danger"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card card-modern shadow-sm h-100 py-3 top-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center position-relative" style="z-index: 2;">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Produk DIAmart</div>
                            <div class="number-stat text-gray-800">{{ number_format($totalDiamart) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store fa-2x text-success"></i>
                        </div>
                    </div>
                    <i class="fas fa-store icon-bg text-success"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card card-modern shadow-sm h-100 py-3 top-warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center position-relative" style="z-index: 2;">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Produk Raditya</div>
                            <div class="number-stat text-gray-800">{{ number_format($totalRaditya) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box-open fa-2x text-warning"></i>
                        </div>
                    </div>
                    <i class="fas fa-box-open icon-bg text-warning"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card card-modern shadow-sm h-100 py-3 top-dark">
                <div class="card-body">
                    <div class="row no-gutters align-items-center position-relative" style="z-index: 2;">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Total Brand</div>
                            <div class="number-stat text-gray-800">{{ number_format($totalBrands) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-dark"></i>
                        </div>
                    </div>
                    <i class="fas fa-handshake icon-bg text-dark"></i>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card card-modern shadow-sm">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="font-weight-bold text-primary mb-3">Selamat Datang di Diraditya Admin! 👋</h4>
                            <p class="text-muted mb-4">
                                Sistem Informasi Pengadaan dan Retribusi Aset Kantor (Diraditya) kini dalam kendali Anda.
                                Pantau metrik terbaru, kelola data master, dan pastikan stok selalu terupdate.
                            </p>
                            <a href="{{ route('diamart.index') }}" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                                <i class="fas fa-arrow-right mr-2"></i> Kelola Produk
                            </a>
                        </div>
                        <div class="col-md-4 d-none d-md-block text-center">
                            {{-- Ilustrasi opsional, menggunakan icon besar --}}
                            <i class="fas fa-chart-line text-primary" style="font-size: 8rem; opacity: 0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
