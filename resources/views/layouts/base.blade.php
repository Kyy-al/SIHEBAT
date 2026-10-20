<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SiHebat — Dashboard Jurnal 7 Kebiasaan Anak Indonesia Hebat">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SiHebat')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
