@extends('layouts.admin')

@section('title', 'Dashboard Administrator — SiHebat')

@section('content')
<script>
function adminDashboard() {
    return {
        modalArtiOpen: false,
        modalArtiTitle: '',
        modalArtiContent: '',
        modalAkarOpen: false,
        modalAkarTitle: '',
        modalAkarPenyebab: [],
        modalAkarSolusi: [],
        modalTanyaAiOpen: false,
        aiQuestion: '',
        aiLoading: false,
        aiAnswer: '',
        aiSource: '',
        aiDetails: null,
        aiCopied: false,

        askAi(customQ = null) {
            if (customQ) this.aiQuestion = customQ;
            if (!this.aiQuestion.trim()) return;
            this.aiLoading = true;
            this.aiAnswer = '';
            this.aiDetails = null;
            this.aiCopied = false;
            fetch('{{ route('admin.ai.ask') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ question: this.aiQuestion })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                this.aiAnswer = data.answer;
                this.aiSource = data.source;
                this.aiDetails = data.details || null;
                this.aiLoading = false;
            }.bind(this))
            .catch(function(err) {
                this.aiAnswer = 'Maaf, terjadi kendala saat memproses pertanyaan Anda.';
                this.aiDetails = null;
                this.aiLoading = false;
            }.bind(this));
        },

        copyAiResponse() {
            let textToCopy = this.aiAnswer || '';
            if (this.aiDetails && this.aiDetails.recommendation) {
                textToCopy = (this.aiDetails.finding ? this.aiDetails.finding + "\n\n" : "") +
                             "Saran Praktis:\n" + this.aiDetails.recommendation;
            }
            textToCopy = textToCopy.replace(/\*/g, '');
            navigator.clipboard.writeText(textToCopy).then(() => {
                this.aiCopied = true;
                setTimeout(() => { this.aiCopied = false; }, 2500);
            });
        },

        formatAiMarkdown(rawText, details) {
            let text = (details && details.finding) ? details.finding : (rawText || '');
            if (!text) return '';
            let escaped = text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
            
            escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong class="font-extrabold text-ink-900">$1</strong>');
            escaped = escaped.replace(/\*([^\*]+)\*/g, '<span class="font-bold text-ink-900">$1</span>');
            escaped = escaped.replace(/(?:^|\n)-\s+(.*?)(?=\n|$)/g, '<li class="ml-4 list-disc text-ink-800">$1</li>');
            return escaped.replace(/\n\n+/g, '</p><p class="mt-2">').replace(/\n/g, '<br>');
        },

        adviceLoading: false,
        adviceSuccessMsg: '',
        adviceTitle: @json($aiResult['insight']->title ?? ''),
        adviceSummary: @json($aiResult['insight']->summary ?? ''),
        adviceStrengths: @json($aiResult['insight']->strengths ?? []),
        adviceRecommendations: @json($aiResult['insight']->recommendations ?? []),
        adviceUpdatedAt: @json(isset($aiResult['insight']) ? $aiResult['insight']->created_at->format('H:i:s') . ' WIB' : ''),

        refreshAdvice() {
            this.adviceLoading = true;
            this.adviceSuccessMsg = '';
            fetch('{{ route('admin.ai.advice') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                this.adviceLoading = false;
                if (data.insight) {
                    this.adviceTitle = data.insight.title;
                    this.adviceSummary = data.insight.summary;
                    this.adviceStrengths = data.insight.strengths;
                    this.adviceRecommendations = data.insight.recommendations;
                    this.adviceUpdatedAt = data.insight.updated_at;
                    this.adviceSuccessMsg = 'Saran baru dari Google Gemini AI berhasil dimuat!';
                    setTimeout(function() { this.adviceSuccessMsg = ''; }.bind(this), 5000);
                }
            }.bind(this))
            .catch(function(err) {
                this.adviceLoading = false;
                alert('Gagal memuat saran AI. Silakan coba lagi.');
            }.bind(this));
        }
    };
}
</script>

