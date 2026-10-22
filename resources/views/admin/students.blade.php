@extends('layouts.admin')

@section('title', 'Master Data Siswa & Impor Excel — SiHebat')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    editModal: false,
    deleteModal: false,
    moderateModal: false,
    editName: '',
    editGender: 'L',
    editNis: '',
    editNisn: '',
    editClassroomId: '',
    editUrl: '',
    deleteUrl: '',
    deleteName: '',
    moderateName: '',
    moderateUrl: '',
    moderateAvatarUrl: '',
    moderateWarning: 'Foto profil Anda telah dihapus oleh pihak sekolah karena tidak sesuai dengan norma/etika pelajar sekolah. Silakan unggah foto profil yang sopan (menggunakan seragam atau pakaian pantas).'
}">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-ink-900 tracking-tight">
                Master Data: Siswa & Impor Excel
            </h1>
            <p class="text-sm font-semibold text-ink-500 mt-1">
                Kelola akun siswa dan impor data secara massal menggunakan format Excel/CSV sesuai standar dapodik sekolah.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="btn-soft text-xs font-extrabold py-2 px-4 self-start sm:self-auto">
            ← Kembali ke Dashboard
        </a>
    </div>

    {{-- Import Excel & CSV Card (Matching Format Image 4) --}}
    <div class="card p-5 sm:p-6 border-2 border-primary-300 shadow-pop bg-gradient-to-br from-primary-50/80 via-white to-primary-50/40 space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="size-12 rounded-2xl bg-primary-400 text-ink-900 flex items-center justify-center font-black text-2xl shadow-sm">
                    📊
                </div>
                <div>
                    <span class="chip bg-primary-400 text-ink-900 text-[10px] font-black uppercase mb-1">
                        FITUR UTAMA: IMPOR EXCEL
                    </span>
                    <h3 class="text-lg sm:text-xl font-black text-ink-900">
                        Impor Data Siswa Massal (.xlsx / .csv)
                    </h3>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.students.export-credentials') }}" class="btn-primary text-xs font-black py-2.5 px-4 rounded-xl flex items-center gap-1.5 shadow-sm">
                    <span>📋</span>
                    <span>Ekspor Akun & Sandi Awal (.xlsx)</span>
                </a>
                <a href="{{ route('admin.students.template') }}" class="btn-soft text-xs font-extrabold py-2.5 px-4 rounded-xl flex items-center gap-1.5 shadow-sm hover:border-primary-400">
                    <span>📥</span>
                    <span>Unduh Format Excel</span>
                </a>
            </div>
        </div>

        {{-- Visual Preview of Expected Excel Columns (Image 4 format!) --}}
        <div class="space-y-2">
            <span class="text-xs font-extrabold text-ink-700 block">
                Format Kolom Excel yang Digunakan (Sesuai Standar Sekolah):
            </span>
            <div class="overflow-x-auto rounded-2xl border border-ink-200 bg-white shadow-sm">
                <table class="w-full text-xs text-left">
                    <thead class="bg-primary-100/70 border-b border-ink-200 font-black text-ink-900">
                        <tr>
                            <th class="py-2.5 px-3 border-r border-ink-200">No.</th>
                            <th class="py-2.5 px-3 border-r border-ink-200">Nama Siswa</th>
                            <th class="py-2.5 px-3 border-r border-ink-200 text-center">JK</th>
                            <th class="py-2.5 px-3 border-r border-ink-200">NIS</th>
                            <th class="py-2.5 px-3 border-r border-ink-200">NISN</th>
                            <th class="py-2.5 px-3">Kelas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100 text-ink-700 font-semibold">
                        <tr class="bg-white">
                            <td class="py-2 px-3 border-r border-ink-100 text-center">1</td>
                            <td class="py-2 px-3 border-r border-ink-100 font-bold text-ink-900">Ahmad Reza Maulana</td>
                            <td class="py-2 px-3 border-r border-ink-100 text-center font-bold text-sky-700">L</td>
                            <td class="py-2 px-3 border-r border-ink-100 font-mono">12009</td>
                            <td class="py-2 px-3 border-r border-ink-100 font-mono">0118102423</td>
                            <td class="py-2 px-3 font-bold text-ink-900">X.1</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-[11px] text-ink-500 font-semibold">
                * Sistem otomatis mendaftarkan rombel kelas jika belum ada di database. Kata sandi default akun siswa adalah NIS mereka sendiri.
            </p>
        </div>

        {{-- Upload Form --}}
        <form method="POST" action="{{ route('admin.students.import') }}" enctype="multipart/form-data" class="pt-3 border-t border-primary-200 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            @csrf
            <div class="flex-1">
                <input type="file"
                       name="excel_file"
                       accept=".xlsx,.csv,.txt"
                       required
                       class="w-full text-xs text-ink-700 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-primary-400 file:text-ink-900 hover:file:bg-primary-500 cursor-pointer bg-white p-1 rounded-2xl border border-ink-200">
            </div>
            <button type="submit" class="btn-primary text-xs sm:text-sm font-black py-2.5 px-6 shadow-btn shrink-0">
                Unggah & Proses Impor Siswa
            </button>
        </form>
    </div>

    {{-- Main Area: List Siswa (2 cols) + Tambah Manual (1 col) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Students List (2 cols) --}}
        <div class="lg:col-span-2 card p-5 sm:p-6 border border-ink-100 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-base font-black text-ink-900">
                    Daftar Siswa ({{ $students->total() }})
                </h3>

                {{-- Filter & Search Form --}}
                <form method="GET" action="{{ route('admin.students') }}" class="flex items-center gap-2">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari nama/NIS/NISN..."
                           class="input text-xs py-1.5 px-3 min-h-[36px] w-40 sm:w-48 rounded-xl">
                    <select name="class_id" onchange="this.form.submit()" class="input text-xs py-1.5 px-3 min-h-[36px] rounded-xl w-32">
                        <option value="">Semua Kelas</option>
                        @foreach($classrooms as $cls)
                            <option value="{{ $cls->id }}" {{ $selectedClass == $cls->id ? 'selected' : '' }}>
                                {{ $cls->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-ink-100 text-xs font-extrabold text-ink-500 uppercase tracking-wider">
                            <th class="py-3 px-3">Nama Siswa</th>
                            <th class="py-3 px-3 text-center">JK</th>
                            <th class="py-3 px-3">NIS / NISN</th>
                            <th class="py-3 px-3">Kelas</th>
                            <th class="py-3 px-3 text-center">Streak</th>
                            <th class="py-3 px-3 text-right">XP</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100">
                        @forelse($students as $st)
                            <tr class="hover:bg-primary-50/50 transition">
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="avatar size-8 text-xs font-extrabold shrink-0 overflow-hidden ring-1 ring-ink-200">
                                            @if($st->avatar_url)
                                                <img src="{{ $st->avatar_url }}" alt="{{ $st->name }}" class="w-full h-full object-cover">
                                            @else
                                                {{ substr($st->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-ink-900 text-xs sm:text-sm">{{ $st->name }}</span>
                                                @if($st->avatar_warning)
                                                    <span class="chip bg-amber-100 text-amber-900 text-[9px] py-0 px-1.5 font-black border border-amber-300" title="{{ $st->avatar_warning }}">
                                                        ⚠️ Foto Dimoderasi
                                                    </span>
                                                @endif
                                            </div>
                                            @if($st->must_change_password)
                                                <span class="block text-[10px] text-amber-700 font-extrabold">Wajib ganti sandi</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if($st->gender === 'L')
                                        <span class="chip bg-sky-100 text-sky-800 text-[10px] py-0.5 px-2 font-black">L</span>
                                    @elseif($st->gender === 'P')
                                        <span class="chip bg-pink-100 text-pink-800 text-[10px] py-0.5 px-2 font-black">P</span>
                                    @else
                                        <span class="text-xs text-ink-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 font-semibold text-ink-700 tabular-nums text-xs">
                                    <span class="font-bold">{{ $st->nis }}</span>
                                    @if($st->nisn)
                                        <span class="block text-[10px] text-ink-400">NISN: {{ $st->nisn }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    @php $cls = $st->currentClassroom(); @endphp
                                    @if($cls)
                                        <span class="chip bg-primary-100 text-primary-800 text-xs font-bold">
                                            {{ $cls->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-ink-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="chip-streak text-xs py-0.5 px-2 font-black">
                                        🔥 {{ $st->current_streak }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right font-black text-amber-700 tabular-nums text-xs sm:text-sm">
                                    {{ number_format($st->xp) }} XP
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        @if($st->avatar)
                                            <button type="button"
                                                    @click="moderateModal = true; moderateName = '{{ addslashes($st->name) }}'; moderateUrl = '{{ route('admin.students.moderate-avatar', $st->id) }}'; moderateAvatarUrl = '{{ $st->avatar_url }}'"
                                                    class="btn-outline text-xs font-bold py-1 px-2 rounded-lg text-amber-800 border-amber-300 hover:bg-amber-100 flex items-center gap-1 cursor-pointer bg-amber-50"
                                                    title="Moderasi & Hapus Foto Profil yang Tidak Pantas">
                                                <span>📸</span>
                                                <span>Moderasi</span>
                                            </button>
                                        @endif
                                        <button type="button"
                                                @click="editModal = true; editName = '{{ addslashes($st->name) }}'; editGender = '{{ $st->gender ?? 'L' }}'; editNis = '{{ $st->nis ?? '' }}'; editNisn = '{{ $st->nisn ?? '' }}'; editClassroomId = '{{ $cls ? $cls->id : '' }}'; editUrl = '{{ route('admin.students.update', $st->id) }}'"
                                                class="btn-outline text-xs font-bold py-1 px-2 rounded-lg flex items-center gap-1 hover:bg-primary-50 hover:text-primary-800 cursor-pointer"
                                                title="Edit Siswa">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            <span>Edit</span>
                                        </button>
                                        <button type="button"
                                                @click="deleteModal = true; deleteName = '{{ addslashes($st->name) }}'; deleteUrl = '{{ route('admin.students.destroy', $st->id) }}'"
                                                class="btn-outline text-xs font-bold py-1 px-2 rounded-lg text-rose-600 border-rose-200 hover:bg-rose-50 flex items-center gap-1 cursor-pointer"
                                                title="Hapus Siswa">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-ink-500 font-semibold">
                                    Tidak ada data siswa ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="pt-4 mt-2 border-t border-ink-100">
                    {{ $students->links() }}
                </div>
            @endif
        </div>

        {{-- Form Add Student Manual (1 col) --}}
        <div class="card p-5 sm:p-6 border border-ink-100 h-fit space-y-4">
            <div>
                <h3 class="text-base font-black text-ink-900">+ Tambah Siswa Manual</h3>
                <p class="text-xs text-ink-500 font-semibold">Kata sandi awal siswa otomatis disetel sama dengan NIS.</p>
            </div>

            <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-3">
                @csrf

                <div>
                    <label for="name" class="input-label">Nama Lengkap Siswa</label>
                    <input type="text"
                           id="name"
                           name="name"
                           placeholder="Contoh: Ahmad Reza Maulana"
                           required
                           class="input text-xs sm:text-sm">
                </div>

                <div>
                    <label class="input-label">Jenis Kelamin (JK)</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="p-2.5 rounded-xl border border-ink-200 flex items-center justify-center gap-2 cursor-pointer font-bold text-xs hover:bg-sky-50 has-[:checked]:bg-sky-100 has-[:checked]:border-sky-400">
                            <input type="radio" name="gender" value="L" checked class="text-primary-500">
                            <span>👦 Laki-laki (L)</span>
                        </label>
                        <label class="p-2.5 rounded-xl border border-ink-200 flex items-center justify-center gap-2 cursor-pointer font-bold text-xs hover:bg-pink-50 has-[:checked]:bg-pink-100 has-[:checked]:border-pink-400">
                            <input type="radio" name="gender" value="P" class="text-pink-500">
                            <span>👧 Perempuan (P)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="nis" class="input-label">Nomor Induk Siswa (NIS)</label>
                    <input type="text"
                           id="nis"
                           name="nis"
                           placeholder="Contoh: 12009"
                           required
                           class="input text-xs sm:text-sm">
                </div>

                <div>
                    <label for="nisn" class="input-label">NISN (Opsional)</label>
                    <input type="text"
                           id="nisn"
                           name="nisn"
                           placeholder="Contoh: 0118102423"
                           class="input text-xs sm:text-sm">
                </div>

                <div>
                    <label for="classroom_id" class="input-label">Pilih Kelas</label>
                    <select id="classroom_id" name="classroom_id" class="input text-xs sm:text-sm">
                        <option value="">-- Tanpa Kelas (Atur Nanti) --</option>
                        @foreach($classrooms as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }} (Kelas {{ $cls->grade }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full text-xs sm:text-sm font-black shadow-btn">
                        Simpan Data Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- MODAL EDIT SISWA --}}
    <div x-show="editModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-xs"
         @keydown.escape.window="editModal = false">
        <div class="card p-6 max-w-md w-full border border-primary-200 shadow-2xl space-y-4 bg-white rounded-3xl"
             @click.away="editModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-ink-100">
                <div class="flex items-center gap-2.5">
                    <div class="size-9 rounded-xl bg-primary-100 text-primary-800 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-ink-900 text-base">Edit Data Siswa</h3>
                        <p class="text-xs font-semibold text-ink-500">Perbarui identitas, NIS/NISN, atau penempatan rombel</p>
                    </div>
                </div>
                <button type="button" @click="editModal = false" class="size-8 rounded-full bg-ink-100 hover:bg-ink-200 text-ink-700 flex items-center justify-center font-bold text-sm">
                    ✕
                </button>
            </div>

            <form method="POST" :action="editUrl" class="space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="input-label">Nama Lengkap Siswa</label>
                    <input type="text"
                           name="name"
                           x-model="editName"
                           required
                           class="input text-sm">
                </div>

                <div>
                    <label class="input-label">Jenis Kelamin</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="p-2 rounded-xl border border-ink-200 flex items-center justify-center gap-2 cursor-pointer font-bold text-xs"
                               :class="editGender === 'L' ? 'bg-sky-100 border-sky-400 text-sky-900' : ''">
                            <input type="radio" name="gender" value="L" x-model="editGender" class="text-sky-500">
                            <span>👦 Laki-laki (L)</span>
                        </label>
                        <label class="p-2 rounded-xl border border-ink-200 flex items-center justify-center gap-2 cursor-pointer font-bold text-xs"
                               :class="editGender === 'P' ? 'bg-pink-100 border-pink-400 text-pink-900' : ''">
                            <input type="radio" name="gender" value="P" x-model="editGender" class="text-pink-500">
                            <span>👧 Perempuan (P)</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="input-label">NIS (Wajib)</label>
                        <input type="text"
                               name="nis"
                               x-model="editNis"
                               required
                               class="input text-sm">
                    </div>
                    <div>
                        <label class="input-label">NISN (Opsional)</label>
                        <input type="text"
                               name="nisn"
                               x-model="editNisn"
                               class="input text-sm">
                    </div>
                </div>

                <div>
                    <label class="input-label">Rombongan Belajar (Kelas)</label>
                    <select name="classroom_id" x-model="editClassroomId" class="input text-sm">
                        <option value="">-- Tanpa Rombel (Kosongkan) --</option>
                        @foreach($classrooms as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }} (Kelas {{ $cls->grade }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-xs flex items-center gap-2">
                    <input type="checkbox" id="reset_pwd_siswa" name="reset_password" value="1" class="rounded text-primary-600 size-4">
                    <label for="reset_pwd_siswa" class="text-ink-800 font-semibold cursor-pointer">
                        Reset kata sandi siswa kembali ke NIS
                    </label>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-ink-100">
                    <button type="button" @click="editModal = false" class="btn-outline text-xs font-bold py-2 px-4 rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary text-xs font-bold py-2 px-4 rounded-xl shadow-btn">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL HAPUS SISWA --}}
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
                <h3 class="font-extrabold text-ink-900 text-base">Hapus Data Siswa?</h3>
                <p class="text-xs text-ink-600 leading-relaxed">
                    Yakin ingin menghapus siswa <strong class="text-rose-600" x-text="deleteName"></strong>? Seluruh catatan jurnal dan XP siswa ini akan dihapus secara permanen.
                </p>
            </div>
            <form method="POST" :action="deleteUrl" class="flex items-center gap-2 pt-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="btn-outline flex-1 text-xs font-bold py-2.5 rounded-xl">
                    Batal
                </button>
                <button type="submit" class="btn-primary flex-1 text-xs font-bold py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-btn">
                    Hapus Siswa
                </button>
            </form>
        </div>
    </div>

    {{-- MODAL MODERASI FOTO SISWA --}}
    <div x-show="moderateModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-xs"
         @keydown.escape.window="moderateModal = false">
        <div class="card p-6 max-w-lg w-full border-2 border-amber-300 shadow-2xl space-y-4 bg-white rounded-3xl"
             @click.away="moderateModal = false">
            <div class="flex items-center gap-3">
                <div class="size-11 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-2xl shrink-0">
                    ⚠️
                </div>
                <div>
                    <h3 class="font-extrabold text-ink-900 text-base">Moderasi & Hapus Foto Profil Siswa</h3>
                    <p class="text-xs text-ink-500 font-semibold">Tindakan ini menghapus foto profil dan mengirimkan catatan peringatan ke akun siswa.</p>
                </div>
            </div>

            {{-- Target Preview --}}
            <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200 flex items-center gap-3.5">
                <img :src="moderateAvatarUrl" alt="Foto Siswa" class="size-16 rounded-xl object-cover ring-2 ring-amber-300 shadow-xs shrink-0 bg-white">
                <div class="min-w-0">
                    <span class="text-[10px] font-black uppercase text-amber-800 block">Siswa Yang Dimoderasi</span>
                    <h4 class="font-black text-sm text-ink-900 truncate" x-text="moderateName"></h4>
                    <p class="text-[11px] text-ink-600 font-medium mt-0.5">
                        Foto tidak sesuai etika pelajar sekolah dan akan langsung di-take down dari dasbor serta papan peringkat.
                    </p>
                </div>
            </div>

            <form method="POST" :action="moderateUrl" class="space-y-3">
                @csrf
                <div>
                    <label class="input-label text-xs">Pesan Peringatan untuk Siswa:</label>
                    <textarea name="warning_message"
                              x-model="moderateWarning"
                              rows="3"
                              maxlength="500"
                              required
                              class="input text-xs sm:text-sm"></textarea>
                    <p class="text-[11px] text-ink-500 font-medium mt-1">
                        Pesan peringatan ini langsung muncul di Beranda dan Profil siswa untuk menginstruksikan mereka memasang foto yang pantas / berseragam.
                    </p>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-ink-100">
                    <button type="button" @click="moderateModal = false" class="btn-outline text-xs font-bold py-2 px-4 rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="btn text-xs font-black py-2 px-4 rounded-xl bg-amber-600 hover:bg-amber-700 text-white shadow-btn">
                        Hapus Foto & Kirim Peringatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
