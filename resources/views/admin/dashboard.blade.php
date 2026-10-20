@extends('layouts.admin')

@section('title', 'Dashboard Administrator — SiHebat')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">
            Dashboard Utama Administrator
        </h1>
        <p class="text-sm font-semibold text-ink-500 mt-1">
            Selamat datang, {{ $user->name }}. Kelola data master dan pantau partisipasi seluruh sekolah.
        </p>
    </div>

    {{-- Banner Ringkasan Kondisi (primary-900 style from design.md) --}}
    <div class="card-banner relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 size-60 rounded-full bg-white/5 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="inline-block px-3 py-1 rounded-full bg-white/10 text-primary-200 text-xs font-extrabold uppercase tracking-wider mb-2">
                    Tahun Ajaran Aktif: {{ $academicYear ? $academicYear->full_name : 'Belum Dipilih' }}
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-white leading-tight">
                    Dashboard 7 Kebiasaan Anak Indonesia Hebat
                </h2>
                <p class="text-sm text-primary-100 font-medium mt-1.5 max-w-xl">
                    Sistem jurnal harian berbasis gamifikasi untuk memantau karakter, kedisiplinan, dan kebiasaan positif siswa.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.students') }}" class="btn bg-white text-ink-900 hover:bg-primary-50 text-sm font-bold shadow-sm">
                    + Tambah Siswa
                </a>
                <a href="{{ route('admin.classrooms') }}" class="btn bg-primary-400 text-ink-900 hover:bg-primary-500 text-sm font-bold shadow-btn">
                    Kelola Kelas
                </a>
            </div>
        </div>
    </div>

    {{-- Quick Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-5 border border-ink-100">
            <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">Total Siswa</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-3xl font-black text-ink-900 tabular-nums">{{ $totalStudents }}</span>
                <span class="text-2xl">🎓</span>
            </div>
            <a href="{{ route('admin.students') }}" class="text-xs font-bold text-primary-700 hover:underline mt-2 inline-block">
                Lihat daftar siswa →
            </a>
        </div>

        <div class="card p-5 border border-ink-100">
            <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">Total Guru / Wali Kelas</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-3xl font-black text-ink-900 tabular-nums">{{ $totalTeachers }}</span>
                <span class="text-2xl">👨‍🏫</span>
            </div>
            <a href="{{ route('admin.teachers') }}" class="text-xs font-bold text-primary-700 hover:underline mt-2 inline-block">
                Lihat daftar guru →
            </a>
        </div>

        <div class="card p-5 border border-ink-100">
            <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">Total Kelas Aktif</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-3xl font-black text-ink-900 tabular-nums">{{ $totalClassrooms }}</span>
                <span class="text-2xl">🏫</span>
            </div>
            <a href="{{ route('admin.classrooms') }}" class="text-xs font-bold text-primary-700 hover:underline mt-2 inline-block">
                Kelola rombel kelas →
            </a>
        </div>

        <div class="card p-5 border border-ink-100">
            <span class="text-xs font-bold text-ink-500 uppercase tracking-wider block">Tahun Ajaran</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-xl font-black text-ink-900 truncate">
                    {{ $academicYear ? $academicYear->name : '-' }}
                </span>
                <span class="text-2xl">📅</span>
            </div>
            <a href="{{ route('admin.academic-years') }}" class="text-xs font-bold text-primary-700 hover:underline mt-2 inline-block">
                Atur semester aktif →
            </a>
        </div>
    </div>

    {{-- Master Data Management Shortcuts --}}
    <div class="card p-6 border border-ink-100">
        <h3 class="text-base font-extrabold text-ink-900 mb-4">Pintasan Manajemen Data Master</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.academic-years') }}" class="p-4 rounded-2xl border border-ink-100 hover:border-primary-400 hover:bg-primary-50/50 transition flex flex-col justify-between">
                <div>
                    <span class="text-2xl mb-2 block">📅</span>
                    <h4 class="font-bold text-ink-900 text-sm">Tahun Ajaran</h4>
                    <p class="text-xs text-ink-500 mt-1">Atur semester ganjil/genap dan masa aktif pengisian.</p>
                </div>
                <span class="text-xs font-bold text-primary-700 mt-3 inline-block">Buka Menu →</span>
            </a>

            <a href="{{ route('admin.classrooms') }}" class="p-4 rounded-2xl border border-ink-100 hover:border-primary-400 hover:bg-primary-50/50 transition flex flex-col justify-between">
                <div>
                    <span class="text-2xl mb-2 block">🏫</span>
                    <h4 class="font-bold text-ink-900 text-sm">Kelas & Rombel</h4>
                    <p class="text-xs text-ink-500 mt-1">Daftarkan kelas per angkatan dan tetapkan wali kelas.</p>
                </div>
                <span class="text-xs font-bold text-primary-700 mt-3 inline-block">Buka Menu →</span>
            </a>

            <a href="{{ route('admin.students') }}" class="p-4 rounded-2xl border border-ink-100 hover:border-primary-400 hover:bg-primary-50/50 transition flex flex-col justify-between">
                <div>
                    <span class="text-2xl mb-2 block">🎓</span>
                    <h4 class="font-bold text-ink-900 text-sm">Data Siswa</h4>
                    <p class="text-xs text-ink-500 mt-1">Kelola akun siswa berbasis NIS dan penempatan kelas.</p>
                </div>
                <span class="text-xs font-bold text-primary-700 mt-3 inline-block">Buka Menu →</span>
            </a>

            <a href="{{ route('admin.teachers') }}" class="p-4 rounded-2xl border border-ink-100 hover:border-primary-400 hover:bg-primary-50/50 transition flex flex-col justify-between">
                <div>
                    <span class="text-2xl mb-2 block">👨‍🏫</span>
                    <h4 class="font-bold text-ink-900 text-sm">Data Guru</h4>
                    <p class="text-xs text-ink-500 mt-1">Kelola akun guru berbasis NIP dan hak akses wali kelas.</p>
                </div>
                <span class="text-xs font-bold text-primary-700 mt-3 inline-block">Buka Menu →</span>
            </a>
        </div>
    </div>
</div>
@endsection
