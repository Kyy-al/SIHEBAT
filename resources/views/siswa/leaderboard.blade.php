@extends('layouts.siswa')

@section('title', 'Peringkat & Klasemen — SiHebat')

@section('content')
<div class="max-w-4xl mx-auto px-3.5 sm:px-4 py-4 sm:py-6 space-y-6 w-full max-w-full overflow-x-hidden">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-ink-900 tracking-tight flex items-center gap-2">
                <span>Papan Peringkat & Prestasi</span>
                <span>🏆</span>
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-ink-500 mt-0.5">
                Apresiasi konsistensi pembiasaan 7 Kebiasaan Anak Indonesia Hebat.
            </p>
        </div>

        {{-- Period Filter Pills (Mingguan, Bulanan, Semester) --}}
        <div class="flex items-center gap-1.5 self-start sm:self-auto bg-white p-1 rounded-2xl border border-ink-100 shadow-xs">
            @foreach(['mingguan' => 'Mingguan', 'bulanan' => 'Bulanan', 'semester' => 'Semester'] as $pkey => $plabel)
                <a href="{{ route('siswa.leaderboard', ['tab' => $tab, 'period' => $pkey]) }}"
                   class="px-3 py-1 rounded-xl text-xs font-bold transition {{ $period === $pkey ? 'bg-primary-400 text-ink-900 shadow-sm' : 'text-ink-600 hover:text-ink-900' }}">
                    {{ $plabel }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Tabs (Peringkat Kelas vs Peringkat Antar-Kelas) --}}
    <div class="flex items-center gap-2 border-b border-ink-200 overflow-x-auto scrollbar-none pb-0.5">
        <a href="{{ route('siswa.leaderboard', ['tab' => 'kelas', 'period' => $period]) }}"
           class="py-2.5 px-3 sm:px-4 font-black text-xs sm:text-sm border-b-2 transition flex items-center gap-2 shrink-0 {{ $tab === 'kelas' ? 'border-primary-500 text-primary-900' : 'border-transparent text-ink-500 hover:text-ink-900' }}">
            <span>👦</span>
            <span>Peringkat Kelas ({{ $classroom ? $classroom->name : 'Kelasmu' }})</span>
        </a>
        <a href="{{ route('siswa.leaderboard', ['tab' => 'antar_kelas', 'period' => $period]) }}"
           class="py-2.5 px-3 sm:px-4 font-black text-xs sm:text-sm border-b-2 transition flex items-center gap-2 shrink-0 {{ $tab === 'antar_kelas' ? 'border-primary-500 text-primary-900' : 'border-transparent text-ink-500 hover:text-ink-900' }}">
            <span>🏫</span>
            <span>Peringkat Antar-Kelas (% Adil)</span>
        </a>
    </div>

    @if($tab === 'kelas')
        {{-- TAB 1: INDIVIDU KELAS --}}
        
        {{-- My Rank Sticky Pill --}}
        <div class="p-4 rounded-3xl bg-primary-100 border border-primary-300 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <div class="avatar size-11 ring-2 ring-primary-400 font-black text-base overflow-hidden">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        {{ substr($user->name, 0, 1) }}
                    @endif
                </div>
                <div>
                    <h4 class="font-black text-sm text-ink-900 leading-tight">{{ $user->name }}</h4>
                    <span class="text-xs font-semibold text-primary-800">
                        🔥 {{ $user->current_streak }} hari streak · Level {{ $user->level }}
                    </span>
                </div>
            </div>

            <div class="text-right">
                <span class="text-xs font-bold text-ink-500 block uppercase">Posisi Kamu</span>
                <span class="text-2xl font-black text-primary-950 tabular-nums">#{{ $userRank }}</span>
                <span class="text-[10px] text-ink-500 font-semibold block">dari {{ count($students) }} siswa</span>
            </div>
        </div>

        {{-- Top 3 Podium (If >= 3 students) --}}
        @if(count($students) >= 3)
            @php
                $first = $students[0];
                $second = $students[1];
                $third = $students[2];
            @endphp

            <div class="card p-3.5 sm:p-5 border border-amber-200/80 bg-gradient-to-b from-amber-50/60 via-white to-primary-50/20 shadow-card overflow-hidden">
                <div class="text-center mb-3">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-primary-100 text-primary-900 text-[10px] font-black uppercase tracking-wider">
                        Top 3 Siswa Terhebat Kelas 🏆
                    </span>
                    <h3 class="text-sm sm:text-base font-black text-ink-900 mt-1">Podium Prestasi Kelas</h3>
                </div>

                {{-- Horizontal 3-Column Podium (Always side-by-side: 2 - 1 - 3, compact & neat) --}}
                <div class="grid grid-cols-3 gap-1.5 sm:gap-3 items-end max-w-md sm:max-w-lg mx-auto text-center pt-2">
                    
                    {{-- 2nd Place (Silver - Left) --}}
                    <div class="flex flex-col items-center min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-black text-slate-800 bg-slate-200/90 border border-slate-300 px-1.5 sm:px-2 py-0.5 rounded-full inline-block uppercase tracking-wider mb-1">
                            #2 Perak
                        </span>

                        <div class="relative inline-block my-1">
                            <div class="avatar size-10 sm:size-13 text-xs sm:text-sm font-black border-2 border-slate-300 mx-auto bg-slate-200 text-slate-800 shadow-xs ring-2 ring-slate-100 overflow-hidden">
                                @if($second->avatar_url)
                                    <img src="{{ $second->avatar_url }}" alt="{{ $second->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($second->name, 0, 1) }}
                                @endif
                            </div>
                            <span class="absolute -bottom-1 -right-1 size-4 sm:size-5 rounded-full bg-slate-600 border border-white text-white font-black text-[9px] sm:text-[10px] flex items-center justify-center">
                                2
                            </span>
                        </div>

                        <p class="text-[11px] sm:text-xs font-black text-ink-900 truncate w-full px-0.5" title="{{ $second->name }}">
                            {{ $second->name }}
                        </p>
                        <span class="text-[9px] font-bold text-slate-600">
                            Lvl {{ $second->level ?? 1 }}
                        </span>

                        {{-- Pedestal Step --}}
                        <div class="w-full mt-1.5 pt-2 pb-2 px-1 rounded-t-2xl bg-gradient-to-b from-slate-200 via-slate-100 to-slate-200/70 border-t-2 border-x-2 border-slate-300 shadow-xs flex flex-col items-center justify-center h-20 sm:h-24">
                            <span class="text-base sm:text-xl font-black text-slate-700 leading-none">2</span>
                            <span class="text-[10px] sm:text-xs font-black text-slate-900 tabular-nums mt-1 leading-tight">
                                {{ number_format($second->xp) }} <span class="text-[8px] sm:text-[9px] text-slate-600 font-bold">XP</span>
                            </span>
                            <span class="text-[9px] sm:text-[10px] font-extrabold text-orange-600 tabular-nums mt-0.5 leading-tight">
                                🔥 {{ $second->current_streak }}h
                            </span>
                        </div>
                    </div>

                    {{-- 1st Place (Gold - Center & Elevated) --}}
                    <div class="flex flex-col items-center min-w-0 -mt-2">
                        <span class="text-[9px] sm:text-[10px] font-black text-amber-950 bg-amber-400 border border-amber-500/50 px-2 sm:px-2.5 py-0.5 rounded-full inline-flex items-center gap-0.5 uppercase tracking-wider mb-1 shadow-xs animate-pop">
                            👑 #1 Emas
                        </span>

                        <div class="relative inline-block my-1">
                            <div class="avatar size-13 sm:size-16 text-sm sm:text-base font-black border-2 border-amber-400 mx-auto bg-amber-200 text-amber-950 shadow-md ring-3 sm:ring-4 ring-amber-300/70 overflow-hidden">
                                @if($first->avatar_url)
                                    <img src="{{ $first->avatar_url }}" alt="{{ $first->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($first->name, 0, 1) }}
                                @endif
                            </div>
                            <span class="absolute -bottom-1 -right-1 size-5 sm:size-6 rounded-full bg-amber-500 border-2 border-white text-white font-black text-[10px] sm:text-xs flex items-center justify-center shadow-xs">
                                1
                            </span>
                        </div>

                        <p class="text-[11px] sm:text-xs font-black text-ink-900 truncate w-full px-0.5" title="{{ $first->name }}">
                            {{ $first->name }}
                        </p>
                        <span class="text-[9px] font-black text-amber-950 bg-amber-200/80 px-1.5 py-0.2 rounded-md">
                            Lvl {{ $first->level ?? 1 }}
                        </span>

                        {{-- Pedestal Step --}}
                        <div class="w-full mt-1.5 pt-2 pb-2.5 px-1 rounded-t-2xl bg-gradient-to-b from-amber-300 via-amber-200 to-amber-300/80 border-t-2 border-x-2 border-amber-400 shadow-md flex flex-col items-center justify-center h-28 sm:h-32">
                            <span class="text-xl sm:text-2xl font-black text-amber-950 leading-none">1</span>
                            <span class="text-[11px] sm:text-sm font-black text-amber-950 tabular-nums mt-1 leading-tight">
                                {{ number_format($first->xp) }} <span class="text-[9px] sm:text-[10px] text-amber-800 font-bold">XP</span>
                            </span>
                            <span class="text-[10px] sm:text-xs font-black text-orange-600 tabular-nums mt-0.5 leading-tight">
                                🔥 {{ $first->current_streak }}h
                            </span>
                        </div>
                    </div>

                    {{-- 3rd Place (Bronze - Right) --}}
                    <div class="flex flex-col items-center min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-black text-amber-950 bg-amber-200/90 border border-amber-400 px-1.5 sm:px-2 py-0.5 rounded-full inline-block uppercase tracking-wider mb-1">
                            #3 Perunggu
                        </span>

                        <div class="relative inline-block my-1">
                            <div class="avatar size-10 sm:size-13 text-xs sm:text-sm font-black border-2 border-amber-600/40 mx-auto bg-orange-100 text-amber-950 shadow-xs ring-2 ring-orange-100 overflow-hidden">
                                @if($third->avatar_url)
                                    <img src="{{ $third->avatar_url }}" alt="{{ $third->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($third->name, 0, 1) }}
                                @endif
                            </div>
                            <span class="absolute -bottom-1 -right-1 size-4 sm:size-5 rounded-full bg-amber-800 border border-white text-white font-black text-[9px] sm:text-[10px] flex items-center justify-center">
                                3
                            </span>
                        </div>

                        <p class="text-[11px] sm:text-xs font-black text-ink-900 truncate w-full px-0.5" title="{{ $third->name }}">
                            {{ $third->name }}
                        </p>
                        <span class="text-[9px] font-bold text-amber-900">
                            Lvl {{ $third->level ?? 1 }}
                        </span>

                        {{-- Pedestal Step --}}
                        <div class="w-full mt-1.5 pt-2 pb-2 px-1 rounded-t-2xl bg-gradient-to-b from-orange-200/80 via-amber-100 to-orange-200/50 border-t-2 border-x-2 border-amber-300 shadow-xs flex flex-col items-center justify-center h-16 sm:h-20">
                            <span class="text-base sm:text-xl font-black text-amber-900 leading-none">3</span>
                            <span class="text-[10px] sm:text-xs font-black text-amber-950 tabular-nums mt-1 leading-tight">
                                {{ number_format($third->xp) }} <span class="text-[8px] sm:text-[9px] text-amber-800 font-bold">XP</span>
                            </span>
                            <span class="text-[9px] sm:text-[10px] font-extrabold text-orange-600 tabular-nums mt-0.5 leading-tight">
                                🔥 {{ $third->current_streak }}h
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        @endif

        {{-- Full Class Table with Privacy Mode (FR-LDB-05) --}}
        <div class="card p-4 sm:p-6 border border-ink-100 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-black text-ink-900">Daftar Peringkat Siswa Kelas</h3>
                    <p class="text-xs text-ink-500 font-semibold">Mode Privasi: Menampilkan Top 10 dan posisimu</p>
                </div>
                <span class="chip bg-primary-100 text-primary-800 text-[10px] font-black uppercase">Top 10 + Kamu</span>
            </div>

            <div class="space-y-2">
                @php
                    $displayStudents = $topStudents ?? $students->take(10);
                @endphp

                @foreach($displayStudents as $index => $st)
                    @php
                        $isMe = $st->id === $user->id;
                        $rank = $index + 1;
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-2xl transition {{ $isMe ? 'bg-primary-100 border-2 border-primary-400' : 'bg-surface hover:bg-canvas border border-ink-100/60' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-7 text-center font-black text-sm tabular-nums {{ $rank <= 3 ? 'text-ink-900' : 'text-ink-500' }}">
                                @if($rank === 1) 🥇
                                @elseif($rank === 2) 🥈
                                @elseif($rank === 3) 🥉
                                @else #{{ $rank }}
                                @endif
                            </span>

                            <div class="avatar size-9 text-xs shrink-0 font-black overflow-hidden">
                                @if($st->avatar_url)
                                    <img src="{{ $st->avatar_url }}" alt="{{ $st->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($st->name, 0, 1) }}
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-bold text-ink-900 truncate">
                                        {{ $st->name }}
                                    </p>
                                    @if($isMe)
                                        <span class="chip bg-primary-400 text-ink-900 text-[10px] py-0 px-2 font-black">
                                            Kamu
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 text-xs text-ink-500 font-semibold mt-0.5">
                                    <span>🔥 {{ $st->current_streak }} hari</span>
                                    <span>·</span>
                                    <span>Lvl {{ $st->level }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="text-base font-black text-ink-900 tabular-nums">{{ number_format($st->xp) }}</span>
                            <span class="text-xs font-bold text-amber-600">XP</span>
                        </div>
                    </div>
                @endforeach

                {{-- If student is outside Top 10, display divider and student's row only without leaking other ranks --}}
                @if(!empty($myStudentOutsideTop))
                    <div class="py-2 flex items-center justify-center gap-2 text-ink-400 text-xs font-bold">
                        <span class="h-px bg-ink-200 flex-1"></span>
                        <span class="px-2 bg-canvas rounded-full text-[11px] text-ink-500 font-semibold">··· Posisi Kamu di Luar Top 10 ···</span>
                        <span class="h-px bg-ink-200 flex-1"></span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl bg-primary-100 border-2 border-primary-400 shadow-sm">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-7 text-center font-black text-sm tabular-nums text-primary-900">
                                #{{ $userRank }}
                            </span>

                            <div class="avatar size-9 text-xs shrink-0 font-black overflow-hidden">
                                @if($myStudentOutsideTop->avatar_url)
                                    <img src="{{ $myStudentOutsideTop->avatar_url }}" alt="{{ $myStudentOutsideTop->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($myStudentOutsideTop->name, 0, 1) }}
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-bold text-ink-900 truncate">
                                        {{ $myStudentOutsideTop->name }}
                                    </p>
                                    <span class="chip bg-primary-400 text-ink-900 text-[10px] py-0 px-2 font-black">
                                        Kamu
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-ink-500 font-semibold mt-0.5">
                                    <span>🔥 {{ $myStudentOutsideTop->current_streak }} hari</span>
                                    <span>·</span>
                                    <span>Lvl {{ $myStudentOutsideTop->level }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="text-base font-black text-ink-900 tabular-nums">{{ number_format($myStudentOutsideTop->xp) }}</span>
                            <span class="text-xs font-bold text-amber-600">XP</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    @else
        {{-- TAB 2: PERINGKAT ANTAR-KELAS (% BERKEADILAN SESUAI PRD FR-LDB-02) --}}
        <div class="card p-5 sm:p-6 border border-ink-100 shadow-card space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-ink-900">
                        Klasemen Ketercapaian Antar-Kelas 🏫
                    </h3>
                    <p class="text-xs text-ink-500 font-semibold">
                        Penilaian adil berdasarkan persentase partisipasi jurnal (40%) dan pemenuhan 7 kebiasaan (60%).
                    </p>
                </div>
                <span class="chip bg-mint-soft text-[#0B6B47] text-xs font-black">
                    ✓ Skema Adil untuk Kelas Kecil & Besar
                </span>
            </div>

            <div class="overflow-x-auto pt-2">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-ink-100 text-xs font-extrabold text-ink-500 uppercase tracking-wider">
                            <th class="py-3 px-3 text-center">Peringkat</th>
                            <th class="py-3 px-3">Nama Rombel Kelas</th>
                            <th class="py-3 px-3">Wali Kelas</th>
                            <th class="py-3 px-3 text-center">Partisipasi</th>
                            <th class="py-3 px-3 text-center">Kebiasaan Tuntas</th>
                            <th class="py-3 px-3 text-right">Skor Prestasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100">
                        @forelse($interClassRankings as $idx => $item)
                            @php
                                $cls = $item['classroom'];
                                $isMyClass = $user->currentClassroom() && $user->currentClassroom()->id === $cls->id;
                                $rank = $idx + 1;
                            @endphp
                            <tr class="{{ $isMyClass ? 'bg-primary-50/80 font-bold border-l-4 border-primary-500' : 'hover:bg-primary-50/30' }} transition">
                                <td class="py-3.5 px-3 text-center font-black text-sm tabular-nums">
                                    @if($rank === 1) 🥇 #1
                                    @elseif($rank === 2) 🥈 #2
                                    @elseif($rank === 3) 🥉 #3
                                    @else #{{ $rank }}
                                    @endif
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-black text-ink-900">{{ $cls->name }}</span>
                                        @if($isMyClass)
                                            <span class="chip bg-primary-400 text-ink-900 text-[10px] py-0 px-2 font-black">Kelasku</span>
                                        @endif
                                    </div>
                                    <span class="text-[11px] text-ink-500 font-semibold block">{{ $item['total_students'] }} siswa terdaftar</span>
                                </td>
                                <td class="py-3.5 px-3 text-xs text-ink-700 font-semibold">
                                    {{ $cls->homeroomTeacher ? $cls->homeroomTeacher->name : '-' }}
                                </td>
                                <td class="py-3.5 px-3 text-center font-extrabold text-primary-700 tabular-nums">
                                    {{ $item['participation_rate'] }}%
                                </td>
                                <td class="py-3.5 px-3 text-center font-extrabold text-emerald-700 tabular-nums">
                                    {{ $item['habit_rate'] }}%
                                </td>
                                <td class="py-3.5 px-3 text-right font-black text-amber-700 text-base tabular-nums">
                                    {{ $item['composite_score'] }} poin
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-ink-500 font-semibold">
                                    Belum ada data aktivitas jurnal untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
