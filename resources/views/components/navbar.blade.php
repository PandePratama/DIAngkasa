<header x-data="{ open: false }" class="bg-teal-700 text-white fixed w-full top-0 z-50 h-20">
    <div class="h-full flex justify-between items-center px-8">
        {{-- Logo --}}
        <div class="font-bold text-lg">
            <a href="{{ route('home') }}">DIRADITYA</a>
        </div>

        {{-- Hamburger --}}
        <button @click="open = true" class="md:hidden text-2xl">
            ☰
        </button>

        {{-- Desktop Menu --}}
        <nav class="hidden md:flex items-center space-x-6">
            <a href="{{ route('home') }}" class="hover:text-gray-200">Home</a>
            <a href="{{ route('minimarket.index') }}" class="hover:text-gray-200">Diamart</a>
            <a href="{{ route('gadget.index') }}" class="hover:text-gray-200">Raditya</a>

            {{-- Cart Icon with Dynamic Badge --}}
            <a href="{{ route('cart.index') }}"
                class="group relative p-2 text-gray-700 hover:text-teal-600 transition-colors duration-200">
                <i class="fa-solid fa-cart-shopping text-xl"></i>

                @if ($cart_count > 0)
                    <span class="absolute top-0 right-0 -mr-1 -mt-1 flex h-5 w-5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span
                            class="relative inline-flex rounded-full h-5 w-5 bg-red-500 text-[10px] font-bold text-white items-center justify-center">
                            {{ $cart_count }}
                        </span>
                    </span>
                @endif
            </a>

            {{-- Auth --}}
            @auth
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center space-x-2 hover:text-gray-200">
                        <span>👤</span>
                        <span class="text-sm">{{ auth()->user()->name }}</span>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-40 bg-white text-gray-800 rounded shadow">
                        <a href="{{ route('profile') }}" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-4 py-2 hover:bg-red-500">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="hover:text-gray-200">Login</a>
            @endauth
        </nav>
    </div>

    {{-- Overlay --}}
    <div x-show="open" x-transition.opacity @click="open = false" class="fixed inset-0 bg-black bg-opacity-60 z-40">
    </div>

    {{-- Sidebar --}}
    <aside x-show="open" x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 w-72 h-full bg-gray-900 text-white z-50 flex flex-col">

        {{-- Header Sidebar --}}
        <div class="p-6 border-b border-gray-700">
            @auth
                <p class="text-sm text-gray-400">Welcome,</p>
                <p class="font-semibold">{{ auth()->user()->name }}</p>
            @else
                <p class="font-semibold">Welcome</p>
            @endauth
        </div>

        {{-- Menu --}}
        <nav class="flex-1 px-6 py-4 space-y-6 text-sm">
            <a href="{{ route('profile') }}" class="block hover:text-teal-400">Profile</a>
            <a href="{{ route('home') }}" class="block hover:text-teal-400">Home</a>
            <a href="{{ route('cart.index') }}" class="block hover:text-teal-400">Cart</a>
            <a href="{{ route('gadget.index') }}" class="block hover:text-teal-400">Raditya</a>
            <a href="#" class="block hover:text-teal-400">Diamart</a>
        </nav>

        {{-- Logout --}}
        <div class="p-6 border-t border-gray-700">
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full bg-teal-600 hover:bg-teal-700 py-2 rounded-full text-sm">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="block text-center bg-teal-600 hover:bg-teal-700 py-2 rounded-full text-sm">
                    Login
                </a>
            @endauth
        </div>
    </aside>
</header>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
