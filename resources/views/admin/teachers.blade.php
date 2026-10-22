@extends('layouts.admin')

@section('title', 'Data Guru — SiHebat')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{
    editModal: false,
    deleteModal: false,
    editName: '',
    editNip: '',
    editEmail: '',
    editGolRuang: '',
    editJabatan: '',
    editUrl: '',
    deleteUrl: '',
    deleteName: ''
}">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">
                Master Data: Guru & Wali Kelas
            </h1>
            <p class="text-sm font-semibold text-ink-500 mt-1">
                Kelola akun pengajar dan rombel. Guru dapat masuk menggunakan NIP atau Email (kata sandi awal dapat diekspor).
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="btn-soft text-xs font-extrabold py-2 px-4 self-start sm:self-auto">
            ← Kembali ke Dashboard
        </a>
    </div>

    {{-- Import Excel & CSV Card (Matching Format Dapodik Rombel) --}}
    <div class="card p-5 sm:p-6 border-2 border-primary-300 shadow-pop bg-gradient-to-br from-primary-50/80 via-white to-primary-50/40 space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="size-12 rounded-2xl bg-primary-400 text-ink-900 flex items-center justify-center font-black text-2xl shadow-sm">
                    👨‍🏫
                </div>
                <div>
                    <span class="chip bg-primary-400 text-ink-900 text-[10px] font-black uppercase mb-1">
                        FITUR UTAMA: IMPOR EXCEL & CSV
                    </span>
                    <h3 class="text-lg sm:text-xl font-black text-ink-900">
                        Impor Data Guru & Rombel Kelas Massal (.xlsx / .csv)
                    </h3>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.teachers.export-credentials') }}" class="btn-primary text-xs font-black py-2.5 px-4 rounded-xl flex items-center gap-1.5 shadow-sm">
                    <span>📋</span>
                    <span>Ekspor Akun & Sandi Awal (.xlsx)</span>
                </a>
                <div class="inline-flex rounded-xl shadow-xs border border-ink-200 overflow-hidden bg-white">
                    <a href="{{ route('admin.teachers.template', ['format' => 'xlsx']) }}" class="btn-soft text-xs font-extrabold py-2.5 px-3.5 flex items-center gap-1.5 hover:border-primary-400">
                        <span>📥</span>
                        <span>Unduh Format Excel (.xlsx)</span>
                    </a>
                    <a href="{{ route('admin.teachers.template', ['format' => 'csv']) }}" class="btn-soft text-xs font-extrabold py-2.5 px-3 border-l border-ink-200 text-ink-600 hover:text-ink-900" title="Unduh format .csv">
                        <span>.csv</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Visual Preview of Expected Excel Columns (Strictly matching user's format) --}}
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-ink-700 block">
                    Format Tabel Excel / Dapodik yang Digunakan:
                </span>
                <span class="text-[11px] font-bold text-primary-800 bg-primary-100/80 px-2.5 py-0.5 rounded-full">
                    Sesuai Standar Tabel Rombel Dapodik
                </span>
            </div>
            <div class="overflow-x-auto rounded-2xl border border-ink-200 bg-white shadow-sm">
                <table class="w-full text-xs text-left border-collapse">
                    <thead class="bg-primary-100/70 border-b border-ink-200 font-black text-ink-900">
                        <tr>
                            <th rowspan="2" class="py-2.5 px-3 border-r border-ink-200 text-center w-12">No</th>
                            <th rowspan="2" class="py-2.5 px-3 border-r border-ink-200">Nama Rombel</th>
                            <th rowspan="2" class="py-2.5 px-3 border-r border-ink-200 text-center">Tingkat Kelas</th>
                            <th colspan="3" class="py-1 px-3 border-r border-ink-200 text-center border-b border-ink-200">Jumlah Siswa</th>
                            <th rowspan="2" class="py-2.5 px-4 font-black">Wali Kelas</th>
                        </tr>
                        <tr class="bg-primary-50/60 font-extrabold text-ink-700">
                            <th class="py-1 px-3 border-r border-ink-200 text-center">L</th>
                            <th class="py-1 px-3 border-r border-ink-200 text-center">P</th>
                            <th class="py-1 px-3 border-r border-ink-200 text-center font-black text-ink-900">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100 text-ink-700 font-semibold">
                        <tr class="bg-white">
                            <td class="py-2 px-3 border-r border-ink-100 text-center font-bold">1</td>
                            <td class="py-2 px-3 border-r border-ink-100 font-bold text-ink-900">X.1</td>
                            <td class="py-2 px-3 border-r border-ink-100 text-center font-bold">10</td>
                            <td class="py-2 px-3 border-r border-ink-100 text-center font-mono">15</td>
                            <td class="py-2 px-3 border-r border-ink-100 text-center font-mono">21</td>
                            <td class="py-2 px-3 border-r border-ink-100 text-center font-bold text-ink-900 font-mono">36</td>
                            <td class="py-2 px-4 font-bold text-ink-900">MUHAMMAD RAMADHANI ZUHRI</td>
                        </tr>
                        <tr class="bg-ink-50/30">
                            <td class="py-2 px-3 border-r border-ink-100 text-center font-bold">2</td>
                            <td class="py-2 px-3 border-r border-ink-100 font-bold text-ink-900">X.2</td>
                            <td class="py-2 px-3 border-r border-ink-100 text-center font-bold">10</td>
                            <td class="py-2 px-3 border-r border-ink-100 text-center font-mono">14</td>
                            <td class="py-2 px-3 border-r border-ink-100 text-center font-mono">22</td>
                            <td class="py-2 px-3 border-r border-ink-100 text-center font-bold text-ink-900 font-mono">36</td>
                            <td class="py-2 px-4 font-bold text-ink-900">Lesmiarti</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-[11px] text-ink-500 font-semibold">
                * Sistem otomatis mendaftarkan guru dan rombel kelas, serta langsung menautkannya sebagai <strong>Wali Kelas</strong>. Kata sandi default akun guru adalah NIP atau kata sandi acak awal yang dapat diekspor.
            </p>
        </div>

        {{-- Upload Form --}}
        <form method="POST" action="{{ route('admin.teachers.import') }}" enctype="multipart/form-data" class="pt-3 border-t border-primary-200 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            @csrf
            <div class="flex-1">
                <input type="file"
                       name="excel_file"
                       accept=".xlsx,.xls,.csv,.txt"
                       required
                       class="w-full text-xs text-ink-700 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-primary-400 file:text-ink-900 hover:file:bg-primary-500 cursor-pointer bg-white p-1 rounded-2xl border border-ink-200">
            </div>
            <button type="submit" class="btn-primary text-xs sm:text-sm font-black py-2.5 px-6 shadow-btn shrink-0">
                Unggah & Proses Impor Guru
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Teachers List (2 cols) --}}
        <div class="lg:col-span-2 card p-5 sm:p-6 border border-ink-100">
            <h3 class="text-base font-extrabold text-ink-900 mb-4">Daftar Guru ({{ $teachers->total() }})</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-ink-100 text-xs font-extrabold text-ink-500 uppercase tracking-wider">
                            <th class="py-3 px-3">Nama Guru</th>
                            <th class="py-3 px-3">NIP</th>
                            <th class="py-3 px-3">Gol/Jabatan</th>
                            <th class="py-3 px-3">Wali Kelas</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100">
                        @forelse($teachers as $tc)
                            <tr class="hover:bg-primary-50/50 transition">
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-2">
                                        <div class="avatar size-8 text-xs font-extrabold shrink-0 bg-lavender-soft text-purple-900">
                                            {{ substr($tc->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-ink-900 block">{{ $tc->name }}</span>
                                            <span class="text-[10px] text-ink-500 font-semibold">{{ $tc->email ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 font-semibold text-ink-700 tabular-nums">
                                    {{ $tc->nip ?? '-' }}
                                </td>
                                <td class="py-3 px-3 text-xs">
                                    @if($tc->jabatan || $tc->gol_ruang)
                                        <span class="font-bold text-ink-800">{{ $tc->jabatan ?? '-' }}</span>
                                        @if($tc->gol_ruang && $tc->gol_ruang !== '-')
                                            <span class="text-[10px] text-primary-700 bg-primary-50 px-1.5 py-0.5 rounded font-bold ml-1">Gol. {{ $tc->gol_ruang }}</span>
                                        @endif
                                    @else
                                        <span class="text-ink-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    @php $homerooms = $tc->homeroomClassrooms; @endphp
                                    @if($homerooms->count() > 0)
                                        @foreach($homerooms as $hr)
                                            <span class="chip bg-primary-100 text-primary-800 text-xs font-bold mr-1">
                                                {{ $hr->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-xs text-ink-500">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button"
                                                @click="editModal = true; editName = '{{ addslashes($tc->name) }}'; editNip = '{{ $tc->nip ?? '' }}'; editEmail = '{{ $tc->email ?? '' }}'; editGolRuang = '{{ $tc->gol_ruang ?? '' }}'; editJabatan = '{{ $tc->jabatan ?? '' }}'; editUrl = '{{ route('admin.teachers.update', $tc->id) }}'"
                                                class="btn-outline text-xs font-bold py-1 px-2.5 rounded-lg flex items-center gap-1 hover:bg-primary-50 hover:text-primary-800 cursor-pointer"
                                                title="Edit Guru">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            <span>Edit</span>
                                        </button>
                                        <button type="button"
                                                @click="deleteModal = true; deleteName = '{{ addslashes($tc->name) }}'; deleteUrl = '{{ route('admin.teachers.destroy', $tc->id) }}'"
                                                class="btn-outline text-xs font-bold py-1 px-2.5 rounded-lg text-rose-600 border-rose-200 hover:bg-rose-50 flex items-center gap-1 cursor-pointer"
                                                title="Hapus Guru">
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
                                <td colspan="5" class="text-center py-8 text-ink-500 font-semibold">
                                    Belum ada data guru terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($teachers->hasPages())
                <div class="pt-4 mt-2 border-t border-ink-100">
                    {{ $teachers->links() }}
                </div>
            @endif
        </div>

        {{-- Form Add Teacher (1 col) --}}
        <div class="card p-5 sm:p-6 border border-ink-100 h-fit">
            <h3 class="text-base font-extrabold text-ink-900 mb-1">+ Tambah Guru Baru</h3>
            <p class="text-xs text-ink-500 mb-4">Kata sandi awal otomatis disetel sama dengan NIP.</p>

            <form method="POST" action="{{ route('admin.teachers.store') }}" class="space-y-3">
                @csrf

                <div>
                    <label for="name" class="input-label">Nama Lengkap & Gelar</label>
                    <input type="text"
                           id="name"
                           name="name"
                           placeholder="Contoh: Bu Ani Suryani, S.Pd."
                           required
                           class="input text-sm">
                </div>

                <div>
                    <label for="nip" class="input-label">Nomor Induk Pegawai (NIP)</label>
                    <input type="text"
                           id="nip"
                           name="nip"
                           placeholder="Contoh: 198501152010012001"
                           required
                           class="input text-sm">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label for="gol_ruang" class="input-label">Gol / Ruang</label>
                        <input type="text"
                               id="gol_ruang"
                               name="gol_ruang"
                               placeholder="Contoh: IX, IV/a, III/b"
                               class="input text-sm">
                    </div>
                    <div>
                        <label for="jabatan" class="input-label">Jabatan</label>
                        <input type="text"
                               id="jabatan"
                               name="jabatan"
                               placeholder="Contoh: Guru Madya, P3K"
                               class="input text-sm">
                    </div>
                </div>

                <div>
                    <label for="email" class="input-label">Email (Opsional)</label>
                    <input type="email"
                           id="email"
                           name="email"
                           placeholder="Contoh: guru@sekolah.sch.id"
                           class="input text-sm">
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full text-sm font-bold shadow-btn">
                        Simpan Data Guru
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- MODAL EDIT GURU --}}
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
                        <h3 class="font-extrabold text-ink-900 text-base">Edit Data Guru</h3>
                        <p class="text-xs font-semibold text-ink-500">Perbarui identitas, NIP, atau kepangkatan guru</p>
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
                    <label class="input-label">Nama Lengkap & Gelar</label>
                    <input type="text"
                           name="name"
                           x-model="editName"
                           required
                           class="input text-sm">
                </div>

                <div>
                    <label class="input-label">NIP</label>
                    <input type="text"
                           name="nip"
                           x-model="editNip"
                           required
                           class="input text-sm">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="input-label">Gol / Ruang</label>
                        <input type="text"
                               name="gol_ruang"
                               x-model="editGolRuang"
                               placeholder="Contoh: IV/a"
                               class="input text-sm">
                    </div>
                    <div>
                        <label class="input-label">Jabatan</label>
                        <input type="text"
                               name="jabatan"
                               x-model="editJabatan"
                               placeholder="Contoh: Guru Madya"
                               class="input text-sm">
                    </div>
                </div>

                <div>
                    <label class="input-label">Email</label>
                    <input type="email"
                           name="email"
                           x-model="editEmail"
                           placeholder="guru@sekolah.sch.id"
                           class="input text-sm">
                </div>

                <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-xs flex items-center gap-2">
                    <input type="checkbox" id="reset_pwd_guru" name="reset_password" value="1" class="rounded text-primary-600 size-4">
                    <label for="reset_pwd_guru" class="text-ink-800 font-semibold cursor-pointer">
                        Reset kata sandi guru kembali ke NIP awal
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

    {{-- MODAL HAPUS GURU --}}
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
                <h3 class="font-extrabold text-ink-900 text-base">Hapus Data Guru?</h3>
                <p class="text-xs text-ink-600 leading-relaxed">
                    Yakin ingin menghapus guru <strong class="text-rose-600" x-text="deleteName"></strong>? Penugasan wali kelas yang bersangkutan akan dikosongkan.
                </p>
            </div>
            <form method="POST" :action="deleteUrl" class="flex items-center gap-2 pt-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="btn-outline flex-1 text-xs font-bold py-2.5 rounded-xl">
                    Batal
                </button>
                <button type="submit" class="btn-primary flex-1 text-xs font-bold py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-btn">
                    Hapus Guru
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
