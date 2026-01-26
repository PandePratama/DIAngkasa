<footer class="relative bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white overflow-hidden">

    {{-- Decorative Background --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-30">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-500 rounded-full mix-blend-overlay filter blur-3xl">
        </div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-pink-500 rounded-full mix-blend-overlay filter blur-3xl">
        </div>
    </div>

    {{-- Main Footer Content --}}
    <div class="relative container mx-auto px-6 pt-16 pb-8">

        {{-- Top Section with Maps --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">

            {{-- Left Side: Info Sections --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">

                {{-- Tentang --}}
                <div
                    class="backdrop-blur-lg bg-white/5 rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3
                            class="text-xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                            Tentang Kami
                        </h3>
                    </div>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        DiRaditya Marketplace menyediakan berbagai produk gadget dan kebutuhan rumah tangga dengan harga
                        terbaik dan pelayanan cepat.
                    </p>
                </div>

                {{-- Menu --}}
                <div
                    class="backdrop-blur-lg bg-white/5 rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </div>
                        <h3
                            class="text-xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                            Menu
                        </h3>
                    </div>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('home') }}"
                                class="group flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
                                <span
                                    class="w-1.5 h-1.5 bg-purple-400 rounded-full group-hover:w-3 transition-all"></span>
                                <span class="text-sm">Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('minimarket.index') }}"
                                class="group flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
                                <span
                                    class="w-1.5 h-1.5 bg-purple-400 rounded-full group-hover:w-3 transition-all"></span>
                                <span class="text-sm">Minimarket</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('gadget.index') }}"
                                class="group flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
                                <span
                                    class="w-1.5 h-1.5 bg-purple-400 rounded-full group-hover:w-3 transition-all"></span>
                                <span class="text-sm">Gadget</span>
                            </a>
                        </li>
                        <li>
                            <a href="#kontak"
                                class="group flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
                                <span
                                    class="w-1.5 h-1.5 bg-purple-400 rounded-full group-hover:w-3 transition-all"></span>
                                <span class="text-sm">Kontak</span>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Kontak & Sosial Media --}}
                <div
                    class="backdrop-blur-lg bg-white/5 rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all duration-300 sm:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <h3
                            class="text-xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                            Hubungi Kami
                        </h3>
                    </div>

                    <div class="space-y-3 mb-6">
                        <a href="mailto:support@diraditya.com"
                            class="flex items-center gap-3 text-gray-300 hover:text-white transition-colors group">
                            <div
                                class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:bg-purple-500/30 transition-colors">
                                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-sm">support@diraditya.com</span>
                        </a>

                        <a href="tel:+6285119329510"
                            class="flex items-center gap-3 text-gray-300 hover:text-white transition-colors group">
                            <div
                                class="w-8 h-8 bg-pink-500/20 rounded-lg flex items-center justify-center group-hover:bg-pink-500/30 transition-colors">
                                <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-sm">+62 851 1932 9510</span>
                        </a>

                        <div class="flex items-center gap-3 text-gray-300">
                            <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <span class="text-sm">Jl. Contoh No. 123, Jakarta</span>
                        </div>
                    </div>

                    {{-- Social Media --}}
                    <div class="border-t border-white/10 pt-4">
                        <p class="text-xs text-gray-400 mb-3">Ikuti Kami</p>
                        <div class="flex gap-3">
                            <a href="#"
                                class="group relative w-10 h-10 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-lg flex items-center justify-center hover:from-purple-500 hover:to-pink-500 transition-all duration-300 overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-purple-500 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                </div>
                                <i
                                    class="fa-brands fa-facebook-f relative z-10 text-purple-400 group-hover:text-white transition-colors"></i>
                            </a>
                            <a href="#"
                                class="group relative w-10 h-10 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-lg flex items-center justify-center hover:from-purple-500 hover:to-pink-500 transition-all duration-300 overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-purple-500 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                </div>
                                <i
                                    class="fa-brands fa-twitter relative z-10 text-purple-400 group-hover:text-white transition-colors"></i>
                            </a>
                            <a href="#"
                                class="group relative w-10 h-10 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-lg flex items-center justify-center hover:from-purple-500 hover:to-pink-500 transition-all duration-300 overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-purple-500 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                </div>
                                <i
                                    class="fa-brands fa-instagram relative z-10 text-purple-400 group-hover:text-white transition-colors"></i>
                            </a>
                            <a href="#"
                                class="group relative w-10 h-10 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-lg flex items-center justify-center hover:from-purple-500 hover:to-pink-500 transition-all duration-300 overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-purple-500 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                </div>
                                <i
                                    class="fa-brands fa-whatsapp relative z-10 text-purple-400 group-hover:text-white transition-colors"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Side: Google Maps --}}
            <div
                class="backdrop-blur-lg bg-white/5 rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all duration-300 h-full">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                            </path>
                        </svg>
                    </div>
                    <h3
                        class="text-xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                        Lokasi Kami
                    </h3>
                </div>

                {{-- Google Maps Embed --}}
                <div class="relative rounded-xl overflow-hidden shadow-2xl group" style="height: 300px;">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-purple-500/20 to-pink-500/20 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                    </div>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.8195613!3d-6.1944491!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5d2e764b12d%3A0x3d2ad6e1e0e9bcc8!2sMonas!5e0!3m2!1sen!2sid!4v1234567890123!5m2!1sen!2sid"
                        class="w-full h-full border-0" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                {{-- Quick Direction Button --}}
                <a href="https://maps.google.com/?q=-6.1944491,106.8195613" target="_blank"
                    class="mt-4 w-full bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold py-3 rounded-lg shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 transition-all duration-300 flex items-center justify-center gap-2 group">
                    <svg class="w-5 h-5 transform group-hover:scale-110 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                        </path>
                    </svg>
                    <span>Buka di Google Maps</span>
                </a>
            </div>

        </div>

        {{-- Divider --}}
        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-white/10"></div>
            </div>
            <div class="relative flex justify-center">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 w-12 h-1 rounded-full"></div>
            </div>
        </div>

        {{-- Bottom Section --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">

            {{-- Logo & Copyright --}}
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center font-black text-xl shadow-lg shadow-purple-500/30">
                    DR
                </div>
                <div>
                    <p
                        class="font-bold text-lg bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                        DIRADITYA
                    </p>
                    <p class="text-xs text-gray-400">
                        &copy; {{ date('Y') }} DiRaditya. All rights reserved.
                    </p>
                </div>
            </div>

            {{-- Payment Methods --}}
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400">Metode Pembayaran:</span>
                <div class="flex gap-2">
                    <div
                        class="w-12 h-8 bg-white/10 rounded border border-white/20 flex items-center justify-center backdrop-blur-sm">
                        <i class="fa-brands fa-cc-visa text-white text-lg"></i>
                    </div>
                    <div
                        class="w-12 h-8 bg-white/10 rounded border border-white/20 flex items-center justify-center backdrop-blur-sm">
                        <i class="fa-brands fa-cc-mastercard text-white text-lg"></i>
                    </div>
                    <div
                        class="w-12 h-8 bg-white/10 rounded border border-white/20 flex items-center justify-center backdrop-blur-sm text-xs font-bold text-white">
                        OVO
                    </div>
                    <div
                        class="w-12 h-8 bg-white/10 rounded border border-white/20 flex items-center justify-center backdrop-blur-sm text-xs font-bold text-white">
                        DANA
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- Floating Particles --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-20">
        @for ($i = 0; $i < 15; $i++)
            <div class="absolute w-1 h-1 bg-white rounded-full animate-float"
                style="left: {{ rand(0, 100) }}%; top: {{ rand(0, 100) }}%; animation-delay: {{ $i * 0.5 }}s; animation-duration: {{ rand(15, 25) }}s;">
            </div>
        @endfor
    </div>

</footer>

<style>
    @keyframes float {

        0%,
        100% {
            transform: translateY(0) translateX(0);
            opacity: 0.3;
        }

        25% {
            transform: translateY(-30px) translateX(15px);
            opacity: 0.6;
        }

        50% {
            transform: translateY(-60px) translateX(-15px);
            opacity: 1;
        }

        75% {
            transform: translateY(-30px) translateX(15px);
            opacity: 0.6;
        }
    }

    .animate-float {
        animation: float linear infinite;
    }
</style>
