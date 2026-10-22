@extends('layouts.admin')

@section('title', 'Kelas & Rombel — SiHebat')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{
    editModal: false,
    editId: null,
    editName: '',
    editGrade: 'X',
    editTeacherId: '',
    editUrl: '',
    deleteModal: false,
    deleteUrl: '',
    deleteName: ''
}">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">
                Master Data: Kelas & Rombongan Belajar
            </h1>
            <p class="text-sm font-semibold text-ink-500 mt-1">
                Tahun Ajaran Aktif: <strong class="text-ink-900">{{ $academicYear ? $academicYear->full_name : 'Belum Ada yang Aktif' }}</strong> · Kelola pembagian wali kelas
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.teachers') }}" class="btn-primary text-xs font-black py-2.5 px-4 rounded-xl flex items-center gap-1.5 shadow-sm">
                <span>📥</span>
                <span>Impor Rombel & Wali Kelas</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn-soft text-xs font-extrabold py-2.5 px-4 rounded-xl">
                ← Dashboard
            </a>
        </div>
    </div>

    @if(! $academicYear)
        <div class="card p-6 text-center border border-coral-soft bg-coral-soft/30 max-w-lg mx-auto">
            <span class="text-2xl block mb-2">⚠️</span>
            <h3 class="font-bold text-ink-900 text-base">Belum Ada Tahun Ajaran Aktif</h3>
            <p class="text-xs text-ink-700 mt-1">Silakan aktifkan salah satu tahun ajaran terlebih dahulu sebelum menambahkan kelas.</p>
            <a href="{{ route('admin.academic-years') }}" class="btn-primary text-xs mt-3">Ke Menu Tahun Ajaran</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Classrooms List (2 cols) --}}
            <div class="lg:col-span-2 card p-5 sm:p-6 border border-ink-100/80 shadow-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-extrabold text-ink-900">Daftar Kelas ({{ count($classrooms) }})</h3>
                    <span class="text-xs font-semibold text-ink-500">Klik "Edit" untuk menetapkan/mengubah Wali Kelas</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-ink-100 text-xs font-extrabold text-ink-500 uppercase tracking-wider">
                                <th class="py-3 px-3">Tingkat</th>
                                <th class="py-3 px-3">Nama Kelas</th>
                                <th class="py-3 px-3">Wali Kelas</th>
                                <th class="py-3 px-3 text-center">Jumlah Siswa</th>
                                <th class="py-3 px-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ink-100">
                            @forelse($classrooms as $cls)
                                <tr class="hover:bg-primary-50/50 transition">
                                    <td class="py-3 px-3">
                                        <span class="chip bg-primary-100 text-primary-800 text-xs font-extrabold">
                                            {{ $cls->grade }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 font-bold text-ink-900">
                                        {{ $cls->name }}
                                    </td>
                                    <td class="py-3 px-3 text-ink-700 font-medium">
                                        @if($cls->homeroomTeacher)
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold text-ink-900">{{ $cls->homeroomTeacher->name }}</span>
                                                @if($cls->homeroomTeacher->nip)
                                                    <span class="text-[10px] text-ink-500">({{ $cls->homeroomTeacher->nip }})</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-amber-600 font-semibold bg-amber-50 px-2 py-0.5 rounded text-xs">Belum ada wali</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-center font-extrabold text-ink-900 tabular-nums">
                                        {{ $cls->students_count }} siswa
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button"
                                                    @click="editModal = true; editId = {{ $cls->id }}; editName = '{{ addslashes($cls->name) }}'; editGrade = '{{ $cls->grade }}'; editTeacherId = '{{ $cls->homeroom_teacher_id ?? '' }}'; editUrl = '{{ route('admin.classrooms.update', $cls->id) }}'"
                                                    class="btn-outline text-xs font-bold py-1 px-2.5 rounded-lg flex items-center gap-1 hover:bg-primary-50 hover:text-primary-800"
                                                    title="Edit Kelas">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                                <span>Edit</span>
                                            </button>
                                            <button type="button"
                                                    @click="deleteModal = true; deleteName = '{{ addslashes($cls->name) }}'; deleteUrl = '{{ route('admin.classrooms.destroy', $cls->id) }}'"
                                                    class="btn-outline text-xs font-bold py-1 px-2.5 rounded-lg text-rose-600 border-rose-200 hover:bg-rose-50 flex items-center gap-1 cursor-pointer"
                                                    title="Hapus Kelas">
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
                                        Belum ada kelas yang terdaftar pada tahun ajaran ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Form Add Classroom (1 col) --}}
            <div class="card p-5 sm:p-6 border border-ink-100/80 shadow-card h-fit space-y-3">
                <h3 class="text-base font-extrabold text-ink-900 mb-1">+ Tambah Kelas Baru</h3>
                <p class="text-xs text-ink-500 font-semibold mb-3">Buat rombel kelas dan tentukan wali kelasnya.</p>

                <form method="POST" action="{{ route('admin.classrooms.store') }}" class="space-y-3">
                    @csrf

                    <div>
                        <label for="grade" class="input-label">Tingkat / Angkatan</label>
                        <select id="grade" name="grade" required class="input text-sm">
                            <option value="X">Kelas X</option>
                            <option value="XI">Kelas XI</option>
                            <option value="XII">Kelas XII</option>
                        </select>
                    </div>

                    <div>
                        <label for="name" class="input-label">Nama Kelas</label>
                        <input type="text"
                                id="name"
                                name="name"
                                placeholder="Contoh: X-1, XI-1, XII-1"
                                required
                                class="input text-sm">
                    </div>

                    <div>
                        <label for="homeroom_teacher_id" class="input-label">Wali Kelas</label>
                        <select id="homeroom_teacher_id" name="homeroom_teacher_id" class="input text-sm">
                            <option value="">-- Pilih Guru (Opsional) --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->name }} {{ $teacher->nip ? '(' . $teacher->nip . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn-primary w-full text-sm font-bold shadow-btn">
                            Simpan Kelas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- MODAL EDIT KELAS & WALI KELAS --}}
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
                        <h3 class="font-extrabold text-ink-900 text-base">Edit Kelas & Wali Kelas</h3>
                        <p class="text-xs font-semibold text-ink-500">Perbarui identitas kelas atau tentukan guru wali kelas</p>
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
                    <label class="input-label">Tingkat / Angkatan</label>
                    <select name="grade" x-model="editGrade" required class="input text-sm">
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                </div>

                <div>
                    <label class="input-label">Nama Kelas</label>
                    <input type="text"
                           name="name"
                           x-model="editName"
                           required
                           class="input text-sm">
                </div>

                <div>
                    <label class="input-label">Wali Kelas</label>
                    <select name="homeroom_teacher_id" x-model="editTeacherId" class="input text-sm">
                        <option value="">-- Tanpa Wali Kelas (Kosongkan) --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">
                                {{ $teacher->name }} {{ $teacher->nip ? '(' . $teacher->nip . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
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
    {{-- MODAL HAPUS KELAS --}}
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
                <h3 class="font-extrabold text-ink-900 text-base">Hapus Rombel Kelas?</h3>
                <p class="text-xs text-ink-600 leading-relaxed">
                    Yakin ingin menghapus kelas <strong class="text-rose-600" x-text="deleteName"></strong>? Siswa yang ada di kelas ini akan dilepaskan status rombelnya.
                </p>
            </div>
            <form method="POST" :action="deleteUrl" class="flex items-center gap-2 pt-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="btn-outline flex-1 text-xs font-bold py-2.5 rounded-xl">
                    Batal
                </button>
                <button type="submit" class="btn-primary flex-1 text-xs font-bold py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-btn">
                    Hapus Kelas
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
