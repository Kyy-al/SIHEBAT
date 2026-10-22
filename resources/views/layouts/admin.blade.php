@extends('layouts.base')

@section('title', 'SiHebat — ' . ($title ?? 'Dashboard'))

@section('body')
<div class="flex min-h-dvh bg-[#EEF3F8]" x-data="{
    modalNotif: false,
    modalSearch: false,
    modalHelp: false,
    modalExport: false,
    searchKeyword: '',
    exportScope: 'school',
    exportClassroomId: '{{ \App\Models\Classroom::first()->id ?? 1 }}',
    exportHabitSlug: 'tidur-cepat',
    exportFormat: 'pdf',
    executeExport() {
        if (this.exportFormat === 'pdf') {
            if (this.exportScope === 'school') {
                window.open('{{ route('admin.report.print') }}', '_blank');
            } else if (this.exportScope === 'classroom') {
                window.open('/guru/laporan/cetak/' + this.exportClassroomId, '_blank');
            } else {
                window.open('{{ route('admin.akar-masalah') }}?habit=' + this.exportHabitSlug, '_blank');
            }
        } else {
            window.location.href = '{{ route('admin.report.excel') }}';
        }
        this.modalExport = false;
    }
}" @open-export.window="modalExport = true; if ($event.detail && $event.detail.format) exportFormat = $event.detail.format">
    {{-- Sidebar (desktop) inspired by reference image 1 --}}
    <aside class="hidden lg:flex lg:flex-col lg:w-64 bg-white border-r border-ink-100/80 p-5 fixed inset-y-0 left-0 z-40 justify-between">
        <div class="space-y-6">
            {{-- Logo Brand --}}
            <div class="px-2">
                <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('guru.dashboard') }}" class="block">
                    <x-logo size="md" :subtitle="Auth::user()->isAdmin() ? 'Panel Admin' : 'Panel Wali Kelas'" />
                </a>
            </div>

            {{-- Nav links with active chevron (Image 1 style) --}}
            <nav class="flex flex-col gap-1.5">
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       class="group {{ request()->routeIs('admin.dashboard') ? 'bg-primary-400 text-ink-900 font-extrabold shadow-sm' : 'text-ink-500 font-bold hover:text-ink-900 hover:bg-primary-50/70' }} px-4 py-3 rounded-2xl flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="currentColor" viewBox="0 0 256 256"><path d="M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40Zm0,160H40V56H216V200ZM176,112a8,8,0,0,1-8,8H88a8,8,0,0,1,0-16h80A8,8,0,0,1,176,112Zm0,32a8,8,0,0,1-8,8H88a8,8,0,0,1,0-16h80A8,8,0,0,1,176,144Z"/></svg>
                            <span class="text-sm">Dashboard</span>
                        </div>
                        @if(request()->routeIs('admin.dashboard'))
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 opacity-80" fill="currentColor" viewBox="0 0 256 256"><path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"/></svg>
                        @endif
                    </a>

                    <span class="text-[11px] font-extrabold text-ink-500 uppercase tracking-wider px-4 pt-3 pb-1">Master Data</span>

                    <a href="{{ route('admin.academic-years') }}"
                       class="group {{ request()->routeIs('admin.academic-years*') ? 'bg-primary-400 text-ink-900 font-extrabold shadow-sm' : 'text-ink-500 font-bold hover:text-ink-900 hover:bg-primary-50/70' }} px-4 py-3 rounded-2xl flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="currentColor" viewBox="0 0 256 256"><path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32Zm0,176H48V48H72v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24Z"/></svg>
                            <span class="text-sm">Tahun Ajaran</span>
                        </div>
                        @if(request()->routeIs('admin.academic-years*'))
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 opacity-80" fill="currentColor" viewBox="0 0 256 256"><path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"/></svg>
                        @endif
                    </a>

                    <a href="{{ route('admin.classrooms') }}"
                       class="group {{ request()->routeIs('admin.classrooms*') ? 'bg-primary-400 text-ink-900 font-extrabold shadow-sm' : 'text-ink-500 font-bold hover:text-ink-900 hover:bg-primary-50/70' }} px-4 py-3 rounded-2xl flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="currentColor" viewBox="0 0 256 256"><path d="M224,48H160a40,40,0,0,0-32,16A40,40,0,0,0,96,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H96a24,24,0,0,1,24,24,8,8,0,0,0,16,0,24,24,0,0,1,24-24h64a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48Z"/></svg>
                            <span class="text-sm">Kelas & Rombel</span>
                        </div>
                        @if(request()->routeIs('admin.classrooms*'))
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 opacity-80" fill="currentColor" viewBox="0 0 256 256"><path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"/></svg>
                        @endif
                    </a>

                    <a href="{{ route('admin.students') }}"
                       class="group {{ request()->routeIs('admin.students*') ? 'bg-primary-400 text-ink-900 font-extrabold shadow-sm' : 'text-ink-500 font-bold hover:text-ink-900 hover:bg-primary-50/70' }} px-4 py-3 rounded-2xl flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="currentColor" viewBox="0 0 256 256"><path d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.14,98.7a8,8,0,0,1-11.07-2.33A79.83,79.83,0,0,0,172,168a8,8,0,0,1,0-16,44,44,0,1,0-16.34-84.87,8,8,0,1,1-5.94-14.85,60,60,0,0,1,55.53,105.64,95.83,95.83,0,0,1,47.22,37.71A8,8,0,0,1,250.14,206.7Z"/></svg>
                            <span class="text-sm">Data Siswa</span>
                        </div>
                        @if(request()->routeIs('admin.students*'))
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 opacity-80" fill="currentColor" viewBox="0 0 256 256"><path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"/></svg>
                        @endif
                    </a>

                    <a href="{{ route('admin.leaderboard') }}"
                       class="group {{ request()->routeIs('admin.leaderboard*') ? 'bg-primary-400 text-ink-900 font-extrabold shadow-sm' : 'text-ink-500 font-bold hover:text-ink-900 hover:bg-primary-50/70' }} px-4 py-3 rounded-2xl flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="currentColor" viewBox="0 0 256 256">
                                <path d="M232,80H208V56a16,16,0,0,0-16-16H64A16,16,0,0,0,48,56V80H24A16,16,0,0,0,8,96v16a64.07,64.07,0,0,0,61.42,63.91,88.16,88.16,0,0,0,42.58,35.45V232H88a8,8,0,0,0,0,16h80a8,8,0,0,0,0-16H144V211.36a88.16,88.16,0,0,0,42.58-35.45A64.07,64.07,0,0,0,248,112V96A16,16,0,0,0,232,80ZM24,112V96H48v39.51A48.09,48.09,0,0,1,24,112Zm168,48a72,72,0,0,1-128,0V56H192Zm40-48a48.09,48.09,0,0,1-24,23.51V96h24Z"/>
                            </svg>
                            <span class="text-sm">Leaderboard & Streak</span>
                        </div>
                        @if(request()->routeIs('admin.leaderboard*'))
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 opacity-80" fill="currentColor" viewBox="0 0 256 256"><path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"/></svg>
                        @endif
                    </a>

                    <a href="{{ route('admin.akar-masalah') }}"
                       class="group {{ request()->routeIs('admin.akar-masalah*') ? 'bg-primary-400 text-ink-900 font-extrabold shadow-sm' : 'text-ink-500 font-bold hover:text-ink-900 hover:bg-primary-50/70' }} px-4 py-3 rounded-2xl flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="currentColor" viewBox="0 0 256 256"><path d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"/></svg>
                            <span class="text-sm">Akar Masalah</span>
                        </div>
                        @if(request()->routeIs('admin.akar-masalah*'))
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 opacity-80" fill="currentColor" viewBox="0 0 256 256"><path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"/></svg>
                        @endif
                    </a>

                    <button type="button"
                            @click="modalExport = true"
                            class="group w-full text-left text-ink-500 font-bold hover:text-ink-900 hover:bg-primary-50/70 px-4 py-3 rounded-2xl flex items-center justify-between transition-colors cursor-pointer">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="currentColor" viewBox="0 0 256 256"><path d="M224,96H200a8,8,0,0,0,0,16h24v80H32V112H56a8,8,0,0,0,0-16H32a16,16,0,0,0-16,16v80a16,16,0,0,0,16,16H64v16a16,16,0,0,0,16,16H176a16,16,0,0,0,16-16V208h32a16,16,0,0,0,16-16V112A16,16,0,0,0,224,96Zm-48,128H80V176h96ZM192,48V80a8,8,0,0,1-16,0V48H80V80a8,8,0,0,1-16,0V48A16,16,0,0,1,80,32h96A16,16,0,0,1,192,48Z"/></svg>
                            <span class="text-sm">Cetak Dokumen</span>
                        </div>
                        <span class="text-[10px] bg-primary-100 text-primary-800 font-extrabold px-2 py-0.5 rounded-md">Pilih</span>
                    </button>

                    <a href="{{ route('admin.teachers') }}"
                       class="group {{ request()->routeIs('admin.teachers*') ? 'bg-primary-400 text-ink-900 font-extrabold shadow-sm' : 'text-ink-500 font-bold hover:text-ink-900 hover:bg-primary-50/70' }} px-4 py-3 rounded-2xl flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="currentColor" viewBox="0 0 256 256"><path d="M226.53,56.41l-96-32a8,8,0,0,0-5.06,0l-96,32A8,8,0,0,0,24,64v80a8,8,0,0,0,16,0V75.1l33.59,11.2A64,64,0,0,0,96,192h64a64,64,0,0,0,22.41-105.7L232,68.49A8,8,0,0,0,226.53,56.41Z"/></svg>
                            <span class="text-sm">Data Guru</span>
                        </div>
                        @if(request()->routeIs('admin.teachers*'))
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 opacity-80" fill="currentColor" viewBox="0 0 256 256"><path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"/></svg>
                        @endif
                    </a>

                    <a href="{{ route('admin.achievements') }}"
                       class="group {{ request()->routeIs('admin.achievements*') ? 'bg-primary-400 text-ink-900 font-extrabold shadow-sm' : 'text-ink-500 font-bold hover:text-ink-900 hover:bg-primary-50/70' }} px-4 py-3 rounded-2xl flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="currentColor" viewBox="0 0 256 256"><path d="M232,64H208V48a8,8,0,0,0-8-8H56a8,8,0,0,0-8,8V64H24A16,16,0,0,0,8,80v32a56.06,56.06,0,0,0,56,56h1.77A64.1,64.1,0,0,0,120,211.82V232H88a8,8,0,0,0,0,16h80a8,8,0,0,0,0-16H136V211.82A64.1,64.1,0,0,0,190.23,168H192a56.06,56.06,0,0,0,56-56V80A16,16,0,0,0,232,64ZM40,112V80H48v56a63.84,63.84,0,0,0,1,11.11A40.34,40.34,0,0,1,40,112ZM192,160a48,48,0,0,1-96,0V56H192Zm24-48a40.34,40.34,0,0,1-9,35.11,63.84,63.84,0,0,0,1-11.11V80h8Z"/></svg>
                            <span class="text-sm">Achievement</span>
                        </div>
                        @if(request()->routeIs('admin.achievements*'))
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 opacity-80" fill="currentColor" viewBox="0 0 256 256"><path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"/></svg>
                        @endif
                    </a>
                @else
                    <a href="{{ route('guru.dashboard') }}"
                       class="group {{ request()->routeIs('guru.dashboard') ? 'bg-primary-400 text-ink-900 font-extrabold shadow-sm' : 'text-ink-500 font-bold hover:text-ink-900 hover:bg-primary-50/70' }} px-4 py-3 rounded-2xl flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="currentColor" viewBox="0 0 256 256"><path d="M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40Zm0,160H40V56H216V200Z"/></svg>
                            <span class="text-sm">Dashboard Kelas</span>
                        </div>
                        @if(request()->routeIs('guru.dashboard'))
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 opacity-80" fill="currentColor" viewBox="0 0 256 256"><path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"/></svg>
                        @endif
                    </a>

                    @php
                        $guruClassroom = Auth::user()->homeroomClassrooms()->first();
                    @endphp
                    @if($guruClassroom)
                        <a href="{{ route('guru.report.print', $guruClassroom->id) }}" target="_blank"
                           class="group text-ink-500 font-bold hover:text-ink-900 hover:bg-primary-50/70 px-4 py-3 rounded-2xl flex items-center justify-between transition-colors">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="currentColor" viewBox="0 0 256 256"><path d="M224,96H200a8,8,0,0,0,0,16h24v80H32V112H56a8,8,0,0,0,0-16H32a16,16,0,0,0-16,16v80a16,16,0,0,0,16,16H64v16a16,16,0,0,0,16,16H176a16,16,0,0,0,16-16V208h32a16,16,0,0,0,16-16V112A16,16,0,0,0,224,96Zm-48,128H80V176h96ZM192,48V80a8,8,0,0,1-16,0V48H80V80a8,8,0,0,1-16,0V48A16,16,0,0,1,80,32h96A16,16,0,0,1,192,48Z"/></svg>
                                <span class="text-sm">Laporan Cetak</span>
                            </div>
                        </a>
                    @endif
                @endif
            </nav>
        </div>

        {{-- Profile Card & Logout (Mode Terang Bersih & Elegan) --}}
        <div class="space-y-3 pt-4 border-t border-ink-100/80">
            {{-- User card --}}
            <div class="flex items-center gap-3 px-2">
                <div class="avatar size-10 rounded-full bg-primary-200 text-primary-900 font-black text-sm shrink-0 shadow-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-ink-900 truncate leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-ink-500 font-bold capitalize">{{ Auth::user()->role }} Sekolah</p>
                </div>
            </div>

            {{-- Logout link --}}
            <form method="POST" action="{{ route('logout') }}" class="px-2 pt-1">
                @csrf
                <button type="submit" class="text-xs text-ink-500 font-bold hover:text-coral transition-colors flex items-center gap-1.5 cursor-pointer">
                    <span>←</span> Keluar Akun
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content container --}}
    <main class="flex-1 lg:ml-64 flex flex-col min-h-dvh">
        {{-- Top App Bar inspired by Image 1 & Image 4 --}}
        <header class="bg-white/80 backdrop-blur-md border-b border-ink-100/80 px-4 sm:px-6 lg:px-8 py-3.5 sticky top-0 z-30 flex items-center justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold text-ink-900 tracking-tight">
                    Selamat Datang, {{ explode(' ', Auth::user()->name)[0] }}! 👋
                </h2>
                <p class="text-xs font-semibold text-ink-500 hidden sm:block">
                    Dashboard 7 Kebiasaan Anak Indonesia Hebat · {{ date('d F Y') }}
                </p>
            </div>

            {{-- Top Right Icons (Gambar 4: Bell, Search, Help) --}}
            <div class="flex items-center gap-2.5">
                {{-- 1. Tombol Notifikasi --}}
                <div class="relative">
                    <button type="button"
                            @click="modalNotif = !modalNotif; modalSearch = false; modalHelp = false"
                            class="btn-icon size-10 rounded-full bg-ink-900 text-white hover:bg-ink-700 transition shadow-sm relative cursor-pointer"
                            title="Notifikasi Sistem">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="currentColor" viewBox="0 0 256 256"><path d="M221.8,175.94C216.25,166.38,208,139.33,208,104a80,80,0,1,0-160,0c0,35.34-8.26,62.38-13.81,71.94A16,16,0,0,0,48,200H88.81a40,40,0,0,0,78.38,0H208a16,16,0,0,0,13.8-24.06ZM128,216a24,24,0,0,1-22.62-16h45.24A24,24,0,0,1,128,216Z"/></svg>
                        <span class="absolute -top-1 -right-1 size-4 rounded-full bg-rose-500 text-white text-[9px] font-black flex items-center justify-center">3</span>
                    </button>

                    {{-- Dropdown Notifikasi --}}
                    <div x-show="modalNotif"
                         x-cloak
                         @click.away="modalNotif = false"
                         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-ink-100 p-4 z-50 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-ink-100">
                            <span class="text-xs font-black text-ink-900 uppercase tracking-wider">Pemberitahuan Sistem</span>
                            <span class="text-[10px] font-bold text-primary-700 bg-primary-100 px-2 py-0.5 rounded-full">3 Baru</span>
                        </div>
                        <div class="space-y-2.5 max-h-80 overflow-y-auto">
                            <div class="p-3 rounded-xl bg-primary-50 border border-primary-100 space-y-1">
                                <div class="flex items-center justify-between text-xs font-bold text-ink-900">
                                    <span>🔔 Jurnal Harian Aktif</span>
                                    <span class="text-[10px] text-ink-400">Hari ini</span>
                                </div>
                                <p class="text-xs text-ink-600 font-medium">31 rombel terpantau aktif mencatat jurnal pembiasaan karakter hari ini.</p>
                            </div>
                            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100 space-y-1">
                                <div class="flex items-center justify-between text-xs font-bold text-ink-900">
                                    <span>⚡ Gemini 3.5 AI Terhubung</span>
                                    <span class="text-[10px] text-ink-400">Aktif</span>
                                </div>
                                <p class="text-xs text-ink-600 font-medium">Model Google Gemini 3.5 Flash-Lite siap menganalisis data Rapor Sekolah secara langsung.</p>
                            </div>
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-100 space-y-1">
                                <div class="flex items-center justify-between text-xs font-bold text-ink-900">
                                    <span>⚠️ Atensi Kebiasaan Tidur</span>
                                    <span class="text-[10px] text-ink-400">Perhatian</span>
                                </div>
                                <p class="text-xs text-ink-600 font-medium">Rata-rata tidur pukul 22.30 WIB. Disarankan sosialisasi pembatasan gawai bersama wali murid.</p>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-ink-100 text-center">
                            <button type="button" @click="modalNotif = false" class="text-xs font-bold text-primary-700 hover:underline">
                                Tutup Pemberitahuan
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 2. Tombol Pencarian Cepat --}}
                <button type="button"
                        @click="modalSearch = true; modalNotif = false; modalHelp = false"
                        class="btn-icon size-10 rounded-full bg-ink-900 text-white hover:bg-ink-700 transition shadow-sm cursor-pointer"
                        title="Pencarian Cepat Menu & Siswa">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="currentColor" viewBox="0 0 256 256"><path d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"/></svg>
                </button>

                {{-- 3. Tombol Bantuan & Panduan --}}
                <button type="button"
                        @click="modalHelp = true; modalNotif = false; modalSearch = false"
                        class="btn-icon size-10 rounded-full bg-ink-900 text-white hover:bg-ink-700 transition shadow-sm hidden sm:inline-flex cursor-pointer"
                        title="Bantuan & Panduan Sistem">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="currentColor" viewBox="0 0 256 256"><path d="M140,180a12,12,0,1,1-12-12A12,12,0,0,1,140,180ZM128,72c-22.06,0-40,16.15-40,36a8,8,0,0,0,16,0c0-11,10.77-20,24-20s24,9,24,20c0,9.22-7.59,15.65-17.72,22.25C122.61,137.91,120,143.51,120,152a8,8,0,0,0,16,0c0-4.14,1.82-7.22,9.39-12.15C157.17,132.06,168,122.58,168,108,168,88.15,150.06,72,128,72Z"/></svg>
                </button>

                {{-- Primary Action Pill Button --}}
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.students') }}" class="btn-primary text-xs sm:text-sm py-2 px-4 rounded-2xl shadow-btn flex items-center gap-1.5 ml-1">
                        <span>+</span>
                        <span class="hidden sm:inline">Tambah</span> Siswa
                    </a>
                @else
                    <a href="{{ route('guru.dashboard') }}" class="btn-primary text-xs sm:text-sm py-2 px-4 rounded-2xl shadow-btn flex items-center gap-1.5 ml-1">
                        <span>📊</span>
                        <span>Rapor Kelas</span>
                    </a>
                @endif
            </div>
        </header>

        {{-- Page Body --}}
        <div class="p-4 sm:p-6 lg:p-8 flex-1">
            @yield('content')
        </div>
    </main>

    {{-- MODAL PENCARIAN CEPAT (COMMAND PALETTE) --}}
    <div x-show="modalSearch"
         x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-20 bg-ink-900/60 backdrop-blur-xs"
         @keydown.escape.window="modalSearch = false">
        <div class="card p-5 max-w-lg w-full bg-white rounded-3xl shadow-2xl border border-ink-100 space-y-4"
             @click.away="modalSearch = false">
            <div class="flex items-center justify-between pb-2 border-b border-ink-100">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-primary-600" fill="currentColor" viewBox="0 0 256 256"><path d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"/></svg>
                    <h3 class="font-extrabold text-ink-900 text-base">Pencarian Cepat SiHebat</h3>
                </div>
                <button type="button" @click="modalSearch = false" class="size-7 rounded-full bg-ink-100 text-ink-600 font-bold text-xs flex items-center justify-center">✕</button>
            </div>

            <form method="GET" action="{{ route('admin.students') }}" class="space-y-3">
                <input type="text"
                       name="search"
                       x-model="searchKeyword"
                       placeholder="Cari siswa berdasarkan nama atau NIS, lalu tekan Enter..."
                       class="input w-full py-3 text-sm rounded-xl">
            </form>

            <div class="space-y-2 pt-1">
                <span class="text-[11px] font-bold text-ink-400 uppercase tracking-wider block">Pintasan Menu Cepat:</span>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <a href="{{ route('admin.dashboard') }}" class="p-2.5 rounded-xl bg-ink-50 hover:bg-primary-50 font-bold text-ink-800 flex items-center gap-2 transition">
                        <span>📊</span> Dashboard
                    </a>
                    <a href="{{ route('admin.classrooms') }}" class="p-2.5 rounded-xl bg-ink-50 hover:bg-primary-50 font-bold text-ink-800 flex items-center gap-2 transition">
                        <span>🏫</span> Kelas & Rombel
                    </a>
                    <a href="{{ route('admin.students') }}" class="p-2.5 rounded-xl bg-ink-50 hover:bg-primary-50 font-bold text-ink-800 flex items-center gap-2 transition">
                        <span>👨‍🎓</span> Data Siswa
                    </a>
                    <a href="{{ route('admin.teachers') }}" class="p-2.5 rounded-xl bg-ink-50 hover:bg-primary-50 font-bold text-ink-800 flex items-center gap-2 transition">
                        <span>👨‍🏫</span> Data Guru
                    </a>
                    <a href="{{ route('admin.achievements') }}" class="p-2.5 rounded-xl bg-ink-50 hover:bg-primary-50 font-bold text-ink-800 flex items-center gap-2 transition">
                        <span>🏆</span> Achievement
                    </a>
                    <a href="{{ route('admin.akar-masalah') }}" class="p-2.5 rounded-xl bg-ink-50 hover:bg-primary-50 font-bold text-ink-800 flex items-center gap-2 transition">
                        <span>🔍</span> Akar Masalah
                    </a>
                    <a href="{{ route('admin.report.print') }}" target="_blank" class="p-2.5 rounded-xl bg-ink-50 hover:bg-primary-50 font-bold text-ink-800 flex items-center gap-2 transition">
                        <span>📄</span> Cetak Rapor
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PANDUAN & BANTUAN SISTEM --}}
    <div x-show="modalHelp"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-xs"
         @keydown.escape.window="modalHelp = false">
        <div class="card p-6 max-w-lg w-full bg-white rounded-3xl shadow-2xl border border-ink-100 space-y-4"
             @click.away="modalHelp = false">
            <div class="flex items-center justify-between pb-2 border-b border-ink-100">
                <div class="flex items-center gap-2.5">
                    <div class="size-8 rounded-xl bg-primary-100 text-primary-800 flex items-center justify-center font-black text-sm">?</div>
                    <h3 class="font-extrabold text-ink-900 text-base">Panduan Penggunaan SiHebat</h3>
                </div>
                <button type="button" @click="modalHelp = false" class="size-7 rounded-full bg-ink-100 text-ink-600 font-bold text-xs flex items-center justify-center">✕</button>
            </div>

            <div class="space-y-3 text-xs leading-relaxed text-ink-700">
                <div class="p-3 rounded-2xl bg-primary-50/60 border border-primary-100 space-y-1">
                    <strong class="font-black text-ink-900 block">🎯 7 Kebiasaan Anak Indonesia Hebat</strong>
                    <p>Program pemantauan karakter harian siswa meliputi: Bangun Pagi, Beribadah, Berolahraga, Makan Sehat, Gemar Belajar, Bermasyarakat, dan Tidur Cepat.</p>
                </div>

                <div class="p-3 rounded-2xl bg-emerald-50/60 border border-emerald-100 space-y-1">
                    <strong class="font-black text-ink-900 block">📋 Kerangka Kerja Rapor Pendidikan (IRB)</strong>
                    <p><strong>Identifikasi:</strong> Cek ringkasan capaian kebiasaan. <strong>Refleksi:</strong> Telusuri akar masalah. <strong>Benahi:</strong> Terapkan saran program aksi dari Gemini AI.</p>
                </div>

                <div class="p-3 rounded-2xl bg-amber-50/60 border border-amber-100 space-y-1">
                    <strong class="font-black text-ink-900 block">⚡ Kecerdasan Buatan (Google Gemini 3.5)</strong>
                    <p>Membantu administrator merumuskan analisis statistik dan rekomendasi aksi nyata secara otomatis berdasarkan data jurnal harian.</p>
                </div>
            </div>

            <div class="pt-2 text-right">
                <button type="button" @click="modalHelp = false" class="btn-primary text-xs font-bold py-2 px-4 rounded-xl shadow-btn">
                    Mengerti
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL INTERAKTIF: PUSAT CETAK DOKUMEN & EKSPOR LAPORAN --}}
    <div x-show="modalExport"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-xs"
         @keydown.escape.window="modalExport = false">
        <div class="card p-6 max-w-lg w-full bg-white rounded-3xl shadow-2xl border border-primary-200 space-y-5"
             @click.away="modalExport = false">
            <div class="flex items-center justify-between pb-3 border-b border-ink-100">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-2xl bg-primary-100 text-primary-800 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-ink-900 text-base">Pusat Cetak Dokumen & Ekspor Laporan</h3>
                        <p class="text-xs font-semibold text-ink-500">Pilih cakupan data dan format tujuan sebelum mencetak</p>
                    </div>
                </div>
                <button type="button" @click="modalExport = false" class="size-8 rounded-full bg-ink-100 text-ink-600 font-bold text-xs flex items-center justify-center">✕</button>
            </div>

            {{-- Langkah 1: Ruang Lingkup Data --}}
            <div class="space-y-2.5">
                <label class="text-xs font-extrabold text-ink-700 uppercase tracking-wider block">1. Pilih Cakupan Laporan:</label>
                <div class="space-y-2">
                    {{-- Opsi A: Seluruh Sekolah --}}
                    <label class="p-3 rounded-2xl border border-ink-100 flex items-start gap-3 cursor-pointer transition"
                           :class="exportScope === 'school' ? 'bg-primary-50/70 border-primary-400 ring-2 ring-primary-300' : 'bg-white hover:bg-ink-50'">
                        <input type="radio" name="scope" value="school" x-model="exportScope" class="mt-0.5 text-primary-600">
                        <div class="text-xs">
                            <strong class="font-black text-ink-900 block">Rapor Satuan Pendidikan (SMAN 1 Sungailiat)</strong>
                            <span class="text-ink-600 font-medium">Rekapitulasi 7 kebiasaan seluruh sekolah, perbandingan rombel, dan top siswa berprestasi.</span>
                        </div>
                    </label>

                    {{-- Opsi B: Per Rombel / Kelas --}}
                    <label class="p-3 rounded-2xl border border-ink-100 flex items-start gap-3 cursor-pointer transition"
                           :class="exportScope === 'classroom' ? 'bg-primary-50/70 border-primary-400 ring-2 ring-primary-300' : 'bg-white hover:bg-ink-50'">
                        <input type="radio" name="scope" value="classroom" x-model="exportScope" class="mt-0.5 text-primary-600">
                        <div class="text-xs flex-1">
                            <strong class="font-black text-ink-900 block">Rapor Khusus Rombongan Belajar (Kelas)</strong>
                            <span class="text-ink-600 font-medium block mb-2">Laporan evaluasi khusus satu kelas beserta daftar siswa dan wali kelas.</span>
                            <div x-show="exportScope === 'classroom'" class="pt-1">
                                <select x-model="exportClassroomId" class="input text-xs py-1.5 px-3 rounded-xl bg-white w-full">
                                    @foreach(\App\Models\Classroom::with('homeroomTeacher')->orderBy('name')->get() as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} (Wali: {{ $c->homeroomTeacher ? $c->homeroomTeacher->name : 'Belum Ditentukan' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </label>

                    {{-- Opsi C: Akar Masalah Tertentu --}}
                    <label class="p-3 rounded-2xl border border-ink-100 flex items-start gap-3 cursor-pointer transition"
                           :class="exportScope === 'habit' ? 'bg-primary-50/70 border-primary-400 ring-2 ring-primary-300' : 'bg-white hover:bg-ink-50'">
                        <input type="radio" name="scope" value="habit" x-model="exportScope" class="mt-0.5 text-primary-600">
                        <div class="text-xs flex-1">
                            <strong class="font-black text-ink-900 block">Analisis Akar Masalah Kebiasaan Tertentu</strong>
                            <span class="text-ink-600 font-medium block mb-2">Buka telaah mendalam pemicu, korelasi statistik, dan intervensi khusus.</span>
                            <div x-show="exportScope === 'habit'" class="pt-1">
                                <select x-model="exportHabitSlug" class="input text-xs py-1.5 px-3 rounded-xl bg-white w-full">
                                    @foreach(\App\Models\Habit::where('is_active', true)->orderBy('order')->get() as $hb)
                                        <option value="{{ $hb->slug }}">{{ $hb->order }}. {{ $hb->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Langkah 2: Format Output --}}
            <div class="space-y-2.5">
                <label class="text-xs font-extrabold text-ink-700 uppercase tracking-wider block">2. Pilih Format Dokumen:</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="p-3.5 rounded-2xl border cursor-pointer text-center space-y-1.5 transition"
                           :class="exportFormat === 'pdf' ? 'bg-primary-100/70 border-primary-500 ring-2 ring-primary-400' : 'bg-white border-ink-100 hover:bg-ink-50'">
                        <input type="radio" name="format" value="pdf" x-model="exportFormat" class="sr-only">
                        <div class="size-8 rounded-xl bg-rose-100 text-rose-700 mx-auto flex items-center justify-center font-black text-xs">PDF</div>
                        <strong class="text-xs font-black text-ink-900 block">Cetak Dokumen PDF</strong>
                        <span class="text-[10px] text-ink-500 font-medium block">Kop Resmi Sekolah, TTD Kepala Sekolah, Format A4</span>
                    </label>

                    <label class="p-3.5 rounded-2xl border cursor-pointer text-center space-y-1.5 transition"
                           :class="exportFormat === 'excel' ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-400' : 'bg-white border-ink-100 hover:bg-ink-50'">
                        <input type="radio" name="format" value="excel" x-model="exportFormat" class="sr-only">
                        <div class="size-8 rounded-xl bg-emerald-100 text-emerald-700 mx-auto flex items-center justify-center font-black text-xs">XLS</div>
                        <strong class="text-xs font-black text-ink-900 block">Ekspor Excel / CSV</strong>
                        <span class="text-[10px] text-ink-500 font-medium block">Spreadsheet UTF-8 BOM Siap Olah di Excel</span>
                    </label>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="pt-3 border-t border-ink-100 flex items-center justify-end gap-2">
                <button type="button" @click="modalExport = false" class="btn-outline text-xs font-bold py-2.5 px-4 rounded-xl">
                    Batal
                </button>
                <button type="button" @click="executeExport()" class="btn-primary text-xs font-bold py-2.5 px-5 rounded-xl shadow-btn flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Lanjutkan & Unduh</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
