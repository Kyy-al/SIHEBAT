@extends('layouts.admin')

@section('title', 'Manajemen Achievement & Lencana — SiHebat')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{
    editModal: false,
    deleteModal: false,
    editId: null,
    editName: '',
    editSlug: '',
    editDescription: '',
    editReqType: 'streak',
    editReqValue: 7,
    editIcon: 'trophy',
    editColor: 'sun',
    editIsActive: true,
    editUrl: '',
    deleteUrl: '',
    deleteName: ''
}">

    {{-- Header & Stats --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight flex items-center gap-2">
                <span>🏆</span>
                <span>Manajemen Achievement & Lencana</span>
            </h1>
            <p class="text-sm font-semibold text-ink-500 mt-1">
                Kelola lencana penghargaan gamifikasi dan syarat otomatis pencapaian siswa.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="bg-white px-3.5 py-2 rounded-2xl border border-ink-100 shadow-xs text-center">
                <span class="text-[10px] font-bold text-ink-500 uppercase block">Total Lencana</span>
                <span class="text-lg font-black text-ink-900 tabular-nums">{{ $totalBadges }}</span>
            </div>
            <div class="bg-white px-3.5 py-2 rounded-2xl border border-ink-100 shadow-xs text-center">
                <span class="text-[10px] font-bold text-emerald-700 uppercase block">Total Diraih</span>
                <span class="text-lg font-black text-emerald-800 tabular-nums">{{ $totalEarned }}x</span>
            </div>
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="rounded-2xl p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl p-4 bg-rose-50 border border-rose-200 text-rose-900 text-sm font-bold">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Achievement List Table (2 cols) --}}
        <div class="lg:col-span-2 card p-5 sm:p-6 border border-ink-100 shadow-card space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h3 class="text-base font-extrabold text-ink-900">
                    Daftar Achievement ({{ $badges->total() }})
                </h3>

                {{-- Search Box --}}
                <form method="GET" action="{{ route('admin.achievements') }}" class="flex items-center gap-2">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari lencana..."
                           class="input text-xs py-1.5 px-3 w-48">
                    @if(request('search'))
                        <a href="{{ route('admin.achievements') }}" class="btn-ghost text-xs py-1.5 px-2">Reset</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-ink-100 text-xs font-extrabold text-ink-500 uppercase tracking-wider">
                            <th class="py-3 px-3">Lencana</th>
                            <th class="py-3 px-3">Syarat Pencapaian</th>
                            <th class="py-3 px-3 text-center">Peraih</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100">
                        @forelse($badges as $b)
                            @php
                                $colorBg = match($b->color) {
                                    'sun'      => 'bg-amber-100 text-amber-900 border-amber-300',
                                    'emerald'  => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                                    'coral'    => 'bg-rose-100 text-rose-900 border-rose-300',
                                    'primary'  => 'bg-sky-100 text-sky-900 border-sky-300',
                                    'lavender' => 'bg-purple-100 text-purple-900 border-purple-300',
                                    'mint'     => 'bg-teal-100 text-teal-900 border-teal-300',
                                    default    => 'bg-amber-100 text-amber-900 border-amber-300',
                                };
                            @endphp
                            <tr class="hover:bg-primary-50/40 transition">
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center gap-3">
                                        <div class="size-12 rounded-2xl border {{ $colorBg }} flex items-center justify-center shrink-0 shadow-xs p-1">
                                            <x-mascot-achievement :icon="$b->icon" :slug="$b->slug" :earned="true" size="md" />
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-extrabold text-ink-900 block truncate leading-tight">{{ $b->name }}</span>
                                            <span class="text-[11px] text-ink-500 font-semibold line-clamp-1 mt-0.5">{{ $b->description }}</span>
                                            <code class="text-[10px] text-ink-400 bg-canvas px-1.5 py-0.2 rounded font-mono">{{ $b->slug }}</code>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-3">
                                    <div class="space-y-1">
                                        @switch($b->requirement_type)
                                            @case('streak')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                                                    🔥 Streak ≥ {{ $b->requirement_value }} Hari
                                                </span>
                                                @break
                                            @case('xp')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-sky-50 border border-sky-200 text-sky-800 text-xs font-bold">
                                                    ⭐ XP ≥ {{ number_format($b->requirement_value) }} Poin
                                                </span>
                                                @break
                                            @case('perfect_day')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-yellow-50 border border-yellow-300 text-yellow-900 text-xs font-bold">
                                                    👑 Perfect Day ≥ {{ $b->requirement_value }}x
                                                </span>
                                                @break
                                            @case('habit_count')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-teal-50 border border-teal-200 text-teal-800 text-xs font-bold">
                                                    🎯 Kebiasaan ≥ {{ $b->requirement_value }}x
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold">
                                                    Khusus ({{ $b->requirement_value }})
                                                </span>
                                        @endswitch
                                    </div>
                                </td>

                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white border border-ink-200 text-ink-800 font-extrabold text-xs tabular-nums">
                                        {{ $b->users_count }} siswa
                                    </span>
                                </td>

                                <td class="py-3.5 px-3 text-center">
                                    @if($b->is_active)
                                        <span class="chip-status-baik text-[10px] py-0.5 px-2 font-black">Aktif</span>
                                    @else
                                        <span class="chip-status-perlu text-[10px] py-0.5 px-2 font-black">Nonaktif</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button type="button"
                                                @click="
                                                    editId = {{ $b->id }};
                                                    editName = '{{ addslashes($b->name) }}';
                                                    editSlug = '{{ addslashes($b->slug) }}';
                                                    editDescription = '{{ addslashes($b->description) }}';
                                                    editReqType = '{{ $b->requirement_type }}';
                                                    editReqValue = {{ $b->requirement_value }};
                                                    editIcon = '{{ $b->icon }}';
                                                    editColor = '{{ $b->color }}';
                                                    editIsActive = {{ $b->is_active ? 'true' : 'false' }};
                                                    editUrl = '{{ route('admin.achievements.update', $b->id) }}';
                                                    editModal = true;
                                                "
                                                class="btn-ghost text-xs p-1.5 rounded-lg text-primary-700 hover:bg-primary-100"
                                                title="Edit Achievement">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="currentColor" viewBox="0 0 256 256"><path d="M227.31,73.37,182.63,28.68a16,16,0,0,0-22.63,0L36.69,152A15.86,15.86,0,0,0,32,163.31V208a16,16,0,0,0,16,16H92.69A15.86,15.86,0,0,0,104,219.31L227.31,96a16,16,0,0,0,0-22.63ZM92.69,208H48V163.31l88-88L180.69,120ZM192,108.68,147.31,64l24-24L216,84.68Z"/></svg>
                                        </button>

                                        <button type="button"
                                                @click="
                                                    deleteUrl = '{{ route('admin.achievements.destroy', $b->id) }}';
                                                    deleteName = '{{ addslashes($b->name) }}';
                                                    deleteModal = true;
                                                "
                                                class="btn-ghost text-xs p-1.5 rounded-lg text-rose-600 hover:bg-rose-100"
                                                title="Hapus Achievement">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="currentColor" viewBox="0 0 256 256"><path d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-ink-400 font-bold">
                                    Belum ada achievement yang tersimpan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($badges->hasPages())
                <div class="pt-4 border-t border-ink-100">
                    {{ $badges->links() }}
                </div>
            @endif
        </div>

        {{-- Create Form (1 col) --}}
        <div class="card p-5 sm:p-6 border border-ink-100 shadow-card space-y-4">
            <h3 class="text-base font-extrabold text-ink-900 flex items-center gap-2">
                <span>➕</span>
                <span>Tambah Achievement Baru</span>
            </h3>
            <p class="text-xs text-ink-500 font-semibold">
                Tentukan kriteria pencapaian otomatis yang memotivasi siswa berdisiplin.
            </p>

            <form method="POST" action="{{ route('admin.achievements.store') }}" class="space-y-3.5">
                @csrf

                <div>
                    <label class="input-label">Nama Lencana</label>
                    <input type="text"
                           name="name"
                           required
                           placeholder="Contoh: Ksatria Fajar 14 Hari"
                           class="input text-sm">
                </div>

                <div>
                    <label class="input-label">Slug / Kode Unik (Opsional)</label>
                    <input type="text"
                           name="slug"
                           placeholder="Otomatis dari nama (mis: ksatria-fajar)"
                           class="input text-sm font-mono text-xs">
                </div>

                <div>
                    <label class="input-label">Deskripsi Penghargaan</label>
                    <textarea name="description"
                              required
                              rows="2"
                              placeholder="Penjelasan apa yang harus dilakukan siswa untuk meraihnya..."
                              class="input text-xs"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="input-label">Jenis Syarat</label>
                        <select name="requirement_type" class="input text-xs font-bold" required>
                            <option value="streak">🔥 Streak Hari</option>
                            <option value="xp">⭐ Total XP</option>
                            <option value="perfect_day">👑 Perfect Day (7/7)</option>
                            <option value="habit_count">🎯 Total Kebiasaan</option>
                            <option value="special">🎖️ Khusus / Manual</option>
                        </select>
                    </div>

                    <div>
                        <label class="input-label">Nilai Target</label>
                        <input type="number"
                               name="requirement_value"
                               required
                               min="1"
                               value="7"
                               class="input text-xs font-bold tabular-nums">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="input-label">Pilihan Ikon</label>
                        <select name="icon" class="input text-xs" required>
                            <option value="trophy">🏆 Piala (Trophy)</option>
                            <option value="fire">🔥 Api Streak (Fire)</option>
                            <option value="crown">👑 Mahkota (Crown)</option>
                            <option value="medal">🥇 Medali (Medal)</option>
                            <option value="star">⭐ Bintang (Star)</option>
                            <option value="sparkle">✨ Kilauan (Sparkle)</option>
                            <option value="sun">🌅 Matahari (Sun)</option>
                            <option value="book">📖 Buku (Book)</option>
                            <option value="heart">💖 Hati Peduli (Heart)</option>
                        </select>
                    </div>

                    <div>
                        <label class="input-label">Aksen Warna</label>
                        <select name="color" class="input text-xs" required>
                            <option value="sun">🟡 Emas / Sun</option>
                            <option value="emerald">🟢 Zamrud / Emerald</option>
                            <option value="coral">🔴 Oranye / Coral</option>
                            <option value="primary">🔵 Biru / Primary</option>
                            <option value="lavender">🟣 Ungu / Lavender</option>
                            <option value="mint">🩵 Toska / Mint</option>
                        </select>
                    </div>
                </div>

                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-primary-500">
                        <span class="text-xs font-bold text-ink-800">Aktifkan penghargaan ini segera</span>
                    </label>
                </div>

                <button type="submit" class="btn-primary w-full text-xs font-extrabold py-2.5 rounded-xl shadow-btn">
                    Simpan Achievement Baru
                </button>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT ACHIEVEMENT --}}
    <div x-show="editModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/40 backdrop-blur-xs"
         x-cloak
         @keydown.escape.window="editModal = false">
        <div class="card p-6 w-full max-w-md border border-ink-100 shadow-modal space-y-4 bg-white"
             @click.outside="editModal = false">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-extrabold text-ink-900 flex items-center gap-2">
                    <span>✏️</span>
                    <span>Edit Data Achievement</span>
                </h3>
                <button type="button" @click="editModal = false" class="text-ink-400 hover:text-ink-900 font-bold text-lg">✕</button>
            </div>

            <form :action="editUrl" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div>
                    <label class="input-label">Nama Lencana</label>
                    <input type="text" name="name" x-model="editName" required class="input text-sm">
                </div>

                <div>
                    <label class="input-label">Slug / Kode Unik</label>
                    <input type="text" name="slug" x-model="editSlug" required class="input text-sm font-mono text-xs">
                </div>

                <div>
                    <label class="input-label">Deskripsi Penghargaan</label>
                    <textarea name="description" x-model="editDescription" required rows="2" class="input text-xs"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="input-label">Jenis Syarat</label>
                        <select name="requirement_type" x-model="editReqType" class="input text-xs font-bold" required>
                            <option value="streak">🔥 Streak Hari</option>
                            <option value="xp">⭐ Total XP</option>
                            <option value="perfect_day">👑 Perfect Day (7/7)</option>
                            <option value="habit_count">🎯 Total Kebiasaan</option>
                            <option value="special">🎖️ Khusus / Manual</option>
                        </select>
                    </div>

                    <div>
                        <label class="input-label">Nilai Target</label>
                        <input type="number" name="requirement_value" x-model="editReqValue" required min="1" class="input text-xs font-bold tabular-nums">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="input-label">Pilihan Ikon</label>
                        <select name="icon" x-model="editIcon" class="input text-xs" required>
                            <option value="trophy">🏆 Piala</option>
                            <option value="fire">🔥 Api Streak</option>
                            <option value="crown">👑 Mahkota</option>
                            <option value="medal">🥇 Medali</option>
                            <option value="star">⭐ Bintang</option>
                            <option value="sparkle">✨ Kilauan</option>
                            <option value="sun">🌅 Matahari</option>
                            <option value="book">📖 Buku</option>
                            <option value="heart">💖 Hati</option>
                        </select>
                    </div>

                    <div>
                        <label class="input-label">Aksen Warna</label>
                        <select name="color" x-model="editColor" class="input text-xs" required>
                            <option value="sun">🟡 Emas / Sun</option>
                            <option value="emerald">🟢 Zamrud</option>
                            <option value="coral">🔴 Oranye / Coral</option>
                            <option value="primary">🔵 Biru / Primary</option>
                            <option value="lavender">🟣 Ungu / Lavender</option>
                            <option value="mint">🩵 Toska / Mint</option>
                        </select>
                    </div>
                </div>

                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" :checked="editIsActive" class="rounded text-primary-500">
                        <span class="text-xs font-bold text-ink-800">Status Aktif</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-ink-100">
                    <button type="button" @click="editModal = false" class="btn-ghost text-xs py-2 px-4">Batal</button>
                    <button type="submit" class="btn-primary text-xs font-extrabold py-2 px-5 rounded-xl shadow-btn">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DELETE ACHIEVEMENT --}}
    <div x-show="deleteModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/40 backdrop-blur-xs"
         x-cloak
         @keydown.escape.window="deleteModal = false">
        <div class="card p-6 w-full max-w-sm border border-ink-100 shadow-modal space-y-4 bg-white text-center"
             @click.outside="deleteModal = false">
            <div class="size-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto text-2xl">
                ⚠️
            </div>

            <div>
                <h3 class="text-base font-extrabold text-ink-900">Hapus Achievement?</h3>
                <p class="text-xs text-ink-500 font-semibold mt-1">
                    Anda yakin ingin menghapus <strong class="text-ink-900" x-text="deleteName"></strong>? Siswa yang telah mendapatkan lencana ini tidak akan lagi melihat lencana tersebut di profil mereka.
                </p>
            </div>

            <form :action="deleteUrl" method="POST" class="flex items-center justify-center gap-2 pt-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="btn-ghost text-xs py-2 px-4">Batal</button>
                <button type="submit" class="btn-danger text-xs font-extrabold py-2 px-5 rounded-xl">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
