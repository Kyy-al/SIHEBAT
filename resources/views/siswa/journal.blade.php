@extends('layouts.siswa')

@section('title', 'Isi Jurnal Hari Ini — SiHebat')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-4 sm:py-6" id="journal-app">
    {{-- Top Navigation / Back --}}
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('siswa.dashboard') }}" class="btn-ghost text-sm flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="currentColor" viewBox="0 0 256 256">
                <path d="M165.66,202.34a8,8,0,0,1-11.32,11.32l-80-80a8,8,0,0,1,0-11.32l80-80a8,8,0,0,1,11.32,11.32L91.31,128Z"/>
            </svg>
            Kembali ke Beranda
        </a>
        <span class="text-xs font-bold px-3 py-1 rounded-full bg-primary-100 text-primary-800">
            {{ $today->locale('id')->isoFormat('dddd, D MMMM Y') }}
        </span>
    </div>

    {{-- Header Banner & Live Progress --}}
    <div class="card p-5 sm:p-6 mb-6 border border-ink-100 sticky top-2 z-20 shadow-md">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
                <span class="text-2xl">📝</span>
                <div>
                    <h1 class="text-lg sm:text-xl font-extrabold text-ink-900">Jurnal 7 Kebiasaan Hebat</h1>
                    <p class="text-xs font-semibold text-ink-500">Sentuh untuk memilih aktivitas yang telah kamu jalani</p>
                </div>
            </div>
            <div class="text-right">
                <span id="progress-text" class="text-base sm:text-lg font-extrabold text-primary-700 tabular-nums">0 dari 7</span>
                <span class="block text-[10px] font-bold text-ink-500 uppercase tracking-wider">Tercapai</span>
            </div>
        </div>

        {{-- Progress Track --}}
        <div class="progress-track h-3 bg-ink-100">
            <div id="progress-bar" class="progress-fill bg-primary-400" style="width: 0%"></div>
        </div>
    </div>

    {{-- Journal Form --}}
    <form method="POST" action="{{ route('siswa.journal.save') }}" id="journal-form" class="space-y-4">
        @csrf

        @foreach($habits as $index => $habit)
            @php
                $entry = $existingEntries[$habit->id] ?? null;
                $val = $entry['value'] ?? [];
                $prefix = 'habit_' . $habit->id;

                $bgClass = match($habit->color) {
                    'bangun'   => 'bg-habit-bangun-soft',
                    'ibadah'   => 'bg-habit-ibadah-soft',
                    'olahraga' => 'bg-habit-olahraga-soft',
                    'makan'    => 'bg-habit-makan-soft',
                    'belajar'  => 'bg-habit-belajar-soft',
                    'sosial'   => 'bg-habit-sosial-soft',
                    'tidur'    => 'bg-habit-tidur-soft',
                    default    => 'bg-primary-50',
                };
            @endphp

            <div class="card p-5 border border-ink-100 transition-all hover:shadow-md habit-card" data-slug="{{ $habit->slug }}" data-id="{{ $habit->id }}">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="size-12 rounded-2xl {{ $bgClass }} flex items-center justify-center text-2xl shadow-sm">
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
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-ink-500 uppercase tracking-wider">Kebiasaan {{ $index + 1 }}</span>
                                <span class="chip-xp text-[10px] py-0.5 px-2">+{{ $habit->xp_reward }} XP</span>
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-ink-900 leading-tight">
                                {{ $habit->name }}
                            </h3>
                        </div>
                    </div>

                    {{-- Completion indicator icon --}}
                    <div class="status-indicator size-8 rounded-full border-2 border-ink-300 flex items-center justify-center text-xs font-bold text-transparent transition-all">
                        ✓
                    </div>
                </div>

                {{-- Input controls per habit --}}
                <div class="pt-2">
                    @switch($habit->slug)
                        {{-- 1. BANGUN PAGI --}}
                        @case('bangun-pagi')
                            @php $savedTime = $val['time'] ?? ''; @endphp
                            <div>
                                <label class="input-label">Pukul berapa kamu bangun pagi ini?</label>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @foreach(['04:30', '05:00', '05:30', '06:00'] as $preset)
                                        <button type="button"
                                                class="chip-btn py-2 px-4 rounded-xl border border-ink-300 bg-white font-bold text-sm text-ink-700 transition hover:bg-primary-50 active:scale-95"
                                                onclick="setPreset('{{ $prefix }}_time', '{{ $preset }}')">
                                            {{ $preset }}
                                        </button>
                                    @endforeach
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="time"
                                           id="{{ $prefix }}_time"
                                           name="{{ $prefix }}_time"
                                           value="{{ $savedTime }}"
                                           class="input max-w-[200px] habit-input"
                                           onchange="updateProgress()">
                                    <span class="text-xs text-ink-500 font-semibold">Tercapai jika ≤ 05.00</span>
                                </div>
                            </div>
                            @break

                        {{-- 2. BERIBADAH --}}
                        @case('beribadah')
                            @php
                                $done = $val['done'] ?? false;
                                $savedTypes = $val['types'] ?? [];
                            @endphp
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox"
                                           id="{{ $prefix }}_done"
                                           name="{{ $prefix }}_done"
                                           value="1"
                                           class="sr-only peer habit-input"
                                           {{ $done ? 'checked' : '' }}
                                           onchange="updateProgress()">
                                    <div class="w-12 h-7 bg-ink-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-mint relative"></div>
                                    <span class="font-bold text-ink-900 text-sm">Sudah melaksanakan ibadah hari ini</span>
                                </label>

                                <div class="border-t border-ink-100 pt-3">
                                    <span class="text-xs font-bold text-ink-700 block mb-2">Jenis / Waktu Ibadah (pilihan inklusif):</span>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                        @foreach(['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya', 'Doa / Ibadah Lain'] as $ib)
                                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-ink-100 bg-white hover:bg-canvas cursor-pointer text-xs font-semibold text-ink-700">
                                                <input type="checkbox"
                                                       name="{{ $prefix }}_types[]"
                                                       value="{{ $ib }}"
                                                       {{ in_array($ib, $savedTypes) ? 'checked' : '' }}
                                                       class="rounded text-primary-500 focus:ring-primary-400">
                                                <span>{{ $ib }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @break

                        {{-- 3. BEROLAHRAGA --}}
                        @case('berolahraga')
                            @php
                                $savedSport = $val['type'] ?? '';
                                $savedDur = $val['duration'] ?? 30;
                            @endphp
                            <div class="space-y-3">
                                <div>
                                    <label class="input-label">Jenis Olahraga</label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(['Lari', 'Futsal / Sepakbola', 'Senam', 'Bersepeda', 'Bulu Tangkis', 'Jalan Santai', 'Lainnya'] as $sport)
                                            <button type="button"
                                                    class="sport-btn chip-btn py-2 px-3.5 rounded-xl border border-ink-300 bg-white font-bold text-xs text-ink-700 transition hover:bg-primary-50 active:scale-95 {{ $savedSport === $sport ? 'border-primary-500 bg-primary-100 text-ink-900' : '' }}"
                                                    onclick="selectSport('{{ $prefix }}_type', '{{ $sport }}', this)">
                                                {{ $sport }}
                                            </button>
                                        @endforeach
                                    </div>
                                    <input type="hidden" id="{{ $prefix }}_type" name="{{ $prefix }}_type" value="{{ $savedSport }}" class="habit-input">
                                </div>

                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="input-label mb-0">Durasi Olahraga</label>
                                        <span class="text-sm font-extrabold text-primary-700"><span id="{{ $prefix }}_dur_label">{{ $savedDur }}</span> menit</span>
                                    </div>
                                    <input type="range"
                                           name="{{ $prefix }}_duration"
                                           id="{{ $prefix }}_duration"
                                           min="0"
                                           max="120"
                                           step="5"
                                           value="{{ $savedDur }}"
                                           class="w-full accent-primary-500 cursor-pointer habit-input"
                                           oninput="document.getElementById('{{ $prefix }}_dur_label').textContent = this.value; updateProgress();">
                                    <div class="flex justify-between text-[10px] text-ink-500 font-bold mt-1">
                                        <span>0 mnt</span>
                                        <span>Target: ≥ 30 menit</span>
                                        <span>120 mnt</span>
                                    </div>
                                </div>
                            </div>
                            @break

                        {{-- 4. MAKAN SEHAT --}}
                        @case('makan-sehat')
                            @php $savedItems = $val['items'] ?? []; @endphp
                            <div>
                                <label class="input-label mb-2">Checklist Menu & Kebiasaan Makan Hari Ini (min. 3 terpenuhi):</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @php
                                        $foodItems = [
                                            'sarapan'     => ['icon' => '🍳', 'label' => 'Sarapan pagi bergizi'],
                                            'siang'       => ['icon' => '🍱', 'label' => 'Makan siang tepat waktu'],
                                            'malam'       => ['icon' => '🍲', 'label' => 'Makan malam tidak larut'],
                                            'sayur_buah'  => ['icon' => '🥦', 'label' => 'Konsumsi sayur atau buah'],
                                            'air_putih'   => ['icon' => '💧', 'label' => 'Minum air putih cukup (±2L)'],
                                        ];
                                    @endphp

                                    @foreach($foodItems as $key => $item)
                                        <label class="flex items-center gap-3 p-3 rounded-2xl border border-ink-100 bg-white hover:bg-canvas cursor-pointer transition">
                                            <input type="checkbox"
                                                   name="{{ $prefix }}_items[]"
                                                   value="{{ $key }}"
                                                   {{ in_array($key, $savedItems) ? 'checked' : '' }}
                                                   class="habit-input food-checkbox rounded text-primary-500 focus:ring-primary-400 size-5"
                                                   onchange="updateProgress()">
                                            <span class="text-xl">{{ $item['icon'] }}</span>
                                            <span class="text-xs sm:text-sm font-bold text-ink-900">{{ $item['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @break

                        {{-- 5. GEMAR BELAJAR --}}
                        @case('gemar-belajar')
                            @php
                                $savedLearnDur = $val['duration'] ?? 45;
                                $savedTopic = $val['topic'] ?? '';
                            @endphp
                            <div class="space-y-3">
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="input-label mb-0">Durasi Belajar / Membaca Mandiri</label>
                                        <span class="text-sm font-extrabold text-primary-700"><span id="{{ $prefix }}_dur_label">{{ $savedLearnDur }}</span> menit</span>
                                    </div>
                                    <input type="range"
                                           name="{{ $prefix }}_duration"
                                           id="{{ $prefix }}_duration"
                                           min="0"
                                           max="180"
                                           step="5"
                                           value="{{ $savedLearnDur }}"
                                           class="w-full accent-primary-500 cursor-pointer habit-input"
                                           oninput="document.getElementById('{{ $prefix }}_dur_label').textContent = this.value; updateProgress();">
                                    <div class="flex justify-between text-[10px] text-ink-500 font-bold mt-1">
                                        <span>0 mnt</span>
                                        <span>Target: ≥ 30 menit</span>
                                        <span>180 mnt</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="input-label">Materi / Buku yang dipelajari (opsional)</label>
                                    <input type="text"
                                           name="{{ $prefix }}_topic"
                                           value="{{ $savedTopic }}"
                                           placeholder="Contoh: Matematika Trigonometri / Buku Sejarah"
                                           class="input text-sm">
                                </div>
                            </div>
                            @break

                        {{-- 6. BERMASYARAKAT --}}
                        @case('bermasyarakat')
                            @php $savedActivities = $val['activities'] ?? []; @endphp
                            <div>
                                <label class="input-label mb-2">Kegiatan Sosial / Gotong Royong Hari Ini (pilih min. 1):</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @php
                                        $socialActivities = [
                                            'bantu_orangtua' => 'Membantu orang tua / keluarga di rumah',
                                            'gotong_royong'  => 'Gotong royong di lingkungan sekolah / RT',
                                            'ekskul'         => 'Mengikuti kegiatan ekstrakurikuler / OSIS',
                                            'tolong_teman'   => 'Membantu teman yang kesulitan belajar',
                                            'sosial_lain'    => 'Kegiatan kemasyarakatan / ibadah bersama',
                                        ];
                                    @endphp

                                    @foreach($socialActivities as $key => $label)
                                        <label class="flex items-center gap-3 p-3 rounded-2xl border border-ink-100 bg-white hover:bg-canvas cursor-pointer transition">
                                            <input type="checkbox"
                                                   name="{{ $prefix }}_activities[]"
                                                   value="{{ $key }}"
                                                   {{ in_array($key, $savedActivities) ? 'checked' : '' }}
                                                   class="habit-input social-checkbox rounded text-primary-500 focus:ring-primary-400 size-5"
                                                   onchange="updateProgress()">
                                            <span class="text-xs sm:text-sm font-semibold text-ink-900">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @break

                        {{-- 7. TIDUR CEPAT --}}
                        @case('tidur-cepat')
                            @php $savedSleep = $val['time'] ?? ''; @endphp
                            <div>
                                <label class="input-label">Target tidur malam ini pukul berapa?</label>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @foreach(['20:30', '21:00', '21:30', '22:00'] as $preset)
                                        <button type="button"
                                                class="chip-btn py-2 px-4 rounded-xl border border-ink-300 bg-white font-bold text-sm text-ink-700 transition hover:bg-primary-50 active:scale-95"
                                                onclick="setPreset('{{ $prefix }}_time', '{{ $preset }}')">
                                            {{ $preset }}
                                        </button>
                                    @endforeach
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="time"
                                           id="{{ $prefix }}_time"
                                           name="{{ $prefix }}_time"
                                           value="{{ $savedSleep }}"
                                           class="input max-w-[200px] habit-input"
                                           onchange="updateProgress()">
                                    <span class="text-xs text-ink-500 font-semibold">Tercapai jika ≤ 21.30</span>
                                </div>
                            </div>
                            @break
                    @endswitch
                </div>
            </div>
        @endforeach

        {{-- Mood & Reflections Card --}}
        <div class="card p-5 sm:p-6 border border-ink-100 space-y-4">
            <h3 class="text-base sm:text-lg font-extrabold text-ink-900 flex items-center gap-2">
                <span>💭</span> Refleksi Harian & Mood
            </h3>

            <div>
                <label class="input-label">Bagaimana perasaanmu hari ini?</label>
                <div class="flex items-center justify-between sm:justify-start gap-2 sm:gap-4 flex-wrap pt-1">
                    @php
                        $moods = [
                            'sangat_senang' => ['emoji' => '😄', 'label' => 'Sangat Senang'],
                            'senang'        => ['emoji' => '😊', 'label' => 'Senang'],
                            'biasa'         => ['emoji' => '😐', 'label' => 'Biasa'],
                            'sedih'         => ['emoji' => '😢', 'label' => 'Sedih'],
                            'sangat_sedih'  => ['emoji' => '😭', 'label' => 'Kurang Baik'],
                        ];
                        $savedMood = $journal ? $journal->mood : 'senang';
                    @endphp

                    @foreach($moods as $val => $m)
                        <label class="flex flex-col items-center gap-1 cursor-pointer">
                            <input type="radio"
                                   name="mood"
                                   value="{{ $val }}"
                                   {{ $savedMood === $val ? 'checked' : '' }}
                                   class="sr-only peer">
                            <span class="size-13 sm:size-14 rounded-2xl flex items-center justify-center text-3xl border-2 border-ink-100 bg-white transition hover:scale-110 peer-checked:border-primary-400 peer-checked:bg-primary-50 peer-checked:shadow-sm">
                                {{ $m['emoji'] }}
                            </span>
                            <span class="text-[11px] font-bold text-ink-500 peer-checked:text-primary-700">
                                {{ $m['label'] }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label for="notes" class="input-label">Catatan Refleksi Singkat (opsional)</label>
                <textarea id="notes"
                          name="notes"
                          rows="3"
                          maxlength="300"
                          placeholder="Apa hal baik atau pelajaran berharga yang kamu dapat hari ini?"
                          class="input resize-none text-sm"
                          oninput="document.getElementById('char-count').textContent = this.value.length">{{ $journal ? $journal->notes : '' }}</textarea>
                <div class="flex justify-between text-xs text-ink-500 mt-1">
                    <span>Tulisanmu membantu mengenali kebiasaan baikmu.</span>
                    <span class="tabular-nums"><span id="char-count">{{ $journal && $journal->notes ? strlen($journal->notes) : 0 }}</span>/300</span>
                </div>
            </div>
        </div>

        {{-- Sticky Bottom Action Bar --}}
        <div class="sticky bottom-16 lg:bottom-4 z-30 pt-2">
            <div class="card p-4 sm:p-5 border border-primary-200 shadow-pop bg-white/95 backdrop-blur-md flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <span class="text-xs font-bold text-ink-500">Estimasi Reward:</span>
                    <div class="flex items-center gap-2">
                        <span id="reward-xp" class="text-lg font-extrabold text-amber-700 tabular-nums">+0 XP</span>
                        <span id="perfect-badge" class="hidden chip bg-sun text-ink-900 text-[10px] font-extrabold">👑 BONUS PERFECT DAY</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('siswa.dashboard') }}" class="btn-soft text-sm">
                        Batal
                    </a>
                    <button type="submit" class="btn-primary flex-1 sm:flex-initial text-base px-8 shadow-btn">
                        Simpan Jurnal Hari Ini
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M221.66,133.66l-72,72a8,8,0,0,1-11.32-11.32L196.69,136H40a8,8,0,0,1,0-16H196.69L138.34,61.66a8,8,0,0,1,11.32-11.32l72,72A8,8,0,0,1,221.66,133.66Z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function setPreset(inputId, value) {
        const input = document.getElementById(inputId);
        if (input) {
            input.value = value;
            updateProgress();
        }
    }

    function selectSport(inputId, sportName, btn) {
        const input = document.getElementById(inputId);
        if (input) {
            input.value = sportName;
            // Highlight button
            document.querySelectorAll('.sport-btn').forEach(b => {
                b.classList.remove('border-primary-500', 'bg-primary-100', 'text-ink-900');
            });
            btn.classList.add('border-primary-500', 'bg-primary-100', 'text-ink-900');
            updateProgress();
        }
    }

    function updateProgress() {
        let completedCount = 0;
        const total = 7;

        document.querySelectorAll('.habit-card').forEach(card => {
            const slug = card.getAttribute('data-slug');
            const id = card.getAttribute('data-id');
            const prefix = 'habit_' + id;
            let isComplete = false;

            if (slug === 'bangun-pagi') {
                const val = document.getElementById(prefix + '_time')?.value;
                if (val && val.trim() !== '') isComplete = true;
            } else if (slug === 'beribadah') {
                const cb = document.getElementById(prefix + '_done');
                if (cb && cb.checked) isComplete = true;
            } else if (slug === 'berolahraga') {
                const sport = document.getElementById(prefix + '_type')?.value;
                const dur = parseInt(document.getElementById(prefix + '_duration')?.value || '0');
                if (sport && dur > 0) isComplete = true;
            } else if (slug === 'makan-sehat') {
                const checked = card.querySelectorAll('.food-checkbox:checked').length;
                if (checked >= 3) isComplete = true;
            } else if (slug === 'gemar-belajar') {
                const dur = parseInt(document.getElementById(prefix + '_duration')?.value || '0');
                if (dur > 0) isComplete = true;
            } else if (slug === 'bermasyarakat') {
                const checked = card.querySelectorAll('.social-checkbox:checked').length;
                if (checked >= 1) isComplete = true;
            } else if (slug === 'tidur-cepat') {
                const val = document.getElementById(prefix + '_time')?.value;
                if (val && val.trim() !== '') isComplete = true;
            }

            const indicator = card.querySelector('.status-indicator');
            if (isComplete) {
                completedCount++;
                if (indicator) {
                    indicator.className = 'status-indicator size-8 rounded-full bg-mint text-ink-900 border-2 border-mint flex items-center justify-center text-xs font-bold transition-all';
                }
            } else {
                if (indicator) {
                    indicator.className = 'status-indicator size-8 rounded-full border-2 border-ink-300 flex items-center justify-center text-xs font-bold text-transparent transition-all';
                }
            }
        });

        // Update progress bar & text
        const pct = Math.round((completedCount / total) * 100);
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        if (progressBar) progressBar.style.width = pct + '%';
        if (progressText) progressText.textContent = completedCount + ' dari ' + total;

        // Reward calculation
        let totalXp = completedCount * 10;
        const perfectBadge = document.getElementById('perfect-badge');
        if (completedCount === 7) {
            totalXp += 30; // bonus
            if (perfectBadge) perfectBadge.classList.remove('hidden');
        } else {
            if (perfectBadge) perfectBadge.classList.add('hidden');
        }

        const rewardEl = document.getElementById('reward-xp');
        if (rewardEl) rewardEl.textContent = '+' + totalXp + ' XP';
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', () => {
        updateProgress();
    });
</script>
@endpush
@endsection
