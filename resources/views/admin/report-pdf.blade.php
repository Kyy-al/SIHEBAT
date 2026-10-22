<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rapor Pendidikan Karakter SiHebat</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0F2A43;
            font-size: 11px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0284C7;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            color: #0F2A43;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header h2 {
            font-size: 13px;
            margin: 4px 0;
            color: #0284C7;
        }
        .header p {
            font-size: 9px;
            margin: 0;
            color: #6B829A;
        }
        .profile-table, .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .profile-table td {
            padding: 3px 6px;
            font-size: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #C5D3E0;
            padding: 6px 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #F0F9FF;
            color: #0F2A43;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0284C7;
            margin: 14px 0 6px 0;
            border-bottom: 1px solid #EAF1F8;
            padding-bottom: 4px;
        }
        .badge-baik {
            background-color: #D8F8EB;
            color: #0B6B47;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
        }
        .badge-cukup {
            background-color: #FFF4CC;
            color: #8A5A00;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
        }
        .badge-perhatian {
            background-color: #FFE6DF;
            color: #A8381B;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
        }
        .footer {
            margin-top: 30px;
            width: 100%;
        }
        .footer td {
            width: 50%;
            vertical-align: top;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Pemerintah Provinsi Kepulauan Bangka Belitung</h1>
        <h2>{{ $schoolProfile['name'] }}</h2>
        <p>{{ $schoolProfile['address'] }} | NPSN: {{ $schoolProfile['npsn'] }} | Akreditasi: {{ $schoolProfile['accreditation'] }}</p>
    </div>

    <table class="profile-table">
        <tr>
            <td width="20%"><strong>Tahun Ajaran:</strong></td>
            <td width="30%">{{ $academicYear ? $academicYear->full_name : '2026/2027' }}</td>
            <td width="20%"><strong>Total Siswa Terdaftar:</strong></td>
            <td width="30%">{{ $totalStudents }} Siswa</td>
        </tr>
        <tr>
            <td><strong>Rata-rata Streak:</strong></td>
            <td>{{ $avgStreak }} Hari Berturut-turut</td>
            <td><strong>Tanggal Cetak:</strong></td>
            <td>{{ date('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    <div class="section-title">1. Ringkasan Capaian 7 Kebiasaan Anak Indonesia Hebat</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Indikator Kebiasaan</th>
                <th width="20%">Target Standar</th>
                <th width="20%">Tingkat Ketercapaian</th>
                <th width="20%">Status Rapor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($habitStats as $index => $hs)
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td><strong>{{ $hs['name'] }}</strong></td>
                    <td>{{ $hs['target'] ?? '-' }}</td>
                    <td><strong>{{ $hs['rate'] }}%</strong> ({{ $hs['completed'] }} jurnal)</td>
                    <td>
                        @if($hs['status'] === 'Baik')
                            <span class="badge-baik">Baik (&ge;75%)</span>
                        @elseif($hs['status'] === 'Cukup')
                            <span class="badge-cukup">Cukup (50-74%)</span>
                        @else
                            <span class="badge-perhatian">Perlu Perhatian</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">2. Rincian Capaian per Rombongan Belajar (Kelas)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Nama Kelas</th>
                <th width="15%">Tingkat</th>
                <th width="30%">Wali Kelas</th>
                <th width="20%">Jumlah Siswa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($classrooms as $index => $cls)
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td><strong>{{ $cls->name }}</strong></td>
                    <td>Kelas {{ $cls->grade }}</td>
                    <td>{{ $cls->homeroomTeacher ? $cls->homeroomTeacher->name : 'Belum Ditugaskan' }}</td>
                    <td>{{ $cls->students_count }} Siswa</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="footer">
        <tr>
            <td></td>
            <td align="center">
                <p>Sungailiat, {{ date('d F Y') }}<br>Kepala Sekolah,</p>
                <br><br><br>
                <p><strong>Drs. H. Hendra Wijaya, M.Pd.</strong><br>NIP. 197405121998021001</p>
            </td>
        </tr>
    </table>

</body>
</html>
