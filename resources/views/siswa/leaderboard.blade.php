@extends('layouts.siswa')

@section('title', 'Peringkat & Klasemen — SiHebat')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-4 sm:py-6 space-y-6">

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
    <div class="flex items-center gap-2 border-b border-ink-200">
        <a href="{{ route('siswa.leaderboard', ['tab' => 'kelas', 'period' => $period]) }}"
           class="py-2.5 px-4 font-black text-sm border-b-2 transition flex items-center gap-2 {{ $tab === 'kelas' ? 'border-primary-500 text-primary-900' : 'border-transparent text-ink-500 hover:text-ink-900' }}">
            <span>👦</span>
            <span>Peringkat Kelas ({{ $classroom ? $classroom->name : 'Kelasmu' }})</span>
        </a>
        <a href="{{ route('siswa.leaderboard', ['tab' => 'antar_kelas', 'period' => $period]) }}"
           class="py-2.5 px-4 font-black text-sm border-b-2 transition flex items-center gap-2 {{ $tab === 'antar_kelas' ? 'border-primary-500 text-primary-900' : 'border-transparent text-ink-500 hover:text-ink-900' }}">
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

            <div class="card p-5 sm:p-6 border border-slate-200 bg-gradient-to-b from-slate-100/90 via-slate-50 to-amber-50/20 shadow-card">
                <div class="text-center mb-5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-100 text-primary-900 text-[11px] font-black uppercase tracking-wider">
                        Top 3 Siswa Terhebat Kelas
                    </span>
                    <h3 class="text-base sm:text-lg font-black text-ink-900 mt-1">Podium Prestasi Kelas</h3>
                    <p class="text-xs text-ink-500 font-semibold mt-0.5">Penghargaan konsistensi pembiasaan karakter di kelasmu.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 items-end max-w-2xl mx-auto text-center pt-2">
                    {{-- Rank 2 (Silver) --}}
                    <div class="rounded-2xl border border-slate-300 bg-gradient-to-b from-slate-200/90 via-slate-100/90 to-slate-200/60 p-3.5 sm:p-4 shadow-sm relative overflow-hidden transition-all hover:-translate-y-0.5">
                        <div class="w-full h-1.5 bg-gradient-to-r from-slate-300 via-slate-400 to-slate-300 absolute top-0 inset-x-0"></div>
                        <span class="text-[10px] font-black text-slate-900 bg-slate-300/90 border border-slate-400/60 px-2.5 py-0.5 rounded-full inline-block uppercase tracking-wider">
                            #2 Perak
                        </span>

                        <div class="relative inline-block my-2">
                            <div class="avatar size-14 text-sm font-black border-2 border-slate-400 mx-auto bg-gradient-to-br from-slate-200 to-slate-300 text-slate-800 shadow-sm ring-2 ring-slate-200 overflow-hidden">
                                @if($second->avatar_url)
                                    <img src="{{ $second->avatar_url }}" alt="{{ $second->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($second->name, 0, 1) }}
                                @endif
                            </div>
                            <span class="absolute -bottom-1 -right-1 size-5 rounded-full bg-slate-700 border-2 border-white text-white font-black text-[10px] flex items-center justify-center shadow-xs">
                                2
                            </span>
                        </div>

                        <p class="text-xs font-black text-ink-900 truncate px-1" title="{{ $second->name }}">
                            {{ $second->name }}
                        </p>
                        <span class="text-[10px] font-bold text-slate-700 bg-white/80 border border-slate-200 px-2 py-0.5 rounded-full inline-block mt-0.5">
                            Level {{ $second->level ?? 1 }}
                        </span>

                        <div class="grid grid-cols-2 gap-1.5 mt-2.5 pt-2 border-t border-slate-300/60 text-left">
                            <div class="bg-white/85 rounded-lg p-1.5 border border-slate-200 text-center">
                                <span class="text-[8px] font-black uppercase text-slate-600 block">Streak</span>
                                <span class="text-xs font-black text-slate-900 tabular-nums">{{ $second->current_streak }}h</span>
                            </div>
                            <div class="bg-white/85 rounded-lg p-1.5 border border-slate-200 text-center">
                                <span class="text-[8px] font-black uppercase text-slate-600 block">Total XP</span>
                                <span class="text-xs font-black text-slate-900 tabular-nums">{{ number_format($second->xp) }}</span>
                            </div>
                        </div>

                        <div class="mt-2.5 py-1 rounded-lg bg-slate-300/90 text-slate-800 font-extrabold text-[10px] uppercase tracking-wider border border-slate-400/40">
                            Peringkat 2
                        </div>
                    </div>

                    {{-- Rank 1 (Gold - Center & Elevated) --}}
                    <div class="rounded-2xl border-2 border-amber-400 bg-gradient-to-b from-amber-100/90 via-amber-50/90 to-amber-100/60 p-4 sm:p-5 shadow-lg shadow-amber-500/15 relative overflow-hidden sm:-mt-4 z-10 transition-all hover:-translate-y-1">
                        <div class="w-full h-2 bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-400 absolute top-0 inset-x-0"></div>
                        <span class="text-[10px] font-black text-amber-950 bg-amber-400 px-3 py-1 rounded-full inline-flex items-center gap-1 uppercase tracking-wider shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-3" fill="currentColor" viewBox="0 0 256 256"><path d="M232,64H208V48a8,8,0,0,0-8-8H56a8,8,0,0,0-8,8V64H24A16,16,0,0,0,8,80v32a56.06,56.06,0,0,0,56,56h1.77A64.1,64.1,0,0,0,120,211.82V232H88a8,8,0,0,0,0,16h80a8,8,0,0,0,0-16H136V211.82A64.1,64.1,0,0,0,190.23,168H192a56.06,56.06,0,0,0,56-56V80A16,16,0,0,0,232,64ZM40,112V80H48v56a63.84,63.84,0,0,0,1,11.11A40.34,40.34,0,0,1,40,112ZM192,160a48,48,0,0,1-96,0V56H192Zm24-48a40.34,40.34,0,0,1-9,35.11,63.84,63.84,0,0,0,1-11.11V80h8Z"/></svg>
                            #1 Emas
                        </span>

                        <div class="relative inline-block my-2">
                            <div class="avatar size-16 text-base font-black border-2 border-amber-400 mx-auto bg-gradient-to-br from-amber-200 to-amber-300 text-amber-950 shadow-md ring-4 ring-amber-300/60 overflow-hidden">
                                @if($first->avatar_url)
                                    <img src="{{ $first->avatar_url }}" alt="{{ $first->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($first->name, 0, 1) }}
                                @endif
                            </div>
                            <span class="absolute -bottom-1 -right-1 size-6 rounded-full bg-amber-500 border-2 border-white text-white font-black text-xs flex items-center justify-center shadow-xs">
                                1
                            </span>
                        </div>

                        <p class="text-xs sm:text-sm font-black text-ink-900 truncate px-1" title="{{ $first->name }}">
                            {{ $first->name }}
                        </p>
                        <span class="text-[10px] font-black text-amber-950 bg-amber-200/90 border border-amber-300 px-2.5 py-0.5 rounded-full inline-block mt-0.5">
                            Level {{ $first->level ?? 1 }} · Bintang Kelas
                        </span>

                        <div class="grid grid-cols-2 gap-1.5 mt-3 pt-2.5 border-t border-amber-300/60 text-left">
                            <div class="bg-white/85 rounded-lg p-2 border border-amber-200 text-center">
                                <span class="text-[8px] font-black uppercase text-amber-800 block">Streak</span>
                                <span class="text-sm font-black text-amber-950 tabular-nums">{{ $first->current_streak }}h</span>
                            </div>
                            <div class="bg-white/85 rounded-lg p-2 border border-amber-200 text-center">
                                <span class="text-[8px] font-black uppercase text-amber-800 block">Total XP</span>
                                <span class="text-sm font-black text-amber-950 tabular-nums">{{ number_format($first->xp) }}</span>
                            </div>
                        </div>

                        <div class="mt-3 py-1.5 rounded-lg bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-400 text-amber-950 font-black text-xs uppercase tracking-wider border border-amber-400 shadow-xs">
                            Juara Kelas Teratas
                        </div>
                    </div>

                    {{-- Rank 3 (Bronze) --}}
                    <div class="rounded-2xl border border-amber-600/40 bg-gradient-to-b from-orange-100/90 via-amber-100/60 to-orange-100/50 p-3.5 sm:p-4 shadow-sm relative overflow-hidden transition-all hover:-translate-y-0.5">
                        <div class="w-full h-1.5 bg-gradient-to-r from-amber-600/40 via-amber-700/60 to-amber-600/40 absolute top-0 inset-x-0"></div>
                        <span class="text-[10px] font-black text-amber-950 bg-amber-200/90 border border-amber-400 px-2.5 py-0.5 rounded-full inline-block uppercase tracking-wider">
                            #3 Perunggu
                        </span>

                        <div class="relative inline-block my-2">
                            <div class="avatar size-14 text-sm font-black border-2 border-amber-600/40 mx-auto bg-gradient-to-br from-amber-200/90 to-orange-200 text-amber-950 shadow-sm ring-2 ring-orange-200/60 overflow-hidden">
                                @if($third->avatar_url)
                                    <img src="{{ $third->avatar_url }}" alt="{{ $third->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($third->name, 0, 1) }}
                                @endif
                            </div>
                            <span class="absolute -bottom-1 -right-1 size-5 rounded-full bg-amber-800 border-2 border-white text-white font-black text-[10px] flex items-center justify-center shadow-xs">
                                3
                            </span>
                        </div>

                        <p class="text-xs font-black text-ink-900 truncate px-1" title="{{ $third->name }}">
                            {{ $third->name }}
                        </p>
                        <span class="text-[10px] font-bold text-amber-900 bg-white/80 border border-amber-200 px-2 py-0.5 rounded-full inline-block mt-0.5">
                            Level {{ $third->level ?? 1 }}
                        </span>

                        <div class="grid grid-cols-2 gap-1.5 mt-2.5 pt-2 border-t border-amber-300/60 text-left">
                            <div class="bg-white/85 rounded-lg p-1.5 border border-amber-200 text-center">
                                <span class="text-[8px] font-black uppercase text-amber-900 block">Streak</span>
                                <span class="text-xs font-black text-amber-950 tabular-nums">{{ $third->current_streak }}h</span>
                            </div>
                            <div class="bg-white/85 rounded-lg p-1.5 border border-amber-200 text-center">
                                <span class="text-[8px] font-black uppercase text-amber-900 block">Total XP</span>
                                <span class="text-xs font-black text-amber-950 tabular-nums">{{ number_format($third->xp) }}</span>
                            </div>
                        </div>

                        <div class="mt-2.5 py-1 rounded-lg bg-amber-200/90 text-amber-950 font-extrabold text-[10px] uppercase tracking-wider border border-amber-300">
                            Peringkat 3
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
