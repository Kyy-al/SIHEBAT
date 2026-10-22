@extends('layouts.admin')

@section('title', 'Master Data Guru & Wali Kelas — SiHebat')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    createModal: false,
    editModal: false,
    deleteModal: false,
    importModal: false,
    editName: '',
    editNip: '',
    editEmail: '',
    editGolRuang: '',
    editJabatan: '',
    editClassroomId: '',
    editUrl: '',
    deleteUrl: '',
    deleteName: ''
}">

    {{-- Top Header & Main Action Toolbar --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="chip bg-primary-100 text-primary-800 text-xs font-black uppercase">
                    Manajemen Pengajar
                </span>
                <span class="text-xs font-bold text-ink-400">•</span>
                <span class="text-xs font-bold text-ink-500">Tahun Ajaran Aktif</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-ink-900 tracking-tight mt-1">
                Master Data: Guru & Wali Kelas
            </h1>
            <p class="text-sm font-semibold text-ink-500 mt-1">
                Kelola akun pendidik, penugasan wali kelas per rombel, serta fasilitas impor data dari Dapodik.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Tombol Tambah Guru (Utama) --}}
            <button type="button"
                    @click="createModal = true"
                    class="btn-primary text-xs sm:text-sm font-black py-2.5 px-4 rounded-xl flex items-center gap-2 shadow-btn cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Guru Baru</span>
            </button>

            {{-- Tombol Impor Dapodik --}}
            <button type="button"
                    @click="importModal = true"
                    class="btn-soft text-xs sm:text-sm font-black py-2.5 px-4 rounded-xl flex items-center gap-1.5 border border-primary-200 hover:border-primary-400 cursor-pointer">
                <span>📥</span>
                <span>Impor dari Dapodik</span>
            </button>

            {{-- Tombol Ekspor Kredensial --}}
            <a href="{{ route('admin.teachers.export-credentials') }}"
               class="btn-soft text-xs sm:text-sm font-extrabold py-2.5 px-3.5 rounded-xl flex items-center gap-1.5 hover:border-ink-300"
               title="Unduh daftar akun dan kata sandi awal guru dalam format Excel">
                <span>📋</span>
                <span class="hidden sm:inline">Ekspor Akun</span>
                <span class="sm:hidden">Ekspor</span>
            </a>

            {{-- Kembali ke Dashboard --}}
            <a href="{{ route('admin.dashboard') }}" class="btn-soft text-xs font-bold py-2.5 px-3 rounded-xl">
                ← Dashboard
            </a>
        </div>
    </div>

    {{-- Stats Cards & Quick Metrics --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-4 sm:p-5 border border-ink-100 flex items-center gap-4 bg-white">
            <div class="size-12 rounded-2xl bg-primary-100 text-primary-800 flex items-center justify-center text-2xl font-black shrink-0">
                👨‍🏫
            </div>
            <div>
                <span class="text-xs font-bold text-ink-500 uppercase tracking-wide block">Total Guru Terdaftar</span>
                <span class="text-2xl font-black text-ink-900 tabular-nums">{{ $totalTeachers ?? $teachers->total() }}</span>
                <span class="text-[11px] text-ink-400 font-semibold block">Pengajar aktif di sistem</span>
            </div>
        </div>

        <div class="card p-4 sm:p-5 border border-ink-100 flex items-center gap-4 bg-white">
            <div class="size-12 rounded-2xl bg-sky-100 text-sky-800 flex items-center justify-center text-2xl font-black shrink-0">
                🏫
            </div>
            <div>
                <span class="text-xs font-bold text-ink-500 uppercase tracking-wide block">Wali Kelas Ditugaskan</span>
                <span class="text-2xl font-black text-ink-900 tabular-nums">{{ $totalWali ?? 0 }}</span>
                <span class="text-[11px] text-sky-700 font-bold block">Memegang kelas binaan</span>
            </div>
        </div>

        <div class="card p-4 sm:p-5 border border-ink-100 flex items-center gap-4 bg-white">
            <div class="size-12 rounded-2xl bg-lavender-soft text-purple-900 flex items-center justify-center text-2xl font-black shrink-0">
                📋
            </div>
            <div>
                <span class="text-xs font-bold text-ink-500 uppercase tracking-wide block">Total Rombel Kelas</span>
                <span class="text-2xl font-black text-ink-900 tabular-nums">{{ $classrooms->count() }}</span>
                <span class="text-[11px] text-purple-700 font-bold block">Siap dipantau jurnalnya</span>
            </div>
        </div>
    </div>

    {{-- Main Teachers Table Card with Search & Filters --}}
    <div class="card p-5 sm:p-6 border border-ink-100 space-y-4">
        {{-- Search, Filter, and Action Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-ink-100">
            <div class="flex items-center gap-3">
                <h3 class="text-lg font-black text-ink-900">
                    Daftar Guru & Wali Kelas
                </h3>
                <span class="chip bg-primary-50 text-primary-800 text-xs font-black border border-primary-200">
                    {{ $teachers->total() }} Pengajar
                </span>
            </div>

            {{-- Search & Filter Form --}}
            <form method="GET" action="{{ route('admin.teachers') }}" class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Cari nama, NIP, email..."
                           class="input text-xs py-2 pl-8 pr-3 min-h-[38px] w-48 sm:w-64 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 text-ink-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <select name="wali" onchange="this.form.submit()" class="input text-xs py-2 px-3 min-h-[38px] rounded-xl w-36">
                    <option value="">Semua Guru</option>
                    <option value="is_wali" {{ ($filterWali ?? '') === 'is_wali' ? 'selected' : '' }}>Hanya Wali Kelas</option>
                    <option value="not_wali" {{ ($filterWali ?? '') === 'not_wali' ? 'selected' : '' }}>Bukan Wali Kelas</option>
                </select>

                @if(!empty($search) || !empty($filterWali))
                    <a href="{{ route('admin.teachers') }}" class="btn-soft text-xs py-2 px-3 rounded-xl text-ink-600 hover:text-ink-900" title="Hapus Filter">
                        ✕ Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Teacher Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-ink-100 text-xs font-extrabold text-ink-500 uppercase tracking-wider bg-ink-50/50">
                        <th class="py-3 px-3.5">Nama Pendidik</th>
                        <th class="py-3 px-3.5">NIP</th>
                        <th class="py-3 px-3.5">Golongan & Jabatan</th>
                        <th class="py-3 px-3.5">Wali Kelas</th>
                        <th class="py-3 px-3.5 text-center w-36">Aksi (CRUD)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @forelse($teachers as $tc)
                        @php
                            $assignedClass = $tc->homeroomClassrooms->first();
                            $assignedClassId = $assignedClass?->id ?? '';
                        @endphp
                        <tr class="hover:bg-primary-50/40 transition">
                            {{-- Nama & Email --}}
                            <td class="py-3.5 px-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="avatar size-9 text-xs font-black shrink-0 bg-lavender-soft text-purple-900 ring-1 ring-purple-200">
                                        {{ strtoupper(substr($tc->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-ink-900 block text-sm">{{ $tc->name }}</span>
                                        <span class="text-xs text-ink-500 font-semibold">{{ $tc->email ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- NIP --}}
                            <td class="py-3.5 px-3.5 font-semibold text-ink-700 tabular-nums">
                                @if(!empty($tc->nip))
                                    <span class="font-mono text-xs font-bold text-ink-800 bg-ink-100/70 px-2 py-0.5 rounded-md">
                                        {{ $tc->nip }}
                                    </span>
                                @else
                                    <span class="chip bg-ink-100 text-ink-500 text-[10px] font-bold">
                                        Belum ada NIP
                                    </span>
                                @endif
                            </td>

                            {{-- Gol / Jabatan --}}
                            <td class="py-3.5 px-3.5 text-xs">
                                @if($tc->jabatan || $tc->gol_ruang)
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @if($tc->gol_ruang && $tc->gol_ruang !== '-')
                                            <span class="chip bg-primary-100 text-primary-900 text-[11px] font-black border border-primary-200">
                                                Gol. {{ $tc->gol_ruang }}
                                            </span>
                                        @endif
                                        <span class="font-bold text-ink-800">{{ $tc->jabatan ?? '-' }}</span>
                                    </div>
                                @else
                                    <span class="text-ink-400 font-medium">-</span>
                                @endif
                            </td>

                            {{-- Status Wali Kelas --}}
                            <td class="py-3.5 px-3.5">
                                @if($assignedClass)
                                    <span class="chip bg-sky-100 text-sky-900 text-xs font-black border border-sky-300">
                                        🏫 Kelas {{ $assignedClass->name }}
                                    </span>
                                    @if($tc->homeroomClassrooms->count() > 1)
                                        <span class="text-[10px] text-ink-500 font-bold ml-1">
                                            (+{{ $tc->homeroomClassrooms->count() - 1 }} rombel)
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-ink-400 font-medium italic">
                                        Belum ditugaskan
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi Edit & Hapus --}}
                            <td class="py-3.5 px-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Edit Button --}}
                                    <button type="button"
                                            @click="
                                                editModal = true;
                                                editName = '{{ addslashes($tc->name) }}';
                                                editNip = '{{ $tc->nip ?? '' }}';
                                                editEmail = '{{ $tc->email ?? '' }}';
                                                editGolRuang = '{{ $tc->gol_ruang ?? '' }}';
                                                editJabatan = '{{ $tc->jabatan ?? '' }}';
                                                editClassroomId = '{{ $assignedClassId }}';
                                                editUrl = '{{ route('admin.teachers.update', $tc->id) }}';
                                            "
                                            class="btn-outline text-xs font-bold py-1.5 px-2.5 rounded-lg flex items-center gap-1 hover:bg-primary-50 hover:text-primary-800 hover:border-primary-300 cursor-pointer"
                                            title="Edit Data Guru & Wali Kelas">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        <span>Edit</span>
                                    </button>

                                    {{-- Hapus Button --}}
                                    <button type="button"
                                            @click="
                                                deleteModal = true;
                                                deleteName = '{{ addslashes($tc->name) }}';
                                                deleteUrl = '{{ route('admin.teachers.destroy', $tc->id) }}';
                                            "
                                            class="btn-outline text-xs font-bold py-1.5 px-2.5 rounded-lg text-rose-600 border-rose-200 hover:bg-rose-50 hover:border-rose-300 flex items-center gap-1 cursor-pointer"
                                            title="Hapus Guru">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-ink-500 font-semibold space-y-2">
                                <div class="text-3xl">👨‍🏫</div>
                                <div class="text-sm font-bold text-ink-700">Tidak ada data guru yang cocok.</div>
                                <div class="text-xs text-ink-400">Silakan gunakan tombol "+ Tambah Guru Baru" atau "Impor dari Dapodik".</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($teachers->hasPages())
            <div class="pt-4 mt-2 border-t border-ink-100">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>

    {{-- ====================================================
         MODAL 1: TAMBAH GURU BARU (CREATE)
         ==================================================== --}}
    <div x-show="createModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-xs"
         @keydown.escape.window="createModal = false">
        <div class="card p-6 max-w-lg w-full border border-primary-200 shadow-2xl space-y-4 bg-white rounded-3xl"
             @click.away="createModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-ink-100">
                <div class="flex items-center gap-2.5">
                    <div class="size-10 rounded-2xl bg-primary-100 text-primary-800 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-ink-900 text-base sm:text-lg">+ Tambah Guru Baru</h3>
                        <p class="text-xs font-semibold text-ink-500">Daftarkan akun pendidik dan tentukan kelas binaannya</p>
                    </div>
                </div>
                <button type="button" @click="createModal = false" class="size-8 rounded-full bg-ink-100 hover:bg-ink-200 text-ink-700 flex items-center justify-center font-bold text-sm cursor-pointer">
                    ✕
                </button>
            </div>

            <form method="POST" action="{{ route('admin.teachers.store') }}" class="space-y-3.5">
                @csrf

                <div>
                    <label for="create_name" class="input-label">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                    <input type="text"
                           id="create_name"
                           name="name"
                           placeholder="Contoh: Bu Ani Suryani, S.Pd."
                           required
                           class="input text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="create_nip" class="input-label">NIP (Opsional)</label>
                        <input type="text"
                               id="create_nip"
                               name="nip"
                               placeholder="Contoh: 198501152010012001"
                               class="input text-sm">
                        <p class="text-[10px] text-ink-400 mt-1">Kosongkan bila guru honorer/belum ber-NIP</p>
                    </div>
                    <div>
                        <label for="create_email" class="input-label">Email (Opsional)</label>
                        <input type="email"
                               id="create_email"
                               name="email"
                               placeholder="Contoh: guru@sekolah.sch.id"
                               class="input text-sm">
                        <p class="text-[10px] text-ink-400 mt-1">Dapat digunakan sebagai login alternatif</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="create_gol_ruang" class="input-label">Golongan / Ruang</label>
                        <input type="text"
                               id="create_gol_ruang"
                               name="gol_ruang"
                               placeholder="Contoh: IX, IV/a, III/b"
                               class="input text-sm">
                    </div>
                    <div>
                        <label for="create_jabatan" class="input-label">Jabatan Fungsional</label>
                        <input type="text"
                               id="create_jabatan"
                               name="jabatan"
                               placeholder="Contoh: Guru Madya, Guru Pertama"
                               class="input text-sm">
                    </div>
                </div>

                {{-- Assignment Wali Kelas --}}
                <div>
                    <label for="create_classroom_id" class="input-label">Penugasan Wali Kelas (Opsional)</label>
                    <select id="create_classroom_id" name="classroom_id" class="input text-sm">
                        <option value="">-- Tidak Menjabat Wali Kelas --</option>
                        @foreach($classrooms as $cls)
                            <option value="{{ $cls->id }}">
                                Kelas {{ $cls->name }} (Tingkat {{ $cls->grade }})
                                @if($cls->homeroomTeacher)
                                    — [Saat ini: {{ $cls->homeroomTeacher->name }}]
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-ink-500 font-semibold mt-1">
                        * Guru yang ditugaskan sebagai wali kelas dapat melihat rapor kebiasaan seluruh siswa di kelas tersebut.
                    </p>
                </div>

                <div class="p-3 rounded-2xl bg-primary-50 border border-primary-200 text-xs text-primary-900 font-semibold space-y-1">
                    <div class="font-extrabold flex items-center gap-1.5">
                        <span>🔐</span>
                        <span>Informasi Kata Sandi Awal:</span>
                    </div>
                    <p class="text-[11px] text-primary-800">
                        Kata sandi default otomatis disetel sama dengan <strong>NIP</strong> (jika diisi), atau dibuatkan kata sandi acak awal (format: <code>GuruXXXX</code>) yang dapat diekspor melalui tombol <strong>Ekspor Akun (.xlsx)</strong>.
                    </p>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-ink-100">
                    <button type="button" @click="createModal = false" class="btn-outline text-xs font-bold py-2.5 px-4 rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary text-xs font-black py-2.5 px-5 rounded-xl shadow-btn cursor-pointer">
                        Simpan Data Guru
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ====================================================
         MODAL 2: EDIT DATA GURU & WALI KELAS (UPDATE)
         ==================================================== --}}
    <div x-show="editModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-xs"
         @keydown.escape.window="editModal = false">
        <div class="card p-6 max-w-lg w-full border border-primary-200 shadow-2xl space-y-4 bg-white rounded-3xl"
             @click.away="editModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-ink-100">
                <div class="flex items-center gap-2.5">
                    <div class="size-10 rounded-2xl bg-primary-100 text-primary-800 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-ink-900 text-base sm:text-lg">Edit Data Guru & Wali Kelas</h3>
                        <p class="text-xs font-semibold text-ink-500">Perbarui identitas, NIP, atau kelas binaan guru</p>
                    </div>
                </div>
                <button type="button" @click="editModal = false" class="size-8 rounded-full bg-ink-100 hover:bg-ink-200 text-ink-700 flex items-center justify-center font-bold text-sm cursor-pointer">
                    ✕
                </button>
            </div>

            <form method="POST" :action="editUrl" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div>
                    <label class="input-label">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                    <input type="text"
                           name="name"
                           x-model="editName"
                           required
                           class="input text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="input-label">NIP (Opsional)</label>
                        <input type="text"
                               name="nip"
                               x-model="editNip"
                               placeholder="Boleh dikosongkan jika belum ada"
                               class="input text-sm">
                    </div>
                    <div>
                        <label class="input-label">Email</label>
                        <input type="email"
                               name="email"
                               x-model="editEmail"
                               placeholder="guru@sekolah.sch.id"
                               class="input text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="input-label">Gol / Ruang</label>
                        <input type="text"
                               name="gol_ruang"
                               x-model="editGolRuang"
                               placeholder="Contoh: IV/a, IX"
                               class="input text-sm">
                    </div>
                    <div>
                        <label class="input-label">Jabatan Fungsional</label>
                        <input type="text"
                               name="jabatan"
                               x-model="editJabatan"
                               placeholder="Contoh: Guru Madya"
                               class="input text-sm">
                    </div>
                </div>

                {{-- Penugasan Wali Kelas --}}
                <div>
                    <label class="input-label">Penugasan Wali Kelas</label>
                    <select name="classroom_id" x-model="editClassroomId" class="input text-sm">
                        <option value="">-- Tidak Menjabat Wali Kelas --</option>
                        @foreach($classrooms as $cls)
                            <option value="{{ $cls->id }}">
                                Kelas {{ $cls->name }} (Tingkat {{ $cls->grade }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-xs flex items-center gap-2.5">
                    <input type="checkbox" id="reset_pwd_guru" name="reset_password" value="1" class="rounded text-primary-600 size-4 cursor-pointer">
                    <label for="reset_pwd_guru" class="text-ink-800 font-semibold cursor-pointer select-none">
                        Reset kata sandi guru kembali ke default (NIP / Sandi Baru) & wajibkan ganti sandi saat login
                    </label>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-ink-100">
                    <button type="button" @click="editModal = false" class="btn-outline text-xs font-bold py-2.5 px-4 rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary text-xs font-black py-2.5 px-5 rounded-xl shadow-btn cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ====================================================
         MODAL 3: HAPUS GURU (DELETE)
         ==================================================== --}}
    <div x-show="deleteModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-xs"
         @keydown.escape.window="deleteModal = false">
        <div class="card p-6 max-w-sm w-full border border-rose-200 shadow-2xl space-y-4 bg-white rounded-3xl"
             @click.away="deleteModal = false">
            <div class="size-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <div class="text-center space-y-1">
                <h3 class="font-black text-ink-900 text-base">Hapus Data Guru?</h3>
                <p class="text-xs text-ink-600 leading-relaxed">
                    Yakin ingin menghapus guru <strong class="text-rose-600" x-text="deleteName"></strong>? Penugasan wali kelas yang bersangkutan akan dikosongkan secara otomatis.
                </p>
            </div>
            <form method="POST" :action="deleteUrl" class="flex items-center gap-2 pt-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="btn-outline flex-1 text-xs font-bold py-2.5 rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="btn-primary flex-1 text-xs font-black py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-btn cursor-pointer">
                    Hapus Guru
                </button>
            </form>
        </div>
    </div>

    {{-- ====================================================
         MODAL 4: IMPOR DAPODIK MASSAL (.xlsx / .csv)
         ==================================================== --}}
    <div x-show="importModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-xs"
         @keydown.escape.window="importModal = false">
        <div class="card p-6 max-w-2xl w-full border-2 border-primary-300 shadow-2xl space-y-5 bg-white rounded-3xl"
             @click.away="importModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-ink-100">
                <div class="flex items-center gap-3">
                    <div class="size-11 rounded-2xl bg-primary-400 text-ink-900 flex items-center justify-center font-black text-xl shadow-xs">
                        📥
                    </div>
                    <div>
                        <span class="chip bg-primary-100 text-primary-800 text-[10px] font-black uppercase mb-0.5">
                            STANDAR DAPODIK ROMBEL
                        </span>
                        <h3 class="font-black text-ink-900 text-lg">
                            Impor Data Guru & Rombel Kelas Massal
                        </h3>
                    </div>
                </div>
                <button type="button" @click="importModal = false" class="size-8 rounded-full bg-ink-100 hover:bg-ink-200 text-ink-700 flex items-center justify-center font-bold text-sm cursor-pointer">
                    ✕
                </button>
            </div>

            {{-- Download Template Section --}}
            <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 bg-primary-50/70 rounded-2xl border border-primary-200">
                <div class="text-xs">
                    <span class="font-extrabold text-ink-900 block">Belum punya format tabel yang sesuai?</span>
                    <span class="text-ink-600 text-[11px]">Gunakan template resmi sesuai kolom Dapodik berikut.</span>
                </div>
                <div class="inline-flex rounded-xl shadow-xs border border-ink-200 overflow-hidden bg-white">
                    <a href="{{ route('admin.teachers.template', ['format' => 'xlsx']) }}" class="btn-soft text-xs font-extrabold py-2 px-3 flex items-center gap-1.5 hover:border-primary-400">
                        <span>📥</span>
                        <span>Format Excel (.xlsx)</span>
                    </a>
                    <a href="{{ route('admin.teachers.template', ['format' => 'csv']) }}" class="btn-soft text-xs font-extrabold py-2 px-2.5 border-l border-ink-200 text-ink-600 hover:text-ink-900" title="Unduh format .csv">
                        <span>.csv</span>
                    </a>
                </div>
            </div>

            {{-- Visual Table Preview matching Dapodik Format --}}
            <div class="space-y-1.5">
                <span class="text-xs font-extrabold text-ink-700 block">
                    Struktur Kolom Sesuai Tabel Ekspor Dapodik Rombel:
                </span>
                <div class="overflow-x-auto rounded-xl border border-ink-200 bg-white">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead class="bg-primary-100/70 border-b border-ink-200 font-black text-ink-900">
                            <tr>
                                <th rowspan="2" class="py-2 px-2.5 border-r border-ink-200 text-center w-10">No</th>
                                <th rowspan="2" class="py-2 px-2.5 border-r border-ink-200">Nama Rombel</th>
                                <th rowspan="2" class="py-2 px-2.5 border-r border-ink-200 text-center">Tingkat</th>
                                <th colspan="3" class="py-0.5 px-2 border-r border-ink-200 text-center border-b border-ink-200">Jumlah Siswa</th>
                                <th rowspan="2" class="py-2 px-3 font-black">Wali Kelas</th>
                            </tr>
                            <tr class="bg-primary-50/60 font-extrabold text-ink-700 text-[11px]">
                                <th class="py-0.5 px-2 border-r border-ink-200 text-center">L</th>
                                <th class="py-0.5 px-2 border-r border-ink-200 text-center">P</th>
                                <th class="py-0.5 px-2 border-r border-ink-200 text-center font-black text-ink-900">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ink-100 text-ink-700 font-semibold text-[11px]">
                            <tr class="bg-white">
                                <td class="py-1.5 px-2.5 border-r border-ink-100 text-center font-bold">1</td>
                                <td class="py-1.5 px-2.5 border-r border-ink-100 font-bold text-ink-900">X.1</td>
                                <td class="py-1.5 px-2.5 border-r border-ink-100 text-center">10</td>
                                <td class="py-1.5 px-2 border-r border-ink-100 text-center font-mono">15</td>
                                <td class="py-1.5 px-2 border-r border-ink-100 text-center font-mono">21</td>
                                <td class="py-1.5 px-2 border-r border-ink-100 text-center font-bold text-ink-900 font-mono">36</td>
                                <td class="py-1.5 px-3 font-bold text-ink-900">MUHAMMAD RAMADHANI ZUHRI</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Form Upload --}}
            <form method="POST" action="{{ route('admin.teachers.import') }}" enctype="multipart/form-data" class="space-y-4 pt-2">
                @csrf
                <div>
                    <label class="input-label">Pilih Berkas Excel atau CSV (.xlsx / .csv)</label>
                    <input type="file"
                           name="excel_file"
                           accept=".xlsx,.xls,.csv,.txt"
                           required
                           class="w-full text-xs text-ink-700 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-primary-400 file:text-ink-900 hover:file:bg-primary-500 cursor-pointer bg-white p-1 rounded-2xl border border-ink-200">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-ink-100">
                    <button type="button" @click="importModal = false" class="btn-outline text-xs font-bold py-2.5 px-4 rounded-xl cursor-pointer">
                        Tutup
                    </button>
                    <button type="submit" class="btn-primary text-xs font-black py-2.5 px-5 rounded-xl shadow-btn cursor-pointer">
                        Unggah & Proses Impor
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
