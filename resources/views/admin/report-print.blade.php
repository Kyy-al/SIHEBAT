<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rapor Pendidikan Karakter — {{ $schoolProfile['name'] }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-icon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0F172A;
            background-color: #F8FAFC;
        }
        @media print {
            body {
                background-color: #FFFFFF !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .print-page {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            @page {
                size: A4 portrait;
                margin: 15mm 15mm 15mm 15mm;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    {{-- Non-printable control header --}}
    <div class="no-print max-w-4xl mx-auto mb-6 p-4 rounded-2xl bg-ink-900 text-white flex items-center justify-between shadow-lg">
        <div class="flex items-center gap-3">
            <span class="text-2xl">📄</span>
            <div>
                <h1 class="text-sm font-extrabold text-white">Pratinjau Dokumen Cetak Rapor Pendidikan</h1>
                <p class="text-xs text-ink-300">Siap dicetak ke kertas A4 atau disimpan dalam bentuk PDF</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="py-2 px-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-bold transition">
                Kembali
            </a>
            <a href="{{ route('admin.report.excel') }}" class="py-2 px-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs flex items-center gap-1.5 shadow transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Unduh Excel</span>
            </a>
            <button onclick="window.print()" class="py-2 px-4 rounded-xl bg-primary-400 text-ink-900 hover:bg-primary-300 font-extrabold text-xs flex items-center gap-2 shadow transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="currentColor" viewBox="0 0 256 256"><path d="M224,96H200a8,8,0,0,0,0,16h24v80H32V112H56a8,8,0,0,0,0-16H32a16,16,0,0,0-16,16v80a16,16,0,0,0,16,16H64v16a16,16,0,0,0,16,16H176a16,16,0,0,0,16-16V208h32a16,16,0,0,0,16-16V112A16,16,0,0,0,224,96Zm-48,128H80V176h96ZM192,48V80a8,8,0,0,1-16,0V48H80V80a8,8,0,0,1-16,0V48A16,16,0,0,1,80,32h96A16,16,0,0,1,192,48Z"/></svg>
                <span>Cetak PDF</span>
            </button>
        </div>
    </div>

    {{-- Official Printable Sheet --}}
    <div class="print-page max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-3xl border border-ink-100 shadow-xl space-y-6">

        {{-- Official School Letterhead (Kop Surat) --}}
        <div class="border-b-4 border-double border-ink-900 pb-4 text-center relative">
            <div class="flex items-center justify-between gap-4">
                {{-- Logo Kemendikbud / Sekolah --}}
                <div class="w-20 h-20 rounded-2xl bg-primary-100 flex items-center justify-center text-primary-800 font-black text-2xl shrink-0">
                    🏫
                </div>

                <div class="flex-1 space-y-0.5">
                    <p class="text-xs font-bold uppercase tracking-widest text-ink-600">Pemerintah Provinsi Kepulauan Bangka Belitung</p>
                    <p class="text-xs font-bold uppercase tracking-wider text-ink-700">Dinas Pendidikan · Cabang Dinas Wilayah II</p>
                    <h2 class="text-xl sm:text-2xl font-black uppercase text-ink-900 tracking-tight">{{ $schoolProfile['name'] }}</h2>
                    <p class="text-xs text-ink-600 font-medium">
                        {{ $schoolProfile['address'] }} · {{ $schoolProfile['city'] }}
                    </p>
                    <p class="text-[11px] text-ink-500 font-semibold">
                        NPSN: {{ $schoolProfile['npsn'] }} · Akreditasi: {{ $schoolProfile['accreditation'] }} · Laman Web Resmi: sman1sungailiat.sch.id
                    </p>
                </div>

                {{-- Logo Tut Wuri / Garuda --}}
                <div class="w-20 h-20 rounded-2xl bg-primary-50 flex items-center justify-center text-primary-700 font-black text-2xl shrink-0">
                    🇮🇩
                </div>
            </div>
        </div>

        {{-- Document Title --}}
        <div class="text-center space-y-1">
            <h3 class="text-base sm:text-lg font-black uppercase tracking-wide text-ink-900">
                Laporan Capaian 7 Kebiasaan Anak Indonesia Hebat
            </h3>
            <p class="text-xs font-bold text-ink-500">
                Tahun Ajaran: {{ $academicYear ? $academicYear->name . ' (' . $academicYear->semester . ')' : date('Y') . '/' . (date('Y')+1) }}
                · Tanggal Terbit: {{ date('d F Y') }}
            </p>
        </div>

        {{-- Executive Summary KPI Cards --}}
        <div class="grid grid-cols-4 gap-3 p-4 rounded-2xl bg-ink-50 border border-ink-100 text-center">
            <div class="border-r border-ink-200 pr-2">
                <span class="text-[10px] font-bold text-ink-500 uppercase block">Total Siswa</span>
                <span class="text-lg font-black text-ink-900 tabular-nums">{{ $totalStudents }}</span>
            </div>
            <div class="border-r border-ink-200 pr-2">
                <span class="text-[10px] font-bold text-ink-500 uppercase block">Total Rombel</span>
                <span class="text-lg font-black text-ink-900 tabular-nums">{{ $classrooms->count() }}</span>
            </div>
            <div class="border-r border-ink-200 pr-2">
                <span class="text-[10px] font-bold text-ink-500 uppercase block">Jurnal Terisi</span>
                <span class="text-lg font-black text-ink-900 tabular-nums">{{ $totalJournals }}</span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-ink-500 uppercase block">Rata-rata Streak</span>
                <span class="text-lg font-black text-ink-900 tabular-nums">{{ $avgStreak }} Hari</span>
            </div>
        </div>

        {{-- Section 1: Tabel 7 Kebiasaan Rapor Pendidikan --}}
        <div class="space-y-2">
            <h4 class="text-xs font-black uppercase tracking-wider text-ink-900">
                I. Capaian Indikator 7 Kebiasaan
            </h4>

            <table class="w-full text-left text-xs border border-ink-200">
                <thead>
                    <tr class="bg-ink-100 border-b border-ink-200 text-ink-700 font-extrabold text-[10px] uppercase">
                        <th class="py-2.5 px-3 w-8 text-center border-r border-ink-200">No</th>
                        <th class="py-2.5 px-3 border-r border-ink-200">Indikator Kebiasaan</th>
                        <th class="py-2.5 px-3 border-r border-ink-200">Target Standar</th>
                        <th class="py-2.5 px-3 text-center border-r border-ink-200 w-24">Capaian (%)</th>
                        <th class="py-2.5 px-3 text-center w-32">Status Rapor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-200">
                    @foreach($habitStats as $index => $stat)
                        <tr class="{{ $index % 2 === 1 ? 'bg-ink-50/50' : 'bg-white' }}">
                            <td class="py-2 px-3 text-center font-bold text-ink-700 border-r border-ink-200">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-2 px-3 font-extrabold text-ink-900 border-r border-ink-200">
                                {{ $stat['habit']->name }}
                            </td>
                            <td class="py-2 px-3 text-ink-600 font-medium border-r border-ink-200">
                                {{ $stat['habit']->target_description ?? $stat['habit']->description }}
                            </td>
                            <td class="py-2 px-3 text-center font-black tabular-nums border-r border-ink-200">
                                {{ $stat['rate'] }}%
                            </td>
                            <td class="py-2 px-3 text-center font-extrabold">
                                @if($stat['status'] === 'Baik')
                                    <span class="inline-block px-2.5 py-0.5 rounded text-[10px] bg-emerald-100 text-emerald-800 font-black">
                                        BAIK
                                    </span>
                                @elseif($stat['status'] === 'Cukup')
                                    <span class="inline-block px-2.5 py-0.5 rounded text-[10px] bg-amber-100 text-amber-800 font-black">
                                        CUKUP
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded text-[10px] bg-rose-100 text-rose-800 font-black">
                                        PERLU PERHATIAN
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Section 2: Rekap Antar Rombel Kelas --}}
        <div class="space-y-2">
            <h4 class="text-xs font-black uppercase tracking-wider text-ink-900">
                II. Partisipasi Per Rombongan Belajar (Rombel)
            </h4>

            <table class="w-full text-left text-xs border border-ink-200">
                <thead>
                    <tr class="bg-ink-100 border-b border-ink-200 text-ink-700 font-extrabold text-[10px] uppercase">
                        <th class="py-2 px-3 w-8 text-center border-r border-ink-200">No</th>
                        <th class="py-2 px-3 border-r border-ink-200">Nama Kelas</th>
                        <th class="py-2 px-3 border-r border-ink-200">Wali Kelas</th>
                        <th class="py-2 px-3 text-center border-r border-ink-200 w-24">Siswa</th>
                        <th class="py-2 px-3 text-center w-28">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-200">
                    @foreach($classrooms as $cIndex => $cls)
                        <tr class="{{ $cIndex % 2 === 1 ? 'bg-ink-50/50' : 'bg-white' }}">
                            <td class="py-2 px-3 text-center font-bold text-ink-700 border-r border-ink-200">
                                {{ $cIndex + 1 }}
                            </td>
                            <td class="py-2 px-3 font-bold text-ink-900 border-r border-ink-200">
                                {{ $cls->name }}
                            </td>
                            <td class="py-2 px-3 text-ink-600 font-medium border-r border-ink-200">
                                {{ $cls->homeroomTeacher ? $cls->homeroomTeacher->name : 'Belum Ditentukan' }}
                            </td>
                            <td class="py-2 px-3 text-center font-bold text-ink-800 border-r border-ink-200">
                                {{ $cls->students_count }} Siswa
                            </td>
                            <td class="py-2 px-3 text-center font-semibold text-emerald-700">
                                Aktif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Section 3: Catatan dan Tindak Lanjut --}}
        <div class="p-4 rounded-xl border border-ink-200 bg-ink-50/60 space-y-1.5 text-xs">
            <h5 class="font-extrabold text-ink-900 uppercase text-[10px] tracking-wider">
                III. Kesimpulan & Rekomendasi Tindak Lanjut Sekolah:
            </h5>
            <ol class="list-decimal list-inside space-y-1 text-ink-700 font-medium">
                <li>Pertahankan konsistensi ibadah dan gotong royong melalui keteladanan harian guru.</li>
                <li>Tingkatkan sosialisasi manajemen waktu istirahat (tidur cepat) bekerjasama dengan orang tua siswa.</li>
                <li>Gunakan sistem gamifikasi SiHebat (XP & Lencana) sebagai instrumen penguat pembiasaan positif di sekolah.</li>
            </ol>
        </div>

        {{-- Signature Block --}}
        <div class="pt-8 grid grid-cols-2 text-xs">
            <div>
                <p class="text-ink-600 font-medium">Mengetahui,</p>
                <p class="font-bold text-ink-900">Koordinator Pembina Karakter</p>
                <div class="h-20"></div>
                <p class="font-extrabold text-ink-900 underline underline-offset-4">Drs. H. Syarifuddin, M.Pd.</p>
                <p class="text-ink-500 font-semibold">NIP. 19740512 199903 1 004</p>
            </div>

            <div class="text-right">
                <p class="text-ink-600 font-medium">Sungailiat, {{ date('d F Y') }}</p>
                <p class="font-bold text-ink-900">Kepala SMAN 1 Sungailiat</p>
                <div class="h-20"></div>
                <p class="font-extrabold text-ink-900 underline underline-offset-4">H. Supartono, S.Pd., M.M.</p>
                <p class="text-ink-500 font-semibold">NIP. 19680315 199201 1 002</p>
            </div>
        </div>

    </div>

</body>
</html>
