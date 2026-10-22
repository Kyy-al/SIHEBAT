@extends('layouts.admin')

@section('title', 'Papan Peringkat & Streak Siswa — SiHebat')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-ink-900 tracking-tight">
                Papan Peringkat & Streak Siswa
            </h1>
            <p class="text-sm font-semibold text-ink-500 mt-1">
                Pantau peringkat keaktifan, streak api harian, dan akumulasi XP siswa di seluruh kelas.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="btn-soft text-xs font-extrabold py-2 px-4 self-start sm:self-auto">
            ← Kembali ke Dashboard
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="card p-4 sm:p-5 border border-ink-100 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.leaderboard') }}" class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-ink-500">Filter Rombel:</span>
            <a href="{{ route('admin.leaderboard') }}"
               class="chip {{ !$selectedClass ? 'bg-primary-400 text-ink-900 font-black' : 'bg-canvas text-ink-600 hover:bg-ink-100' }} text-xs py-1.5 px-3 rounded-full transition">
                Semua Kelas
            </a>
            @foreach($classrooms as $cls)
                <a href="{{ route('admin.leaderboard', ['classroom_id' => $cls->id]) }}"
                   class="chip {{ $selectedClass == $cls->id ? 'bg-primary-400 text-ink-900 font-black' : 'bg-canvas text-ink-600 hover:bg-ink-100' }} text-xs py-1.5 px-3 rounded-full transition">
                    {{ $cls->name }}
                </a>
            @endforeach
        </form>

        <span class="text-xs font-bold text-ink-500">
            Total Siswa Ditampilkan: <strong class="text-ink-900">{{ $students->total() }}</strong>
        </span>
    </div>

    {{-- TOP 3 PODIUM JUARA (Only on Page 1) --}}
    @if($students->currentPage() === 1 && $students->count() > 0)
        @php
            $first = $students->get(0);
            $second = $students->get(1);
            $third = $students->get(2);
        @endphp

        <div class="card p-6 sm:p-8 border border-slate-200 shadow-card bg-gradient-to-b from-slate-100/90 via-slate-50 to-amber-50/30">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-100 text-primary-900 text-xs font-black uppercase tracking-wider">
                        Tiga Besar Siswa Paling Konsisten
                    </span>
                    <h3 class="text-lg sm:text-xl font-black text-ink-900 mt-1.5">Podium Kehormatan 7 Kebiasaan</h3>
                    <p class="text-xs text-ink-500 font-semibold mt-0.5">Penetapan prestasi otomatis berdasarkan konsistensi streak harian dan akumulasi poin kebiasaan.</p>
                </div>

                {{-- Podium Grid: 2 (Left), 1 (Center - Elevated), 3 (Right) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end pt-2">
                    
                    {{-- PODIUM #2: JUARA 2 (PERAK) --}}
                    <div class="rounded-3xl border border-slate-300 bg-gradient-to-b from-slate-200/90 via-slate-100/90 to-slate-200/60 p-4 sm:p-5 shadow-md text-center relative overflow-hidden transition-all hover:-translate-y-0.5">
                        <div class="w-full h-1.5 bg-gradient-to-r from-slate-300 via-slate-400 to-slate-300 absolute top-0 inset-x-0"></div>
                        @if($second)
                            @php $cls2 = $second->currentClassroom(); @endphp
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-300/80 border border-slate-400/60 text-slate-900 text-xs font-black tracking-wider uppercase">
                                <span class="size-2 rounded-full bg-slate-500"></span> Juara 2 · Medali Perak
                            </div>

                            <div class="relative inline-block my-1">
                                <div class="size-16 rounded-2xl bg-gradient-to-br from-slate-200 to-slate-300 border-2 border-slate-400 text-slate-800 font-black text-xl flex items-center justify-center mx-auto shadow-sm ring-4 ring-slate-200 overflow-hidden">
                                    @if($second->avatar_url)
                                        <img src="{{ $second->avatar_url }}" alt="{{ $second->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($second->name, 0, 1) }}
                                    @endif
                                </div>
                                <span class="absolute -bottom-1 -right-1 size-6 rounded-full bg-slate-700 border-2 border-white text-white font-black text-[11px] flex items-center justify-center shadow-xs">
                                    2
                                </span>
                            </div>

                            <div>
                                <h4 class="font-black text-sm text-ink-900 truncate px-1" title="{{ $second->name }}">
                                    {{ $second->name }}
                                </h4>
                                <div class="flex items-center justify-center gap-1 flex-wrap mt-1">
                                    @if($cls2)
                                        <span class="text-[11px] font-extrabold text-slate-800 bg-slate-200/90 border border-slate-300 px-2.5 py-0.5 rounded-full">
                                            {{ $cls2->name }}
                                        </span>
                                    @endif
                                    <span class="text-[10px] font-bold text-slate-700 bg-white/80 border border-slate-200 px-2 py-0.5 rounded-full">
                                        Level {{ $second->level ?? 1 }}
                                    </span>
                                </div>
                            </div>

                            {{-- Micro-Stats Matrix --}}
                            <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-slate-300/60 text-left">
                                <div class="bg-white/85 rounded-xl p-2.5 border border-slate-200 shadow-xs">
                                    <div class="flex items-center gap-1 text-[9px] font-black uppercase text-slate-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3 text-amber-600" fill="currentColor" viewBox="0 0 256 256"><path d="M175.76,96.69a8,8,0,0,0-8.23.59C155.15,106.63,144,120.44,144,136a8,8,0,0,0,16,0c0-10.09,7.66-20.25,18.72-28.52a8,8,0,0,0,1.52-10.79ZM216,136c0,48.52-39.48,88-88,88s-88-39.48-88-88c0-30.82,15.61-59.5,41.74-76.71a8,8,0,0,1,11.83,3.74C100.86,81.44,112.56,96,128,96c21.84,0,38.85-15.93,42.75-39.75a8,8,0,0,1,12.54-5.33C201.76,64.39,216,99.28,216,136Z"/></svg>
                                        Streak
                                    </div>
                                    <div class="text-sm font-black text-slate-900 mt-0.5 tabular-nums">
                                        {{ $second->current_streak }} <span class="text-[10px] font-bold text-slate-600">Hari</span>
                                    </div>
                                    <div class="text-[9px] font-semibold text-slate-500">
                                        Rekor: {{ $second->longest_streak ?? $second->current_streak }}h
                                    </div>
                                </div>
                                <div class="bg-white/85 rounded-xl p-2.5 border border-slate-200 shadow-xs">
                                    <div class="flex items-center gap-1 text-[9px] font-black uppercase text-slate-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3 text-amber-500" fill="currentColor" viewBox="0 0 256 256"><path d="M239.2,97.29a16,16,0,0,0-13.81-11L166,81.17l-23.28-55.36a15.95,15.95,0,0,0-29.44,0L89.94,81.17,30.61,86.28a16,16,0,0,0-9.1,27.87l44.38,40.54-13.25,58.07a16,16,0,0,0,23.84,17.34L128,199.9l51.52,30.2a16,16,0,0,0,23.84-17.34l-13.25-58.07,44.38-40.54A16,16,0,0,0,239.2,97.29Z"/></svg>
                                        Total XP
                                    </div>
                                    <div class="text-sm font-black text-slate-900 mt-0.5 tabular-nums">
                                        {{ number_format($second->xp) }}
                                    </div>
                                    <div class="text-[9px] font-semibold text-slate-500">
                                        Poin Disiplin
                                    </div>
                                </div>
                            </div>

                            {{-- Pedestal Base Step --}}
                            <div class="mt-3 pt-2 pb-1.5 rounded-xl bg-slate-300/90 text-slate-800 font-extrabold text-[11px] uppercase tracking-wider flex items-center justify-center gap-1 border border-slate-400/40">
                                Konsistensi Perak
                            </div>
                        @else
                            <div class="text-xs text-ink-400 font-bold py-6">Belum Ada Data</div>
                        @endif
                    </div>

                    {{-- PODIUM #1: JUARA 1 (EMAS - ELEVATED) --}}
                    <div class="rounded-3xl border-2 border-amber-400 bg-gradient-to-b from-amber-100/90 via-amber-50/90 to-amber-100/60 p-5 sm:p-6 shadow-xl shadow-amber-500/15 text-center relative overflow-hidden md:-mt-5 z-10 transition-all hover:-translate-y-1">
                        <div class="w-full h-2 bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-400 absolute top-0 inset-x-0"></div>
                        @if($first)
                            @php $cls1 = $first->currentClassroom(); @endphp
                            <div class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-amber-400 text-amber-950 text-xs font-black tracking-wider uppercase shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="currentColor" viewBox="0 0 256 256"><path d="M232,64H208V48a8,8,0,0,0-8-8H56a8,8,0,0,0-8,8V64H24A16,16,0,0,0,8,80v32a56.06,56.06,0,0,0,56,56h1.77A64.1,64.1,0,0,0,120,211.82V232H88a8,8,0,0,0,0,16h80a8,8,0,0,0,0-16H136V211.82A64.1,64.1,0,0,0,190.23,168H192a56.06,56.06,0,0,0,56-56V80A16,16,0,0,0,232,64ZM40,112V80H48v56a63.84,63.84,0,0,0,1,11.11A40.34,40.34,0,0,1,40,112ZM192,160a48,48,0,0,1-96,0V56H192Zm24-48a40.34,40.34,0,0,1-9,35.11,63.84,63.84,0,0,0,1-11.11V80h8Z"/></svg>
                                Juara 1 · Medali Emas
                            </div>

                            <div class="relative inline-block my-1.5">
                                <div class="size-20 rounded-2xl bg-gradient-to-br from-amber-200 to-amber-300 border-2 border-amber-400 text-amber-950 font-black text-2xl flex items-center justify-center mx-auto shadow-md ring-4 ring-amber-300/60 overflow-hidden">
                                    @if($first->avatar_url)
                                        <img src="{{ $first->avatar_url }}" alt="{{ $first->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($first->name, 0, 1) }}
                                    @endif
                                </div>
                                <span class="absolute -bottom-1 -right-1 size-7 rounded-full bg-amber-500 border-2 border-white text-white font-black text-xs flex items-center justify-center shadow-sm">
                                    1
                                </span>
                            </div>

                            <div>
                                <h4 class="font-black text-base text-ink-900 truncate px-1" title="{{ $first->name }}">
                                    {{ $first->name }}
                                </h4>
                                <div class="flex items-center justify-center gap-1.5 flex-wrap mt-1">
                                    @if($cls1)
                                        <span class="text-xs font-black text-amber-950 bg-amber-200/90 border border-amber-300/80 px-3 py-0.5 rounded-full">
                                            {{ $cls1->name }}
                                        </span>
                                    @endif
                                    <span class="text-xs font-bold text-amber-900 bg-white/80 border border-amber-200 px-2.5 py-0.5 rounded-full">
                                        Level {{ $first->level ?? 1 }}
                                    </span>
                                </div>
                            </div>

                            {{-- Micro-Stats Matrix --}}
                            <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-amber-300/60 text-left">
                                <div class="bg-white/85 rounded-2xl p-2.5 border border-amber-200/80 shadow-xs">
                                    <div class="flex items-center gap-1 text-[10px] font-black uppercase text-amber-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 text-amber-600" fill="currentColor" viewBox="0 0 256 256"><path d="M175.76,96.69a8,8,0,0,0-8.23.59C155.15,106.63,144,120.44,144,136a8,8,0,0,0,16,0c0-10.09,7.66-20.25,18.72-28.52a8,8,0,0,0,1.52-10.79ZM216,136c0,48.52-39.48,88-88,88s-88-39.48-88-88c0-30.82,15.61-59.5,41.74-76.71a8,8,0,0,1,11.83,3.74C100.86,81.44,112.56,96,128,96c21.84,0,38.85-15.93,42.75-39.75a8,8,0,0,1,12.54-5.33C201.76,64.39,216,99.28,216,136Z"/></svg>
                                        Streak Aktif
                                    </div>
                                    <div class="text-base font-black text-amber-950 mt-0.5 tabular-nums">
                                        {{ $first->current_streak }} <span class="text-xs font-bold text-amber-700">Hari</span>
                                    </div>
                                    <div class="text-[10px] font-semibold text-amber-700/80">
                                        Rekor: {{ $first->longest_streak ?? $first->current_streak }}h
                                    </div>
                                </div>
                                <div class="bg-white/85 rounded-2xl p-2.5 border border-amber-200/80 shadow-xs">
                                    <div class="flex items-center gap-1 text-[10px] font-black uppercase text-amber-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 text-amber-500" fill="currentColor" viewBox="0 0 256 256"><path d="M239.2,97.29a16,16,0,0,0-13.81-11L166,81.17l-23.28-55.36a15.95,15.95,0,0,0-29.44,0L89.94,81.17,30.61,86.28a16,16,0,0,0-9.1,27.87l44.38,40.54-13.25,58.07a16,16,0,0,0,23.84,17.34L128,199.9l51.52,30.2a16,16,0,0,0,23.84-17.34l-13.25-58.07,44.38-40.54A16,16,0,0,0,239.2,97.29Z"/></svg>
                                        Total XP
                                    </div>
                                    <div class="text-base font-black text-amber-950 mt-0.5 tabular-nums">
                                        {{ number_format($first->xp) }}
                                    </div>
                                    <div class="text-[10px] font-semibold text-amber-700/80">
                                        Poin Pembiasaan
                                    </div>
                                </div>
                            </div>

                            {{-- Pedestal Base Step --}}
                            <div class="mt-3.5 pt-2.5 pb-2 rounded-2xl bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-400 text-amber-950 font-black text-xs uppercase tracking-wider flex items-center justify-center gap-1.5 shadow-xs border border-amber-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="currentColor" viewBox="0 0 256 256"><path d="M232,64H208V48a8,8,0,0,0-8-8H56a8,8,0,0,0-8,8V64H24A16,16,0,0,0,8,80v32a56.06,56.06,0,0,0,56,56h1.77A64.1,64.1,0,0,0,120,211.82V232H88a8,8,0,0,0,0,16h80a8,8,0,0,0,0-16H136V211.82A64.1,64.1,0,0,0,190.23,168H192a56.06,56.06,0,0,0,56-56V80A16,16,0,0,0,232,64ZM40,112V80H48v56a63.84,63.84,0,0,0,1,11.11A40.34,40.34,0,0,1,40,112ZM192,160a48,48,0,0,1-96,0V56H192Zm24-48a40.34,40.34,0,0,1-9,35.11,63.84,63.84,0,0,0,1-11.11V80h8Z"/></svg>
                                Teladan Tertinggi Sekolah
                            </div>
                        @else
                            <div class="text-xs text-ink-400 font-bold py-6">Belum Ada Data</div>
                        @endif
                    </div>

                    {{-- PODIUM #3: JUARA 3 (PERUNGGU) --}}
                    <div class="rounded-3xl border border-amber-600/35 bg-gradient-to-b from-orange-100/90 via-amber-100/60 to-orange-100/50 p-4 sm:p-5 shadow-md text-center relative overflow-hidden transition-all hover:-translate-y-0.5">
                        <div class="w-full h-1.5 bg-gradient-to-r from-amber-600/40 via-amber-700/60 to-amber-600/40 absolute top-0 inset-x-0"></div>
                        @if($third)
                            @php $cls3 = $third->currentClassroom(); @endphp
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-200/90 border border-amber-400 text-amber-950 text-xs font-black tracking-wider uppercase">
                                <span class="size-2 rounded-full bg-amber-700"></span> Juara 3 · Medali Perunggu
                            </div>

                            <div class="relative inline-block my-1">
                                <div class="size-16 rounded-2xl bg-gradient-to-br from-amber-200/90 to-orange-200 border-2 border-amber-600/40 text-amber-950 font-black text-xl flex items-center justify-center mx-auto shadow-sm ring-4 ring-orange-200/60 overflow-hidden">
                                    @if($third->avatar_url)
                                        <img src="{{ $third->avatar_url }}" alt="{{ $third->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($third->name, 0, 1) }}
                                    @endif
                                </div>
                                <span class="absolute -bottom-1 -right-1 size-6 rounded-full bg-amber-800 border-2 border-white text-white font-black text-[11px] flex items-center justify-center shadow-xs">
                                    3
                                </span>
                            </div>

                            <div>
                                <h4 class="font-black text-sm text-ink-900 truncate px-1" title="{{ $third->name }}">
                                    {{ $third->name }}
                                </h4>
                                <div class="flex items-center justify-center gap-1 flex-wrap mt-1">
                                    @if($cls3)
                                        <span class="text-[11px] font-extrabold text-amber-950 bg-amber-200/90 border border-amber-300 px-2.5 py-0.5 rounded-full">
                                            {{ $cls3->name }}
                                        </span>
                                    @endif
                                    <span class="text-[10px] font-bold text-amber-900 bg-white/80 border border-amber-200 px-2 py-0.5 rounded-full">
                                        Level {{ $third->level ?? 1 }}
                                    </span>
                                </div>
                            </div>

                            {{-- Micro-Stats Matrix --}}
                            <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-amber-300/60 text-left">
                                <div class="bg-white/85 rounded-xl p-2.5 border border-amber-200/80 shadow-xs">
                                    <div class="flex items-center gap-1 text-[9px] font-black uppercase text-amber-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3 text-amber-600" fill="currentColor" viewBox="0 0 256 256"><path d="M175.76,96.69a8,8,0,0,0-8.23.59C155.15,106.63,144,120.44,144,136a8,8,0,0,0,16,0c0-10.09,7.66-20.25,18.72-28.52a8,8,0,0,0,1.52-10.79ZM216,136c0,48.52-39.48,88-88,88s-88-39.48-88-88c0-30.82,15.61-59.5,41.74-76.71a8,8,0,0,1,11.83,3.74C100.86,81.44,112.56,96,128,96c21.84,0,38.85-15.93,42.75-39.75a8,8,0,0,1,12.54-5.33C201.76,64.39,216,99.28,216,136Z"/></svg>
                                        Streak
                                    </div>
                                    <div class="text-sm font-black text-amber-950 mt-0.5 tabular-nums">
                                        {{ $third->current_streak }} <span class="text-[10px] font-bold text-amber-800">Hari</span>
                                    </div>
                                    <div class="text-[9px] font-semibold text-amber-700/80">
                                        Rekor: {{ $third->longest_streak ?? $third->current_streak }}h
                                    </div>
                                </div>
                                <div class="bg-white/85 rounded-xl p-2.5 border border-amber-200/80 shadow-xs">
                                    <div class="flex items-center gap-1 text-[9px] font-black uppercase text-amber-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3 text-amber-500" fill="currentColor" viewBox="0 0 256 256"><path d="M239.2,97.29a16,16,0,0,0-13.81-11L166,81.17l-23.28-55.36a15.95,15.95,0,0,0-29.44,0L89.94,81.17,30.61,86.28a16,16,0,0,0-9.1,27.87l44.38,40.54-13.25,58.07a16,16,0,0,0,23.84,17.34L128,199.9l51.52,30.2a16,16,0,0,0,23.84-17.34l-13.25-58.07,44.38-40.54A16,16,0,0,0,239.2,97.29Z"/></svg>
                                        Total XP
                                    </div>
                                    <div class="text-sm font-black text-amber-950 mt-0.5 tabular-nums">
                                        {{ number_format($third->xp) }}
                                    </div>
                                    <div class="text-[9px] font-semibold text-amber-700/80">
                                        Poin Disiplin
                                    </div>
                                </div>
                            </div>

                            {{-- Pedestal Base Step --}}
                            <div class="mt-3 pt-2 pb-1.5 rounded-xl bg-amber-200/90 text-amber-950 font-extrabold text-[11px] uppercase tracking-wider flex items-center justify-center gap-1 border border-amber-300">
                                Konsistensi Perunggu
                            </div>
                        @else
                            <div class="text-xs text-ink-400 font-bold py-6">Belum Ada Data</div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- Leaderboard Table --}}
    <div class="card p-5 sm:p-6 border border-ink-100 shadow-card">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-ink-100 text-xs font-extrabold text-ink-500 uppercase tracking-wider">
                        <th class="py-3 px-3 text-center">Rank</th>
                        <th class="py-3 px-3">Nama Siswa</th>
                        <th class="py-3 px-3">NIS / NISN</th>
                        <th class="py-3 px-3">Kelas</th>
                        <th class="py-3 px-3 text-center">Streak Aktif</th>
                        <th class="py-3 px-3 text-center">Rekor Terpanjang</th>
                        <th class="py-3 px-3 text-center">Level</th>
                        <th class="py-3 px-3 text-right">XP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @forelse($students as $index => $st)
                        @php
                            $rankNum = ($students->currentPage() - 1) * $students->perPage() + $index + 1;
                            $medal = match($rankNum) {
                                1 => '🥇 #1',
                                2 => '🥈 #2',
                                3 => '🥉 #3',
                                default => '#' . $rankNum,
                            };
                            $rowBg = match($rankNum) {
                                1 => 'bg-amber-50/40 hover:bg-amber-50/70',
                                2 => 'bg-slate-50/60 hover:bg-slate-100/60',
                                3 => 'bg-orange-50/40 hover:bg-orange-50/70',
                                default => 'hover:bg-primary-50/50',
                            };
                            $cls = $st->currentClassroom();
                        @endphp
                        <tr class="{{ $rowBg }} transition">
                            <td class="py-3.5 px-3 text-center font-black text-xs tabular-nums text-ink-900">
                                @if($rankNum <= 3)
                                    <span class="inline-block px-2.5 py-1 rounded-full {{ $rankNum === 1 ? 'bg-amber-100 text-amber-900' : ($rankNum === 2 ? 'bg-slate-200 text-slate-800' : 'bg-orange-100 text-orange-900') }} font-black">
                                        {{ $medal }}
                                    </span>
                                @else
                                    <span class="text-ink-600 font-bold">{{ $medal }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="avatar size-9 text-xs font-extrabold shrink-0 overflow-hidden {{ $rankNum === 1 ? 'ring-2 ring-amber-400' : ($rankNum === 2 ? 'ring-2 ring-slate-300' : ($rankNum === 3 ? 'ring-2 ring-amber-600' : '')) }}">
                                        @if($st->avatar_url)
                                            <img src="{{ $st->avatar_url }}" alt="{{ $st->name }}" class="w-full h-full object-cover">
                                        @else
                                            {{ substr($st->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-ink-900">{{ $st->name }}</span>
                                        <span class="block text-[11px] text-ink-500 font-bold">
                                            {{ $st->gender === 'L' ? 'Laki-laki' : ($st->gender === 'P' ? 'Perempuan' : '-') }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-3 font-semibold text-ink-700 tabular-nums text-xs">
                                <div>{{ $st->nis ?? '-' }}</div>
                                @if($st->nisn)
                                    <div class="text-[10px] text-ink-400">NISN: {{ $st->nisn }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-3">
                                @if($cls)
                                    <span class="chip bg-primary-100 text-primary-800 text-xs font-extrabold">
                                        {{ $cls->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-ink-400">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <span class="chip-streak text-xs py-1 px-3 font-black">
                                    🔥 {{ $st->current_streak }} Hari
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-center font-bold text-xs text-ink-700 tabular-nums">
                                🏆 {{ $st->longest_streak }} Hari
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <span class="chip bg-lavender-soft text-purple-800 text-xs font-black">
                                    Lv. {{ $st->level }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-right font-black text-amber-700 tabular-nums text-base">
                                {{ number_format($st->xp) }} XP
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-ink-500 font-semibold">
                                Tidak ada data siswa pada filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="pt-4 mt-3 border-t border-ink-100">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
