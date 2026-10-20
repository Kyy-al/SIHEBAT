@extends('layouts.admin')

@section('title', 'Dashboard Wali Kelas — SiHebat')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">
                Halo, {{ $user->name }}! 👨‍🏫
            </h1>
            <p class="text-sm font-semibold text-ink-500 mt-1">
                @if($classroom)
                    Pantauan Jurnal 7 Kebiasaan Siswa Kelas <strong class="text-ink-900">{{ $classroom->name }}</strong>
                @else
                    Belum ada kelas yang diampu pada tahun ajaran aktif.
                @endif
            </p>
        </div>

        @if($classrooms->count() > 1)
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-ink-500">Pilih Kelas:</span>
                <div class="flex gap-1.5">
                    @foreach($classrooms as $cls)
                        <a href="{{ route('guru.classroom', $cls->id) }}"
                           class="chip {{ $classroom && $classroom->id === $cls->id ? 'bg-primary-400 text-ink-900 font-extrabold' : 'bg-white border border-ink-100 text-ink-700' }}">
                            {{ $cls->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @if(! $classroom)
        <div class="card p-8 text-center max-w-lg mx-auto">
            <x-mascot state="bingung" size="xl" />
            <h3 class="text-lg font-extrabold text-ink-900 mt-3">Belum Ada Kelas Terhubung</h3>
            <p class="text-sm font-semibold text-ink-500 mt-1">
                Akun guru ini belum ditugaskan sebagai wali kelas di tahun ajaran aktif. Hubungi administrator sekolah untuk mengatur kelasmu.
            </p>
        </div>
    @else

        {{-- Bento Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="card p-5 border border-ink-100">
                <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">Total Siswa</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl sm:text-4xl font-black text-ink-900 tabular-nums">{{ $stats['total_students'] }}</span>
                    <span class="text-xs font-bold text-ink-500">siswa terdaftar</span>
                </div>
            </div>

            <div class="card p-5 border border-ink-100">
                <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">Partisipasi Hari Ini</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl sm:text-4xl font-black text-primary-600 tabular-nums">{{ $stats['today_filled'] }}</span>
                    <span class="text-xs font-bold text-ink-500">
                        dari {{ $stats['total_students'] }} siswa ({{ $stats['total_students'] > 0 ? round(($stats['today_filled'] / $stats['total_students']) * 100) : 0 }}%)
                    </span>
                </div>
            </div>

            <div class="card p-5 border border-ink-100">
                <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">Rata-rata Kebiasaan / Hari</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl sm:text-4xl font-black text-emerald-600 tabular-nums">{{ $stats['avg_completion'] }}</span>
                    <span class="text-xs font-bold text-ink-500">dari 7 kebiasaan</span>
                </div>
            </div>
        </div>

        {{-- 7 Kebiasaan Cards (Rapor Pendidikan style) --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-ink-900">Status 7 Kebiasaan Kelas</h2>
                    <p class="text-xs font-semibold text-ink-500">Tingkat capaian kumulatif minggu ini</p>
                </div>
                <a href="{{ route('guru.classroom', $classroom->id) }}" class="btn-ghost text-xs sm:text-sm">
                    Detail Siswa & Kelas →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($stats['habits'] as $item)
                    @php
                        $habit = $item['habit'];
                        $rate = $item['rate'];
                        $status = $item['status'];
                        $statusClass = match($status) {
                            'Baik' => 'chip-status-baik',
                            'Cukup' => 'chip-status-cukup',
                            default => 'chip-status-perlu',
                        };
                        $statusIcon = match($status) {
                            'Baik' => '✓',
                            'Cukup' => '!',
                            default => '▲',
                        };
                    @endphp

                    <div class="card p-5 border border-ink-100 flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between mb-3">
                                <div class="size-10 rounded-2xl {{ $habit->soft_color }} flex items-center justify-center text-xl shadow-sm">
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

                                <span class="{{ $statusClass }}">
                                    <span>{{ $statusIcon }}</span>
                                    <span>{{ $status }}</span>
                                </span>
                            </div>

                            <h4 class="font-extrabold text-ink-900 text-sm leading-snug">
                                {{ $habit->name }}
                            </h4>

                            <div class="mt-3 flex items-baseline gap-1">
                                <span class="text-2xl font-black text-ink-900 tabular-nums">{{ $rate }}%</span>
                                <span class="text-xs font-semibold text-ink-500">siswa mencapai target</span>
                            </div>

                            <div class="progress-track h-2 mt-2 bg-ink-100">
                                <div class="progress-fill" style="width: {{ $rate }}%; background-color: {{ $habit->color_hex }}"></div>
                            </div>
                        </div>

                        <div class="pt-4 mt-2 border-t border-ink-100">
                            <span class="text-[11px] font-semibold text-ink-500">
                                Ambang target: {{ $status === 'Baik' ? 'Di atas 75%' : ($status === 'Cukup' ? '50% - 75%' : 'Di bawah 50%') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Students Needing Attention (Early Detection) --}}
        <div class="card p-5 sm:p-6 border border-ink-100">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-xl">⚠️</span>
                    <div>
                        <h3 class="text-base font-extrabold text-ink-900">Deteksi Dini: Siswa Perlu Perhatian</h3>
                        <p class="text-xs font-semibold text-ink-500">Siswa dengan streak terputus atau belum mengisi jurnal dalam 2 hari terakhir</p>
                    </div>
                </div>
            </div>

            @if(count($stats['needs_attention']) > 0)
                <div class="space-y-2">
                    @foreach($stats['needs_attention'] as $st)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 rounded-2xl bg-canvas border border-ink-100 gap-3">
                            <div class="flex items-center gap-3">
                                <div class="avatar size-9 text-xs shrink-0 font-bold">
                                    {{ substr($st->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-ink-900">{{ $st->name }}</p>
                                    <p class="text-xs font-semibold text-ink-500">
                                        NIS: {{ $st->nis }} ·
                                        Terakhir mengisi: {{ $st->last_journal_date ? $st->last_journal_date->locale('id')->isoFormat('D MMM Y') : 'Belum pernah' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-auto">
                                <span class="chip-status-perlu text-[11px] py-1 px-3">
                                    Streak: {{ $st->current_streak }} hari
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <p class="text-sm font-bold text-emerald-700">🎉 Semua siswa di kelas ini aktif dan konsisten mengisi jurnal!</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
