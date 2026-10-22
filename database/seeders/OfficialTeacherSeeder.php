<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OfficialTeacherSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first() ?? School::create([
            'npsn' => '10900232',
            'name' => 'SMAN 1 SUNGAILIAT',
            'address' => 'JL. PEMUDA SUNGAILIAT, Kelurahan Bukit Betung, Kec. Sungailiat',
            'city' => 'Kab. Bangka, Prov. Kepulauan Bangka Belitung',
            'accreditation' => 'A',
        ]);

        $academicYear = AcademicYear::active() ?? AcademicYear::latest()->first() ?? AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        // Official teacher list matching Lampiran IV Keputusan Kepala SMAN 1 Sungailiat (Image 3)
        $teachersData = [
            ['nama' => 'M. Ramadhani Zuhri, S.Pd.', 'nip' => '199506022022211008', 'gol' => 'IX', 'jabatan' => 'P3K', 'kelas' => 'X-1', 'tingkat' => 'X'],
            ['nama' => 'Lesmiarti, S.Sos', 'nip' => '197708052005012012', 'gol' => 'IV/a', 'jabatan' => 'Guru Madya', 'kelas' => 'X-2', 'tingkat' => 'X'],
            ['nama' => 'Komariah, S.Pd.', 'nip' => '197205192003122004', 'gol' => 'IV/a', 'jabatan' => 'Guru Madya', 'kelas' => 'X-3', 'tingkat' => 'X'],
            ['nama' => 'Rini Natalia Gultom, S.Pd.', 'nip' => '198312242019022003', 'gol' => 'III/b', 'jabatan' => 'Guru Pertama', 'kelas' => 'X-4', 'tingkat' => 'X'],
            ['nama' => 'Juita Mahardika, S.Pd.', 'nip' => '199607202023212025', 'gol' => 'IX', 'jabatan' => 'P3K', 'kelas' => 'X-5', 'tingkat' => 'X'],
            ['nama' => 'Winda Fetra, S.Pd', 'nip' => '199203172019022009', 'gol' => 'III/b', 'jabatan' => 'Guru Pertama', 'kelas' => 'X-6', 'tingkat' => 'X'],
            ['nama' => 'Rezki Anggita Raksi, S.Si', 'nip' => '198605262011012006', 'gol' => 'III/d', 'jabatan' => 'Guru Muda', 'kelas' => 'X-7', 'tingkat' => 'X'],
            ['nama' => 'Suci Wulandari, S.Pd', 'nip' => '198805052024212045', 'gol' => 'IX', 'jabatan' => 'P3K', 'kelas' => 'X-8', 'tingkat' => 'X'],
            ['nama' => 'Siti Khajir, S.Pd.', 'nip' => '198210122006042013', 'gol' => 'IV/a', 'jabatan' => 'Guru Madya', 'kelas' => 'X-9', 'tingkat' => 'X'],
            ['nama' => 'Ister Pirawati, S.Pd.', 'nip' => '19911210202422035', 'gol' => 'IX', 'jabatan' => 'P3K', 'kelas' => 'XI-1', 'tingkat' => 'XI'],
            ['nama' => 'Asmadi, S.Pd', 'nip' => '197101011998021002', 'gol' => 'IV/b', 'jabatan' => 'Guru Madya', 'kelas' => 'XI-2', 'tingkat' => 'XI'],
            ['nama' => 'Sopiah, S.Pd.', 'nip' => '197605292006042015', 'gol' => 'IV/a', 'jabatan' => 'Guru Madya', 'kelas' => 'XI-3', 'tingkat' => 'XI'],
            ['nama' => 'Diah Veranita, S.Pd.', 'nip' => '199005062023212015', 'gol' => 'IX', 'jabatan' => 'P3K', 'kelas' => 'XI-4', 'tingkat' => 'XI'],
            ['nama' => 'Komar Azani, S.Pd.I.', 'nip' => '198908162019021004', 'gol' => 'III/b', 'jabatan' => 'Guru Pertama', 'kelas' => 'XI-5', 'tingkat' => 'XI'],
            ['nama' => 'Dwi Angelita, M.Pd.', 'nip' => 'PTT-10900232-15', 'gol' => '-', 'jabatan' => 'PTT', 'kelas' => 'XI-6', 'tingkat' => 'XI'],
            ['nama' => 'Hendiansyah, S.Pd.', 'nip' => '1989080820242111006', 'gol' => 'IX', 'jabatan' => 'P3K', 'kelas' => 'XI-7', 'tingkat' => 'XI'],
            ['nama' => 'Anna Pratiwi, S.Pd.', 'nip' => '199304082022212013', 'gol' => 'IX', 'jabatan' => 'P3K', 'kelas' => 'XI-8', 'tingkat' => 'XI'],
            ['nama' => 'Yulita, M.Pd.', 'nip' => '198007122003122006', 'gol' => 'III/d', 'jabatan' => 'Guru Muda', 'kelas' => 'XI-9', 'tingkat' => 'XI'],
            ['nama' => 'Aswin Hidayat, S.Pd', 'nip' => '198007072010011029', 'gol' => 'III/d', 'jabatan' => 'Guru Muda', 'kelas' => 'XI-10', 'tingkat' => 'XI'],
            ['nama' => 'Virsa Elsintha, S.Pd.', 'nip' => '197402042005012005', 'gol' => 'IV/a', 'jabatan' => 'Guru Madya', 'kelas' => 'XII-1', 'tingkat' => 'XII'],
            ['nama' => 'Edi Saputra, S.Pd', 'nip' => '198401182024211003', 'gol' => 'IX', 'jabatan' => 'P3K', 'kelas' => 'XII-2', 'tingkat' => 'XII'],
            ['nama' => 'Ari Frandika, S.Pd.', 'nip' => '199107012019021005', 'gol' => 'III/b', 'jabatan' => 'Guru Pertama', 'kelas' => 'XII-3', 'tingkat' => 'XII'],
            ['nama' => 'Hj. Nurlela, S.Ag', 'nip' => '196904231998022001', 'gol' => 'IV/b', 'jabatan' => 'Guru Madya', 'kelas' => 'XII-4', 'tingkat' => 'XII'],
            ['nama' => 'Mega Aulia Lestari, S.Pd.', 'nip' => '199608072025212122', 'gol' => 'IX', 'jabatan' => 'P3K', 'kelas' => 'XII-5', 'tingkat' => 'XII'],
            ['nama' => 'Puspita Halim, M.Pd', 'nip' => '197708112000122002', 'gol' => 'IV/b', 'jabatan' => 'Guru Madya', 'kelas' => 'XII-6', 'tingkat' => 'XII'],
            ['nama' => 'Robi, S.Pd.', 'nip' => '199305272024211013', 'gol' => 'IX', 'jabatan' => 'P3K', 'kelas' => 'XII-7', 'tingkat' => 'XII'],
            ['nama' => 'Siti Aisyah Zumirroh, S.Pd.', 'nip' => '198909162014032002', 'gol' => 'III/c', 'jabatan' => 'Guru Muda', 'kelas' => 'XII-8', 'tingkat' => 'XII'],
            ['nama' => 'Siti Maryani, M.Pd.I.', 'nip' => '198002032023212005', 'gol' => 'IX', 'jabatan' => 'P3K', 'kelas' => 'XII-9', 'tingkat' => 'XII'],
            ['nama' => 'Meilisa Ika Safitri, S.Pd.', 'nip' => '198605082009032002', 'gol' => 'III/c', 'jabatan' => 'Guru Muda', 'kelas' => 'XII-10', 'tingkat' => 'XII'],
        ];

        foreach ($teachersData as $t) {
            $slugEmail = Str::slug(explode(',', $t['nama'])[0]).'@sman1sungailiat.sch.id';

            $user = User::updateOrCreate(
                ['nip' => $t['nip']],
                [
                    'name' => $t['nama'],
                    'school_id' => $school->id,
                    'email' => $slugEmail,
                    'gol_ruang' => $t['gol'],
                    'jabatan' => $t['jabatan'],
                    'role' => 'guru',
                    'password' => Hash::make('password'),
                    'must_change_password' => false,
                ]
            );

            // Find or create the classroom (e.g. X-1 or X.1)
            $class = Classroom::where('name', $t['kelas'])
                ->orWhere('name', str_replace('-', '.', $t['kelas']))
                ->first();

            if (! $class) {
                $class = Classroom::create([
                    'name' => $t['kelas'],
                    'grade' => $t['tingkat'],
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'homeroom_teacher_id' => $user->id,
                ]);
            } else {
                $class->update([
                    'name' => $t['kelas'],
                    'grade' => $t['tingkat'],
                    'homeroom_teacher_id' => $user->id,
                ]);
            }
        }
    }
}
