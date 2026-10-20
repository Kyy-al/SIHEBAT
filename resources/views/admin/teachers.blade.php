@extends('layouts.admin')

@section('title', 'Data Guru — SiHebat')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">
                Master Data: Guru & Wali Kelas
            </h1>
            <p class="text-sm font-semibold text-ink-500 mt-1">
                Kelola akun pengajar. Guru dapat masuk menggunakan NIP atau Email (kata sandi awal = NIP).
            </p>
        </div>
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
                            <th class="py-3 px-3">Email</th>
                            <th class="py-3 px-3">Wali Kelas</th>
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
                                        <span class="font-bold text-ink-900">{{ $tc->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3 font-semibold text-ink-700 tabular-nums">
                                    {{ $tc->nip ?? '-' }}
                                </td>
                                <td class="py-3 px-3 text-ink-700 text-xs">
                                    {{ $tc->email ?? '-' }}
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-ink-500 font-semibold">
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
</div>
@endsection
