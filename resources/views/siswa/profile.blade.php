@extends('layouts.siswa')

@section('title', 'Profil Saya — SiHebat')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-4 sm:py-6 space-y-6">

    {{-- Profile Card --}}
    <div class="card p-6 border border-ink-100 flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
        <div class="avatar-lg size-20 text-2xl font-black shrink-0 ring-4 ring-primary-200">
            {{ substr($user->name, 0, 1) }}
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h1 class="text-2xl font-extrabold text-ink-900 leading-tight">{{ $user->name }}</h1>
                    <p class="text-sm font-semibold text-ink-500 mt-0.5">
                        NIS: <span class="text-ink-900 font-bold">{{ $user->nis ?? '-' }}</span> ·
                        {{ $user->currentClassroom() ? 'Kelas ' . $user->currentClassroom()->name : 'Siswa Hebat' }}
                    </p>
                </div>

                <div class="flex items-center justify-center sm:justify-end gap-2 flex-wrap">
                    <span class="chip bg-lavender-soft text-purple-800 text-xs py-1 px-3 font-extrabold">
                        Level {{ $user->level }} — {{ $user->level_name }}
                    </span>
                    <span class="chip-streak text-xs py-1 px-3">
                        🔥 {{ $user->current_streak }} Hari
                    </span>
                </div>
            </div>

            {{-- Level Progress bar --}}
            <div class="mt-4 bg-primary-50 rounded-2xl p-3 border border-primary-100">
                <div class="flex justify-between items-center text-xs font-bold mb-1">
                    <span class="text-ink-700">Perjalanan ke Level {{ $user->level + 1 }}</span>
                    <span class="text-primary-700 tabular-nums">{{ $user->xp }} XP</span>
                </div>
                <div class="progress-track h-2.5">
                    <div class="progress-fill" style="width: {{ $user->xp_progress }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Bento Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="card p-4 border border-ink-100 text-center">
            <span class="text-2xl mb-1 block">📝</span>
            <span class="text-2xl font-black text-ink-900 tabular-nums block">{{ $totalJournals }}</span>
            <span class="text-xs font-bold text-ink-500">Total Jurnal</span>
        </div>

        <div class="card p-4 border border-ink-100 text-center">
            <span class="text-2xl mb-1 block">👑</span>
            <span class="text-2xl font-black text-amber-600 tabular-nums block">{{ $perfectDays }}</span>
            <span class="text-xs font-bold text-ink-500">Hari Sempurna (7/7)</span>
        </div>

        <div class="card p-4 border border-ink-100 text-center">
            <span class="text-2xl mb-1 block">🔥</span>
            <span class="text-2xl font-black text-coral tabular-nums block">{{ $user->longest_streak }}</span>
            <span class="text-xs font-bold text-ink-500">Streak Terpanjang</span>
        </div>

        <div class="card p-4 border border-ink-100 text-center">
            <span class="text-2xl mb-1 block">⭐</span>
            <span class="text-2xl font-black text-amber-500 tabular-nums block">{{ number_format($user->xp) }}</span>
            <span class="text-xs font-bold text-ink-500">Total XP</span>
        </div>
    </div>

    {{-- Badges Collection --}}
    @php
        $allBadges = \App\Models\Badge::where('is_active', true)->get();
        $earnedBadgeIds = $badges->pluck('id')->toArray();
        $earnedPivots = $badges->keyBy('id');
    @endphp

    <div class="card p-5 sm:p-6 border border-ink-100 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-ink-900">Koleksi Lencana Penghargaan</h3>
                <p class="text-xs font-semibold text-ink-500">
                    Kamu telah membuka {{ count($earnedBadgeIds) }} dari {{ count($allBadges) }} lencana
                </p>
            </div>
            <span class="chip bg-primary-100 text-primary-800 text-xs font-bold">
                {{ count($earnedBadgeIds) }}/{{ count($allBadges) }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            @foreach($allBadges as $badge)
                @php
                    $isEarned = in_array($badge->id, $earnedBadgeIds);
                    $earnedAt = $isEarned ? $earnedPivots[$badge->id]->pivot->earned_at : null;
                @endphp

                <div class="rounded-3xl p-4 border transition-all flex items-start gap-3.5 {{ $isEarned ? 'bg-white border-primary-300 shadow-sm' : 'bg-canvas/60 border-ink-100 opacity-60' }}">
                    <div class="size-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 {{ $isEarned ? 'bg-primary-100 shadow-sm' : 'bg-ink-100 text-ink-300' }}">
                        @if($isEarned)
                            @switch($badge->icon)
                                @case('sparkle') 👑 @break
                                @case('fire') 🔥 @break
                                @case('sun-horizon') 🌅 @break
                                @case('trophy') 🏆 @break
                                @case('medal') 🏅 @break
                                @default 🎖️
                            @endswitch
                        @else
                            🔒
                        @endif
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <h4 class="font-extrabold text-sm text-ink-900 truncate">{{ $badge->name }}</h4>
                            @if($isEarned)
                                <span class="text-mint font-bold text-xs">✓</span>
                            @endif
                        </div>
                        <p class="text-xs font-medium text-ink-500 mt-0.5 line-clamp-2 leading-relaxed">
                            {{ $badge->description }}
                        </p>
                        @if($isEarned && $earnedAt)
                            <span class="text-[10px] font-bold text-primary-700 mt-1 block">
                                Diraih {{ Carbon\Carbon::parse($earnedAt)->locale('id')->isoFormat('D MMM Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Account Options --}}
    <div class="card p-5 sm:p-6 border border-ink-100 space-y-3">
        <h3 class="text-base font-extrabold text-ink-900 mb-2">Pengaturan Akun</h3>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-1">
            <a href="{{ route('password.change') }}" class="btn-soft w-full sm:w-auto text-sm">
                🔒 Ganti Kata Sandi
            </a>
            <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="btn w-full sm:w-auto bg-coral-soft text-coral text-sm hover:bg-coral hover:text-white transition-colors">
                    Keluar dari Akun
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
