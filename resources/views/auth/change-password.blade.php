@extends('layouts.base')

@section('title', 'Buat Kata Sandi Baru — SiHebat')

@section('body')
<div class="min-h-dvh bg-primary-100 flex flex-col items-center justify-center p-4 relative overflow-hidden">
    {{-- Decorative background shapes --}}
    <div class="absolute -top-24 -left-24 size-80 rounded-full bg-primary-200/50 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 size-96 rounded-full bg-sun-soft/60 blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        {{-- Mascot & Greetings --}}
        <div class="text-center mb-6">
            <x-mascot state="semangat" size="lg" />
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 mt-2">Buat Kata Sandi Baru</h1>
            <p class="text-sm font-medium text-ink-700 mt-1 max-w-sm mx-auto">
                Halo, <span class="font-bold text-ink-900">{{ Auth::user()->name }}</span>! Demi keamanan akunmu, yuk buat kata sandi baru yang mudah kamu ingat.
            </p>
        </div>

        {{-- Form Card --}}
        <div class="card p-6 sm:p-8 shadow-card border border-ink-100">
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="password" class="input-label">Kata Sandi Baru</label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           minlength="8"
                           placeholder="Minimal 8 karakter"
                           class="input @error('password') input-error @enderror"
                           autocomplete="new-password">
                    <p class="input-hint">Gunakan kombinasi huruf dan angka agar kuat.</p>
                    @error('password')
                        <p class="input-error-msg">
                            <span>⚠️</span>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="input-label">Ulangi Kata Sandi Baru</label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           required
                           minlength="8"
                           placeholder="Ketik ulang kata sandi baru"
                           class="input"
                           autocomplete="new-password">
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full text-base">
                        Simpan & Mulai Jurnal
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M221.66,133.66l-72,72a8,8,0,0,1-11.32-11.32L196.69,136H40a8,8,0,0,1,0-16H196.69L138.34,61.66a8,8,0,0,1,11.32-11.32l72,72A8,8,0,0,1,221.66,133.66Z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- Logout link --}}
        <div class="text-center mt-5">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-semibold text-ink-500 hover:text-ink-900 transition-colors">
                    ← Keluar dan ganti akun
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
