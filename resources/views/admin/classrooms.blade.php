@extends('layouts.admin')

@section('title', 'Kelas & Rombel — SiHebat')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">
                Master Data: Kelas & Rombongan Belajar
            </h1>
            <p class="text-sm font-semibold text-ink-500 mt-1">
                Tahun Ajaran Aktif: <strong class="text-ink-900">{{ $academicYear ? $academicYear->full_name : 'Belum Ada yang Aktif' }}</strong>
            </p>
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
            <div class="lg:col-span-2 card p-5 sm:p-6 border border-ink-100">
                <h3 class="text-base font-extrabold text-ink-900 mb-4">Daftar Kelas ({{ count($classrooms) }})</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-ink-100 text-xs font-extrabold text-ink-500 uppercase tracking-wider">
                                <th class="py-3 px-3">Tingkat</th>
                                <th class="py-3 px-3">Nama Kelas</th>
                                <th class="py-3 px-3">Wali Kelas</th>
                                <th class="py-3 px-3 text-center">Jumlah Siswa</th>
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
                                        {{ $cls->homeroomTeacher ? $cls->homeroomTeacher->name : '-' }}
                                    </td>
                                    <td class="py-3 px-3 text-center font-extrabold text-ink-900 tabular-nums">
                                        {{ $cls->students_count }} siswa
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-ink-500 font-semibold">
                                        Belum ada kelas yang terdaftar pada tahun ajaran ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Form Add Classroom (1 col) --}}
            <div class="card p-5 sm:p-6 border border-ink-100 h-fit">
                <h3 class="text-base font-extrabold text-ink-900 mb-3">+ Tambah Kelas</h3>

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
                               placeholder="Contoh: X-1, XI MIPA 2"
                               required
                               class="input text-sm">
                    </div>

                    <div>
                        <label for="homeroom_teacher_id" class="input-label">Wali Kelas</label>
                        <select id="homeroom_teacher_id" name="homeroom_teacher_id" class="input text-sm">
                            <option value="">-- Pilih Guru (Opsional) --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
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
</div>
@endsection
