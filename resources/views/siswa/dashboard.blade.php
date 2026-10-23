@extends('layouts.siswa')

@section('title', 'Beranda Siswa — SiHebat')

@section('content')
<div class="max-w-4xl mx-auto px-3.5 sm:px-4 py-4 sm:py-6 space-y-5 w-full max-w-full overflow-x-hidden"
     x-data="{
        streakModal: false,
        notifModal: false,
        hasNotifPermission: ('Notification' in window && Notification.permission === 'granted'),
        requestNotif() {
            if ('Notification' in window) {
                Notification.requestPermission().then(p => {
                    this.hasNotifPermission = (p === 'granted');
                    if (p === 'granted') {
                        new Notification('SiHebat 7 Kebiasaan', {
                            body: 'Pengingat harian aktif! Terus jaga api streak kebiasaan hebatmu.',
                            icon: '/favicon.ico'
                        });
                    }
                });
            }
        }
     }">

    {{-- Top Bar (Matching Image 2 phone header) --}}
    <div class="flex items-center justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl font-extrabold text-ink-900 tracking-tight flex items-center gap-1.5 truncate">
                <span>Halo, {{ explode(' ', $user->name)[0] }}!</span>
                <span class="inline-block animate-wiggle">👋</span>
            </h1>
            <div class="flex items-center gap-2 mt-0.5">
                <span class="inline-flex items-center gap-1 text-xs font-bold text-ink-500 bg-white/80 border border-ink-100 rounded-full px-2.5 py-0.5">
                    <span>🏫</span>
                    <span>{{ $user->currentClassroom() ? 'Kelas ' . $user->currentClassroom()->name : 'SMAN 1 Sungailiat' }}</span>
                </span>
            </div>
        </div>

        {{-- Top Right: Streak, XP & Notification Bell --}}
        <div class="flex items-center gap-2 shrink-0">
            {{-- Notification Bell --}}
            @php
                $habitsDoneToday = $todayJournal ? $todayJournal->habits_completed : 0;
                $hasUnreadNotif = ($habitsDoneToday < 7) || !empty($user->avatar_warning);
            @endphp
            <button type="button"
                    @click="notifModal = true"
                    class="size-9 sm:size-10 rounded-2xl bg-white border border-ink-100 shadow-xs flex items-center justify-center text-base sm:text-lg hover:bg-primary-50 transition relative"
                    title="Pusat Notifikasi & Pengingat">
                <span>🔔</span>
                @if($hasUnreadNotif)
                    <span class="absolute 1 top-1 right-1 size-2.5 rounded-full bg-rose-500 ring-2 ring-white animate-ping"></span>
                    <span class="absolute top-1 right-1 size-2.5 rounded-full bg-rose-500 ring-2 ring-white"></span>
                @endif
            </button>

            {{-- Streak Pill (Clickable -> Opens Duolingo Evolution Modal) --}}
            <button type="button"
                    @click="streakModal = true"
                    class="chip-streak py-1.5 px-3 rounded-full shadow-sm text-xs sm:text-sm cursor-pointer hover:scale-105 active:scale-95 transition-transform flex items-center gap-1"
                    title="Lihat Peta Evolusi Maskot Streak Duolingo">
                <span class="animate-flame inline-block">🔥</span>
                <span class="font-black tabular-nums">{{ $user->current_streak }}</span>
                <span class="hidden sm:inline">Hari</span>
            </button>

            {{-- XP Pill --}}
            <div class="chip-xp py-1.5 px-3 rounded-full shadow-sm text-xs sm:text-sm">
                <span>⭐</span>
                <span class="font-black tabular-nums">{{ $user->xp }}</span>
                <span class="hidden sm:inline">XP</span>
            </div>
        </div>
    </div>

    {{-- Daily Journal Reminder Notification Banner (Duolingo Style: Friendly & Time-Aware) --}}
    @php
        $completedCount = $todayJournal ? $todayJournal->habits_completed : 0;
        $remainingCount = 7 - $completedCount;
        $currentHour = Carbon\Carbon::now()->hour;
    @endphp

    @if($remainingCount > 0)
        <div class="p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-amber-500/15 via-orange-500/10 to-amber-500/15 border-2 border-amber-300 shadow-sm relative overflow-hidden transition-all hover:border-amber-400">
            <div class="flex items-start sm:items-center justify-between gap-3 flex-col sm:flex-row">
                <div class="flex items-start gap-3.5 min-w-0">
                    <div class="size-12 rounded-2xl bg-amber-400 text-ink-950 flex items-center justify-center text-2xl shrink-0 shadow-xs ring-4 ring-amber-200">
                        ⏰
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="chip bg-amber-400 text-ink-950 text-[10px] py-0.5 px-2 font-black uppercase tracking-wider">
                                PENGINGAT JURNAL HARI INI
                            </span>
                            @if($currentHour >= 18)
                                <span class="chip bg-rose-500 text-white text-[10px] py-0.5 px-2 font-black animate-pulse">
                                    ⚠️ Sebelum 23:59 WIB!
                                </span>
                            @endif
                        </div>

                        <h3 class="text-sm sm:text-base font-black text-ink-900 mt-1 leading-snug">
                            @if($completedCount === 0)
                                Jangan Lupa Isi Jurnal 7 Kebiasaan Hebatmu Hari Ini! 🌟
                            @else
                                Tinggal {{ $remainingCount }} Kebiasaan Lagi Menuju Hari Sempurna! 👑
                            @endif
                        </h3>
                        <p class="text-xs font-semibold text-ink-600 mt-0.5">
                            @if($user->current_streak > 0)
                                Jaga api streak <strong class="text-orange-600 font-black">🔥 {{ $user->current_streak }} hari</strong> kamu agar tidak padam dan kumpulkan bonus XP!
                            @else
                                Catat kebiasaan hari ini untuk menyalakan kembali api streak maskotmu!
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto shrink-0 pt-1 sm:pt-0">
                    <a href="{{ route('siswa.journal', ['date' => Carbon\Carbon::today()->format('Y-m-d')]) }}"
                       class="btn flex-1 sm:flex-initial py-2.5 px-5 bg-gradient-to-r from-amber-400 to-orange-400 hover:from-amber-500 hover:to-orange-500 text-ink-950 text-xs sm:text-sm font-black shadow-md rounded-2xl flex items-center justify-center gap-2 transition-transform hover:scale-102">
                        <span>Isi Jurnal Sekarang</span>
                        <span>✍️</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Moderation Warning Notification --}}
    @if($user->avatar_warning)
        <div class="p-4 rounded-3xl bg-amber-50 border-2 border-amber-300 text-amber-950 flex items-start gap-3.5 shadow-sm">
            <div class="size-10 rounded-2xl bg-amber-200 text-amber-900 flex items-center justify-center text-xl shrink-0">
                ⚠️
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <h4 class="font-black text-sm text-amber-950">Peringatan Foto Profil dari Pihak Sekolah</h4>
                    <a href="{{ route('siswa.profile') }}" class="text-xs font-black text-amber-900 underline hover:text-amber-950 shrink-0">
                        Ganti Foto di Profil →
                    </a>
                </div>
                <p class="text-xs font-medium text-amber-900 mt-0.5">
                    "{{ $user->avatar_warning }}"
                </p>
                <p class="text-[11px] font-bold text-amber-800 mt-1.5">
                    Silakan buka menu Profil untuk mengunggah foto baru yang pantas (berseragam sekolah). Peringatan ini otomatis selesai setelah foto baru tersimpan.
                </p>
            </div>
        </div>
    @endif

    {{-- Hero Card (Executive Balanced UI/UX: Evolving Duolingo-style Streak Mascot on Stage) --}}
    <div class="card-hero rounded-3xl p-4 sm:p-6 shadow-md relative overflow-hidden bg-gradient-to-r from-primary-400 via-primary-300 to-primary-400 space-y-3.5">
        {{-- Curved Background Waves & Bubbles --}}
        <div class="absolute -right-6 -top-6 size-40 rounded-full bg-white/20 pointer-events-none"></div>
        <div class="absolute right-24 -bottom-12 size-36 rounded-full bg-primary-200/40 pointer-events-none"></div>

        {{-- Top Section: Greeting & Duolingo-style Streak Mascot Stage (Side by Side & Equal Weight) --}}
        <div class="relative z-10 flex items-center justify-between gap-3 sm:gap-4">
            <div class="min-w-0 flex-1 space-y-1.5 sm:space-y-2">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/75 text-ink-900 text-[10px] sm:text-[11px] font-black uppercase tracking-wider shadow-xs">
                    <span>📅</span>
                    <span>{{ Carbon\Carbon::today()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                </div>

                @if($todayJournal && $todayJournal->habits_completed > 0)
                    <h2 class="text-base sm:text-2xl font-black text-ink-900 leading-tight">
                        Hebat, Jurnalmu Sudah Tercatat! 🎉
                    </h2>
                    <p class="text-xs sm:text-sm font-semibold text-ink-900/85">
                        Tuntas <strong class="text-ink-900 font-black">{{ $todayJournal->habits_completed }} dari 7</strong> kebiasaan (+{{ $todayJournal->xp_earned }} XP).
                    </p>
                @else
                    <h2 class="text-base sm:text-2xl font-black text-ink-900 leading-tight">
                        Ayo Isi Jurnal Hari Ini! 🌟
                    </h2>
                    <p class="text-xs sm:text-sm font-semibold text-ink-900/85">
                        Cuma butuh 2 menit untuk jaga api streak dan kumpulkan XP!
                    </p>
                @endif
            </div>

            {{-- Evolving Streak Mascot Stage: Duolingo Flame Evolution with Click to View Roadmap --}}
            <div class="shrink-0 relative flex flex-col items-center justify-center cursor-pointer group hover:scale-105 active:scale-95 transition-transform"
                 @click="streakModal = true"
                 title="Klik untuk melihat evolusi maskot streak Duolingo-mu!">
                <x-mascot-streak :streak="$user->current_streak" size="lg" :showLabel="true" />
                <span class="text-[9px] font-black text-primary-950 bg-white/70 px-2 py-0.5 rounded-full mt-1 group-hover:bg-white shadow-2xs">
                    Peta Evolusi ➔
                </span>
            </div>
        </div>

        {{-- Bottom Section: Full-Width Dedicated Action Row (Ergonomic for Thumbs) --}}
        <div class="relative z-10 pt-2.5 border-t border-ink-900/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <a href="{{ route('siswa.journal', ['date' => $today->format('Y-m-d')]) }}"
               class="btn-dark flex-1 sm:flex-initial text-xs sm:text-sm py-2.5 px-5 rounded-2xl shadow-btn-dark active:translate-y-0.5 flex items-center justify-center gap-2 font-black">
                @if($todayJournal)
                    <span>Perbarui Jurnal ({{ $todayJournal->habits_completed }}/7)</span>
                @else
                    <span>Isi Jurnal Sekarang</span>
                    <span>→</span>
                @endif
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('siswa.rapor') }}"
                   class="btn-soft flex-1 sm:flex-initial text-xs py-2 px-3.5 rounded-xl shadow-btn-soft text-center font-bold">
                    📊 Rapor Saya
                </a>

                @if($user->streak_freezes > 0)
                    <span class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-white/80 text-[10px] sm:text-[11px] font-extrabold text-ink-900 shadow-xs shrink-0">
                        <span>🧊</span>
                        <span>Pelindung: {{ $user->streak_freezes }}</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- H-1 Backfill Alert if Yesterday is missing (FR-JRN-03) --}}
    @if(!$yesterdayJournal)
        <div class="rounded-3xl p-4 bg-amber-50 border border-amber-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="text-2xl">⏳</span>
                <div>
                    <h4 class="text-xs sm:text-sm font-black text-amber-950">
                        Jurnal Kemarin Belum Terisi ({{ $yesterday->locale('id')->isoFormat('dddd, D MMMM') }})
                    </h4>
                    <p class="text-[11px] text-amber-800 font-semibold mt-0.5">
                        Kamu masih bisa mengisi jurnal kemarin untuk menyelamatkan catatan kebiasaanmu!
                    </p>
                </div>
            </div>
            <a href="{{ route('siswa.journal', ['date' => $yesterday->format('Y-m-d')]) }}" class="btn-primary text-xs font-black py-2 px-4 shadow-btn shrink-0">
                Isi Jurnal Kemarin (H-1)
            </a>
        </div>
    @endif

    {{-- Weekly Calendar Dot Matrix (Directly inspired by Image 2 middle phone) --}}
    <div class="card p-4 sm:p-5 border border-ink-100 shadow-card overflow-hidden">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="font-extrabold text-ink-900 text-sm sm:text-base">Jejak Kebiasaan Minggu Ini</h3>
                <p class="text-[11px] text-ink-500 font-semibold">Titik terisi menandakan jurnal harianmu telah tuntas</p>
            </div>
            <span class="chip bg-primary-100 text-primary-900 text-xs font-black shrink-0">7 Hari</span>
        </div>

        <div class="overflow-x-auto scrollbar-none pt-1">
            <div class="grid grid-cols-7 gap-1 sm:gap-2 text-center min-w-[280px]">
                @foreach($weekDays as $day)
                    <div class="flex flex-col items-center gap-1.5">
                        <span class="text-[11px] font-extrabold text-ink-500 uppercase">
                            {{ substr($day['day_name'], 0, 3) }}
                        </span>

                        {{-- Circular date badge like reference Image 2 --}}
                        @if($day['is_perfect'])
                            <div class="size-9 rounded-full bg-sun text-ink-900 font-black text-xs flex items-center justify-center shadow-sm {{ $day['is_today'] ? 'ring-4 ring-primary-400 ring-offset-2' : '' }}" title="Hari Sempurna 7/7">
                                ★
                            </div>
                        @elseif($day['completed'] >= 4)
                            <div class="size-9 rounded-full bg-primary-400 text-ink-900 font-black text-xs flex items-center justify-center shadow-sm {{ $day['is_today'] ? 'ring-4 ring-primary-500 ring-offset-2' : '' }}" title="{{ $day['completed'] }}/7 kebiasaan">
                                {{ $day['completed'] }}
                            </div>
                        @elseif($day['completed'] > 0)
                            <div class="size-9 rounded-full bg-primary-200 text-primary-900 font-bold text-xs flex items-center justify-center {{ $day['is_today'] ? 'ring-4 ring-primary-400 ring-offset-2' : '' }}" title="{{ $day['completed'] }}/7 kebiasaan">
                                {{ $day['completed'] }}
                            </div>
                        @else
                            <div class="size-9 rounded-full border-2 border-ink-200 text-ink-400 font-bold text-xs flex items-center justify-center {{ $day['is_today'] ? 'ring-4 ring-primary-400 ring-offset-2 border-primary-400 text-primary-700' : '' }}">
                                {{ $day['is_future'] ? '·' : '○' }}
                            </div>
                        @endif

                        <span class="text-[11px] font-extrabold {{ $day['is_today'] ? 'text-primary-800' : 'text-ink-700' }}">
                            {{ $day['date']->format('d') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Today's 7 Habits Cards (Visual Grid) --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base sm:text-lg font-extrabold text-ink-900">7 Kebiasaan Hari Ini</h2>
                <p class="text-xs font-semibold text-ink-500">
                    Capaian: <strong class="text-ink-900">{{ $todayJournal ? $todayJournal->habits_completed : 0 }} dari 7</strong> kebiasaan selesai
                </p>
            </div>
            <a href="{{ route('siswa.journal') }}" class="btn-ghost text-xs font-bold">
                Buka Jurnal →
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
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

                <a href="{{ route('siswa.journal') }}" class="rounded-3xl p-3.5 sm:p-4 {{ $bgClass }} border border-black/5 flex flex-col justify-between hover:scale-[1.02] transition-transform shadow-xs overflow-hidden">
                    <div class="flex items-start justify-between">
                        <div class="size-11 rounded-2xl bg-white/95 shadow-sm flex items-center justify-center p-1">
                            <x-mascot-habit :habit="$habit->slug" size="sm" />
                        </div>

                        @if($isDone)
                            <span class="chip-status-baik text-[10px] py-0.5 px-2 font-extrabold">
                                ✓ Selesai
                            </span>
                        @else
                            <span class="chip bg-white/80 text-ink-500 text-[10px] py-0.5 px-2 font-bold">
                                +10 XP
                            </span>
                        @endif
                    </div>

                    <div class="mt-2.5">
                        <h4 class="font-extrabold text-ink-900 text-xs sm:text-sm leading-snug">
                            {{ $habit->name }}
                        </h4>
                        <span class="text-[10px] font-bold {{ $isDone ? 'text-emerald-800' : 'text-ink-500' }} mt-0.5 block">
                            {{ $isDone ? 'Telah tercatat' : 'Belum diisi' }}
                        </span>
                    </div>
                </a>
            @endforeach

            {{-- 8th Card: Perfect Day Bonus --}}
            <div class="rounded-3xl p-3.5 sm:p-4 bg-sun-soft border border-sun/30 flex flex-col justify-between shadow-xs overflow-hidden">
                <div class="flex items-start justify-between">
                    <div class="size-11 rounded-2xl bg-white/95 shadow-sm flex items-center justify-center p-1">
                        <x-mascot-habit habit="hari-sempurna" size="sm" />
                    </div>
                    <span class="chip bg-sun text-ink-900 text-[10px] py-0.5 px-2 font-black">
                        +30 XP
                    </span>
                </div>
                <div class="mt-2.5">
                    <h4 class="font-extrabold text-ink-900 text-xs sm:text-sm">
                        Hari Sempurna
                    </h4>
                    <span class="text-[10px] font-bold text-amber-900 mt-0.5 block">
                        Klaim bonus 7/7
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Weekly Missions (FR-GAM-06) --}}
    @if(count($weeklyMissions) > 0)
    <div class="card p-4 sm:p-5 border border-ink-100 shadow-card space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-ink-900 text-sm sm:text-base flex items-center gap-1.5">
                    <span></span>
                    <span>Misi Minggu Ini</span>
                </h3>
                <p class="text-[11px] text-ink-500 font-semibold">Tantangan mingguan untuk dapat bonus XP ekstra</p>
            </div>
            <span class="chip bg-primary-100 text-primary-900 text-xs font-black">
                {{ count(array_filter($weeklyMissions, fn($m) => $m['is_completed'])) }} / {{ count($weeklyMissions) }} Tuntas
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($weeklyMissions as $wm)
                @php
                    $m = $wm['mission'];
                    $um = $wm['user_mission'];
                @endphp
                <div class="p-3.5 rounded-2xl border transition {{ $wm['is_completed'] ? 'bg-mint-soft/60 border-mint/40' : 'bg-surface border-ink-100' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <div class="size-9 rounded-xl flex items-center justify-center text-lg {{ $wm['is_completed'] ? 'bg-mint text-white' : 'bg-primary-100 text-primary-800' }}">
                                @if($wm['is_completed'])
                                    ✓
                                @else
                                    @switch($m->icon)
                                        @case('barbell') <x-mascot-habit habit="berolahraga" size="xs" /> @break
                                        @case('sun') <x-mascot-habit habit="bangun-pagi" size="xs" /> @break
                                        @case('book-open') <x-mascot-habit habit="gemar-belajar" size="xs" /> @break
                                        @case('trophy') <x-mascot-habit habit="hari-sempurna" size="xs" /> @break
                                        @default 🎯
                                    @endswitch
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-xs sm:text-sm text-ink-900 leading-snug">{{ $m->title }}</h4>
                                <span class="chip-xp text-[10px] py-0.2 px-1.5 font-black">+{{ $m->xp_reward }} XP</span>
                            </div>
                        </div>

                        <span class="text-xs font-extrabold text-ink-700 tabular-nums shrink-0">
                            {{ $um->current_count }} / {{ $m->target_count }}
                        </span>
                    </div>

                    <p class="text-[11px] text-ink-500 font-medium mt-2 leading-relaxed">
                        {{ $m->description }}
                    </p>

                    <div class="progress-track h-2 mt-2 bg-ink-100">
                        <div class="progress-fill {{ $wm['is_completed'] ? 'bg-mint' : 'bg-primary-400' }}" style="width: {{ $wm['progress'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Classroom Leaderboard Snippet --}}
    <div class="card p-4 sm:p-5 border border-ink-100 shadow-card space-y-3.5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-ink-900 text-sm sm:text-base flex items-center gap-1.5">
                    <span>🏆</span>
                    <span>Peringkat Kelas Teratas</span>
                </h3>
                <p class="text-xs font-semibold text-ink-500">Semangat konsistensi bersama teman sekelas</p>
            </div>
            <a href="{{ route('siswa.leaderboard') }}" class="btn-ghost text-xs font-bold shrink-0">
                Lihat Semua ➔
            </a>
        </div>

        @if(count($leaderboard) >= 3)
            @php
                $first = $leaderboard[0];
                $second = $leaderboard[1];
                $third = $leaderboard[2];
            @endphp
            {{-- Compact Horizontal 3-Column Mini Podium (Always side-by-side: 2 - 1 - 3) --}}
            <div class="p-3 sm:p-4 rounded-3xl bg-gradient-to-b from-amber-50/70 via-white to-primary-50/20 border border-amber-200/70 shadow-2xs">
                <div class="grid grid-cols-3 gap-1 sm:gap-2.5 items-end max-w-sm mx-auto text-center pt-1">
                    {{-- 2nd Place (Silver - Left) --}}
                    <div class="flex flex-col items-center min-w-0">
                        <span class="text-[8px] sm:text-[9px] font-black text-slate-700 bg-slate-200 px-1.5 py-0.2 rounded-full uppercase">
                            #2 Perak
                        </span>
                        <div class="relative my-1">
                            <div class="avatar size-9 sm:size-11 text-xs font-black border border-slate-300 mx-auto overflow-hidden bg-slate-100">
                                @if($second->avatar_url)
                                    <img src="{{ $second->avatar_url }}" alt="{{ $second->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($second->name, 0, 1) }}
                                @endif
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 size-3.5 sm:size-4 rounded-full bg-slate-600 text-white font-black text-[8px] flex items-center justify-center">2</span>
                        </div>
                        <p class="text-[10px] sm:text-[11px] font-black text-ink-900 truncate w-full px-0.5">{{ $second->name }}</p>
                        <div class="w-full mt-1 pt-1.5 pb-1 px-0.5 rounded-t-xl bg-gradient-to-b from-slate-200 via-slate-100 to-slate-200/60 border-t border-x border-slate-300 h-14 sm:h-16 flex flex-col items-center justify-center">
                            <span class="text-xs sm:text-sm font-black text-slate-700">2</span>
                            <span class="text-[9px] sm:text-[10px] font-black text-slate-900 tabular-nums leading-tight">{{ number_format($second->xp) }} <span class="text-[7px] text-slate-600">XP</span></span>
                            <span class="text-[8px] sm:text-[9px] font-extrabold text-orange-600 tabular-nums leading-tight">🔥{{ $second->current_streak }}h</span>
                        </div>
                    </div>

                    {{-- 1st Place (Gold - Center & Elevated) --}}
                    <div class="flex flex-col items-center min-w-0 -mt-1.5">
                        <span class="text-[8px] sm:text-[9px] font-black text-amber-950 bg-amber-400 px-1.5 py-0.2 rounded-full uppercase shadow-xs">
                            👑 #1 Emas
                        </span>
                        <div class="relative my-1">
                            <div class="avatar size-11 sm:size-13 text-xs sm:text-sm font-black border-2 border-amber-400 mx-auto overflow-hidden bg-amber-100 shadow-sm ring-2 ring-amber-300">
                                @if($first->avatar_url)
                                    <img src="{{ $first->avatar_url }}" alt="{{ $first->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($first->name, 0, 1) }}
                                @endif
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 size-4 sm:size-5 rounded-full bg-amber-500 text-white font-black text-[9px] flex items-center justify-center border border-white">1</span>
                        </div>
                        <p class="text-[10px] sm:text-[11px] font-black text-ink-900 truncate w-full px-0.5">{{ $first->name }}</p>
                        <div class="w-full mt-1 pt-2 pb-1.5 px-0.5 rounded-t-xl bg-gradient-to-b from-amber-300 via-amber-200 to-amber-300/80 border-t-2 border-x-2 border-amber-400 h-20 sm:h-24 flex flex-col items-center justify-center shadow-xs">
                            <span class="text-sm sm:text-base font-black text-amber-950">1</span>
                            <span class="text-[10px] sm:text-[11px] font-black text-amber-950 tabular-nums leading-tight">{{ number_format($first->xp) }} <span class="text-[8px] text-amber-800">XP</span></span>
                            <span class="text-[9px] sm:text-[10px] font-black text-orange-600 tabular-nums leading-tight">🔥{{ $first->current_streak }}h</span>
                        </div>
                    </div>

                    {{-- 3rd Place (Bronze - Right) --}}
                    <div class="flex flex-col items-center min-w-0">
                        <span class="text-[8px] sm:text-[9px] font-black text-amber-950 bg-amber-200 px-1.5 py-0.2 rounded-full uppercase">
                            #3 Perunggu
                        </span>
                        <div class="relative my-1">
                            <div class="avatar size-9 sm:size-11 text-xs font-black border border-amber-600/40 mx-auto overflow-hidden bg-orange-50">
                                @if($third->avatar_url)
                                    <img src="{{ $third->avatar_url }}" alt="{{ $third->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($third->name, 0, 1) }}
                                @endif
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 size-3.5 sm:size-4 rounded-full bg-amber-800 text-white font-black text-[8px] flex items-center justify-center">3</span>
                        </div>
                        <p class="text-[10px] sm:text-[11px] font-black text-ink-900 truncate w-full px-0.5">{{ $third->name }}</p>
                        <div class="w-full mt-1 pt-1.5 pb-1 px-0.5 rounded-t-xl bg-gradient-to-b from-orange-200/80 via-amber-100 to-orange-200/50 border-t border-x border-amber-300 h-12 sm:h-14 flex flex-col items-center justify-center">
                            <span class="text-xs sm:text-sm font-black text-amber-900">3</span>
                            <span class="text-[9px] sm:text-[10px] font-black text-amber-950 tabular-nums leading-tight">{{ number_format($third->xp) }} <span class="text-[7px] text-amber-800">XP</span></span>
                            <span class="text-[8px] sm:text-[9px] font-extrabold text-orange-600 tabular-nums leading-tight">🔥{{ $third->current_streak }}h</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="space-y-1.5">
            @foreach(collect($leaderboard)->skip(3) as $index => $st)
                @php
                    $isMe = $st->id === $user->id;
                    $rank = $index + 4;
                @endphp
                <div class="flex items-center justify-between p-2.5 rounded-2xl {{ $isMe ? 'bg-primary-100 border border-primary-300' : 'bg-surface hover:bg-canvas' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-6 text-center font-extrabold text-xs tabular-nums text-ink-500">
                            #{{ $rank }}
                        </span>
                        <div class="avatar size-8 text-xs font-black shrink-0 overflow-hidden ring-1 ring-ink-200">
                            @if($st->avatar_url)
                                <img src="{{ $st->avatar_url }}" alt="{{ $st->name }}" class="w-full h-full object-cover">
                            @else
                                {{ substr($st->name, 0, 1) }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm font-bold text-ink-900 truncate">
                                {{ $st->name }}
                                @if($isMe)
                                    <span class="chip bg-primary-400 text-ink-900 text-[9px] py-0 px-1.5 font-black ml-1">Kamu</span>
                                @endif
                            </p>
                            <span class="text-[10px] font-semibold text-ink-500">
                                🔥 {{ $st->current_streak }} hari beruntun
                            </span>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="text-xs font-black text-ink-900 tabular-nums">{{ $st->xp }}</span>
                        <span class="text-[10px] font-bold text-amber-600">XP</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- MODAL EVOLUSI MASKOT STREAK (DUOLINGO STYLE) --}}
    <div x-show="streakModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-xs"
         @keydown.escape.window="streakModal = false">
        <div class="card p-5 sm:p-6 max-w-xl w-full border-2 border-primary-300 shadow-2xl space-y-4 bg-white rounded-3xl max-h-[90vh] overflow-y-auto"
             @click.away="streakModal = false">

            <div class="flex items-center justify-between pb-3 border-b border-ink-100">
                <div class="flex items-center gap-2.5">
                    <div class="size-10 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center text-xl shadow-xs">
                        🔥
                    </div>
                    <div>
                        <h3 class="font-extrabold text-ink-900 text-base sm:text-lg">Peta Evolusi Maskot Streak</h3>
                        <p class="text-xs text-ink-500 font-semibold">Semakin panjang streak-mu, wujud maskotmu semakin keren!</p>
                    </div>
                </div>
                <button type="button" @click="streakModal = false" class="text-ink-400 hover:text-ink-700 text-xl font-black p-1">
                    ✕
                </button>
            </div>

            {{-- Current Streak Stage Showcase --}}
            <div class="p-4 rounded-3xl bg-gradient-to-br from-primary-50 via-amber-50/60 to-primary-100/40 border border-primary-200 text-center relative overflow-hidden">
                <span class="inline-block text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full bg-primary-400 text-ink-950 mb-2">
                    WUJUD MASKOTMU SAAT INI
                </span>

                <div class="py-2 flex justify-center">
                    <x-mascot-streak :streak="$user->current_streak" size="xl" :showLabel="true" />
                </div>

                <div class="max-w-sm mx-auto mt-2">
                    <p class="text-xs font-semibold text-ink-600">
                        @if($user->current_streak === 0)
                            Streak kamu sedang padam! Catat kebiasaan hari ini untuk membangkitkan wujud <strong>Percikan Semangat</strong>!
                        @elseif($user->current_streak < 3)
                            Hebat! Pertahankan {{ 3 - $user->current_streak }} hari lagi untuk berevolusi menjadi wujud <strong>Api Membara</strong>!
                        @elseif($user->current_streak < 7)
                            Keren banget! {{ 7 - $user->current_streak }} hari lagi menuju wujud ksatria <strong>Ksatria Api</strong>!
                        @elseif($user->current_streak < 14)
                            Luar biasa! {{ 14 - $user->current_streak }} hari lagi menuju wujud <strong>Pahlawan Hebat</strong>!
                        @elseif($user->current_streak < 30)
                            Spektakuler! {{ 30 - $user->current_streak }} hari lagi menuju status tertinggi <strong>Legenda Abadi</strong>!
                        @else
                            👑 Kamu telah mencapai puncak evolusi maskot tertinggi: <strong>Legenda Abadi</strong>!
                        @endif
                    </p>
                </div>
            </div>

            {{-- All 6 Tiers Evolution Ladder --}}
            <div class="space-y-2">
                <h4 class="text-xs font-extrabold text-ink-700 uppercase tracking-wider">Tingkatan Evolusi Maskot:</h4>

                @php
                    $tiersData = [
                        [
                            'name' => 'Api Padam',
                            'req'  => 0,
                            'badge' => 'Streak 0 Hari',
                            'desc' => 'Maskot sedang beristirahat/membeku saat api streak padam.',
                            'active' => $user->current_streak === 0,
                            'unlocked' => true,
                        ],
                        [
                            'name' => 'Percikan Semangat',
                            'req'  => 1,
                            'badge' => 'Streak 1 - 2 Hari',
                            'desc' => 'Langkah awal pembiasaan, maskot memegang percikan api mungil.',
                            'active' => $user->current_streak >= 1 && $user->current_streak < 3,
                            'unlocked' => $user->current_streak >= 1,
                        ],
                        [
                            'name' => 'Api Membara',
                            'req'  => 3,
                            'badge' => 'Streak 3 - 6 Hari',
                            'desc' => 'Api di atas kepala mulai menyala berkobar dengan penuh percaya diri.',
                            'active' => $user->current_streak >= 3 && $user->current_streak < 7,
                            'unlocked' => $user->current_streak >= 3,
                        ],
                        [
                            'name' => 'Ksatria Api',
                            'req'  => 7,
                            'badge' => 'Streak 7 - 13 Hari',
                            'desc' => 'Mengenakan bandana ksatria merah dengan obor api membara.',
                            'active' => $user->current_streak >= 7 && $user->current_streak < 14,
                            'unlocked' => $user->current_streak >= 7,
                        ],
                        [
                            'name' => 'Pahlawan Hebat',
                            'req'  => 14,
                            'badge' => 'Streak 14 - 29 Hari',
                            'desc' => 'Mengenakan jubah pahlawan merah gagah dan kacamata hitam keren!',
                            'active' => $user->current_streak >= 14 && $user->current_streak < 30,
                            'unlocked' => $user->current_streak >= 14,
                        ],
                        [
                            'name' => 'Legenda Abadi',
                            'req'  => 30,
                            'badge' => 'Streak 30+ Hari',
                            'desc' => 'Mahkota emas bertatah permata api dengan aura kosmik super bersinar!',
                            'active' => $user->current_streak >= 30,
                            'unlocked' => $user->current_streak >= 30,
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @foreach($tiersData as $td)
                        <div class="p-3 rounded-2xl border transition-all flex items-center gap-3 {{ $td['active'] ? 'bg-amber-50/90 border-amber-400 ring-2 ring-amber-300 shadow-sm' : ($td['unlocked'] ? 'bg-white border-ink-200' : 'bg-slate-50 border-slate-200 opacity-60') }}">
                            <div class="shrink-0">
                                <x-mascot-streak :streak="$td['req']" size="md" :animate="$td['active']" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-1">
                                    <h5 class="font-extrabold text-xs text-ink-900 truncate">{{ $td['name'] }}</h5>
                                    @if($td['active'])
                                        <span class="chip bg-amber-400 text-ink-950 text-[9px] py-0 px-1.5 font-black">Aktif</span>
                                    @elseif($td['unlocked'])
                                        <span class="text-mint text-xs font-bold">✓</span>
                                    @else
                                        <span class="text-ink-400 text-xs">🔒</span>
                                    @endif
                                </div>
                                <span class="text-[10px] font-bold text-ink-500 block">{{ $td['badge'] }}</span>
                                <p class="text-[10px] text-ink-500 mt-0.5 line-clamp-2 leading-relaxed">{{ $td['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-3 border-t border-ink-100 flex items-center justify-between gap-3">
                <span class="text-xs text-ink-500 font-semibold">
                    💡 Isi jurnal setiap hari sebelum jam 23:59 agar streak-mu terus bertambah!
                </span>
                <button type="button" @click="streakModal = false" class="btn-dark text-xs font-bold py-2 px-4 rounded-xl">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL PUSAT NOTIFIKASI & PENGINGAT (NOTIFICATION CENTER) --}}
    <div x-show="notifModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-xs"
         @keydown.escape.window="notifModal = false">
        <div class="card p-5 sm:p-6 max-w-lg w-full border-2 border-primary-300 shadow-2xl space-y-4 bg-white rounded-3xl"
             @click.away="notifModal = false">

            <div class="flex items-center justify-between pb-3 border-b border-ink-100">
                <div class="flex items-center gap-2.5">
                    <div class="size-10 rounded-2xl bg-primary-100 text-primary-800 flex items-center justify-center text-xl">
                        🔔
                    </div>
                    <div>
                        <h3 class="font-extrabold text-ink-900 text-base">Pusat Notifikasi & Pengingat</h3>
                        <p class="text-xs text-ink-500 font-semibold">Pemberitahuan aktivitas dan pengingat pembiasaan</p>
                    </div>
                </div>
                <button type="button" @click="notifModal = false" class="text-ink-400 hover:text-ink-700 text-xl font-black p-1">
                    ✕
                </button>
            </div>

            <div class="space-y-3">
                {{-- 1. Pengingat Jurnal Harian --}}
                <div class="p-3.5 rounded-2xl border {{ $remainingCount > 0 ? 'bg-amber-50 border-amber-300' : 'bg-emerald-50 border-emerald-300' }} flex items-start gap-3">
                    <div class="size-9 rounded-xl flex items-center justify-center text-lg shrink-0 {{ $remainingCount > 0 ? 'bg-amber-200 text-amber-900' : 'bg-emerald-200 text-emerald-900' }}">
                        {{ $remainingCount > 0 ? '⏰' : '✓' }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs sm:text-sm text-ink-900">
                            {{ $remainingCount > 0 ? 'Jurnal Harian Belum Lengkap' : 'Jurnal Hari Ini Sudah Tuntas!' }}
                        </h4>
                        <p class="text-xs text-ink-600 mt-0.5">
                            {{ $completedCount }}/7 kebiasaan tercatat hari ini. {{ $remainingCount > 0 ? 'Selesaikan sebelum pukul 23:59 WIB agar streak tidak padam.' : 'Kerja bagus, kamu meraih predikat Hari Sempurna!' }}
                        </p>
                        @if($remainingCount > 0)
                            <a href="{{ route('siswa.journal', ['date' => Carbon\Carbon::today()->format('Y-m-d')]) }}"
                               class="btn text-xs font-black py-1 px-3 bg-amber-400 text-ink-950 hover:bg-amber-500 rounded-lg mt-2 inline-flex items-center gap-1 shadow-xs">
                                <span>Isi Sekarang</span>
                                <span>✍️</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- 2. Status Pelindung Streak (Streak Freeze) --}}
                <div class="p-3.5 rounded-2xl bg-sky-50 border border-sky-200 flex items-start gap-3">
                    <div class="size-9 rounded-xl bg-sky-200 text-sky-900 flex items-center justify-center text-lg shrink-0">
                        🧊
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs sm:text-sm text-ink-900">Pelindung Streak (Streak Freeze)</h4>
                        <p class="text-xs text-ink-600 mt-0.5">
                            @if($user->streak_freezes > 0)
                                Tersedia <strong>{{ $user->streak_freezes }}x</strong> pelindung aktif. Streak-mu otomatis terlindungi jika kamu lupa mengisi jurnal 1 hari dalam seminggu.
                            @else
                                Pelindung streak telah terpakai minggu ini dan akan diperbarui otomatis minggu depan.
                            @endif
                        </p>
                    </div>
                </div>

                {{-- 3. Web Push Notification Toggle --}}
                <div class="p-3.5 rounded-2xl bg-surface border border-ink-200 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xl">📲</span>
                        <div>
                            <h4 class="font-bold text-xs text-ink-900">Notifikasi Peramban (Jam 20:00)</h4>
                            <p class="text-[11px] text-ink-500 font-semibold" x-text="hasNotifPermission ? 'Notifikasi peramban aktif untuk mengingatkanmu' : 'Aktifkan agar muncul pop-up pengingat di layar HP/Laptop'"></p>
                        </div>
                    </div>

                    <button type="button"
                            @click="requestNotif"
                            class="btn text-xs font-bold py-1.5 px-3 rounded-xl"
                            :class="hasNotifPermission ? 'bg-mint-soft text-mint font-extrabold cursor-default' : 'btn-primary'">
                        <span x-text="hasNotifPermission ? '✓ Aktif' : 'Aktifkan'"></span>
                    </button>
                </div>
            </div>

            <div class="pt-2 text-right">
                <button type="button" @click="notifModal = false" class="btn-soft text-xs font-bold py-2 px-4 rounded-xl">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
