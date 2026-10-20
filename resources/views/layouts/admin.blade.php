@extends('layouts.base')

@section('title', 'SiHebat — ' . ($title ?? 'Dashboard'))

@section('body')
<div class="flex min-h-dvh">
    {{-- Sidebar (desktop) --}}
    <aside class="hidden lg:flex lg:flex-col lg:w-60 bg-white border-r border-ink-100 p-4 fixed inset-y-0 left-0 z-40">
        {{-- Logo --}}
        <div class="mb-6 px-2">
            <div class="flex items-center gap-2">
                <div class="size-10 rounded-2xl bg-primary-400 flex items-center justify-center">
                    <span class="text-xl font-extrabold text-ink-900">S</span>
                </div>
                <div>
                    <span class="text-lg font-extrabold text-ink-900">SiHebat</span>
                    <span class="block text-[10px] text-ink-500 font-semibold -mt-0.5">Panel Admin</span>
                </div>
            </div>
        </div>

        {{-- Nav links --}}
        <nav class="flex flex-col gap-1 flex-1">
            @php
                $prefix = Auth::user()->isAdmin() ? 'admin' : 'guru';
            @endphp

            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40Zm0,160H40V56H216V200ZM176,112a8,8,0,0,1-8,8H88a8,8,0,0,1,0-16h80A8,8,0,0,1,176,112Zm0,32a8,8,0,0,1-8,8H88a8,8,0,0,1,0-16h80A8,8,0,0,1,176,144Z"/></svg>
                    Dashboard
                </a>
                <span class="text-xs font-bold text-ink-500 uppercase tracking-wider px-4 mt-4 mb-1">Master Data</span>
                <a href="{{ route('admin.academic-years') }}"
                   class="sidebar-link {{ request()->routeIs('admin.academic-years*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32Zm0,176H48V48H72v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24Z"/></svg>
                    Tahun Ajaran
                </a>
                <a href="{{ route('admin.classrooms') }}"
                   class="sidebar-link {{ request()->routeIs('admin.classrooms*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M224,48H160a40,40,0,0,0-32,16A40,40,0,0,0,96,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H96a24,24,0,0,1,24,24,8,8,0,0,0,16,0,24,24,0,0,1,24-24h64a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48Z"/></svg>
                    Kelas
                </a>
                <a href="{{ route('admin.students') }}"
                   class="sidebar-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.14,98.7a8,8,0,0,1-11.07-2.33A79.83,79.83,0,0,0,172,168a8,8,0,0,1,0-16,44,44,0,1,0-16.34-84.87,8,8,0,1,1-5.94-14.85,60,60,0,0,1,55.53,105.64,95.83,95.83,0,0,1,47.22,37.71A8,8,0,0,1,250.14,206.7Z"/></svg>
                    Siswa
                </a>
                <a href="{{ route('admin.teachers') }}"
                   class="sidebar-link {{ request()->routeIs('admin.teachers*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M226.53,56.41l-96-32a8,8,0,0,0-5.06,0l-96,32A8,8,0,0,0,24,64v80a8,8,0,0,0,16,0V75.1l33.59,11.2A64,64,0,0,0,96,192h64a64,64,0,0,0,22.41-105.7L232,68.49A8,8,0,0,0,226.53,56.41Z"/></svg>
                    Guru
                </a>
            @else
                <a href="{{ route('guru.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 256 256"><path d="M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40Zm0,160H40V56H216V200Z"/></svg>
                    Dashboard Kelas
                </a>
            @endif
        </nav>

        {{-- Profile at bottom --}}
        <div class="border-t border-ink-100 pt-4 mt-4">
            <div class="flex items-center gap-3 px-2">
                <div class="avatar">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-ink-500 capitalize">{{ Auth::user()->role }}</p>
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
    <main class="flex-1 lg:ml-60">
        {{-- Mobile header --}}
        <header class="lg:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-ink-100 sticky top-0 z-30">
            <div class="flex items-center gap-2">
                <div class="size-8 rounded-xl bg-primary-400 flex items-center justify-center">
                    <span class="text-sm font-extrabold text-ink-900">S</span>
                </div>
                <span class="font-extrabold text-ink-900">SiHebat</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-ink-500 font-semibold">Keluar</button>
            </form>
        </header>

        <div class="p-4 sm:p-6 lg:p-8">
            @yield('content')
        </div>
    </main>
</div>
@endsection
