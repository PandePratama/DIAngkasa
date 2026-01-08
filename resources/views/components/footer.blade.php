<footer class="bg-teal-700 text-white py-8">
    <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">

        {{-- Tentang --}}
        <div>
            <h3 class="text-lg font-semibold mb-4">Tentang DIAngkasa</h3>
            <p class="text-gray-200 text-sm">
                DIAngkasa Marketplace menyediakan berbagai produk gadget dan kebutuhan rumah tangga dengan harga terbaik dan pelayanan cepat.
            </p>
        </div>

        {{-- Navigasi --}}
        <div>
            <h3 class="text-lg font-semibold mb-4">Menu</h3>
            <ul class="space-y-2 text-gray-200 text-sm">
                <li><a href="#" class="hover:text-white">Home</a></li>
                <li><a href="#" class="hover:text-white">Minimarket</a></li>
                <li><a href="#" class="hover:text-white">Gadget</a></li>
                <li><a href="#" class="hover:text-white">Kontak</a></li>
            </ul>
        </div>

        {{-- Kontak / Sosial Media --}}
        <div>
            <h3 class="text-lg font-semibold mb-4">Kontak & Sosial Media</h3>
            <ul class="space-y-2 text-gray-200 text-sm">
                <li>Email: support@diangkasa.com</li>
                <li>Telepon: +62 812 3456 7890</li>
                <li class="flex space-x-3 mt-2">
                    <a href="#" class="hover:text-white"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="hover:text-white"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="hover:text-white"><i class="fa-brands fa-instagram"></i></a>
                </li>
            </ul>
        </div>

    </div>

    <div class="mt-8 text-center text-gray-300 text-sm">
        &copy; {{ date('Y') }} DIAngkasa. Semua hak cipta dilindungi.
    </div>
</footer>