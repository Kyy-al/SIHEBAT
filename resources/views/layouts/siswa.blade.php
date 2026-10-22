@extends('layouts.base')

@section('title', 'SiHebat — ' . ($title ?? 'Beranda'))

@section('body')
@php
    $navUser = Auth::user();
    $todayDateStr = \Carbon\Carbon::today()->format('Y-m-d');
    $todayNavJournal = $navUser ? \App\Models\Journal::where('user_id', $navUser->id)->where('date', $todayDateStr)->first() : null;
    $needsJournalToday = !$todayNavJournal || ($todayNavJournal->habits_completed < 7);
@endphp
<div class="flex min-h-dvh">
    {{-- Sidebar (desktop) --}}
    <aside class="hidden lg:flex lg:flex-col lg:w-60 bg-white border-r border-ink-100 p-4 fixed inset-y-0 left-0 z-40">
        {{-- Logo --}}
        <div class="mb-6 px-1">
            <a href="{{ route('siswa.dashboard') }}" class="block">
                <x-logo size="md" subtitle="7 Kebiasaan Hebat" />
            </a>
        </div>

        {{-- Nav links --}}
        <nav class="flex flex-col gap-1 flex-1">
            <a href="{{ route('siswa.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M219.31,108.68l-80-80a16,16,0,0,0-22.62,0l-80,80A15.87,15.87,0,0,0,32,120v96a8,8,0,0,0,8,8H216a8,8,0,0,0,8-8V120A15.87,15.87,0,0,0,219.31,108.68ZM208,208H48V120l80-80,80,80Z"/></svg>
                Beranda
            </a>
            <a href="{{ route('siswa.journal') }}"
               class="sidebar-link {{ request()->routeIs('siswa.journal*') ? 'active' : '' }} relative flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M224,48H160a40,40,0,0,0-32,16A40,40,0,0,0,96,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H96a24,24,0,0,1,24,24,8,8,0,0,0,16,0,24,24,0,0,1,24-24h64a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48ZM96,192H32V64H96a24,24,0,0,1,24,24V200A39.81,39.81,0,0,0,96,192Zm128,0H160a39.81,39.81,0,0,0-24,8V88a24,24,0,0,1,24-24h64Z"/></svg>
                    <span>Jurnal</span>
                </div>
                @if($needsJournalToday)
                    <span class="flex h-2 w-2 relative" title="Jurnal hari ini belum lengkap">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                @endif
            </a>
            <a href="{{ route('siswa.leaderboard') }}"
               class="sidebar-link {{ request()->routeIs('siswa.leaderboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M232,64H208V48a8,8,0,0,0-8-8H56a8,8,0,0,0-8,8V64H24A16,16,0,0,0,8,80v32a56.06,56.06,0,0,0,56,56h1.77A64.1,64.1,0,0,0,120,211.82V232H88a8,8,0,0,0,0,16h80a8,8,0,0,0,0-16H136V211.82A64.1,64.1,0,0,0,190.23,168H192a56.06,56.06,0,0,0,56-56V80A16,16,0,0,0,232,64ZM64,152a40,40,0,0,1-40-40V80H48v56a63.84,63.84,0,0,0,1,11.11A40.34,40.34,0,0,1,64,152Zm128-40a48,48,0,0,1-96,0V56H192Zm40,0a40,40,0,0,1-25,37.11,63.84,63.84,0,0,0,1-11.11V80h24Z"/></svg>
                Peringkat
            </a>
            <a href="{{ route('siswa.rapor') }}"
               class="sidebar-link {{ request()->routeIs('siswa.rapor') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM160,51.31,188.69,80H160ZM200,216H56V40h88V88a8,8,0,0,0,8,8h48V216Zm-40-64a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16h48A8,8,0,0,1,160,152Zm0,32a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16h48A8,8,0,0,1,160,184Z"/></svg>
                Rapor Saya
            </a>
            <a href="{{ route('siswa.profile') }}"
               class="sidebar-link {{ request()->routeIs('siswa.profile') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M230.92,212c-15.23-26.33-38.7-45.21-66.09-54.16a72,72,0,1,0-73.66,0C63.78,166.78,40.31,185.66,25.08,212a8,8,0,1,0,13.85,8C55.71,190.36,80,176,128,176s72.29,14.36,89.07,44a8,8,0,1,0,13.85-8ZM72,96a56,56,0,1,1,56,56A56.06,56.06,0,0,1,72,96Z"/></svg>
                Profil
            </a>
        </nav>

        {{-- Profile at bottom --}}
        <div class="border-t border-ink-100 pt-4 mt-4">
            <div class="flex items-center gap-3 px-2">
                <div class="avatar overflow-hidden">
                    @if(Auth::user()->avatar_url)
                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                    @else
                        {{ substr(Auth::user()->name, 0, 1) }}
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-ink-500">{{ Auth::user()->nis }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3 px-2">
                @csrf
                <button type="submit" class="text-sm text-ink-500 font-semibold hover:text-coral transition-colors">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <main class="flex-1 lg:ml-60 pb-24 lg:pb-8">
        @yield('content')
    </main>

    {{-- Bottom nav (mobile) --}}
    <nav class="bottom-nav lg:hidden" aria-label="Navigasi utama">
        <div class="flex items-center justify-around px-2 pt-1">
            <a href="{{ route('siswa.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                @if(request()->routeIs('siswa.dashboard'))
                    <span class="bottom-nav-pill flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M219.31,108.68l-80-80a16,16,0,0,0-22.62,0l-80,80A15.87,15.87,0,0,0,32,120v96a8,8,0,0,0,8,8H216a8,8,0,0,0,8-8V120A15.87,15.87,0,0,0,219.31,108.68ZM208,208H48V120l80-80,80,80Z"/></svg>
                        Beranda
                    </span>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M219.31,108.68l-80-80a16,16,0,0,0-22.62,0l-80,80A15.87,15.87,0,0,0,32,120v96a8,8,0,0,0,8,8H216a8,8,0,0,0,8-8V120A15.87,15.87,0,0,0,219.31,108.68ZM208,208H48V120l80-80,80,80Z"/></svg>
                    <span>Beranda</span>
                @endif
            </a>
            <a href="{{ route('siswa.journal') }}" class="bottom-nav-item {{ request()->routeIs('siswa.journal*') ? 'active' : '' }}">
                @if(request()->routeIs('siswa.journal*'))
                    <span class="bottom-nav-pill flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M224,48H160a40,40,0,0,0-32,16A40,40,0,0,0,96,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H96a24,24,0,0,1,24,24,8,8,0,0,0,16,0,24,24,0,0,1,24-24h64a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48ZM96,192H32V64H96a24,24,0,0,1,24,24V200A39.81,39.81,0,0,0,96,192Zm128,0H160a39.81,39.81,0,0,0-24,8V88a24,24,0,0,1,24-24h64Z"/></svg>
                        Jurnal
                    </span>
                @else
                    <div class="relative inline-flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M224,48H160a40,40,0,0,0-32,16A40,40,0,0,0,96,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H96a24,24,0,0,1,24,24,8,8,0,0,0,16,0,24,24,0,0,1,24-24h64a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48ZM96,192H32V64H96a24,24,0,0,1,24,24V200A39.81,39.81,0,0,0,96,192Zm128,0H160a39.81,39.81,0,0,0-24,8V88a24,24,0,0,1,24-24h64Z"/></svg>
                        @if($needsJournalToday)
                            <span class="absolute -top-1 -right-1 size-2 rounded-full bg-amber-500 animate-pulse"></span>
                        @endif
                    </div>
                    <span>Jurnal</span>
                @endif
            </a>
            <a href="{{ route('siswa.leaderboard') }}" class="bottom-nav-item {{ request()->routeIs('siswa.leaderboard') ? 'active' : '' }}">
                @if(request()->routeIs('siswa.leaderboard'))
                    <span class="bottom-nav-pill flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M232,64H208V48a8,8,0,0,0-8-8H56a8,8,0,0,0-8,8V64H24A16,16,0,0,0,8,80v32a56.06,56.06,0,0,0,56,56h1.77A64.1,64.1,0,0,0,120,211.82V232H88a8,8,0,0,0,0,16h80a8,8,0,0,0,0-16H136V211.82A64.1,64.1,0,0,0,190.23,168H192a56.06,56.06,0,0,0,56-56V80A16,16,0,0,0,232,64Z"/></svg>
                        Peringkat
                    </span>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M232,64H208V48a8,8,0,0,0-8-8H56a8,8,0,0,0-8,8V64H24A16,16,0,0,0,8,80v32a56.06,56.06,0,0,0,56,56h1.77A64.1,64.1,0,0,0,120,211.82V232H88a8,8,0,0,0,0,16h80a8,8,0,0,0,0-16H136V211.82A64.1,64.1,0,0,0,190.23,168H192a56.06,56.06,0,0,0,56-56V80A16,16,0,0,0,232,64Z"/></svg>
                    <span>Peringkat</span>
                @endif
            </a>
            <a href="{{ route('siswa.rapor') }}" class="bottom-nav-item {{ request()->routeIs('siswa.rapor') ? 'active' : '' }}">
                @if(request()->routeIs('siswa.rapor'))
                    <span class="bottom-nav-pill flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM160,51.31,188.69,80H160ZM200,216H56V40h88V88a8,8,0,0,0,8,8h48V216Zm-40-64a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16h48A8,8,0,0,1,160,152Zm0,32a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16h48A8,8,0,0,1,160,184Z"/></svg>
                        Rapor
                    </span>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM160,51.31,188.69,80H160ZM200,216H56V40h88V88a8,8,0,0,0,8,8h48V216Zm-40-64a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16h48A8,8,0,0,1,160,152Zm0,32a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16h48A8,8,0,0,1,160,184Z"/></svg>
                    <span>Rapor</span>
                @endif
            </a>
            <a href="{{ route('siswa.profile') }}" class="bottom-nav-item {{ request()->routeIs('siswa.profile') ? 'active' : '' }}">
                @if(request()->routeIs('siswa.profile'))
                    <span class="bottom-nav-pill flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M230.92,212c-15.23-26.33-38.7-45.21-66.09-54.16a72,72,0,1,0-73.66,0C63.78,166.78,40.31,185.66,25.08,212a8,8,0,1,0,13.85,8C55.71,190.36,80,176,128,176s72.29,14.36,89.07,44a8,8,0,1,0,13.85-8Z"/></svg>
                        Profil
                    </span>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M230.92,212c-15.23-26.33-38.7-45.21-66.09-54.16a72,72,0,1,0-73.66,0C63.78,166.78,40.31,185.66,25.08,212a8,8,0,1,0,13.85,8C55.71,190.36,80,176,128,176s72.29,14.36,89.07,44a8,8,0,1,0,13.85-8Z"/></svg>
                    <span>Profil</span>
                @endif
            </a>
        </div>
    </nav>
</div>
@endsection
