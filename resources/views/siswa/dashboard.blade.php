@extends('layouts.siswa')

@section('title', 'Beranda Siswa — SiHebat')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-4 sm:py-6 space-y-6">

    {{-- Top Header: Greeting & Gamification Chips --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">
                Halo, {{ explode(' ', $user->name)[0] }}! 👋
            </h1>
            <p class="text-sm font-semibold text-ink-500 mt-0.5">
                {{ $user->currentClassroom() ? 'Kelas ' . $user->currentClassroom()->name : 'Siswa Hebat' }} · Siap jalani hari penuh kebiasaan baik?
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            {{-- Streak Chip --}}
            <div class="chip-streak text-sm py-1.5 px-3.5 shadow-sm">
                <span class="animate-flame inline-block">🔥</span>
                <span class="tabular-nums font-extrabold">{{ $user->current_streak }}</span>
                <span>hari</span>
            </div>

            {{-- XP Chip --}}
            <div class="chip-xp text-sm py-1.5 px-3.5 shadow-sm">
                <span>⭐</span>
                <span class="tabular-nums font-extrabold">{{ $user->xp }}</span>
                <span>XP</span>
            </div>

            {{-- Level Badge --}}
            <div class="chip bg-lavender-soft text-purple-800 text-sm py-1.5 px-3 shadow-sm">
                <span>🎖️</span>
                <span class="font-extrabold">Lvl {{ $user->level }}</span>
            </div>
        </div>
    </div>

    {{-- Hero Card & Weekly Calendar Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        {{-- Hero Card (2 cols on lg) --}}
        <div class="lg:col-span-2 card-hero flex flex-col justify-between min-h-[220px]">
            {{-- Background decorative shapes --}}
            <div class="absolute -right-12 -top-12 size-56 rounded-full bg-white/20 pointer-events-none"></div>
            <div class="absolute right-24 -bottom-16 size-48 rounded-full bg-primary-300/30 pointer-events-none"></div>

            <div class="relative z-10 flex items-start justify-between gap-4">
                <div class="max-w-md">
                    <span class="inline-block px-3 py-1 rounded-full bg-white/40 text-ink-900 text-xs font-extrabold uppercase tracking-wider mb-2">
                        {{ Carbon\Carbon::today()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </span>

                    @if($todayJournal && $todayJournal->habits_completed > 0)
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-ink-900 leading-tight">
                            Hebat! Jurnalmu hari ini sudah tercatat 🎉
                        </h2>
                        <p class="text-sm font-semibold text-ink-900/80 mt-1">
                            Kamu telah menyelesaikan <strong class="text-ink-900 font-extrabold">{{ $todayJournal->habits_completed }} dari 7</strong> kebiasaan hari ini (+{{ $todayJournal->xp_earned }} XP didapat).
                        </p>
                    @else
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-ink-900 leading-tight">
                            Ayo isi jurnal hari ini! 🌟
                        </h2>
                        <p class="text-sm font-semibold text-ink-900/80 mt-1">
                            Cuma butuh 2 menit untuk jaga api streak-mu tetap menyala dan kumpulkan XP!
                        </p>
                    @endif
                </div>

                <div class="hidden sm:block shrink-0">
                    <x-mascot :state="$todayJournal && $todayJournal->habits_completed > 0 ? 'senang' : 'melambai'" size="lg" />
                </div>
            </div>

            <div class="relative z-10 pt-4 flex items-center gap-3">
                <a href="{{ route('siswa.journal') }}" class="btn-dark text-sm sm:text-base px-6">
                    @if($todayJournal)
                        Perbarui Jurnal ({{ $todayJournal->habits_completed }}/7)
                    @else
                        Isi Jurnal Sekarang
                    @endif
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M221.66,133.66l-72,72a8,8,0,0,1-11.32-11.32L196.69,136H40a8,8,0,0,1,0-16H196.69L138.34,61.66a8,8,0,0,1,11.32-11.32l72,72A8,8,0,0,1,221.66,133.66Z"/>
                    </svg>
                </a>

                @if($user->streak_freezes > 0)
                    <div class="hidden sm:flex items-center gap-1.5 px-3 py-2 rounded-2xl bg-white/40 text-xs font-bold text-ink-900">
                        <span>🧊</span>
                        <span>Pelindung Streak: {{ $user->streak_freezes }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Weekly Progress Dots Card --}}
        <div class="card flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-extrabold text-ink-900 text-base">Jejak Minggu Ini</h3>
                    <span class="text-xs font-bold text-primary-700">7 Hari</span>
                </div>
                <p class="text-xs text-ink-500 font-medium mb-4">
                    Pertahankan titik agar selalu berwarna untuk streak tanpa putus!
                </p>

                <div class="grid grid-cols-7 gap-1.5 sm:gap-2 text-center">
                    @foreach($weekDays as $day)
                        <div class="flex flex-col items-center gap-1.5">
                            <span class="text-[11px] font-bold text-ink-500 uppercase">
                                {{ substr($day['day_name'], 0, 3) }}
                            </span>

                            @if($day['is_perfect'])
                                <div class="week-dot-perfect {{ $day['is_today'] ? 'week-dot-today' : '' }}" title="Hari Sempurna (7/7)">
                                    ★
                                </div>
                            @elseif($day['completed'] >= 4)
                                <div class="week-dot-full {{ $day['is_today'] ? 'week-dot-today' : '' }}" title="{{ $day['completed'] }}/7 kebiasaan">
                                    {{ $day['completed'] }}
                                </div>
                            @elseif($day['completed'] > 0)
                                <div class="week-dot-partial {{ $day['is_today'] ? 'week-dot-today' : '' }}" title="{{ $day['completed'] }}/7 kebiasaan">
                                    {{ $day['completed'] }}
                                </div>
                            @else
                                <div class="week-dot-empty {{ $day['is_today'] ? 'week-dot-today' : '' }}" title="Belum diisi">
                                    {{ $day['is_future'] ? '·' : '○' }}
                                </div>
                            @endif

                            <span class="text-[10px] font-bold {{ $day['is_today'] ? 'text-primary-700 font-extrabold' : 'text-ink-500' }}">
                                {{ $day['date']->format('d') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Legend info --}}
            <div class="border-t border-ink-100 pt-3 mt-3 flex items-center justify-between text-[11px] text-ink-500 font-semibold">
                <span class="flex items-center gap-1">
                    <span class="size-2 rounded-full bg-sun"></span> Sempurna
                </span>
                <span class="flex items-center gap-1">
                    <span class="size-2 rounded-full bg-primary-400"></span> Aktif
                </span>
                <span class="flex items-center gap-1">
                    <span class="size-2 rounded-full border border-ink-300"></span> Kosong
                </span>
            </div>
        </div>
    </div>

    {{-- Today's 7 Habits Cards --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold text-ink-900">7 Kebiasaan Hari Ini</h2>
                <p class="text-xs font-semibold text-ink-500">Capaian hari ini: {{ $todayJournal ? $todayJournal->habits_completed : 0 }} dari 7 selesai</p>
            </div>
            <a href="{{ route('siswa.journal') }}" class="btn-ghost text-xs sm:text-sm">
                Buka Formulir Jurnal →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            @php
                $entryMap = [];
                if ($todayJournal) {
                    foreach ($todayJournal->entries as $entry) {
                        $entryMap[$entry->habit_id] = $entry;
                    }
                }
            @endphp

            @foreach($habits as $habit)
                @php
                    $isDone = isset($entryMap[$habit->id]) && $entryMap[$habit->id]->completed;
                    $bgClass = match($habit->color) {
                        'bangun'   => 'bg-habit-bangun-soft',
                        'ibadah'   => 'bg-habit-ibadah-soft',
                        'olahraga' => 'bg-habit-olahraga-soft',
                        'makan'    => 'bg-habit-makan-soft',
                        'belajar'  => 'bg-habit-belajar-soft',
                        'sosial'   => 'bg-habit-sosial-soft',
                        'tidur'    => 'bg-habit-tidur-soft',
                        default    => 'bg-primary-100',
                    };
                @endphp

                <div class="rounded-3xl p-4 {{ $bgClass }} border border-black/5 flex flex-col justify-between transition-transform hover:-translate-y-0.5">
                    <div class="flex items-start justify-between">
                        <div class="size-11 rounded-2xl bg-white/80 shadow-sm flex items-center justify-center text-xl">
                            @switch($habit->slug)
                                @case('bangun-pagi') 🌅 @break
                                @case('beribadah') 🤲 @break
                                @case('berolahraga') 🏃 @break
                                @case('makan-sehat') 🥗 @break
                                @case('gemar-belajar') 📚 @break
                                @case('bermasyarakat') 🤝 @break
                                @case('tidur-cepat') 🌙 @break
                                @default ⭐
                            @endswitch
                        </div>

                        @if($isDone)
                            <span class="chip-status-baik text-[11px] py-0.5 px-2.5">
                                ✓ Selesai
                            </span>
                        @else
                            <span class="chip bg-white/80 text-ink-500 text-[11px] py-0.5 px-2.5">
                                Belum
                            </span>
                        @endif
                    </div>

                    <div class="mt-3">
                        <h4 class="font-extrabold text-ink-900 text-sm sm:text-base leading-snug">
                            {{ $habit->name }}
                        </h4>
                        <div class="flex items-center justify-between mt-2 text-xs font-bold text-ink-700">
                            <span>+{{ $habit->xp_reward }} XP</span>
                            @if($isDone)
                                <span class="text-emerald-700 text-[11px]">Tercatat</span>
                            @else
                                <span class="text-ink-500 text-[11px] underline">Isi sekarang</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- 8th Card: Bonus Perfect Day --}}
            <div class="rounded-3xl p-4 bg-sun-soft border border-sun/20 flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="size-11 rounded-2xl bg-white/90 shadow-sm flex items-center justify-center text-xl">
                        👑
                    </div>
                    <span class="chip bg-sun text-ink-900 text-[11px] py-0.5 px-2.5 font-extrabold">
                        +30 XP
                    </span>
                </div>
                <div class="mt-3">
                    <h4 class="font-extrabold text-ink-900 text-sm sm:text-base">
                        Hari Sempurna
                    </h4>
                    <p class="text-xs font-semibold text-ink-700 mt-1">
                        Selesaikan ke-7 kebiasaan sekaligus hari ini untuk klaim bonus!
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Classroom Mini-Leaderboard & Motivation Card --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        {{-- Leaderboard Preview (2 cols) --}}
        <div class="lg:col-span-2 card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-extrabold text-ink-900 text-base sm:text-lg">Peringkat Kelas Teratas</h3>
                    <p class="text-xs font-semibold text-ink-500">Persaingan sehat untuk memupuk konsistensi kebiasaan</p>
                </div>
                <a href="{{ route('siswa.leaderboard') }}" class="btn-ghost text-xs sm:text-sm">
                    Lihat Semua →
                </a>
            </div>

            @if(count($leaderboard) > 0)
                <div class="space-y-2">
                    @foreach($leaderboard as $index => $student)
                        @php
                            $isMe = $student->id === $user->id;
                            $medal = match($index) {
                                0 => '🥇',
                                1 => '🥈',
                                2 => '🥉',
                                default => '#' . ($index + 1),
                            };
                        @endphp
                        <div class="flex items-center justify-between p-3 rounded-2xl transition-colors {{ $isMe ? 'bg-primary-100 border border-primary-300' : 'bg-surface hover:bg-canvas' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="text-sm font-extrabold w-6 text-center tabular-nums text-ink-700">
                                    {{ $medal }}
                                </span>
                                <div class="avatar shrink-0 size-9 text-xs">
                                    {{ substr($student->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-ink-900 truncate">
                                        {{ $student->name }}
                                        @if($isMe)
                                            <span class="ml-1 text-[10px] font-extrabold px-1.5 py-0.5 rounded-full bg-primary-400 text-ink-900">Kamu</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-ink-500 font-semibold">
                                        🔥 {{ $student->current_streak }} hari beruntun
                                    </p>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="text-sm font-extrabold text-ink-900 tabular-nums">{{ $student->xp }}</span>
                                <span class="text-xs font-bold text-amber-600">XP</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm text-ink-500 font-semibold">Belum ada data peringkat kelas.</p>
                </div>
            @endif
        </div>

        {{-- Level & Mascot Motivation Card --}}
        <div class="card flex flex-col justify-between text-center p-6 bg-gradient-to-b from-primary-50 to-white">
            <div>
                <x-mascot state="semangat" size="md" />
                <h4 class="font-extrabold text-ink-900 text-base mt-2">
                    Level {{ $user->level }}: {{ $user->level_name }}
                </h4>
                <p class="text-xs text-ink-500 font-medium mt-1">
                    Kumpulkan XP untuk naik ke tingkat berikutnya!
                </p>

                {{-- XP Progress bar --}}
                <div class="mt-4 text-left">
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-ink-700">Kemajuan Level</span>
                        <span class="text-primary-700">{{ $user->xp_progress }}%</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill" style="width: {{ $user->xp_progress }}%"></div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-ink-100 mt-4">
                <a href="{{ route('siswa.profile') }}" class="btn-soft w-full text-xs font-bold">
                    Lihat Koleksi Badge 🎖️
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
