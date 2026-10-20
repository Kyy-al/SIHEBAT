@extends('layouts.siswa')

@section('title', 'Peringkat Kelas — SiHebat')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-4 sm:py-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">
                Peringkat Kelas 🏆
            </h1>
            <p class="text-sm font-semibold text-ink-500">
                {{ $user->currentClassroom() ? 'Kelas ' . $user->currentClassroom()->name : 'Kelasmu' }} · Konsistensi kebiasaan hebat
            </p>
        </div>

        {{-- My rank pill --}}
        <div class="chip bg-primary-100 text-ink-900 border border-primary-300 py-2 px-4 shadow-sm self-start sm:self-auto">
            <span>Posisi Kamu:</span>
            <span class="text-base font-extrabold text-primary-800 tabular-nums">#{{ $userRank }}</span>
            <span class="text-xs text-ink-500 font-semibold">dari {{ count($students) }} siswa</span>
        </div>
    </div>

    {{-- Top 3 Podium (if >= 3 students) --}}
    @if(count($students) >= 3)
        @php
            $first = $students[0];
            $second = $students[1];
            $third = $students[2];
        @endphp

        <div class="card p-6 border border-ink-100 bg-gradient-to-b from-primary-50 to-white">
            <div class="grid grid-cols-3 gap-2 sm:gap-4 items-end max-w-lg mx-auto text-center pt-4">

                {{-- Rank 2 (Silver) --}}
                <div class="flex flex-col items-center">
                    <span class="text-xl sm:text-2xl">🥈</span>
                    <div class="avatar shrink-0 size-14 sm:size-16 text-base font-extrabold border-4 border-[#C5D3E0] shadow-md my-1.5 bg-white">
                        {{ substr($second->name, 0, 1) }}
                    </div>
                    <p class="text-xs sm:text-sm font-bold text-ink-900 truncate max-w-[100px] sm:max-w-[120px]">
                        {{ explode(' ', $second->name)[0] }}
                    </p>
                    <span class="chip bg-[#EAF1F8] text-ink-700 text-[10px] sm:text-xs font-extrabold mt-1">
                        {{ $second->xp }} XP
                    </span>
                    <div class="w-full h-16 sm:h-20 bg-[#C5D3E0]/40 rounded-t-2xl mt-2 flex items-center justify-center font-extrabold text-ink-700 text-lg">
                        #2
                    </div>
                </div>

                {{-- Rank 1 (Gold) --}}
                <div class="flex flex-col items-center">
                    <span class="text-3xl sm:text-4xl animate-bounce">👑</span>
                    <div class="avatar shrink-0 size-18 sm:size-20 text-lg font-extrabold border-4 border-sun shadow-pop my-1.5 bg-white">
                        {{ substr($first->name, 0, 1) }}
                    </div>
                    <p class="text-xs sm:text-sm font-extrabold text-ink-900 truncate max-w-[100px] sm:max-w-[120px]">
                        {{ explode(' ', $first->name)[0] }}
                    </p>
                    <span class="chip bg-sun-soft text-amber-800 text-[10px] sm:text-xs font-extrabold mt-1">
                        {{ $first->xp }} XP
                    </span>
                    <div class="w-full h-24 sm:h-28 bg-sun/30 rounded-t-2xl mt-2 flex items-center justify-center font-extrabold text-ink-900 text-xl border-t-2 border-sun">
                        🥇 #1
                    </div>
                </div>

                {{-- Rank 3 (Bronze) --}}
                <div class="flex flex-col items-center">
                    <span class="text-xl sm:text-2xl">🥉</span>
                    <div class="avatar shrink-0 size-14 sm:size-16 text-base font-extrabold border-4 border-[#F2A66B] shadow-md my-1.5 bg-white">
                        {{ substr($third->name, 0, 1) }}
                    </div>
                    <p class="text-xs sm:text-sm font-bold text-ink-900 truncate max-w-[100px] sm:max-w-[120px]">
                        {{ explode(' ', $third->name)[0] }}
                    </p>
                    <span class="chip bg-[#FFE6DF] text-coral text-[10px] sm:text-xs font-extrabold mt-1">
                        {{ $third->xp }} XP
                    </span>
                    <div class="w-full h-12 sm:h-16 bg-[#F2A66B]/30 rounded-t-2xl mt-2 flex items-center justify-center font-extrabold text-ink-700 text-base">
                        #3
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Full Leaderboard Table Card --}}
    <div class="card p-4 sm:p-6 border border-ink-100">
        <h3 class="text-base font-extrabold text-ink-900 mb-4">Daftar Peringkat</h3>

        <div class="space-y-2">
            @foreach($students as $index => $student)
                @php
                    $isMe = $student->id === $user->id;
                    $rank = $index + 1;
                @endphp

                <div class="flex items-center justify-between p-3.5 rounded-2xl transition-colors {{ $isMe ? 'bg-primary-100 border-2 border-primary-400' : 'bg-surface hover:bg-primary-50/50 border border-ink-100/60' }}">
                    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                        <span class="w-7 text-center font-extrabold text-sm sm:text-base tabular-nums {{ $rank <= 3 ? 'text-ink-900 font-black' : 'text-ink-500' }}">
                            @if($rank === 1) 🥇
                            @elseif($rank === 2) 🥈
                            @elseif($rank === 3) 🥉
                            @else #{{ $rank }}
                            @endif
                        </span>

                        <div class="avatar size-10 text-xs shrink-0 font-extrabold">
                            {{ substr($student->name, 0, 1) }}
                        </div>

                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-bold text-ink-900 truncate">
                                    {{ $student->name }}
                                </p>
                                @if($isMe)
                                    <span class="chip bg-primary-400 text-ink-900 text-[10px] py-0 px-2 font-extrabold">
                                        Kamu
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 text-xs text-ink-500 font-semibold mt-0.5">
                                <span class="flex items-center gap-0.5">
                                    🔥 {{ $student->current_streak }} hari
                                </span>
                                <span>·</span>
                                <span>Lvl {{ $student->level }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="text-base sm:text-lg font-extrabold text-ink-900 tabular-nums">{{ number_format($student->xp) }}</span>
                        <span class="text-xs font-bold text-amber-600 block sm:inline">XP</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Mode Privasi Info --}}
        <div class="mt-4 pt-3 border-t border-ink-100 flex items-center justify-between text-xs text-ink-500 font-medium">
            <span>🔒 Mode ramah siswa: peringkat fokus pada apresiasi konsistensi kebiasaan.</span>
        </div>
    </div>
</div>
@endsection
