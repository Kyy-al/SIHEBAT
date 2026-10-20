@extends('layouts.admin')

@section('title', 'Tahun Ajaran — SiHebat')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">
                Master Data: Tahun Ajaran
            </h1>
            <p class="text-sm font-semibold text-ink-500 mt-1">
                Atur periode aktif untuk tahun ajaran dan semester yang sedang berjalan.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- List of Academic Years (2 cols) --}}
        <div class="lg:col-span-2 card p-5 sm:p-6 border border-ink-100">
            <h3 class="text-base font-extrabold text-ink-900 mb-4">Daftar Tahun Ajaran</h3>

            <div class="space-y-3">
                @forelse($years as $year)
                    <div class="p-4 rounded-2xl border transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3 {{ $year->is_active ? 'bg-primary-50 border-primary-400' : 'bg-surface border-ink-100' }}">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-extrabold text-ink-900 text-base">
                                    {{ $year->name }} · Semester {{ $year->semester }}
                                </h4>
                                @if($year->is_active)
                                    <span class="chip-status-baik text-[11px] py-0.5 px-2.5">
                                        ✓ Aktif
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs font-semibold text-ink-500 mt-1">
                                Periode: {{ $year->start_date->format('d/m/Y') }} s.d. {{ $year->end_date->format('d/m/Y') }}
                            </p>
                        </div>

                        <div>
                            @if(! $year->is_active)
                                <form method="POST" action="{{ route('admin.academic-years.toggle', $year->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-soft text-xs py-2 px-3 font-bold">
                                        Jadikan Aktif
                                    </button>
                                </form>
                            @else
                                <span class="text-xs font-bold text-primary-700">Sedang Digunakan</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-ink-500 text-sm">
                        Belum ada tahun ajaran yang dibuat.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Form Add Academic Year (1 col) --}}
        <div class="card p-5 sm:p-6 border border-ink-100 h-fit">
            <h3 class="text-base font-extrabold text-ink-900 mb-3">+ Tambah Tahun Ajaran</h3>

            <form method="POST" action="{{ route('admin.academic-years.store') }}" class="space-y-3">
                @csrf

                <div>
                    <label for="name" class="input-label">Tahun Ajaran</label>
                    <input type="text"
                           id="name"
                           name="name"
                           placeholder="Contoh: 2026/2027"
                           required
                           class="input text-sm">
                </div>

                <div>
                    <label for="semester" class="input-label">Semester</label>
                    <select id="semester" name="semester" required class="input text-sm">
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>

                <div>
                    <label for="start_date" class="input-label">Tanggal Mulai</label>
                    <input type="date"
                           id="start_date"
                           name="start_date"
                           required
                           class="input text-sm">
                </div>

                <div>
                    <label for="end_date" class="input-label">Tanggal Selesai</label>
                    <input type="date"
                           id="end_date"
                           name="end_date"
                           required
                           class="input text-sm">
                </div>

                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="rounded text-primary-500 size-4">
                        <span class="text-xs font-bold text-ink-700">Langsung jadikan tahun ajaran aktif</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full text-sm font-bold shadow-btn">
                        Simpan Tahun Ajaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
