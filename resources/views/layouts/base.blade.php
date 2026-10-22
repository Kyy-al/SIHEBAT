<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SiHebat — Dashboard Jurnal 7 Kebiasaan Anak Indonesia Hebat">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SiHebat')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-icon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        // Memastikan selalu menggunakan mode terang (Light Mode) bersih
        try {
            localStorage.removeItem('theme');
            localStorage.setItem('theme', 'light');
        } catch (e) {}
        document.documentElement.classList.remove('dark');
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>
<body class="min-h-dvh bg-canvas font-sans text-ink-900 antialiased">
    @yield('body')

    {{-- Toast notifications --}}
    @if(session('success'))
    <div class="toast" id="toast-success" role="alert">
        <span class="text-mint text-lg">✓</span>
        <span>{{ session('success') }}</span>
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('toast-success')?.remove();
        }, 4000);
    </script>
    @endif

    @if(session('error'))
    <div class="toast" id="toast-error" role="alert">
        <span class="text-coral text-lg">!</span>
        <span>{{ session('error') }}</span>
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('toast-error')?.remove();
        }, 4000);
    </script>
    @endif

    @stack('scripts')
</body>
</html>
