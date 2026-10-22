@extends('layouts.admin')

@section('title', 'Analisis Akar Masalah: ' . $habit->name . ' — SiHebat')

@php
    if (!function_exists('getHabitSvg')) {
        function getHabitSvg($slug, $size = 'size-4') {
            return match($slug) {
                'bangun-pagi' => '<svg class="'.$size.'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
                'beribadah' => '<svg class="'.$size.'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18zm0 0v-8m0 0l3 3m-3-3l-3 3"/></svg>',
                'berolahraga' => '<svg class="'.$size.'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                'makan-sehat' => '<svg class="'.$size.'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>',
                'gemar-belajar' => '<svg class="'.$size.'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                'bermasyarakat' => '<svg class="'.$size.'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
                'tidur-cepat' => '<svg class="'.$size.'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>',
                default => '<svg class="'.$size.'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>',
            };
        }
    }
@endphp

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    aiLoading: false,
    aiAnalysis: null,
    aiSource: '',
    fetchAiHabitAnalysis() {
        this.aiLoading = true;
        fetch('{{ route('admin.akar-masalah.ai') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ habit_slug: '{{ $habit->slug }}' })
        })
        .then(r => r.json())
        .then(data => {
            this.aiLoading = false;
            if (data.status === 'success' && data.analysis) {
                this.aiAnalysis = data.analysis;
                this.aiSource = data.analysis.source || 'gemini_api';
            }
        })
        .catch(err => {
            this.aiLoading = false;
            alert('Gagal memuat analisis AI.');
        });
    }
}">

    {{-- Breadcrumb & Back Action --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs font-bold text-ink-500">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600 transition flex items-center gap-1">
                <span>←</span> Kembali ke Dashboard
            </a>
            <span>/</span>
            <span class="text-ink-900 font-extrabold">Analisis Akar Masalah</span>
        </div>

        <div class="flex items-center gap-2">
            <button type="button"
                    @click="$dispatch('open-export', { format: 'excel' })"
                    class="btn-outline text-xs py-2 px-3.5 rounded-xl flex items-center gap-1.5 font-bold bg-white hover:bg-ink-50 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Unduh Excel</span>
            </button>
            <button type="button"
                    @click="$dispatch('open-export', { format: 'pdf' })"
                    class="btn-primary text-xs py-2 px-3.5 rounded-xl flex items-center gap-1.5 font-bold shadow-btn cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak PDF</span>
            </button>
        </div>
    </div>

    {{-- Habit Selector Pills (Clean SVGs, No Raw Text Leaks) --}}
    <div class="card p-3 border border-ink-100/80 shadow-card overflow-x-auto">
        <div class="flex items-center gap-2 min-w-max">
            <span class="text-xs font-extrabold text-ink-500 uppercase px-2 tracking-wider">Pilih Kebiasaan:</span>
            @foreach($allHabits as $h)
                <a href="{{ route('admin.akar-masalah', ['habit' => $h->slug]) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition {{ $h->id === $habit->id ? 'bg-ink-900 text-white shadow-sm font-extrabold' : 'bg-ink-50 text-ink-700 hover:bg-primary-50 hover:text-primary-800' }}">
                    <span class="shrink-0">{!! getHabitSvg($h->slug, 'size-3.5') !!}</span>
                    <span>{{ $h->name }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Main Habit Banner (Clean, Professional, No Raw moon-stars text) --}}
    <div class="card p-6 border border-ink-100/80 shadow-card bg-gradient-to-r from-white via-primary-50/20 to-white flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start gap-4">
            <div class="size-14 rounded-2xl bg-primary-100 text-primary-800 flex items-center justify-center shrink-0 shadow-sm">
                {!! getHabitSvg($habit->slug, 'size-7') !!}
            </div>
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-ink-500 uppercase tracking-wider">Identifikasi Akar Masalah Kebiasaan #{{ $habit->order }}</span>
                    <span class="text-[11px] font-black px-2.5 py-0.5 rounded-full {{ $diagnosis['badge_color'] === 'rose' ? 'bg-rose-100 text-rose-800' : ($diagnosis['badge_color'] === 'amber' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                        {{ $diagnosis['status_badge'] }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-ink-900 tracking-tight">{{ $habit->name }}</h1>
                <p class="text-xs sm:text-sm font-semibold text-ink-600 max-w-2xl">
                    {{ $habit->description }} Target standar: <strong class="text-ink-900">{{ $habit->target_description ?? 'Sesuai indikator 7 Kebiasaan Anak Indonesia Hebat' }}</strong>.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <div class="p-4 rounded-2xl bg-white border border-ink-100 shadow-sm text-center min-w-[130px]">
                <span class="text-[10px] font-extrabold text-ink-500 uppercase block tracking-wider">Rentang Analisis</span>
                <span class="text-sm font-extrabold text-ink-900 mt-0.5 block">7 Hari Terakhir</span>
                <span class="text-[10px] font-bold text-emerald-700 bg-mint-soft px-2 py-0.5 rounded-full inline-block mt-1">Data Realtime</span>
            </div>
        </div>
    </div>

    {{-- Live AI Action Bar --}}
    <div class="card p-4 sm:p-5 border-2 border-primary-300 shadow-card bg-gradient-to-r from-primary-50/80 via-white to-primary-50/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="size-11 rounded-2xl bg-primary-400 text-ink-900 flex items-center justify-center shrink-0 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-black text-ink-900 text-sm sm:text-base">Analisis AI Khusus: {{ $habit->name }}</h3>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-primary-100 text-primary-900">Google Gemini 3.5</span>
                </div>
                <p class="text-xs text-ink-600 font-medium">
                    Minta saran aksi nyata dan diagnosis pemicu spesifik untuk kebiasaan ini secara instan berbasis data riil sekolah.
                </p>
            </div>
        </div>

        <button type="button"
                @click="fetchAiHabitAnalysis()"
                :disabled="aiLoading"
                class="btn-primary text-xs font-bold py-2.5 px-4 rounded-xl shadow-btn flex items-center gap-2 shrink-0 cursor-pointer disabled:opacity-50">
            <svg x-show="!aiLoading" xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <svg x-show="aiLoading" class="animate-spin size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span x-text="aiLoading ? 'Menganalisis Data...' : 'Minta Saran AI Kebiasaan Ini'"></span>
        </button>
    </div>

    {{-- Hasil AI Live jika sudah dimuat --}}
    <div x-show="aiAnalysis" x-cloak class="card p-5 sm:p-6 border-2 border-emerald-300 bg-emerald-50/40 shadow-card space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-emerald-200">
            <div class="flex items-center gap-2">
                <span class="size-6 rounded-full bg-emerald-500 text-white font-black text-xs flex items-center justify-center">✓</span>
                <h3 class="font-extrabold text-emerald-950 text-sm sm:text-base" x-text="aiAnalysis ? aiAnalysis.title : ''"></h3>
            </div>
            <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full"
                  x-text="aiSource === 'gemini_api' ? 'Google Gemini AI (Live)' : 'Statistik Cerdas Satdik'"></span>
        </div>
        <p class="text-xs font-semibold text-emerald-900 leading-relaxed" x-text="aiAnalysis ? aiAnalysis.summary : ''"></p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
            <div class="p-4 rounded-2xl bg-white border border-emerald-200 space-y-2">
                <span class="text-xs font-black text-rose-800 uppercase tracking-wider block">⚠️ Pemicu Khusus Terdeteksi:</span>
                <ul class="text-xs text-ink-800 font-semibold space-y-1.5 list-disc list-inside">
                    <template x-for="(trig, idx) in (aiAnalysis ? aiAnalysis.triggers : [])" :key="idx">
                        <li x-text="trig"></li>
                    </template>
                </ul>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-emerald-200 space-y-2">
                <span class="text-xs font-black text-emerald-800 uppercase tracking-wider block">💡 Rekomendasi Program Benahi:</span>
                <ul class="text-xs text-ink-800 font-semibold space-y-1.5 list-disc list-inside">
                    <template x-for="(rec, idx) in (aiAnalysis ? aiAnalysis.recommendations : [])" :key="idx">
                        <li x-text="rec"></li>
                    </template>
                </ul>
            </div>
        </div>
    </div>

    {{-- Two Column: Diagnosa Akar Masalah vs Korelasi Antar Kebiasaan --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Diagnosa Akar Masalah Spesifik Kebiasaan --}}
        <div class="card p-6 border border-ink-100/80 shadow-card space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-ink-100">
                <div class="flex items-center gap-2.5">
                    <div class="size-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-extrabold text-ink-900">Diagnosis Akar Masalah</h2>
                        <span class="text-[11px] font-semibold text-ink-500">Khas Kebiasaan: {{ $habit->name }}</span>
                    </div>
                </div>
                <span class="text-[11px] font-bold text-ink-500">Berbasis Data Jurnal</span>
            </div>

            <div class="space-y-3">
                <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-200 space-y-2">
                    <h3 class="text-xs font-bold text-rose-900 uppercase tracking-wider flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 text-rose-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Pemicu Utama Rendahnya Ketercapaian</span>
                    </h3>
                    <ul class="text-xs text-ink-900 font-semibold space-y-1.5 list-disc list-inside">
                        @foreach($diagnosis['triggers'] as $trig)
                            <li>{{ $trig }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-1.5">
                    <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        <span>Pola Hari & Waktu Kritis</span>
                    </h3>
                    <p class="text-xs text-ink-800 font-semibold leading-relaxed">
                        {{ $diagnosis['critical_pattern'] }}
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-1.5">
                    <h3 class="text-xs font-bold text-emerald-900 uppercase tracking-wider flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Rekomendasi Intervensi Sekolah</span>
                    </h3>
                    <ul class="text-xs text-ink-900 font-semibold space-y-1.5 list-disc list-inside">
                        @foreach($diagnosis['interventions'] as $inte)
                            <li>{{ $inte }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- Korelasi Antar Kebiasaan --}}
        <div class="card p-6 border border-ink-100/80 shadow-card space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-ink-100">
                <div class="flex items-center gap-2.5">
                    <div class="size-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-extrabold text-ink-900">Matriks Korelasi Kebiasaan</h2>
                        <span class="text-[11px] font-semibold text-ink-500">Hubungan Statistik "{{ $habit->name }}"</span>
                    </div>
                </div>
                <span class="text-[11px] font-bold text-ink-500">Analisis Dampak</span>
            </div>

            <div class="space-y-3">
                @foreach($diagnosis['correlations'] as $desc)
                    <div class="p-4 rounded-2xl bg-canvas border border-ink-100 flex items-start gap-3 hover:border-primary-300 transition">
                        <div class="size-6 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">
                            ✓
                        </div>
                        <div class="text-xs font-semibold text-ink-800 leading-relaxed">
                            {{ $desc }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Class Breakdown Table --}}
    <div class="card p-6 border border-ink-100/80 shadow-card space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-ink-100">
            <div>
                <h2 class="text-base font-extrabold text-ink-900">Capaian Per Kelas (7 Hari Terakhir)</h2>
                <p class="text-xs font-semibold text-ink-500">Perbandingan persentase ketercapaian kebiasaan "{{ $habit->name }}" antar seluruh rombongan belajar</p>
            </div>
            <span class="text-xs font-extrabold text-ink-600 bg-ink-100 px-3 py-1 rounded-xl">
                Total {{ count($classBreakdown) }} Rombel Terpantau
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-ink-100 text-ink-500 uppercase text-[10px] font-extrabold">
                        <th class="py-3 px-3">Nama Kelas</th>
                        <th class="py-3 px-3">Wali Kelas</th>
                        <th class="py-3 px-3">Persentase Ketercapaian</th>
                        <th class="py-3 px-3 text-center">Status Mutu</th>
                        <th class="py-3 px-3 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @forelse($classBreakdown as $row)
                        @php
                            $badgeClass = match($row['status']) {
                                'Baik' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                'Cukup' => 'bg-amber-100 text-amber-800 border-amber-300',
                                default => 'bg-rose-100 text-rose-800 border-rose-300',
                            };
                            $barColor = match($row['status']) {
                                'Baik' => 'bg-emerald-500',
                                'Cukup' => 'bg-amber-500',
                                default => 'bg-rose-500',
                            };
                        @endphp
                        <tr class="hover:bg-primary-50/40 transition">
                            <td class="py-3 px-3 font-bold text-ink-900">
                                {{ $row['classroom']->name }}
                            </td>
                            <td class="py-3 px-3 text-ink-700 font-medium">
                                {{ $row['classroom']->homeroomTeacher->name ?? '-' }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-32 bg-ink-100 rounded-full h-2 overflow-hidden">
                                        <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ min(100, $row['rate']) }}%"></div>
                                    </div>
                                    <span class="font-extrabold text-ink-900 tabular-nums">{{ $row['rate'] }}%</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black border {{ $badgeClass }}">
                                    {{ $row['status'] }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <a href="{{ route('admin.leaderboard', ['classroom_id' => $row['classroom']->id]) }}"
                                   class="btn-outline text-[11px] font-bold py-1 px-2.5 rounded-lg inline-flex items-center gap-1">
                                    <span>Pantau Siswa</span>
                                    <span>→</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-ink-500 font-semibold">
                                Belum ada data rombel yang dapat ditampilkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
