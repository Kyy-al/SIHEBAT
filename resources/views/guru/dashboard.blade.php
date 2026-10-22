@extends('layouts.admin')

@section('title', 'Dashboard Wali Kelas — SiHebat')

@section('content')
<script>
function guruDashboard() {
    return {
        modalTanyaAi: false,
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
            fetch('{{ route('guru.ai.ask') }}', {
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
        }
    };
}
</script>

<div class="max-w-7xl mx-auto space-y-6" x-data="guruDashboard()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">
                Dashboard Wali Kelas
            </h1>
            <p class="text-sm font-semibold text-ink-500 mt-1">
                @if($classroom)
                    Pantauan Jurnal 7 Kebiasaan Kelas <strong class="text-ink-900">{{ $classroom->name }}</strong> · Semester {{ $classroom->academicYear->semester ?? 'Ganjil' }}
                @else
                    Belum ada kelas yang diampu pada tahun ajaran aktif.
                @endif
            </p>
        </div>

        @if($classroom)
            <div class="flex items-center gap-2">
                <button type="button"
                        @click="modalTanyaAi = true"
                        class="btn-primary text-xs font-extrabold py-2 px-3.5 rounded-xl shadow-btn flex items-center gap-1.5 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Tanya Data AI</span>
                </button>

                <a href="{{ route('guru.report.print', $classroom->id) }}" target="_blank"
                   class="btn-outline text-xs font-bold py-2 px-3.5 rounded-xl flex items-center gap-1.5 bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="currentColor" viewBox="0 0 256 256"><path d="M224,96H200a8,8,0,0,0,0,16h24v80H32V112H56a8,8,0,0,0,0-16H32a16,16,0,0,0-16,16v80a16,16,0,0,0,16,16H64v16a16,16,0,0,0,16,16H176a16,16,0,0,0,16-16V208h32a16,16,0,0,0,16-16V112A16,16,0,0,0,224,96Zm-48,128H80V176h96ZM192,48V80a8,8,0,0,1-16,0V48H80V80a8,8,0,0,1-16,0V48A16,16,0,0,1,80,32h96A16,16,0,0,1,192,48Z"/></svg>
                    <span>Cetak Rapor Kelas</span>
                </a>
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

        {{-- Top 3 Bento Metric Cards (Image 1 style) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
            {{-- Metric 1: Total Siswa --}}
            <div class="card p-5 border border-ink-100/70 shadow-card flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">Total Siswa Kelas</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black text-ink-900 tabular-nums">{{ $stats['total_students'] }}</span>
                        <span class="text-xs font-extrabold text-emerald-700 bg-mint-soft px-1.5 py-0.5 rounded-md">
                            100% Terdaftar
                        </span>
                    </div>
                    <span class="text-[11px] font-semibold text-ink-500">Kelas {{ $classroom->name }}</span>
                </div>

                {{-- Mini Bar comparison (SVG) --}}
                <div class="h-12 w-20 flex items-end justify-between gap-1 px-1" aria-hidden="true">
                    <span class="w-2.5 bg-primary-100 rounded-t-sm h-[40%]"></span>
                    <span class="w-2.5 bg-primary-200 rounded-t-sm h-[60%]"></span>
                    <span class="w-2.5 bg-primary-300 rounded-t-sm h-[75%]"></span>
                    <span class="w-2.5 bg-primary-400 rounded-t-sm h-[90%]"></span>
                    <span class="w-2.5 bg-primary-600 rounded-t-sm h-[100%]"></span>
                </div>
            </div>

            {{-- Metric 2: Partisipasi Hari Ini --}}
            <div class="card p-5 border border-ink-100/70 shadow-card flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">Partisipasi Hari Ini</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black text-primary-600 tabular-nums">{{ $stats['today_filled'] }}/{{ $stats['total_students'] }}</span>
                        @php
                            $fillRate = $stats['total_students'] > 0 ? round(($stats['today_filled'] / $stats['total_students']) * 100) : 0;
                        @endphp
                        <span class="text-xs font-extrabold text-primary-700 bg-primary-100 px-1.5 py-0.5 rounded-md">
                            {{ $fillRate }}%
                        </span>
                    </div>
                    <span class="text-[11px] font-semibold text-ink-500">Siswa telah mencatat jurnal</span>
                </div>

                <div class="h-12 w-20 flex items-center justify-center gap-1.5" aria-hidden="true">
                    <span class="size-3 rounded-full bg-primary-200"></span>
                    <span class="size-4 rounded-full bg-primary-400"></span>
                    <span class="size-5 rounded-full bg-primary-600"></span>
                </div>
            </div>

            {{-- Metric 3: Rata-rata Ketercapaian Kebiasaan --}}
            <div class="card p-5 border border-ink-100/70 shadow-card flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">Rata-rata Kebiasaan</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black text-emerald-600 tabular-nums">{{ $stats['avg_completion'] }}</span>
                        <span class="text-xs font-extrabold text-ink-500">/ 7 kebiasaan</span>
                    </div>
                    <span class="text-[11px] font-semibold text-ink-500">Performa mingguan kelas</span>
                </div>

                <div class="h-10 w-20 flex items-center justify-center" aria-hidden="true">
                    <svg viewBox="0 0 80 40" class="w-full h-full stroke-emerald-500 fill-none" stroke-width="3" stroke-linecap="round">
                        <path d="M 5 28 Q 20 8, 40 18 T 75 6" />
                        <circle cx="75" cy="6" r="4" fill="#0B6B47" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- 7 Kebiasaan Bento Grid (Rapor Pendidikan) --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-ink-900">Rapor 7 Kebiasaan Kelas {{ $classroom->name }}</h2>
                    <p class="text-xs font-semibold text-ink-500">Persentase siswa yang mencapai target pada masing-masing kebiasaan</p>
                </div>
                <a href="{{ route('guru.classroom', $classroom->id) }}" class="btn-ghost text-xs sm:text-sm font-bold">
                    Detail Seluruh Siswa →
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

                    <div class="card p-5 border border-ink-100/70 shadow-card flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between mb-3">
                                <div class="size-11 rounded-2xl {{ $habit->soft_color }} flex items-center justify-center p-1 shadow-sm">
                                    <x-mascot-habit :habit="$habit->slug" size="sm" />
                                </div>

                                <span class="{{ $statusClass }} text-[11px] py-0.5 px-2.5 font-extrabold">
                                    <span>{{ $statusIcon }}</span>
                                    <span>{{ $status }}</span>
                                </span>
                            </div>

                            <h4 class="font-extrabold text-ink-900 text-sm leading-snug">
                                {{ $habit->name }}
                            </h4>

                            <div class="mt-3 flex items-baseline gap-1">
                                <span class="text-2xl font-black text-ink-900 tabular-nums">{{ $rate }}%</span>
                                <span class="text-xs font-semibold text-ink-500">tuntas</span>
                            </div>

                            <div class="progress-track h-2 mt-2 bg-ink-100">
                                <div class="progress-fill" style="width: {{ $rate }}%; background-color: {{ $habit->color_hex }}"></div>
                            </div>
                        </div>

                        <div class="pt-3 mt-3 border-t border-ink-100">
                            <span class="text-[11px] font-semibold text-ink-500">
                                Ambang: {{ $status === 'Baik' ? '≥ 75%' : ($status === 'Cukup' ? '50% - 74%' : '< 50%') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Deteksi Dini & Mascot Motivation --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Siswa Perlu Perhatian (2 cols) --}}
            <div class="lg:col-span-2 card p-5 sm:p-6 border border-ink-100/70 shadow-card">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">⚠️</span>
                        <div>
                            <h3 class="text-base font-extrabold text-ink-900">Deteksi Dini: Siswa Perlu Perhatian</h3>
                            <p class="text-xs font-semibold text-ink-500">Siswa dengan streak terputus atau belum mengisi jurnal</p>
                        </div>
                    </div>
                </div>

                @if(count($stats['needs_attention']) > 0)
                    <div class="space-y-2">
                        @foreach($stats['needs_attention'] as $st)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 rounded-2xl bg-canvas border border-ink-100 gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="avatar size-9 text-xs shrink-0 font-black">
                                        {{ substr($st->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-ink-900">{{ $st->name }}</p>
                                        <p class="text-xs font-semibold text-ink-500">
                                            NIS: {{ $st->nis }} · Terakhir isi: {{ $st->last_journal_date ? $st->last_journal_date->locale('id')->isoFormat('D MMM Y') : 'Belum pernah' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 self-end sm:self-auto">
                                    <span class="chip-status-perlu text-[11px] py-1 px-3 font-extrabold">
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

            {{-- Mascot AI Insight Box --}}
            <div class="card p-5 border border-primary-200 shadow-pop bg-gradient-to-b from-primary-50 to-white flex flex-col justify-between text-center">
                <div>
                    <x-mascot state="semangat" size="lg" />
                    <span class="chip bg-primary-400 text-ink-900 text-[10px] font-black uppercase mt-2 inline-block">
                        Tips AI Wali Kelas
                    </span>
                    <h4 class="font-extrabold text-ink-900 text-sm mt-2">
                        Beri Semangat di Kebiasaan Tidur Cepat
                    </h4>
                    <p class="text-xs text-ink-700 font-medium mt-1 leading-relaxed">
                        Mayoritas siswa tidur di atas pukul 22.00 WIB. Diskusikan pentingnya istirahat cukup pada sesi perwalian mingguan.
                    </p>
                </div>

                <div class="pt-4 mt-3 border-t border-ink-100">
                    <a href="{{ route('guru.classroom', $classroom->id) }}" class="btn-primary w-full text-xs font-bold py-2.5">
                        Lihat Data Rombel Lengkap →
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL: TANYA DATA AI (WALI KELAS) --}}
    <div x-show="modalTanyaAi"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-sm"
         @keydown.escape.window="modalTanyaAi = false">
        <div class="card p-6 sm:p-7 max-w-xl w-full border border-primary-200 shadow-2xl space-y-4 bg-white rounded-3xl"
             @click.away="modalTanyaAi = false">
            <div class="flex items-center justify-between pb-3 border-b border-ink-100">
                <div class="flex items-center gap-2.5">
                    <div class="size-9 rounded-xl bg-primary-100 text-primary-800 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-ink-900 text-base">Tanya Data AI (Wali Kelas)</h3>
                        <p class="text-xs font-semibold text-ink-500">Ajukan pertanyaan natural tentang data kebiasaan kelas</p>
                    </div>
                </div>
                <button type="button" @click="modalTanyaAi = false" class="btn-icon size-9 rounded-full bg-ink-100 hover:bg-ink-200 text-ink-700">
                    ✕
                </button>
            </div>

            {{-- Preset questions --}}
            <div class="space-y-1.5">
                <span class="text-[11px] font-bold text-ink-500 uppercase tracking-wider block">Pilihan Pertanyaan Cepat:</span>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button"
                            @click="askAi('Kebiasaan apa yang paling rendah di sekolah minggu ini?')"
                            class="px-2.5 py-1 rounded-lg bg-ink-50 hover:bg-primary-50 text-ink-700 text-xs font-semibold border border-ink-100 transition">
                        Kebiasaan terendah minggu ini?
                    </button>
                    <button type="button"
                            @click="askAi('Berapa rata-rata streak siswa kelas ini?')"
                            class="px-2.5 py-1 rounded-lg bg-ink-50 hover:bg-primary-50 text-ink-700 text-xs font-semibold border border-ink-100 transition">
                        Rata-rata streak kelas?
                    </button>
                    <button type="button"
                            @click="askAi('Berapa siswa yang sudah mengisi jurnal hari ini?')"
                            class="px-2.5 py-1 rounded-lg bg-ink-50 hover:bg-primary-50 text-ink-700 text-xs font-semibold border border-ink-100 transition">
                        Siswa mengisi hari ini?
                    </button>
                </div>
            </div>

            {{-- Question Form --}}
            <form @submit.prevent="askAi()" class="space-y-3">
                <div class="relative">
                    <input type="text"
                           x-model="aiQuestion"
                           placeholder="Ketik pertanyaan Anda tentang data kebiasaan..."
                           class="input w-full pr-12 text-xs sm:text-sm py-3 rounded-2xl border-ink-200"
                           :disabled="aiLoading">
                    <button type="submit"
                            :disabled="aiLoading || !aiQuestion.trim()"
                            class="absolute right-2 top-2 btn-primary px-3 py-1.5 rounded-xl text-xs font-bold disabled:opacity-50">
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
                    <p class="text-[11px] font-semibold text-ink-500">Mengkalkulasi capaian 7 kebiasaan kelas & merumuskan rekomendasi praktis.</p>
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
                                <span class="text-[10px] font-semibold text-ink-500">Analisis Karakter Kelas & Sekolah</span>
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
                                <span class="text-xs uppercase tracking-wider font-black">Rekomendasi Tindakan Wali Kelas</span>
                            </div>
                            <p class="font-semibold text-ink-900 leading-relaxed text-xs sm:text-sm" x-text="aiDetails.recommendation"></p>
                        </div>
                    </template>

                    {{-- Action buttons toolbar --}}
                    <div class="flex items-center justify-between pt-1">
                        <div>
                            @if($classroom)
                                <a href="{{ route('guru.classroom', $classroom->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs shadow-xs transition">
                                    <span>🔍 Buka Jurnal Kelas {{ $classroom->name }}</span>
                                </a>
                            @endif
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
@endsection
