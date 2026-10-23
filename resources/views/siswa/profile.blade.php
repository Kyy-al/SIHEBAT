@extends('layouts.siswa')

@section('title', 'Profil Saya — SiHebat')

@section('content')
<div class="max-w-4xl mx-auto px-3.5 sm:px-4 py-4 sm:py-6 space-y-6 w-full max-w-full overflow-x-hidden"
     x-data="{
        avatarModal: false,
        previewUrl: null,
        onFileChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.previewUrl = URL.createObjectURL(file);
            }
        }
     }">

    {{-- Moderation Warning Banner (If school admin removed an inappropriate photo) --}}
    @if($user->avatar_warning)
        <div class="p-4 sm:p-5 rounded-3xl bg-amber-50 border-2 border-amber-300 text-amber-950 flex items-start gap-4 shadow-sm">
            <div class="size-11 rounded-2xl bg-amber-200 text-amber-900 flex items-center justify-center text-2xl shrink-0 shadow-xs">
                ⚠️
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <h4 class="font-black text-sm sm:text-base text-amber-950">
                        Pemberitahuan Pihak Sekolah: Foto Profil Dimoderasi
                    </h4>
                    @if($user->avatar_moderated_at)
                        <span class="text-[11px] font-bold text-amber-800 bg-amber-200/60 px-2.5 py-0.5 rounded-full w-fit">
                            {{ \Carbon\Carbon::parse($user->avatar_moderated_at)->locale('id')->isoFormat('D MMMM Y, HH:mm') }}
                        </span>
                    @endif
                </div>
                <p class="text-xs sm:text-sm font-semibold text-amber-900 mt-1 leading-relaxed">
                    "{{ $user->avatar_warning }}"
                </p>
                <div class="mt-3 pt-2.5 border-t border-amber-200 flex flex-wrap items-center justify-between gap-2">
                    <span class="text-[11px] font-bold text-amber-800">
                        💡 Unggah foto profil baru yang rapi (disarankan berseragam sekolah). Peringatan ini otomatis selesai setelah Anda memperbarui foto profil.
                    </span>
                    <button type="button"
                            @click="avatarModal = true"
                            class="btn text-xs font-black py-1.5 px-3 bg-amber-500 text-ink-900 hover:bg-amber-600 shadow-xs rounded-xl">
                        Unggah Foto Sekarang →
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Profile Card --}}
    <div class="card p-6 border border-ink-100 flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
        {{-- Avatar with Edit Trigger --}}
        <div class="flex flex-col items-center shrink-0">
            <div class="relative group cursor-pointer" @click="avatarModal = true" title="Klik untuk mengubah foto profil">
                <div class="avatar-lg size-24 text-3xl font-black shrink-0 ring-4 ring-primary-300 shadow-md overflow-hidden relative bg-primary-100 flex items-center justify-center transition-transform group-hover:scale-105">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <span>{{ substr($user->name, 0, 1) }}</span>
                    @endif

                    {{-- Hover overlay --}}
                    <div class="absolute inset-0 bg-ink-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-[11px] font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Ubah
                    </div>
                </div>
                <button type="button"
                        class="absolute -bottom-1 -right-1 size-8 rounded-full bg-primary-400 text-ink-900 border-2 border-white shadow-sm flex items-center justify-center text-xs font-bold hover:bg-primary-500 transition"
                        title="Ubah Foto Profil">
                    📷
                </button>
            </div>
            <button type="button"
                    @click="avatarModal = true"
                    class="text-xs font-extrabold text-primary-700 hover:text-primary-900 underline underline-offset-2 mt-2">
                {{ $user->avatar ? 'Ganti Foto' : '+ Pasang Foto' }}
            </button>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h1 class="text-2xl font-extrabold text-ink-900 leading-tight">{{ $user->name }}</h1>
                    <p class="text-sm font-semibold text-ink-500 mt-0.5">
                        NIS: <span class="text-ink-900 font-bold">{{ $user->nis ?? '-' }}</span> ·
                        {{ $user->currentClassroom() ? 'Kelas ' . $user->currentClassroom()->name : 'Siswa Hebat' }}
                    </p>
                </div>

                <div class="flex items-center justify-center sm:justify-end gap-2 flex-wrap">
                    <span class="chip bg-lavender-soft text-purple-800 text-xs py-1 px-3 font-extrabold">
                        Level {{ $user->level }} — {{ $user->level_name }}
                    </span>
                    <span class="chip-streak text-xs py-1 px-3">
                        🔥 {{ $user->current_streak }} Hari
                    </span>
                </div>
            </div>

            {{-- Level Progress bar --}}
            <div class="mt-4 bg-primary-50 rounded-2xl p-3 border border-primary-100">
                <div class="flex justify-between items-center text-xs font-bold mb-1">
                    <span class="text-ink-700">Perjalanan ke Level {{ $user->level + 1 }}</span>
                    <span class="text-primary-700 tabular-nums">{{ $user->xp }} XP</span>
                </div>
                <div class="progress-track h-2.5">
                    <div class="progress-fill" style="width: {{ $user->xp_progress }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Bento Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="card p-4 border border-ink-100 text-center">
            <span class="text-2xl mb-1 block">📝</span>
            <span class="text-2xl font-black text-ink-900 tabular-nums block">{{ $totalJournals }}</span>
            <span class="text-xs font-bold text-ink-500">Total Jurnal</span>
        </div>

        <div class="card p-4 border border-ink-100 text-center">
            <span class="text-2xl mb-1 block">👑</span>
            <span class="text-2xl font-black text-amber-600 tabular-nums block">{{ $perfectDays }}</span>
            <span class="text-xs font-bold text-ink-500">Hari Sempurna (7/7)</span>
        </div>

        <div class="card p-3 border border-ink-100 text-center flex flex-col items-center justify-center">
            <x-mascot-streak :streak="$user->current_streak" size="sm" />
            <span class="text-xl font-black text-coral tabular-nums block mt-0.5">{{ $user->current_streak }} Hari</span>
            <span class="text-[10px] font-bold text-ink-500">Streak (Rekor: {{ $user->longest_streak }}h)</span>
        </div>

        <div class="card p-4 border border-ink-100 text-center">
            <span class="text-2xl mb-1 block">⭐</span>
            <span class="text-2xl font-black text-amber-500 tabular-nums block">{{ number_format($user->xp) }}</span>
            <span class="text-xs font-bold text-ink-500">Total XP</span>
        </div>
    </div>

    {{-- Badges Collection --}}
    @php
        $allBadges = \App\Models\Badge::where('is_active', true)->get();
        $earnedBadgeIds = $badges->pluck('id')->toArray();
        $earnedPivots = $badges->keyBy('id');
    @endphp

    <div class="card p-5 sm:p-6 border border-ink-100 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-ink-900">Koleksi Lencana Penghargaan</h3>
                <p class="text-xs font-semibold text-ink-500">
                    Kamu telah membuka {{ count($earnedBadgeIds) }} dari {{ count($allBadges) }} lencana
                </p>
            </div>
            <span class="chip bg-primary-100 text-primary-800 text-xs font-bold">
                {{ count($earnedBadgeIds) }}/{{ count($allBadges) }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            @foreach($allBadges as $badge)
                @php
                    $isEarned = in_array($badge->id, $earnedBadgeIds);
                    $earnedAt = $isEarned ? $earnedPivots[$badge->id]->pivot->earned_at : null;
                @endphp

                <div class="rounded-3xl p-4 border transition-all flex items-start gap-3.5 {{ $isEarned ? 'bg-white border-primary-300 shadow-sm' : 'bg-canvas/60 border-ink-100 opacity-60' }}">
                    <div class="size-14 rounded-2xl flex items-center justify-center shrink-0 {{ $isEarned ? 'bg-primary-50/90 border border-primary-200 shadow-xs' : 'bg-ink-100/60 border border-ink-200' }}">
                        <x-mascot-achievement :icon="$badge->icon" :slug="$badge->slug" :earned="$isEarned" size="lg" />
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <h4 class="font-extrabold text-sm text-ink-900 truncate">{{ $badge->name }}</h4>
                            @if($isEarned)
                                <span class="text-mint font-bold text-xs">✓</span>
                            @endif
                        </div>
                        <p class="text-xs font-medium text-ink-500 mt-0.5 line-clamp-2 leading-relaxed">
                            {{ $badge->description }}
                        </p>
                        @if($isEarned && $earnedAt)
                            <span class="text-[10px] font-bold text-primary-700 mt-1 block">
                                Diraih {{ Carbon\Carbon::parse($earnedAt)->locale('id')->isoFormat('D MMM Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Account Options --}}
    <div class="card p-5 sm:p-6 border border-ink-100 space-y-3">
        <h3 class="text-base font-extrabold text-ink-900 mb-2">Pengaturan Akun</h3>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-1">
            <a href="{{ route('password.change') }}" class="btn-soft w-full sm:w-auto text-sm">
                🔒 Ganti Kata Sandi
            </a>
            <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="btn w-full sm:w-auto bg-coral-soft text-coral text-sm hover:bg-coral hover:text-white transition-colors">
                    Keluar dari Akun
                </button>
            </form>
        </div>
    </div>

    {{-- Modal Ganti Foto Profil Siswa --}}
    <div x-show="avatarModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="card p-6 border-2 border-primary-300 bg-white max-w-md w-full space-y-4 shadow-xl"
             @click.away="avatarModal = false; previewUrl = null">

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="size-10 rounded-2xl bg-primary-100 text-primary-800 flex items-center justify-center text-xl">
                        📸
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-ink-900">Foto Profil Kamu</h3>
                        <p class="text-xs text-ink-500 font-semibold">Tampil di papan peringkat & dasbor kelas</p>
                    </div>
                </div>
                <button type="button" @click="avatarModal = false; previewUrl = null" class="text-ink-400 hover:text-ink-700 text-lg font-black p-1">
                    ✕
                </button>
            </div>

            {{-- Preview Stage --}}
            <div class="flex flex-col items-center justify-center py-4 bg-primary-50/50 rounded-2xl border border-primary-100">
                <div class="size-28 rounded-2xl ring-4 ring-primary-300 shadow-md overflow-hidden bg-white flex items-center justify-center">
                    <template x-if="previewUrl">
                        <img :src="previewUrl" alt="Pratinjau Foto" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!previewUrl">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl font-black text-primary-400">{{ substr($user->name, 0, 1) }}</span>
                        @endif
                    </template>
                </div>
                <p class="text-[11px] font-bold text-ink-500 mt-2">
                    <span x-text="previewUrl ? 'Pratinjau Foto Baru' : 'Foto Profil Saat Ini'"></span>
                </p>
            </div>

            {{-- Guidelines notice --}}
            <div class="p-3 bg-sky-50 rounded-xl border border-sky-200 text-[11px] text-sky-900 font-semibold space-y-1">
                <p class="font-black text-sky-950">Aturan Foto Profil Sekolah:</p>
                <ul class="list-disc list-inside text-sky-800 text-[11px] space-y-0.5">
                    <li>Gunakan foto diri yang sopan & pantas (disarankan berseragam sekolah).</li>
                    <li>Format gambar yang didukung: JPG, PNG, atau WebP (maks. 2MB).</li>
                    <li>Foto yang tidak pantas akan dimoderasi & dihapus oleh pihak admin sekolah.</li>
                </ul>
            </div>

            {{-- Upload Form --}}
            <form method="POST" action="{{ route('siswa.profile.avatar') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="input-label text-xs">Pilih Berkas Foto Baru</label>
                    <input type="file"
                           name="avatar"
                           accept="image/jpeg,image/png,image/webp"
                           @change="onFileChange"
                           required
                           class="w-full text-xs text-ink-700 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-primary-400 file:text-ink-900 hover:file:bg-primary-500 cursor-pointer bg-white p-1 rounded-xl border border-ink-200">
                </div>

                <div class="flex items-center justify-between gap-2 pt-2 border-t border-ink-100">
                    @if($user->avatar)
                        <button type="button"
                                @click="if(confirm('Yakin ingin menghapus foto profil ini dan kembali menggunakan inisial nama?')) { $refs.deleteAvatarForm.submit(); }"
                                class="text-xs font-bold text-rose-600 hover:text-rose-800 underline underline-offset-2">
                            Hapus Foto
                        </button>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center gap-2">
                        <button type="button" @click="avatarModal = false; previewUrl = null" class="btn-soft text-xs font-bold py-2 px-3">
                            Batal
                        </button>
                        <button type="submit" class="btn text-xs font-black py-2 px-4 bg-primary-400 text-ink-900 hover:bg-primary-500 shadow-sm">
                            Simpan Foto
                        </button>
                    </div>
                </div>
            </form>

            @if($user->avatar)
                <form x-ref="deleteAvatarForm" method="POST" action="{{ route('siswa.profile.avatar.delete') }}" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
