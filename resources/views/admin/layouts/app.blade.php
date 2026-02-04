<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title', 'Admin Dashboard')</title>

    <link href="{{ asset('sbadmin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="{{ asset('sbadmin/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    @stack('scripts')

</head>

<body id="page-top">

    <div id="wrapper">

        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">DIRADITYA</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <li class="nav-item {{ request()->routeIs('admin.qr.scan.view') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.qr.scan.view') }}">
                    <i class="fas fa-fw fa-qrcode"></i>
                    <span>Scan QR</span></a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                Unit Bisnis
            </div>

            <li class="nav-item {{ request()->routeIs('raditya.*') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRaditya"
                    aria-expanded="true" aria-controls="collapseRaditya">
                    <i class="fas fa-fw fa-mobile-alt"></i>
                    <span>Raditya (Gadget)</span>
                </a>
                <div id="collapseRaditya" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Manajemen Gadget</h6>
                        <a class="collapse-item"
                            href="{{ route('categories.index', ['group' => 'raditya']) }}">Kategori</a>
                        <a class="collapse-item" href="{{ route('brands.index') }}">Brand</a>
                        <a class="collapse-item" href="{{ route('raditya.index') }}">Produk Gadget</a>
                    </div>
                </div>
            </li>

            <li class="nav-item {{ request()->routeIs('diamart.*') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDiamart"
                    aria-expanded="true" aria-controls="collapseDiamart">
                    <i class="fas fa-fw fa-store"></i>
                    <span>Diamart (Sembako)</span>
                </a>
                <div id="collapseDiamart" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Manajemen Sembako</h6>
                        <a class="collapse-item"
                            href="{{ route('categories.index', ['group' => 'diamart']) }}">Kategori</a>
                        <a class="collapse-item" href="{{ route('diamart.index') }}">Produk Sembako</a>
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                Manajemen Toko
            </div>

            <!-- Nav Item - Unit Kerja -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('unit-kerja.index') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Unit Kerja</span></a>
            </li>

            <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Users</span></a>
            </li>

            <li class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('transactions.index') }}">
                    <i class="fas fa-fw fa-history"></i> {{-- Saya ganti icon biar beda --}}
                    <span>Riwayat Transaksi</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('credits.*') ? 'active' : '' }}">
                {{-- Arahkan ke route baru: credits.index --}}
                <a class="nav-link" href="{{ route('credits.index') }}">
                    <i class="fas fa-fw fa-file-invoice-dollar"></i> {{-- Saya ganti icon biar beda --}}
                    <span>Tanggungan Tenor</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reports.monthly') }}">
                    <i class="fas fa-fw fa-chart-line"></i>
                    <span>Laporan Transaksi</span>
                </a>
            </li>
            
            {{-- <li class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.orders.index') }}">
                    <i class="fas fa-fw fa-shopping-cart"></i>
                    <span>Order Masuk</span></a>
            </li> --}}

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    {{ auth()->user()->name ?? 'User' }}
                                </span>
                                <img class="img-profile rounded-circle"
                                    src="{{ asset('sbadmin/img/undraw_profile.svg') }}">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal"
                                    data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Diraditya {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    Select "Logout" below if you are ready to end your current session.
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        Cancel
                    </button>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            Logout
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('sbadmin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('sbadmin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <script src="{{ asset('sbadmin/js/sb-admin-2.min.js') }}"></script>

    <script>
        // Cek apakah ada session 'success' dari Controller
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000 // Otomatis tutup setelah 2 detik
            });
        @endif

        // Cek apakah ada session 'error' atau 'failed' dari Controller
        @if (session('error') || session('failed'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') ?? session('failed') }}",
                confirmButtonText: 'OK'
            });
        @endif

        // (Opsional) Cek Error Validasi Form
        @if ($errors->any())
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                html: '<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            });
        @endif
    </script>

    <script>
        // Fungsi Konfirmasi Delete Global
        function confirmDelete(event) {
            event.preventDefault(); // Tahan dulu submit formnya

            // Ambil form terdekat dari tombol yang diklik
            const form = event.target.closest('form');

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', // Merah
                cancelButtonColor: '#3085d6', // Biru
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika user klik Ya, baru submit form secara manual
                    form.submit();
                }
            });
        }
    </script>

</body>

</html>
