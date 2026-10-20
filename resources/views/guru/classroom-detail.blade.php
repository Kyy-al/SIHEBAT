@extends('layouts.admin')

@section('title', 'Detail Kelas ' . $classroom->name . ' — SiHebat')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Breadcrumb & Header --}}
    <div class="flex items-center gap-2 text-xs font-semibold text-ink-500">
        <a href="{{ route('guru.dashboard') }}" class="hover:text-primary-700">Dashboard</a>
        <span>/</span>
        <span class="text-ink-900 font-bold">Kelas {{ $classroom->name }}</span>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">
                Daftar Siswa Kelas {{ $classroom->name }}
            </h1>
            <p class="text-sm font-semibold text-ink-500 mt-1">
                Tingkat {{ $classroom->grade }} · Tahun Ajaran {{ $classroom->academicYear->full_name ?? '-' }} · {{ count($students) }} Siswa
            </p>
        </div>

        <a href="{{ route('guru.dashboard') }}" class="btn-soft text-sm self-start sm:self-auto">
            ← Kembali ke Ringkasan
        </a>
    </div>

    {{-- Student Table Card --}}
    <div class="card p-5 sm:p-6 border border-ink-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-ink-100 text-xs font-extrabold text-ink-500 uppercase tracking-wider">
                        <th class="py-3 px-3">#</th>
                        <th class="py-3 px-3">Nama Siswa</th>
                        <th class="py-3 px-3">NIS</th>
                        <th class="py-3 px-3 text-center">Streak</th>
                        <th class="py-3 px-3 text-center">Total XP</th>
                        <th class="py-3 px-3 text-center">Level</th>
                        <th class="py-3 px-3 text-right">Terakhir Mengisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100 text-sm">
                    @forelse($students as $index => $st)
                        <tr class="hover:bg-primary-50/50 transition-colors">
                            <td class="py-3 px-3 font-bold text-ink-500 tabular-nums">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="avatar size-8 text-xs font-extrabold shrink-0">
                                        {{ substr($st->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-ink-900">{{ $st->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 font-semibold text-ink-700 tabular-nums">
                                {{ $st->nis }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="chip-streak text-xs py-0.5 px-2.5">
                                    🔥 {{ $st->current_streak }} hari
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center font-extrabold text-amber-700 tabular-nums">
                                {{ number_format($st->xp) }} XP
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="chip bg-lavender-soft text-purple-800 text-xs py-0.5 px-2">
                                    Lvl {{ $st->level }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right font-medium text-xs text-ink-500">
                                @if($st->last_journal_date)
                                    {{ $st->last_journal_date->locale('id')->isoFormat('D MMM Y') }}
                                @else
                                    <span class="text-coral font-semibold">Belum pernah</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-ink-500 font-semibold">
                                Belum ada siswa yang terdaftar di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