<div class="max-w-7xl mx-auto space-y-6 sm:space-y-8" x-data="adminDashboard()">

    {{-- HEADER UTAMA: Identitas Sekolah & Aksi Cepat (Posisi paling atas agar konteks jelas) --}}
    <div class="card p-5 sm:p-6 border border-ink-100/80 shadow-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="size-13 rounded-2xl bg-ink-900 text-white flex items-center justify-center shrink-0 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-ink-900 tracking-tight">{{ $schoolProfile['name'] }}</h1>
                    <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800">
                        Akreditasi {{ $schoolProfile['accreditation'] }}
                    </span>
                    <span class="text-[10px] font-bold text-ink-600 bg-ink-100 px-2 py-0.5 rounded-md">
                        Tahun Ajaran {{ $schoolProfile['selected_year'] }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-semibold text-ink-500 mt-1">
                    <span>NPSN: <strong class="text-ink-700">{{ $schoolProfile['npsn'] }}</strong></span>
                    <span>•</span>
                    <span>{{ $schoolProfile['address'] }}</span>
                    <span>•</span>
                    <span>{{ $schoolProfile['city'] }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <button type="button"
                    @click="modalTanyaAiOpen = true"
                    class="btn-primary text-xs font-bold py-2.5 px-3.5 rounded-xl shadow-btn flex items-center gap-1.5 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>Tanya Data AI</span>
            </button>

            <button type="button"
                    @click="$dispatch('open-export', { format: 'excel' })"
                    class="btn-outline text-xs font-bold py-2.5 px-3.5 rounded-xl flex items-center gap-1.5 bg-white hover:bg-ink-50 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Unduh Excel</span>
            </button>

            <button type="button"
                    @click="$dispatch('open-export', { format: 'pdf' })"
                    class="btn-outline text-xs font-bold py-2.5 px-3.5 rounded-xl flex items-center gap-1.5 bg-white hover:bg-ink-50 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-ink-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak PDF</span>
            </button>
        </div>
    </div>

    {{-- ALUR KERJA CEPAT: Memberi peta visual bagi orang awam agar tidak bingung --}}
    <div class="bg-white/80 border border-ink-100/90 rounded-2xl p-2.5 sm:p-3 shadow-xs">
        <div class="flex items-center justify-between gap-2 overflow-x-auto text-xs font-semibold text-ink-500">
            <div class="flex items-center gap-2 shrink-0">
                <span class="text-[11px] font-black uppercase tracking-wider text-ink-400">Alur Pantauan:</span>
            </div>
            <a href="#tahap-ikhtisar" class="hover:text-primary-700 flex items-center gap-1.5 px-2.5 py-1 rounded-lg hover:bg-primary-50 transition shrink-0">
                <span class="size-5 rounded-full bg-primary-100 text-primary-800 font-bold text-[11px] flex items-center justify-center">1</span>
                <span>Ikhtisar Keaktifan</span>
            </a>
            <span class="text-ink-300">→</span>
            <a href="#tahap-rapor" class="hover:text-primary-700 flex items-center gap-1.5 px-2.5 py-1 rounded-lg hover:bg-primary-50 transition shrink-0">
                <span class="size-5 rounded-full bg-primary-100 text-primary-800 font-bold text-[11px] flex items-center justify-center">2</span>
                <span>Rapor Mutu Karakter</span>
            </a>
            <span class="text-ink-300">→</span>
            <a href="#tahap-ai" class="hover:text-primary-700 flex items-center gap-1.5 px-2.5 py-1 rounded-lg hover:bg-primary-50 transition shrink-0">
                <span class="size-5 rounded-full bg-primary-100 text-primary-800 font-bold text-[11px] flex items-center justify-center">3</span>
                <span>Rekomendasi Cerdas AI</span>
            </a>
            <span class="text-ink-300">→</span>
            <a href="#tahap-grafik" class="hover:text-primary-700 flex items-center gap-1.5 px-2.5 py-1 rounded-lg hover:bg-primary-50 transition shrink-0">
                <span class="size-5 rounded-full bg-primary-100 text-primary-800 font-bold text-[11px] flex items-center justify-center">4</span>
                <span>Grafik & Tren</span>
            </a>
            <span class="text-ink-300">→</span>
            <a href="#tahap-peringkat" class="hover:text-primary-700 flex items-center gap-1.5 px-2.5 py-1 rounded-lg hover:bg-primary-50 transition shrink-0">
                <span class="size-5 rounded-full bg-primary-100 text-primary-800 font-bold text-[11px] flex items-center justify-center">5</span>
                <span>Top 10 Siswa</span>
            </a>
        </div>
    </div>

    {{-- TAHAP 1: IKHTISAR KEAKTIFAN SEKOLAH (Pulse / Denyut Sekolah Hari Ini) --}}
    <div id="tahap-ikhtisar" class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="size-6 rounded-full bg-primary-500 text-white font-black text-xs flex items-center justify-center">1</span>
                    <h2 class="text-base font-black text-ink-900 tracking-tight">Ikhtisar & Keaktifan Sekolah Hari Ini</h2>
                </div>
                <p class="text-xs font-semibold text-ink-500 mt-0.5">
                    Data partisipasi siswa, guru pendamping, dan tingkat ketahanan kebiasaan di sekolah.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Card 1: Total Siswa --}}
            <div class="card p-5 border border-ink-100/80 shadow-card flex items-center justify-between hover:border-primary-300 transition-all">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">Total Siswa Terdaftar</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black text-ink-900 tabular-nums">{{ number_format($totalStudents) }}</span>
                        <span class="text-[11px] font-extrabold text-primary-800 bg-primary-100 px-2 py-0.5 rounded-full">
                            {{ $totalClassrooms }} Rombel
                        </span>
                    </div>
                    <span class="text-xs font-semibold text-ink-400">Siswa aktif mengisi jurnal</span>
                </div>
                <div class="size-13 rounded-2xl bg-primary-50 text-primary-600 border border-primary-100/80 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>

            {{-- Card 2: Guru & Wali Kelas --}}
            <div class="card p-5 border border-ink-100/80 shadow-card flex items-center justify-between hover:border-primary-300 transition-all">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">Wali Kelas & Guru</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black text-ink-900 tabular-nums">{{ number_format($totalTeachers) }}</span>
                        <span class="text-[11px] font-extrabold text-emerald-800 bg-mint-soft px-2 py-0.5 rounded-full">
                            Mendampingi
                        </span>
                    </div>
                    <span class="text-xs font-semibold text-ink-400">Pembimbing karakter 7 kebiasaan</span>
                </div>
                <div class="size-13 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100/80 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v7" />
                    </svg>
                </div>
            </div>

            {{-- Card 3: Rata-rata Streak Sekolah --}}
            <div class="card p-5 border border-ink-100/80 shadow-card flex items-center justify-between hover:border-primary-300 transition-all">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">Rata-rata Streak Siswa</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black text-ink-900 tabular-nums">{{ $avgSchoolStreak }}</span>
                        <span class="text-[11px] font-extrabold text-amber-800 bg-sun-soft px-2 py-0.5 rounded-full">
                            Hari Berturut
                        </span>
                    </div>
                    <span class="text-xs font-semibold text-ink-400">Ukuran konsistensi disiplin</span>
                </div>
                <div class="size-13 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100/80 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- TAHAP 2: EVALUASI MUTU KARAKTER (Rapor Pendidikan Satuan Pendidikan) --}}
    <div id="tahap-rapor" class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="size-6 rounded-full bg-primary-500 text-white font-black text-xs flex items-center justify-center">2</span>
                    <h2 class="text-base font-black text-ink-900 tracking-tight">Evaluasi Mutu Karakter (Rapor Pendidikan)</h2>
                </div>
                <p class="text-xs font-semibold text-ink-500 mt-0.5">
                    Identifikasi capaian indikator utama kebiasaan siswa untuk dasar evaluasi dan perencanaan sekolah.
                </p>
            </div>
        </div>

        {{-- Deep Navy Executive Highlight Card (Kerangka Kemendikbud) --}}
        <div class="rounded-3xl p-6 sm:p-7 text-white shadow-card space-y-5" style="background: linear-gradient(145deg, #0F2A43 0%, #15395B 100%);">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <span class="text-xs font-bold text-primary-200 uppercase tracking-wider block">Ringkasan Eksekutif Satdik</span>
                    <h3 class="text-lg sm:text-xl font-black text-white tracking-tight mt-0.5">
                        Ringkasan Kondisi Satuan Pendidikan Anda Tahun 2026
                    </h3>
                </div>
                <a href="{{ route('admin.akar-masalah') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-extrabold text-ink-900 bg-white hover:bg-primary-50 px-3.5 py-2 rounded-xl transition shadow-xs">
                    <span>Pelajari Akar Masalah Lengkap</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            {{-- 3 Executive Metric Highlights (FR-DSH-02) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                {{-- 1. Peningkatan Tertinggi --}}
                <div class="rounded-2xl p-4 flex items-center gap-3.5" style="background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255, 255, 255, 0.14);">
                    <div class="size-10 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[11px] font-bold text-white/70 uppercase tracking-wider block">Peningkatan Tertinggi</span>
                        <h4 class="font-extrabold text-sm text-white truncate">{{ $raporMetrics['topImprovement']['name'] ?? 'Karakter & Disiplin' }}</h4>
                        @if(isset($raporMetrics['topImprovement']))
                            @if($raporMetrics['topImprovement']['delta'] >= 0)
                                <span class="text-xs font-bold text-emerald-300">↑ +{{ $raporMetrics['topImprovement']['delta'] }}% tren kenaikan</span>
                            @else
                                <span class="text-xs font-bold text-rose-300">↓ {{ $raporMetrics['topImprovement']['delta'] }}% perubahan</span>
                            @endif
                        @else
                            <span class="text-xs font-bold text-white/60">Belum ada data pembanding</span>
                        @endif
                    </div>
                </div>

                {{-- 2. Capaian Terbaik --}}
                <div class="rounded-2xl p-4 flex items-center gap-3.5" style="background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255, 255, 255, 0.14);">
                    <div class="size-10 rounded-xl bg-sky-500/20 text-sky-300 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[11px] font-bold text-white/70 uppercase tracking-wider block">Capaian Terbaik</span>
                        <h4 class="font-extrabold text-sm text-white truncate">{{ $raporMetrics['bestHabit']['name'] ?? 'Disiplin Harian' }}</h4>
                        <span class="text-xs font-bold text-sky-300">{{ $raporMetrics['bestHabit']['rate'] ?? 0 }}% target tercapai ({{ $raporMetrics['bestHabit']['status'] ?? '-' }})</span>
                    </div>
                </div>

                {{-- 3. Capaian Terendah --}}
                <div class="rounded-2xl p-4 flex items-center gap-3.5" style="background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255, 255, 255, 0.14);">
                    <div class="size-10 rounded-xl bg-rose-500/20 text-rose-300 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[11px] font-bold text-white/70 uppercase tracking-wider block">Capaian Terendah</span>
                        <h4 class="font-extrabold text-sm text-white truncate">{{ $raporMetrics['lowestHabit']['name'] ?? 'Tidur Cepat' }}</h4>
                        <span class="text-xs font-bold text-rose-300">{{ $raporMetrics['lowestHabit']['rate'] ?? 0 }}% target tercapai ({{ $raporMetrics['lowestHabit']['status'] ?? '-' }})</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pertanyaan Refleksi Situasi Sekolah --}}
        <div>
            <h3 class="text-base sm:text-lg font-black text-ink-900 tracking-tight">
                Bagaimana situasi yang dihadapi oleh satuan pendidikan saya saat ini?
            </h3>
            <p class="text-xs font-semibold text-ink-500 mt-0.5">
                Rincian capaian 3 dimensi utama berdasarkan data pengisian jurnal seluruh siswa.
            </p>
        </div>

        {{-- 3 Kolom Dimensi: Literasi, Numerasi, Karakter --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Column 1: Kemampuan Literasi & Belajar --}}
            <div class="card p-5 border border-ink-100 flex flex-col justify-between hover:border-primary-300 transition-all">
                <div class="space-y-3.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-ink-500 uppercase tracking-wider">Kemampuan Literasi</span>
                        <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Baik · 97,78%
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <span class="text-ink-600 font-semibold">Tren Tahun Lalu:</span>
                        <span class="font-bold text-rose-600 flex items-center gap-0.5">
                            ↓ 2,22 poin
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-canvas border border-ink-100/80 text-xs space-y-1.5">
                        <div class="flex items-center justify-between font-bold text-ink-700">
                            <span>Provinsi</span>
                            <span class="text-primary-700 bg-primary-50 px-2 py-0.5 rounded text-[11px]">Peringkat Atas (1-20%)</span>
                        </div>
                        <div class="flex items-center justify-between font-bold text-ink-700">
                            <span>Nasional</span>
                            <span class="text-primary-700 bg-primary-50 px-2 py-0.5 rounded text-[11px]">Peringkat Atas (1-20%)</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 mt-4 border-t border-ink-100 flex items-center gap-2">
                    <button type="button"
                            @click="modalArtiOpen = true; modalArtiTitle = 'Arti Capaian Kemampuan Literasi'; modalArtiContent = 'Kemampuan literasi mengukur seberapa konsisten siswa membaca buku dan merefleksikan bacaan dalam jurnal harian SiHebat. Skor 97,78% menunjukkan siswa memiliki minat baca memadai, namun durasi baca mandiri perlu terus didorong.'"
                            class="flex-1 btn-outline text-xs font-bold py-2 rounded-xl text-center">
                        Arti Capaian
                    </button>
                    <button type="button"
                            @click="modalAkarOpen = true; modalAkarTitle = 'Akar Masalah: Kemampuan Literasi'; modalAkarPenyebab = ['Siswa mengalihkan waktu baca ke media sosial di malam hari.', 'Variasi buku di perpustakaan digital sekolah belum diperbarui.']; modalAkarSolusi = ['Jadwalkan Pojok Literasi 15 menit setiap sebelum KBM pertama.', 'Berikan reward poin XP SiHebat untuk siswa yang mereview buku favorit.']"
                            class="flex-1 btn-dark text-xs font-bold py-2 rounded-xl text-center">
                        Pelajari Akar Masalah
                    </button>
                </div>
            </div>

            {{-- Column 2: Kemampuan Numerasi & Disiplin Bangun Pagi --}}
            <div class="card p-5 border border-ink-100 flex flex-col justify-between hover:border-primary-300 transition-all">
                <div class="space-y-3.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-ink-500 uppercase tracking-wider">Kemampuan Numerasi</span>
                        <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Baik · 93,33%
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <span class="text-ink-600 font-semibold">Tren Tahun Lalu:</span>
                        <span class="font-bold text-rose-600 flex items-center gap-0.5">
                            ↓ 2,23 poin
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-canvas border border-ink-100/80 text-xs space-y-1.5">
                        <div class="flex items-center justify-between font-bold text-ink-700">
                            <span>Provinsi</span>
                            <span class="text-primary-700 bg-primary-50 px-2 py-0.5 rounded text-[11px]">Peringkat Atas (1-20%)</span>
                        </div>
                        <div class="flex items-center justify-between font-bold text-ink-700">
                            <span>Nasional</span>
                            <span class="text-primary-700 bg-primary-50 px-2 py-0.5 rounded text-[11px]">Peringkat Atas (1-20%)</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 mt-4 border-t border-ink-100 flex items-center gap-2">
                    <button type="button"
                            @click="modalArtiOpen = true; modalArtiTitle = 'Arti Capaian Kemampuan Numerasi'; modalArtiContent = 'Kemampuan numerasi dan disiplin pagi mencerminkan ketepatan waktu bangun pagi siswa sebelum pukul 05.00 WIB serta keteraturan perencanaan belajar. 93,33% siswa aktif mencatat kebiasaan ini secara konsisten.'"
                            class="flex-1 btn-outline text-xs font-bold py-2 rounded-xl text-center">
                        Arti Capaian
                    </button>
                    <button type="button"
                            @click="modalAkarOpen = true; modalAkarTitle = 'Akar Masalah: Kemampuan Numerasi'; modalAkarPenyebab = ['Siswa tidur larut sehingga kesulitan bangun pagi tepat waktu.', 'Kelelahan di akhir pekan mempengaruhi konsistensi Senin pagi.']; modalAkarSolusi = ['Integrasikan target bangun pagi dengan badge Early Bird di SiHebat.', 'Sosialisasi kebiasaan sarapan sehat bersama wali kelas.']"
                            class="flex-1 btn-dark text-xs font-bold py-2 rounded-xl text-center">
                        Pelajari Akar Masalah
                    </button>
                </div>
            </div>

            {{-- Column 3: Karakter & Iklim Kebiasaan --}}
            <div class="card p-5 border border-ink-100 flex flex-col justify-between hover:border-primary-300 transition-all">
                <div class="space-y-3.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-ink-500 uppercase tracking-wider">Karakter & Kebiasaan</span>
                        <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Baik
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <span class="text-ink-600 font-semibold">Tren Tahun Lalu:</span>
                        <span class="font-bold text-emerald-600 flex items-center gap-0.5">
                            ↑ 5,13 poin
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-canvas border border-ink-100/80 text-xs space-y-1.5">
                        <div class="flex items-center justify-between font-bold text-ink-700">
                            <span>Provinsi</span>
                            <span class="text-primary-700 bg-primary-50 px-2 py-0.5 rounded text-[11px]">Peringkat Atas (1-20%)</span>
                        </div>
                        <div class="flex items-center justify-between font-bold text-ink-700">
                            <span>Nasional</span>
                            <span class="text-primary-700 bg-primary-50 px-2 py-0.5 rounded text-[11px]">Peringkat Atas (1-20%)</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 mt-4 border-t border-ink-100 flex items-center gap-2">
                    <button type="button"
                            @click="modalArtiOpen = true; modalArtiTitle = 'Arti Capaian Karakter'; modalArtiContent = 'Karakter diukur dari konsistensi pengisian 7 Kebiasaan Anak Indonesia Hebat (ibadah, olahraga, makan sehat, bermasyarakat, dan istirahat). Terjadi kenaikan signifikan 5,13 poin berkat motivasi gamifikasi streak & XP.'"
                            class="flex-1 btn-outline text-xs font-bold py-2 rounded-xl text-center">
                        Arti Capaian
                    </button>
                    <button type="button"
                            @click="modalAkarOpen = true; modalAkarTitle = 'Akar Masalah: Karakter'; modalAkarPenyebab = ['Kekuatan: Semangat ibadah dan interaksi sosial gotong royong sangat tinggi.', 'Perhatian: Konsistensi olahraga di hari libur masih perlu didorong.']; modalAkarSolusi = ['Pertahankan apresiasi berkala saat upacara bendera.', 'Tantangan olahraga ringan akhir pekan bersama keluarga.']"
                            class="flex-1 btn-dark text-xs font-bold py-2 rounded-xl text-center">
                        Pelajari Akar Masalah
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- TAHAP 3: SOLUSI & REKOMENDASI CERDAS AI (Diletakkan langsung setelah Rapor agar alur diagnosa -> tindakan menyatu) --}}
    <div id="tahap-ai" class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="size-6 rounded-full bg-primary-500 text-white font-black text-xs flex items-center justify-center">3</span>
                    <h2 class="text-base font-black text-ink-900 tracking-tight">Rekomendasi Solusi Cerdas (Google Gemini AI)</h2>
                </div>
                <p class="text-xs font-semibold text-ink-500 mt-0.5">
                    Hasil analisis otomatis dari data Rapor di atas, merumuskan kekuatan utama dan langkah pembenahan konkret bagi sekolah.
                </p>
            </div>
        </div>

        <div class="card p-6 border border-primary-200/80 shadow-card bg-gradient-to-br from-primary-50/40 via-white to-primary-50/20 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-primary-100">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-primary-400 text-ink-900 flex items-center justify-center shrink-0 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-extrabold text-primary-950">AI Diagnostic & Solution</span>
                            <span class="text-[10px] font-bold text-primary-800 bg-primary-100 px-2 py-0.5 rounded-md">Google Gemini 3.5 Flash-Lite</span>
                            <span class="text-[10px] font-semibold text-ink-400" x-show="adviceUpdatedAt">
                                · <span x-text="adviceUpdatedAt"></span>
                            </span>
                        </div>
                        <h3 class="text-base font-black text-ink-900 mt-0.5">Langkah Pembenahan Otomatis Berbasis Data Jurnal</h3>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button type="button"
                            @click="modalTanyaAiOpen = true"
                            class="btn-dark text-xs font-bold py-2 px-3.5 rounded-xl shadow-btn-dark flex items-center gap-1.5 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>Tanya Data AI</span>
                    </button>

                    <button type="button"
                            @click="refreshAdvice()"
                            :disabled="adviceLoading"
                            class="btn-primary text-xs font-bold py-2 px-3.5 rounded-xl shadow-btn flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                        <svg x-show="!adviceLoading" xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span x-show="adviceLoading" class="size-3.5 border-2 border-ink-900 border-t-transparent rounded-full animate-spin"></span>
                        <span x-text="adviceLoading ? 'Memproses...' : 'Minta Saran Baru'"></span>
                    </button>
                </div>
            </div>

            {{-- Dynamic update alert --}}
            <div x-show="adviceSuccessMsg"
                 x-cloak
                 class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span x-text="adviceSuccessMsg"></span>
            </div>

            {{-- AI Content Container --}}
            <div class="space-y-3.5" :class="adviceLoading ? 'opacity-40 pointer-events-none' : ''">
                <div class="p-4 rounded-2xl bg-white border border-primary-100 shadow-xs">
                    <h4 class="font-extrabold text-ink-900 text-sm mb-1" x-text="adviceTitle">
                        {{ $aiResult['insight']->title ?? 'Laporan Kondisi Karakter Siswa' }}
                    </h4>
                    <p class="text-xs sm:text-sm text-ink-700 leading-relaxed font-semibold" x-text="adviceSummary">
                        {{ $aiResult['insight']->summary ?? 'Menganalisis data jurnal...' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                    {{-- Strengths --}}
                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200/70">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-800 uppercase tracking-wider mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Kekuatan Teridentifikasi</span>
                        </div>
                        <ul class="text-xs text-ink-800 font-semibold space-y-1.5 list-disc list-inside">
                            <template x-for="st in adviceStrengths" :key="st">
                                <li x-text="st"></li>
                            </template>
                            @if(isset($aiResult['insight']))
                                <template x-if="!adviceStrengths || adviceStrengths.length === 0">
                                    <div>
                                        @foreach($aiResult['insight']->strengths ?? [] as $st)
                                            <li>{{ $st }}</li>
                                        @endforeach
                                    </div>
                                </template>
                            @endif
                        </ul>
                    </div>

                    {{-- Recommendations --}}
                    <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/70">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-amber-800 uppercase tracking-wider mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Rekomendasi Tindakan Strategis</span>
                        </div>
                        <ul class="text-xs text-ink-800 font-semibold space-y-1.5 list-disc list-inside">
                            <template x-for="rec in adviceRecommendations" :key="rec">
                                <li x-text="rec"></li>
                            </template>
                            @if(isset($aiResult['insight']))
                                <template x-if="!adviceRecommendations || adviceRecommendations.length === 0">
                                    <div>
                                        @foreach($aiResult['insight']->recommendations ?? [] as $rec)
                                            <li>{{ $rec }}</li>
                                        @endforeach
                                    </div>
                                </template>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAHAP 4: BUKTI DATA & TREN REAL-TIME (Grafik Historis & Komparasi Rombel) --}}
    <div id="tahap-grafik" class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="size-6 rounded-full bg-primary-500 text-white font-black text-xs flex items-center justify-center">4</span>
                    <h2 class="text-base font-black text-ink-900 tracking-tight">Bukti Data Statistik & Tren Aktivitas</h2>
                </div>
                <p class="text-xs font-semibold text-ink-500 mt-0.5">
                    Visualisasi data empiris pengisian jurnal harian, rasio gender, dan konsistensi antar rombel kelas.
                </p>
            </div>
            <span class="text-xs font-bold text-ink-500 bg-ink-100 px-2.5 py-1 rounded-full">Pembaruan Realtime</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Line Chart: Tren 7 Hari --}}
            <div class="lg:col-span-2 card p-5 border border-ink-100/80 shadow-card">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-extrabold text-ink-900 text-sm">Tren Jurnal Masuk & Kebiasaan Tuntas (7 Hari Terakhir)</h4>
                        <p class="text-[11px] text-ink-400 font-semibold">Mengukur dinamika kepatuhan siswa setiap hari</p>
                    </div>
                    <div class="flex items-center gap-3 text-xs font-bold">
                        <span class="flex items-center gap-1.5 text-sky-700">
                            <span class="size-2 rounded-full bg-sky-500"></span> Jurnal Masuk
                        </span>
                        <span class="flex items-center gap-1.5 text-emerald-700">
                            <span class="size-2 rounded-full bg-emerald-500"></span> Kebiasaan Tuntas
                        </span>
                    </div>
                </div>
                <div class="h-60 sm:h-64 w-full">
                    <canvas id="chartKeseluruhan"></canvas>
                </div>
            </div>

            {{-- Donut Chart: Distribusi Gender --}}
            <div class="card p-5 border border-ink-100/80 shadow-card flex flex-col justify-between">
                <div>
                    <h4 class="font-extrabold text-ink-900 text-sm mb-1">Distribusi Gender Siswa</h4>
                    <p class="text-[11px] text-ink-400 font-semibold mb-2">Komparasi siswa Laki-laki vs Perempuan</p>
                    <div class="relative size-44 mx-auto my-1">
                        <canvas id="chartGender"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-2xl font-black text-ink-900 tabular-nums">{{ $totalStudents }}</span>
                            <span class="text-[10px] font-bold text-ink-400 uppercase">Total Siswa</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-ink-100 grid grid-cols-2 gap-2 text-center text-xs">
                    <div class="p-2 rounded-xl bg-sky-50 border border-sky-100">
                        <span class="text-[11px] font-bold text-sky-800 block">Laki-laki</span>
                        <span class="text-sm font-black text-ink-900 tabular-nums">{{ $maleCount }}</span>
                        <span class="text-[10px] text-ink-500 block">({{ $totalStudents > 0 ? round(($maleCount / $totalStudents) * 100) : 0 }}%)</span>
                    </div>
                    <div class="p-2 rounded-xl bg-pink-50 border border-pink-100">
                        <span class="text-[11px] font-bold text-pink-700 block">Perempuan</span>
                        <span class="text-sm font-black text-ink-900 tabular-nums">{{ $femaleCount }}</span>
                        <span class="text-[10px] text-ink-500 block">({{ $totalStudents > 0 ? round(($femaleCount / $totalStudents) * 100) : 0 }}%)</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bar Chart: Per Rombel Kelas --}}
        <div class="card p-5 border border-ink-100/80 shadow-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="font-extrabold text-ink-900 text-sm">Partisipasi & Rata-rata Streak per Kelas</h4>
                    <p class="text-[11px] text-ink-400 font-semibold">Melihat rombel mana yang paling disiplin mempertahankan streak</p>
                </div>
                <div class="flex items-center gap-3 text-xs font-bold">
                    <span class="flex items-center gap-1.5 text-sky-700">
                        <span class="size-2 rounded-full bg-sky-400"></span> Jumlah Siswa
                    </span>
                    <span class="flex items-center gap-1.5 text-amber-700">
                        <span class="size-2 rounded-full bg-amber-400"></span> Streak Rata-rata (Hari)
                    </span>
                </div>
            </div>
            <div class="h-60 sm:h-64 w-full">
                <canvas id="chartPerKelas"></canvas>
            </div>
        </div>
    </div>

    {{-- TAHAP 5: APRESIASI SISWA TELADAN (Papan Peringkat Top 10) --}}
    <div id="tahap-peringkat" class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="size-6 rounded-full bg-primary-500 text-white font-black text-xs flex items-center justify-center">5</span>
                    <h2 class="text-base font-black text-ink-900 tracking-tight">Apresiasi Siswa Teladan (Top 10 Leaderboard)</h2>
                </div>
                <p class="text-xs font-semibold text-ink-500 mt-0.5">
                    Podium Kehormatan 7 Kebiasaan: Apresiasi konsistensi siswa untuk peringkat 1 (Emas), 2 (Perak), dan 3 (Perunggu).
                </p>
            </div>
            <a href="{{ route('admin.leaderboard') }}"
               class="btn-outline text-xs font-bold py-2 px-3.5 rounded-xl flex items-center gap-1.5 hover:bg-ink-50">
                <span>Lihat Seluruh Siswa</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <div class="card p-5 sm:p-6 border border-ink-100/80 shadow-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-ink-100 text-[11px] font-extrabold text-ink-500 uppercase tracking-wider">
                            <th class="py-2.5 px-3 w-28 text-center">Peringkat</th>
                            <th class="py-2.5 px-3">Nama Siswa</th>
                            <th class="py-2.5 px-3">Kelas</th>
                            <th class="py-2.5 px-3 text-center">Streak Aktif</th>
                            <th class="py-2.5 px-3 text-right">Total XP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100/70">
                        @forelse($topStudents->take(10) as $index => $st)
                            @php
                                $rank = $index + 1;
                                $cls = $st->currentClassroom();
                                $badgeLabel = match($rank) {
                                    1 => '#1 · Emas',
                                    2 => '#2 · Perak',
                                    3 => '#3 · Perunggu',
                                    default => '#' . $rank,
                                };
                                $badgeClass = match($rank) {
                                    1 => 'bg-amber-100 text-amber-900 border border-amber-300 font-black',
                                    2 => 'bg-slate-200 text-slate-800 border border-slate-300 font-black',
                                    3 => 'bg-orange-100 text-orange-900 border border-orange-200 font-black',
                                    default => 'bg-ink-100 text-ink-600 font-bold',
                                };
                            @endphp
                            <tr class="hover:bg-primary-50/40 transition">
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs {{ $badgeClass }}">
                                        {{ $badgeLabel }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="size-7 rounded-full bg-primary-100 text-primary-900 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ substr($st->name, 0, 1) }}
                                        </div>
                                        <span class="font-bold text-ink-900 truncate">{{ $st->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    @if($cls)
                                        <span class="text-[11px] font-bold text-ink-700 bg-canvas border border-ink-100 px-2 py-0.5 rounded-md">
                                            {{ $cls->name }}
                                        </span>
                                    @else
                                        <span class="text-ink-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-flex items-center gap-1 font-bold text-amber-700 bg-sun-soft/60 px-2.5 py-0.5 rounded-full text-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.527.82-1.17 2.106-1.523 3.65a12.87 12.87 0 00-.31 1.764c-.45-.233-.92-.47-1.396-.708a1 1 0 00-1.424.877c-.01.597.027 1.343.197 2.158.347 1.666 1.13 3.447 2.478 4.678 1.48 1.353 3.518 1.83 5.378 1.155 1.86-.675 3.077-2.348 3.077-4.327 0-2.316-1.22-4.57-2.34-6.31-.692-1.077-1.37-2.023-1.865-2.774z" clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ $st->current_streak }} Hari</span>
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right font-black text-ink-900 tabular-nums">
                                    {{ number_format($st->xp) }} <span class="text-[10px] text-ink-500 font-bold">XP</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-ink-500 font-semibold">
                                    Belum ada data peringkat siswa untuk ditampilkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL: APA ARTI CAPAIAN SAYA --}}
    <div x-show="modalArtiOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-xs"
         @keydown.escape.window="modalArtiOpen = false">
        <div class="card p-6 max-w-lg w-full space-y-4 shadow-pop bg-white rounded-3xl" @click.away="modalArtiOpen = false">
            <div class="flex items-center justify-between">
                <h4 class="font-black text-ink-900 text-base" x-text="modalArtiTitle">Penjelasan Capaian</h4>
                <button type="button" @click="modalArtiOpen = false" class="text-ink-400 hover:text-ink-900 font-bold text-lg p-1">✕</button>
            </div>
            <div class="p-4 rounded-2xl bg-canvas border border-ink-100">
                <p class="text-xs sm:text-sm text-ink-700 font-semibold leading-relaxed" x-text="modalArtiContent"></p>
            </div>
            <div class="flex justify-end pt-1">
                <button type="button" @click="modalArtiOpen = false" class="btn-primary text-xs font-bold py-2 px-4 rounded-xl">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL: PELAJARI AKAR MASALAH --}}
    <div x-show="modalAkarOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-xs"
         @keydown.escape.window="modalAkarOpen = false">
        <div class="card p-6 max-w-lg w-full space-y-4 shadow-pop bg-white rounded-3xl" @click.away="modalAkarOpen = false">
            <div class="flex items-center justify-between">
                <h4 class="font-black text-ink-900 text-base" x-text="modalAkarTitle">Identifikasi Masalah</h4>
                <button type="button" @click="modalAkarOpen = false" class="text-ink-400 hover:text-ink-900 font-bold text-lg p-1">✕</button>
            </div>

            <div class="space-y-3">
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200">
                    <span class="text-xs font-bold text-rose-800 uppercase block mb-1">Akar Masalah Teridentifikasi:</span>
                    <ul class="text-xs text-ink-900 font-semibold space-y-1 list-disc list-inside">
                        <template x-for="p in modalAkarPenyebab" :key="p">
                            <li x-text="p"></li>
                        </template>
                    </ul>
                </div>

                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200">
                    <span class="text-xs font-bold text-emerald-800 uppercase block mb-1">Rekomendasi Pembenahan:</span>
                    <ul class="text-xs text-ink-900 font-semibold space-y-1 list-disc list-inside">
                        <template x-for="s in modalAkarSolusi" :key="s">
                            <li x-text="s"></li>
                        </template>
                    </ul>
                </div>
            </div>

            <div class="flex justify-end pt-1">
                <button type="button" @click="modalAkarOpen = false" class="btn-dark text-xs font-bold py-2 px-4 rounded-xl">
                    Selesai
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL: TANYA DATA AI --}}
    <div x-show="modalTanyaAiOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-xs"
         @keydown.escape.window="modalTanyaAiOpen = false">
        <div class="card p-6 max-w-xl w-full border border-primary-200 shadow-2xl space-y-4 bg-white rounded-3xl"
             @click.away="modalTanyaAiOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-ink-100">
                <div class="flex items-center gap-2.5">
                    <div class="size-9 rounded-xl bg-primary-100 text-primary-800 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-ink-900 text-base">Tanya Data AI</h3>
                        <p class="text-xs font-semibold text-ink-500">Ajukan pertanyaan analitis seputar data kebiasaan sekolah</p>
                    </div>
                </div>
                <button type="button" @click="modalTanyaAiOpen = false" class="size-8 rounded-full bg-ink-100 hover:bg-ink-200 text-ink-700 flex items-center justify-center font-bold text-sm">
                    ✕
                </button>
            </div>

            {{-- Preset questions --}}
            <div class="space-y-1.5">
                <span class="text-[11px] font-bold text-ink-500 uppercase tracking-wider block">Pertanyaan Cepat:</span>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button"
                            @click="askAi('Kebiasaan apa yang paling rendah di sekolah minggu ini?')"
                            class="px-2.5 py-1 rounded-lg bg-ink-50 hover:bg-primary-50 text-ink-700 text-xs font-semibold border border-ink-100 transition">
                        Kebiasaan terendah minggu ini?
                    </button>
                    <button type="button"
                            @click="askAi('Berapa rata-rata streak seluruh siswa di sekolah?')"
                            class="px-2.5 py-1 rounded-lg bg-ink-50 hover:bg-primary-50 text-ink-700 text-xs font-semibold border border-ink-100 transition">
                        Rata-rata streak sekolah?
                    </button>
                    <button type="button"
                            @click="askAi('Berapa persen siswa yang aktif mengisi jurnal hari ini?')"
                            class="px-2.5 py-1 rounded-lg bg-ink-50 hover:bg-primary-50 text-ink-700 text-xs font-semibold border border-ink-100 transition">
                        Kepatuhan jurnal hari ini?
                    </button>
                </div>
            </div>

            {{-- Question Form --}}
            <form @submit.prevent="askAi()" class="space-y-3">
                <div class="relative">
                    <input type="text"
                           x-model="aiQuestion"
                           placeholder="Ketik pertanyaan analitis Anda tentang data sekolah..."
                           class="input w-full pr-16 text-xs sm:text-sm py-2.5 rounded-xl border-ink-200"
                           :disabled="aiLoading">
                    <button type="submit"
                            :disabled="aiLoading || !aiQuestion.trim()"
                            class="absolute right-1.5 top-1.5 btn-primary px-3 py-1 rounded-lg text-xs font-bold disabled:opacity-50">
                        <span x-show="!aiLoading">Kirim</span>
                        <span x-show="aiLoading">...</span>
                    </button>
                </div>
            </form>

            {{-- Answer area --}}
            <div x-show="aiLoading" class="p-6 rounded-2xl bg-primary-50/60 border border-primary-200 text-center space-y-3">
                <div class="flex justify-center">
                    <x-mascot state="berpikir" size="md" :animate="true" />
                </div>
                <div class="space-y-1">
                    <p class="text-xs sm:text-sm font-extrabold text-ink-900">Asisten SiHebat Sedang Menganalisis...</p>
                    <p class="text-[11px] font-semibold text-ink-500">Mengkalkulasi capaian 7 kebiasaan & merumuskan rekomendasi praktis.</p>
                </div>
            </div>

            <div x-show="aiAnswer && !aiLoading" class="space-y-3 pt-1">
                <div class="p-4 rounded-2xl bg-white border border-primary-200 shadow-sm space-y-3">
                    {{-- Header with mascot & source badge --}}
                    <div class="flex items-center justify-between pb-2.5 border-b border-ink-100">
                        <div class="flex items-center gap-2.5">
                            <div class="size-8 rounded-full bg-primary-50 border border-primary-200 flex items-center justify-center overflow-hidden shrink-0">
                                <x-mascot state="senang" size="sm" :animate="false" class="scale-75" />
                            </div>
                            <div>
                                <span class="text-xs font-extrabold text-ink-900 block leading-tight">Insight Asisten SiHebat</span>
                                <span class="text-[10px] font-semibold text-ink-500">Analisis Rapor Karakter Sekolah</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-primary-800 bg-primary-50 px-2.5 py-1 rounded-full border border-primary-200"
                              x-text="aiSource === 'gemini_api' || aiSource === 'gemini' ? '✨ Gemini AI' : '📊 Realtime Database'"></span>
                    </div>

                    {{-- Bento Metric Card --}}
                    <template x-if="aiDetails && aiDetails.metric_value">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-primary-50/90 via-sky-50/40 to-white border border-primary-200/90 shadow-2xs">
                            <div class="flex items-center gap-2.5">
                                <div class="size-9 rounded-xl bg-primary-600 text-white flex items-center justify-center font-black text-sm shadow-xs shrink-0">
                                    📈
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-ink-500 uppercase tracking-wider block" x-text="aiDetails.metric_label"></span>
                                    <span class="text-base sm:text-lg font-black text-ink-900 tracking-tight" x-text="aiDetails.metric_value"></span>
                                </div>
                            </div>
                            <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-full border shadow-2xs"
                                  :class="{
                                      'bg-emerald-50 text-emerald-800 border-emerald-200': aiDetails.metric_status === 'baik',
                                      'bg-amber-50 text-amber-800 border-amber-200': aiDetails.metric_status === 'cukup',
                                      'bg-rose-50 text-rose-800 border-rose-200': aiDetails.metric_status === 'perhatian'
                                  }"
                                  x-text="aiDetails.metric_badge || (aiDetails.metric_status === 'baik' ? 'Baik' : (aiDetails.metric_status === 'cukup' ? 'Cukup' : 'Perlu Perhatian'))">
                            </span>
                        </div>
                    </template>

                    {{-- Finding observation text (Markdown converted, bold highlighted, zero raw asterisks) --}}
                    <div class="p-3.5 rounded-xl bg-ink-50/70 border border-ink-100 text-xs sm:text-sm font-medium text-ink-800 leading-relaxed space-y-1.5"
                         x-html="formatAiMarkdown(aiAnswer, aiDetails)">
                    </div>

                    {{-- Distinct Actionable Recommendation Card --}}
                    <template x-if="aiDetails && aiDetails.recommendation">
                        <div class="p-3.5 rounded-xl bg-amber-50/80 border border-amber-200 text-xs sm:text-sm text-amber-950 space-y-1.5 shadow-2xs">
                            <div class="flex items-center gap-1.5 font-extrabold text-amber-900">
                                <span class="text-base">💡</span>
                                <span class="text-xs uppercase tracking-wider font-black">Rekomendasi Tindakan Praktis</span>
                            </div>
                            <p class="font-semibold text-ink-900 leading-relaxed text-xs sm:text-sm" x-text="aiDetails.recommendation"></p>
                        </div>
                    </template>

                    {{-- Action buttons toolbar --}}
                    <div class="flex items-center justify-between pt-1">
                        <div>
                            <template x-if="aiDetails && aiDetails.habit_slug">
                                <a :href="'{{ route('admin.akar-masalah') }}?habit=' + aiDetails.habit_slug"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs shadow-xs transition">
                                    <span>🔍 Pelajari Akar Masalah</span>
                                </a>
                            </template>
                        </div>

                        <button type="button"
                                @click="copyAiResponse()"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-ink-200 hover:bg-ink-100 text-ink-700 font-bold text-xs transition">
                            <span x-text="aiCopied ? '✓ Tersalin!' : '📋 Salin Ringkasan'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Grafik Tren Keseluruhan
    const ctxKeseluruhan = document.getElementById('chartKeseluruhan');
    if (ctxKeseluruhan) {
        new Chart(ctxKeseluruhan, {
            type: 'line',
            data: {
                labels: @json($chartDates),
                datasets: [
                    {
                        label: 'Jurnal Masuk',
                        data: @json($chartJournals),
                        borderColor: '#0284C7',
                        backgroundColor: 'rgba(56, 189, 248, 0.15)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#0284C7',
                        pointRadius: 3.5,
                        pointHoverRadius: 5,
                    },
                    {
                        label: 'Kebiasaan Tuntas',
                        data: @json($chartHabitsCompleted),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#10B981',
                        pointRadius: 3.5,
                        pointHoverRadius: 5,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600' }
                        },
                        grid: { color: '#EAF1F8' }
                    },
                    x: {
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600' }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 2. Grafik Distribusi Gender
    const ctxGender = document.getElementById('chartGender');
    if (ctxGender) {
        new Chart(ctxGender, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [{{ $maleCount }}, {{ $femaleCount }}],
                    backgroundColor: ['#38BDF8', '#F472B6'],
                    borderWidth: 3,
                    borderColor: '#FFFFFF',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // 3. Grafik Per Kelas
    const ctxPerKelas = document.getElementById('chartPerKelas');
    if (ctxPerKelas) {
        new Chart(ctxPerKelas, {
            type: 'bar',
            data: {
                labels: @json($classNames),
                datasets: [
                    {
                        label: 'Jumlah Siswa',
                        data: @json($classStudentCounts),
                        backgroundColor: '#38BDF8',
                        borderRadius: 8,
                    },
                    {
                        label: 'Rata-rata Streak (Hari)',
                        data: @json($classAvgStreaks),
                        backgroundColor: '#FBBF24',
                        borderRadius: 8,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600' }
                        },
                        grid: { color: '#EAF1F8' }
                    },
                    x: {
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600' }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
