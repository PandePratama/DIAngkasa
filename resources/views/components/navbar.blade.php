<header x-data="{ open: false }"
    class="fixed top-0 w-full z-50 h-20 bg-gradient-to-r from-cyan-600 to-teal-600 text-white shadow-lg">

    <div class="max-w-7xl mx-auto h-full flex items-center justify-between px-6">

        {{-- LOGO --}}
        <a href="{{ route('home') }}" class="flex items-center gap-3 group ml-8">
            {{-- Logo 1 --}}
            <img src="{{ asset('sbadmin/img/logo raditya R white.webp') }}"
                alt="Raditya"
                class="h-10 md:h-11 object-contain">

            {{-- Divider --}}
            <span class="h-6 w-px bg-white/50"></span>

            {{-- Logo 2 --}}
            <img src="{{ asset('sbadmin/img/Logo Dia_White_2.webp') }}"
                alt="Diamart"
                class="h-8 md:h-9 object-contain opacity-90 group-hover:opacity-100 transition">
        </a>


        {{-- HAMBURGER (MOBILE) --}}
        <button @click="open = true"
            class="md:hidden text-3xl focus:outline-none">
            ☰
        </button>

        {{-- DESKTOP MENU --}}
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium">

            <a href="{{ route('home') }}" class="hover:text-gray-200 transition">Home</a>
            <a href="{{ route('minimarket.index') }}" class="hover:text-gray-200 transition">Diamart</a>
            <a href="{{ route('gadget.index') }}" class="hover:text-gray-200 transition">Raditya</a>

            {{-- CART --}}
            <a href="{{ route('cart.index') }}" class="relative">
                <i class="fa-solid fa-cart-shopping text-lg hover:text-gray-200"></i>

                @if (isset($cart_count) && $cart_count > 0)
                <span
                    class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold
                               rounded-full h-5 w-5 flex items-center justify-center">
                    {{ $cart_count }}
                </span>
                @endif
            </a>

            {{-- AUTH --}}
            @auth
            <div x-data="{ dropdown: false }" class="relative">
                <button @click="dropdown = !dropdown"
                    class="flex items-center gap-2 hover:text-gray-200 transition">
                    <i class="fa-solid fa-user"></i>
                    <span class="text-sm">{{ auth()->user()->name }}</span>
                </button>

                <div x-show="dropdown" @click.away="dropdown = false"
                    class="absolute right-0 mt-3 w-44 bg-white text-gray-800 rounded-xl shadow-lg overflow-hidden">

                    <a href="{{ route('profile.index') }}"
                        class="block px-4 py-3 hover:bg-gray-100 transition">
                        Profile
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            class="w-full text-left px-4 py-3 hover:bg-red-500 hover:text-white transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
            @else
            <a href="{{ route('login') }}"
                class="px-4 py-2 rounded-lg bg-white/20 hover:bg-white/30 transition">
                Login
            </a>
            @endauth
        </nav>
    </div>

    {{-- OVERLAY --}}
    <div x-show="open" x-transition.opacity
        @click="open = false"
        class="fixed inset-0 bg-black/60 md:hidden">
    </div>

    {{-- MOBILE SIDEBAR --}}
    <aside x-show="open"
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 w-72 h-full bg-white text-gray-800 z-50 shadow-2xl flex flex-col">

        {{-- SIDEBAR HEADER --}}
        <div class="p-6 bg-gradient-to-r from-teal-600 to-cyan-600 text-white">
            @auth
            <p class="text-sm opacity-80">Welcome</p>
            <p class="font-semibold">{{ auth()->user()->name }}</p>
            @else
            <p class="font-semibold">Welcome</p>
            @endauth
        </div>

        {{-- MENU --}}
        <nav class="flex-1 p-6 space-y-5 text-sm">
            <a href="{{ route('home') }}" class="block hover:text-teal-600">Home</a>
            <a href="{{ route('minimarket.index') }}" class="block hover:text-teal-600">Diamart</a>
            <a href="{{ route('gadget.index') }}" class="block hover:text-teal-600">Raditya</a>
            <a href="{{ route('cart.index') }}" class="block hover:text-teal-600">Cart</a>
            <a href="{{ route('profile.index') }}" class="block hover:text-teal-600">Profile</a>
        </nav>

        {{-- FOOTER --}}
        <div class="p-6 border-t">
            @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    class="w-full py-2 rounded-xl bg-gradient-to-r from-teal-600 to-cyan-600
                               text-white font-semibold hover:opacity-90 transition">
                    Logout
                </button>
            </form>
            @else
            <a href="{{ route('login') }}"
                class="block text-center py-2 rounded-xl bg-gradient-to-r from-teal-600 to-cyan-600
                           text-white font-semibold hover:opacity-90 transition">
                Login
            </a>
            @endauth
        </div>
    </aside>
</header>