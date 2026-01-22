<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - Diamart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .main-image-container {
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
        }

        .main-image-container img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }

        .thumbnail-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
            border-radius: 5px;
        }

        .thumbnail-img:hover,
        .thumbnail-img.active {
            border-color: #198754;
        }

        .related-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('front.diamart.index') }}">
                <i class="fas fa-store me-2"></i>DIAMART
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('front.diamart.index') }}">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Toko
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('front.diamart.index') }}"
                        class="text-success text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="#"
                        class="text-success text-decoration-none">{{ $product->category->category_name ?? 'Produk' }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($product->name, 20) }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3">
                    <div class="main-image-container mb-3">
                        @if ($product->primaryImage)
                            <img id="mainImage" src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                alt="{{ $product->name }}">
                        @else
                            <div class="text-muted"><i class="fas fa-image fa-5x"></i></div>
                        @endif
                    </div>

                    @if ($product->images->count() > 1)
                        <div class="d-flex gap-2 overflow-auto pb-2">
                            @foreach ($product->images as $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                    class="thumbnail-img {{ $loop->first ? 'active' : '' }}"
                                    onclick="changeImage(this, '{{ asset('storage/' . $image->image_path) }}')">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <small
                            class="text-muted text-uppercase fw-bold">{{ $product->category->category_name ?? 'Umum' }}</small>
                        <h2 class="fw-bold mt-2">{{ $product->name }}</h2>

                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-warning text-dark me-2">
                                <i class="fas fa-star"></i> 4.8
                            </span>
                            <span class="text-muted small">| Terjual 100+</span>
                        </div>

                        <h3 class="text-success fw-bold mb-4">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </h3>

                        <hr>

                        <div class="mb-4">
                            <h6 class="fw-bold">Deskripsi Produk:</h6>
                            <p class="text-muted" style="line-height: 1.6;">
                                {!! nl2br(e($product->desc ?? 'Tidak ada deskripsi untuk produk ini.')) !!}
                            </p>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold">Brand:</span>
                                <span>{{ $product->brand->brand_name ?? 'No Brand' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Stok:</span>
                                <span class="{{ $product->stock > 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                    {{ $product->stock > 0 ? 'Tersedia (' . $product->stock . ')' : 'Habis' }}
                                </span>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-success btn-lg" {{ $product->stock < 1 ? 'disabled' : '' }}>
                                <i class="fas fa-shopping-cart me-2"></i>
                                {{ $product->stock < 1 ? 'Stok Habis' : 'Masukkan Keranjang' }}
                            </button>
                            <a href="https://wa.me/6281234567890?text=Halo admin, saya mau beli {{ $product->name }}"
                                target="_blank" class="btn btn-outline-success">
                                <i class="fab fa-whatsapp me-2"></i> Beli via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($relatedProducts->count() > 0)
            <div class="mt-5">
                <h4 class="fw-bold mb-4">Produk Sejenis</h4>
                <div class="row row-cols-2 row-cols-md-4 g-4">
                    @foreach ($relatedProducts as $related)
                        <div class="col">
                            <div class="card h-100 related-card border-0 shadow-sm">
                                <div class="position-relative"
                                    style="height: 180px; overflow: hidden; background: #f8f9fa;">
                                    @if ($related->primaryImage)
                                        <img src="{{ asset('storage/' . $related->primaryImage->image_path) }}"
                                            class="w-100 h-100" style="object-fit: contain; padding: 10px;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                            <i class="fas fa-image fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title fw-bold text-truncate">{{ $related->name }}</h6>
                                    <p class="text-success fw-bold mb-0">Rp
                                        {{ number_format($related->price, 0, ',', '.') }}</p>
                                    <a href="{{ route('front.diamart.show', $related->id) }}"
                                        class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <footer class="bg-white py-4 mt-5 border-top">
        <div class="container text-center text-muted">
            <small>&copy; {{ date('Y') }} Diamart Indonesia</small>
        </div>
    </footer>

    <script>
        // Script sederhana untuk ganti gambar utama saat thumbnail diklik
        function changeImage(element, src) {
            document.getElementById('mainImage').src = src;

            // Hapus class active dari semua thumbnail
            document.querySelectorAll('.thumbnail-img').forEach(el => el.classList.remove('active'));

            // Tambah class active ke thumbnail yang diklik
            element.classList.add('active');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
