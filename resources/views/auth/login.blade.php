@extends('layouts.base')

@section('title', 'Masuk — SiHebat')

@section('body')
<div class="min-h-dvh bg-primary-100 flex flex-col items-center justify-center p-4 relative overflow-hidden">
    {{-- Decorative shapes --}}
    <div class="absolute top-0 right-0 w-80 h-80 bg-primary-200 rounded-full -translate-y-1/2 translate-x-1/3 opacity-60"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-primary-200 rounded-full translate-y-1/2 -translate-x-1/3 opacity-40"></div>
    <div class="absolute top-1/4 left-1/4 w-20 h-20 bg-sun rounded-full opacity-20 animate-floaty"></div>
    <div class="absolute bottom-1/3 right-1/4 w-16 h-16 bg-lavender rounded-full opacity-20 animate-floaty" style="animation-delay: 1s"></div>

    {{-- Mascot --}}
    <div class="relative z-10 mb-2">
        <x-mascot state="melambai" size="xl" />
    </div>

    {{-- Login card --}}
    <div class="relative z-10 card w-full max-w-sm p-6 sm:p-8">
        <div class="text-center mb-6">
            <div class="flex justify-center">
                <x-logo size="md" subtitle="7 Kebiasaan Anak Indonesia Hebat" />
            </div>
        </div>

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <div class="mb-4">
                <label for="identifier" class="input-label">NIS / NIP</label>
                <input
                    type="text"
                    id="identifier"
                    name="identifier"
                    class="input @error('identifier') input-error @enderror"
                    placeholder="Masukkan NIS atau NIP"
                    value="{{ old('identifier') }}"
                    autocomplete="username"
                    autofocus
                    required
                >
                @error('identifier')
                    <p class="input-error-msg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="currentColor" viewBox="0 0 256 256"><path d="M236.8,188.09,149.35,36.22h0a24.76,24.76,0,0,0-42.7,0L19.2,188.09a23.51,23.51,0,0,0,0,23.72A24.35,24.35,0,0,0,40.55,224h174.9a24.35,24.35,0,0,0,21.33-12.19A23.51,23.51,0,0,0,236.8,188.09ZM120,104a8,8,0,0,1,16,0v40a8,8,0,0,1-16,0Zm8,88a12,12,0,1,1,12-12A12,12,0,0,1,128,192Z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="input-label">Kata Sandi</label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="input @error('password') input-error @enderror"
                        placeholder="Masukkan kata sandi"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-500 hover:text-ink-700 transition-colors"
                            aria-label="Tampilkan kata sandi">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,123.97,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,192c-30.78,0-57.67-11.19-79.93-33.29A169.47,169.47,0,0,1,24.57,128,169.47,169.47,0,0,1,48.07,97.29C70.33,75.19,97.22,64,128,64s57.67,11.19,79.93,33.29A169.47,169.47,0,0,1,231.43,128C223.55,141.07,184.7,192,128,192Zm0-112a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Z"/></svg>
                    </button>
                </div>
                @error('password')
                    <p class="input-error-msg">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2 mb-6">
                <input type="checkbox" name="remember" id="remember"
                       class="size-5 rounded-lg border-ink-300 text-primary-400 focus:ring-primary-300"
                       {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" class="text-sm font-medium text-ink-700">Ingat saya</label>
            </div>

            <button type="submit" class="btn-primary w-full text-base">
                Masuk
            </button>
        </form>
    </div>

    <p class="relative z-10 mt-6 text-xs text-ink-500 font-medium text-center">
        &copy; {{ date('Y') }} SiHebat &mdash; 7 Kebiasaan Anak Indonesia Hebat
    </p>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endsection
