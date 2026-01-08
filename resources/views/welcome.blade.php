<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIAngkasa Marketplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>


</head>

<body class="bg-gray-100">

    <x-navbar />

    {{-- MAIN CONTENT --}}
    <main class="pt-[80px]">
        {{-- Hero Banner --}}
        <section class="bg-gradient-to-r from-blue-200 to-white pt-4 pb-12">
            <div class="container mx-auto flex justify-center px-6">
                <img
                    src="{{ asset('sbadmin/img/Hero_banner.png') }}"
                    alt="Banner Gadget"
                    class="rounded-lg shadow-lg max-w-full h-auto
                        max-h-[260px] sm:max-h-[320px] md:max-h-none
                        object-contain">
            </div>
        </section>

        {{-- Produk Best Seller Gadget --}}
        <section class="container mx-auto px-6 py-12">
            <h1 class="text-xl font-semibold mb-6 text-center">PRODUK BEST SELLER</h1>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 justify-center mx-auto">
                @forelse ($products ?? [] as $product)
                <div class="bg-white shadow rounded-lg p-4 text-center">
                    <img
                        src="{{ $product->primaryImage
                                ? asset('storage/' . $product->primaryImage->image_path)
                                : asset('images/placeholder.png') }}"
                        class="mx-auto mb-2 h-24 object-contain">
                    <p class="font-semibold">{{ $product->name }}</p>
                    <p class="text-gray-700">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>
                </div>
                @empty
                <p class="col-span-full text-center text-gray-500">
                    Produk belum tersedia
                </p>
                @endforelse
            </div>
        </section>

        {{-- Banner Promo --}}
        <section class="bg-gray-200 py-12">
            <div class="container mx-auto px-6">
                <img src="{{ asset('sbadmin/img/december.png') }}"
                    class="rounded-lg shadow-lg max-w-full h-auto">
            </div>
        </section>

        {{-- Produk Best Seller Minimarket --}}
        <section class="container mx-auto px-6 py-12">
            <h2 class="text-xl font-semibold mb-6 text-center">PRODUK BEST SELLER</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                @for ($i = 0; $i < 4; $i++)
                    <div class="bg-white shadow rounded-lg p-4 text-center">
                    <img src="{{ asset('sbadmin/img/beras super.jpg') }}"
                        class="mx-auto mb-2 h-24 object-contain">
                    <p class="font-semibold">Minyak Goreng</p>
                    <p class="text-gray-700">Rp. 6.000.000</p>
            </div>
            @endfor
            </div>
        </section>
    </main>

    <x-footer />

</body>

</html>