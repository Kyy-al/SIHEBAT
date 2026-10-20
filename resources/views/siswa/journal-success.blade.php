@extends('layouts.siswa')

@section('title', 'Jurnal Tersimpan! — SiHebat')

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center px-4 py-8 relative overflow-hidden text-center">

    {{-- Confetti Particle Canvas / Elements --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        @for($i = 0; $i < 24; $i++)
            @php
                $left = rand(5, 95);
                $delay = rand(0, 1500) / 1000;
                $colors = ['#FFC93C', '#FF7A59', '#38BDF8', '#34D399', '#A78BFA', '#FF8FB1'];
                $col = $colors[$i % count($colors)];
                $size = rand(8, 14);
            @endphp
            <div class="absolute rounded-sm animate-confetti"
                 style="left: {{ $left }}%; top: -20px; width: {{ $size }}px; height: {{ $size }}px; background-color: {{ $col }}; animation: confetti-fall 3.5s linear infinite; animation-delay: {{ $delay }}s;"></div>
        @endfor
    </div>

    <div class="max-w-md w-full relative z-10 space-y-6">
        {{-- Celebrating Mascot --}}
        <div>
            <x-mascot state="merayakan" size="2xl" />
        </div>

        <div>
            <span class="inline-block px-4 py-1.5 rounded-full bg-mint-soft text-[#0B6B47] text-xs font-extrabold uppercase tracking-wider mb-2">
                ✓ Jurnal Tersimpan
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-ink-900 tracking-tight">
                Luar Biasa, {{ explode(' ', $user->name)[0] }}! 🎉
            </h1>
            <p class="text-sm font-semibold text-ink-700 mt-2">
                Kamu telah menyelesaikan <strong class="text-ink-900 font-extrabold">{{ $result['habits_completed'] }} dari 7</strong> kebiasaan hebat hari ini.
            </p>
        </div>

        {{-- Reward Summary Card --}}
        <div class="card p-6 border border-ink-100 shadow-pop space-y-4 bg-white/90 backdrop-blur-sm">
            <div class="grid grid-cols-2 gap-3 divide-x divide-ink-100">
                <div class="text-center">
                    <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">XP Didapat</span>
                    <span class="text-3xl font-extrabold text-amber-600 tabular-nums">
                        +{{ $result['xp_earned'] }}
                    </span>
                    <span class="text-xs font-bold text-amber-700 block">XP</span>
                </div>

                <div class="text-center pl-3">
                    <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">Streak Hari Ini</span>
                    <div class="flex items-center justify-center gap-1">
                        <span class="text-2xl animate-flame">🔥</span>
                        <span class="text-3xl font-extrabold text-coral tabular-nums">
                            {{ $result['streak'] }}
                        </span>
                    </div>
                    <span class="text-xs font-bold text-coral block">Hari Beruntun</span>
                </div>
            </div>

            @if($result['is_perfect_day'])
                <div class="rounded-2xl p-3.5 bg-gradient-to-r from-sun-soft to-sun/30 border border-sun flex items-center justify-center gap-2">
                    <span class="text-2xl">👑</span>
                    <div class="text-left">
                        <p class="text-xs font-extrabold text-ink-900 uppercase tracking-wider">Perfect Day Bonus Tercapai!</p>
                        <p class="text-[11px] font-semibold text-ink-700">7 dari 7 kebiasaan selesai. Bonus +30 XP diberikan!</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Navigation Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <a href="{{ route('siswa.dashboard') }}" class="btn-primary flex-1 text-base shadow-btn">
                Kembali ke Beranda
            </a>
            <a href="{{ route('siswa.leaderboard') }}" class="btn-soft flex-1 text-base">
                Lihat Peringkat 🏆
            </a>
        </div>
    </div>
</div>
@endsection
