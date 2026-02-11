<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DiRaditya')</title>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Vite Assets (CSS & JS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    {{-- NAVBAR --}}
    <x-navbar />

    {{-- SPACER (Agar konten tidak tertutup navbar fixed) --}}
    <div class="h-20"></div>

    {{-- CONTENT --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <x-footer />

    @stack('scripts')
</body>

</html>
