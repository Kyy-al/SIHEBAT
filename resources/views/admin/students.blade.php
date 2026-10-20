@extends('layouts.admin')

@section('title', 'Data Siswa — SiHebat')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">
                Master Data: Siswa
            </h1>
            <p class="text-sm font-semibold text-ink-500 mt-1">
                Kelola akun siswa yang terdaftar. Siswa masuk menggunakan NIS (kata sandi awal = NIS).
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Students List (2 cols) --}}
        <div class="lg:col-span-2 card p-5 sm:p-6 border border-ink-100">
            <h3 class="text-base font-extrabold text-ink-900 mb-4">Daftar Siswa ({{ $students->total() }})</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-ink-100 text-xs font-extrabold text-ink-500 uppercase tracking-wider">
                            <th class="py-3 px-3">Nama</th>
                            <th class="py-3 px-3">NIS</th>
                            <th class="py-3 px-3">Kelas</th>
                            <th class="py-3 px-3 text-center">Streak</th>
                            <th class="py-3 px-3 text-right">XP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100">
                        @forelse($students as $st)
                            <tr class="hover:bg-primary-50/50 transition">
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-2">
                                        <div class="avatar size-8 text-xs font-extrabold shrink-0">
                                            {{ substr($st->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-ink-900">{{ $st->name }}</span>
                                            @if($st->must_change_password)
                                                <span class="block text-[10px] text-amber-700 font-bold">Wajib ganti sandi</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 font-semibold text-ink-700 tabular-nums">
                                    {{ $st->nis }}
                                </td>
                                <td class="py-3 px-3">
                                    @php $cls = $st->currentClassroom(); @endphp
                                    @if($cls)
                                        <span class="chip bg-primary-100 text-primary-800 text-xs font-bold">
                                            {{ $cls->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-ink-500">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="chip-streak text-xs py-0.5 px-2">
                                        🔥 {{ $st->current_streak }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right font-extrabold text-amber-700 tabular-nums">
                                    {{ number_format($st->xp) }} XP
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-ink-500 font-semibold">
                                    Belum ada data siswa terdaftar.
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

        {{-- Form Add Student (1 col) --}}
        <div class="card p-5 sm:p-6 border border-ink-100 h-fit">
            <h3 class="text-base font-extrabold text-ink-900 mb-1">+ Tambah Siswa Baru</h3>
            <p class="text-xs text-ink-500 mb-4">Kata sandi awal siswa otomatis disetel sama dengan NIS.</p>

            <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-3">
                @csrf

                <div>
                    <label for="name" class="input-label">Nama Lengkap Siswa</label>
                    <input type="text"
                           id="name"
                           name="name"
                           placeholder="Contoh: Rina Wulandari"
                           required
                           class="input text-sm">
                </div>

                <div>
                    <label for="nis" class="input-label">Nomor Induk Siswa (NIS)</label>
                    <input type="text"
                           id="nis"
                           name="nis"
                           placeholder="Contoh: 20261006"
                           required
                           class="input text-sm">
                    <p class="input-hint">Digunakan siswa untuk masuk ke akunnya.</p>
                </div>

                <div>
                    <label for="classroom_id" class="input-label">Pilih Kelas</label>
                    <select id="classroom_id" name="classroom_id" class="input text-sm">
                        <option value="">-- Tanpa Kelas (Atur Nanti) --</option>
                        @foreach($classrooms as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }} (Kelas {{ $cls->grade }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full text-sm font-bold shadow-btn">
                        Simpan Data Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
