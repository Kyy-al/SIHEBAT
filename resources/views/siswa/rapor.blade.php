@extends('layouts.siswa')

@section('title', 'Rapor Kebiasaan Saya — SiHebat')

@section('content')
<div class="max-w-4xl mx-auto px-3.5 sm:px-4 py-4 sm:py-6 space-y-6 w-full max-w-full overflow-x-hidden">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('siswa.dashboard') }}" class="btn-ghost text-xs">← Beranda</a>
                <span class="text-ink-300">/</span>
                <span class="text-xs font-bold text-ink-500">Rapor Saya</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-ink-900 tracking-tight mt-1">
                Rapor Kebiasaan Saya 📊
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-ink-500">
                Pantau jejak kebiasaan harian, capaian karakter, dan akumulasi poin kebaikanmu.
            </p>
        </div>

        {{-- Month Selector Form --}}
        <form method="GET" action="{{ route('siswa.rapor') }}" class="flex items-center gap-2 self-start sm:self-auto bg-white p-1 rounded-2xl border border-ink-100 shadow-xs">
            <select name="month" onchange="this.form.submit()" class="input text-xs font-bold py-1.5 px-3 min-h-[36px] rounded-xl border-none">
                @for($m = 1; $m <= 12; $m++)
                    @php $monthDate = Carbon\Carbon::create(null, $m, 1); @endphp
                    <option value="{{ $m }}" {{ $selectedMonth->month == $m ? 'selected' : '' }}>
                        {{ $monthDate->locale('id')->isoFormat('MMMM') }}
                    </option>
                @endfor
            </select>
            <span class="text-xs font-black text-ink-700 pr-3">{{ $selectedMonth->year }}</span>
        </form>
    </div>

    {{-- Monthly Calendar Heatmap (Directly inspired by Image 2 middle phone!) --}}
    <div class="card p-4 sm:p-6 border border-ink-100 shadow-card space-y-4 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-extrabold text-ink-900 text-sm sm:text-base">
                    Kalender Partisipasi {{ $selectedMonth->locale('id')->isoFormat('MMMM Y') }}
                </h3>
                <p class="text-xs text-ink-500 font-semibold">
                    Total {{ $totalJournalsThisMonth }} hari terisi bulan ini
                </p>
            </div>

            {{-- Legend --}}
            <div class="flex items-center gap-1.5 sm:gap-2 text-[10px] font-bold text-ink-600 flex-wrap">
                <span class="flex items-center gap-1">
                    <span class="size-3 rounded-full border-2 border-ink-200"></span> Kosong
                </span>
                <span class="flex items-center gap-1">
                    <span class="size-3 rounded-full bg-primary-200"></span> 1-3
                </span>
                <span class="flex items-center gap-1">
                    <span class="size-3 rounded-full bg-primary-400"></span> 4-6
                </span>
                <span class="flex items-center gap-1">
                    <span class="size-3 rounded-full bg-sun"></span> 7/7 Sempurna
                </span>
            </div>
        </div>

        {{-- Calendar Grid (7 columns: Min, Sen, Sel, Rab, Kam, Jum, Sab) --}}
        <div class="pt-2 overflow-x-auto scrollbar-none">
            <div class="grid grid-cols-7 gap-1 sm:gap-2 text-center min-w-[280px]">
                @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dayHeader)
                    <span class="text-xs font-black text-ink-400 uppercase pb-2">
                        {{ $dayHeader }}
                    </span>
                @endforeach

                {{-- Empty slots before the first day of the month --}}
                @php $firstDayOfWeek = $selectedMonth->copy()->startOfMonth()->dayOfWeek; @endphp
                @for($pad = 0; $pad < $firstDayOfWeek; $pad++)
                    <div></div>
                @endfor

                @foreach($calendarDays as $day)
                    <div class="flex flex-col items-center gap-1 py-1">
                        @if($day['is_perfect'])
                            <div class="size-8 sm:size-10 rounded-full bg-sun text-ink-900 font-black text-xs flex items-center justify-center shadow-xs {{ $day['is_today'] ? 'ring-2 ring-primary-500 ring-offset-2' : '' }}" title="{{ $day['date']->format('d M') }}: 7/7 Sempurna (+{{ $day['xp'] }} XP)">
                                ★
                            </div>
                        @elseif($day['completed'] >= 4)
                            <div class="size-8 sm:size-10 rounded-full bg-primary-400 text-ink-900 font-black text-xs flex items-center justify-center shadow-xs {{ $day['is_today'] ? 'ring-2 ring-primary-500 ring-offset-2' : '' }}" title="{{ $day['date']->format('d M') }}: {{ $day['completed'] }}/7 Tuntas">
                                {{ $day['completed'] }}
                            </div>
                        @elseif($day['completed'] > 0)
                            <div class="size-8 sm:size-10 rounded-full bg-primary-200 text-primary-900 font-bold text-xs flex items-center justify-center {{ $day['is_today'] ? 'ring-2 ring-primary-500 ring-offset-2' : '' }}" title="{{ $day['date']->format('d M') }}: {{ $day['completed'] }}/7 Tuntas">
                                {{ $day['completed'] }}
                            </div>
                        @else
                            <div class="size-8 sm:size-10 rounded-full border-2 border-ink-200 text-ink-400 font-bold text-xs flex items-center justify-center {{ $day['is_today'] ? 'ring-2 ring-primary-500 ring-offset-2 border-primary-400 text-primary-700' : '' }}">
                                {{ $day['day'] }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 7 Kebiasaan Breakdown Cards (Rapor Pendidikan Ala Siswa) --}}
    <div class="space-y-3">
        <h3 class="font-black text-ink-900 text-lg">
            Capaian 7 Kebiasaan Bulan Ini
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($habitBreakdown as $hb)
                @php
                    $h = $hb['habit'];
                    $rate = $hb['rate'];
                    $status = $hb['status'];
                    $statusClass = match($status) {
                        'Baik' => 'chip-status-baik',
                        'Cukup' => 'chip-status-cukup',
                        default => 'chip-status-perlu',
                    };
                @endphp
                <div class="card p-4 sm:p-5 border border-ink-100 shadow-card flex flex-col justify-between overflow-hidden">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div class="size-10 rounded-2xl {{ $h->soft_color }} flex items-center justify-center p-0.5 shadow-xs shrink-0">
                                <x-mascot-habit :habit="$h->slug" size="xs" />
                            </div>
                            <span class="{{ $statusClass }} text-[10px] py-0.5 px-2 font-black shrink-0">
                                {{ $status }}
                            </span>
                        </div>

                        <h4 class="font-extrabold text-ink-900 text-sm leading-snug">{{ $h->name }}</h4>

                        <div class="mt-2 flex items-baseline gap-1">
                            <span class="text-xl font-black text-ink-900 tabular-nums">{{ $rate }}%</span>
                            <span class="text-[11px] text-ink-500 font-semibold">konsisten</span>
                        </div>

                        <div class="progress-track h-2 mt-2 bg-ink-100">
                            <div class="progress-fill" style="width: {{ $rate }}%; background-color: {{ $h->color_hex }}"></div>
                        </div>
                    </div>

                    <div class="pt-3 mt-3 border-t border-ink-100 text-[10px] font-bold text-ink-500">
                        Terlaksana {{ $hb['done_count'] }} kali
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Transparent XP Logs (FR-GAM / FR-ADM) --}}
    <div class="card p-4 sm:p-6 border border-ink-100 shadow-card space-y-3 overflow-hidden">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <h3 class="font-extrabold text-ink-900 text-sm sm:text-base flex items-center gap-2">
                <span>⭐</span> Riwayat Perolehan XP Terbaru
            </h3>
            <span class="text-xs font-bold text-ink-500">Total: <strong class="text-amber-700">{{ number_format($user->xp) }} XP</strong></span>
        </div>

        <div class="divide-y divide-ink-100 text-xs">
            @forelse($xpLogs as $log)
                <div class="py-2.5 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-base shrink-0">
                            @switch($log->source)
                                @case('perfect_day') 👑 @break
                                @case('streak_bonus') 🔥 @break
                                @case('mission') 🎯 @break
                                @case('badge') 🎖️ @break
                                @default 📝
                            @endswitch
                        </span>
                        <div class="min-w-0">
                            <p class="font-bold text-ink-900 truncate">{{ $log->description }}</p>
                            <span class="text-[10px] text-ink-400 font-semibold block truncate">
                                {{ Carbon\Carbon::parse($log->date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                            </span>
                        </div>
                    </div>

                    <span class="chip-xp py-0.5 px-2.5 font-black text-xs shrink-0 tabular-nums">
                        +{{ $log->amount }} XP
                    </span>
                </div>
            @empty
                <p class="text-xs text-ink-500 py-4 text-center">Belum ada riwayat XP tercatat.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
