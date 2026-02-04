<footer class="bg-gradient-to-r from-teal-700 to-cyan-700 text-white">

    {{-- MAIN FOOTER --}}
    <div class="max-w-7xl mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-3 gap-12">

        {{-- BRAND / ABOUT --}}
        <div>
            <h3 class="text-xl font-extrabold mb-4">DiRaditya</h3>
            <p class="text-white/80 text-sm leading-relaxed max-w-sm">
                DiRaditya Marketplace menyediakan berbagai produk gadget dan kebutuhan
                harian karyawan dengan sistem kredit internal yang aman, cepat,
                dan transparan.
            </p>
        </div>

        {{-- NAVIGATION --}}
        <div>
            <h3 class="text-lg font-semibold mb-4">Menu</h3>
            <ul class="space-y-3 text-sm text-white/80">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-white transition">
                        Home
                    </a>
                </li>
                <li>
                    <a href="{{ route('minimarket.index') }}" class="hover:text-white transition">
                        Diamart
                    </a>
                </li>
                <li>
                    <a href="{{ route('gadget.index') }}" class="hover:text-white transition">
                        Raditya
                    </a>
                </li>
                <li>
                    <a href="#kontak" class="hover:text-white transition">
                        Kontak
                    </a>
                </li>
            </ul>
        </div>

        {{-- CONTACT & SOCIAL --}}
        <div>
            <h3 class="text-lg font-semibold mb-4">Kontak</h3>

            <ul class="space-y-3 text-sm text-white/80">
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-envelope"></i>
                    support@diraditya.com
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-phone"></i>
                    +62 851 1932 9510
                </li>
            </ul>

            {{-- SOCIAL ICON --}}
            <div class="flex gap-4 mt-6">
                <a href="#"
                    class="w-9 h-9 flex items-center justify-center rounded-full
                           bg-white/10 hover:bg-white/20 transition">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
                <a href="#"
                    class="w-9 h-9 flex items-center justify-center rounded-full
                           bg-white/10 hover:bg-white/20 transition">
                    <i class="fa-brands fa-twitter"></i>
                </a>
                <a href="#"
                    class="w-9 h-9 flex items-center justify-center rounded-full
                           bg-white/10 hover:bg-white/20 transition">
                    <i class="fa-brands fa-instagram"></i>
                </a>
            </div>
        </div>

    </div>

    {{-- DIVIDER --}}
    <div class="border-t border-white/20"></div>

    {{-- COPYRIGHT --}}
    <div class="py-6 text-center text-sm text-white/70">
        &copy; {{ date('Y') }} <span class="font-semibold">DiRaditya</span>.
        Semua hak cipta dilindungi.
    </div>

</footer>