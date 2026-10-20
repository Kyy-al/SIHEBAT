<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Badge;
use App\Models\Classroom;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed 7 Kebiasaan
        $habitsData = [
            [
                'name' => 'Bangun Pagi',
                'slug' => 'bangun-pagi',
                'icon' => 'sun',
                'color' => 'bangun',
                'xp_reward' => 10,
                'order' => 1,
            ],
            [
                'name' => 'Beribadah',
                'slug' => 'beribadah',
                'icon' => 'hands-praying',
                'color' => 'ibadah',
                'xp_reward' => 10,
                'order' => 2,
            ],
            [
                'name' => 'Berolahraga',
                'slug' => 'berolahraga',
                'icon' => 'barbell',
                'color' => 'olahraga',
                'xp_reward' => 10,
                'order' => 3,
            ],
            [
                'name' => 'Makan Sehat',
                'slug' => 'makan-sehat',
                'icon' => 'bowl-food',
                'color' => 'makan',
                'xp_reward' => 10,
                'order' => 4,
            ],
            [
                'name' => 'Gemar Belajar',
                'slug' => 'gemar-belajar',
                'icon' => 'book-open',
                'color' => 'belajar',
                'xp_reward' => 10,
                'order' => 5,
            ],
            [
                'name' => 'Bermasyarakat',
                'slug' => 'bermasyarakat',
                'icon' => 'users-three',
                'color' => 'sosial',
                'xp_reward' => 10,
                'order' => 6,
            ],
            [
                'name' => 'Tidur Cepat',
                'slug' => 'tidur-cepat',
                'icon' => 'moon-stars',
                'color' => 'tidur',
                'xp_reward' => 10,
                'order' => 7,
            ],
        ];

        $habits = [];
        foreach ($habitsData as $data) {
            $habits[$data['slug']] = Habit::updateOrCreate(['slug' => $data['slug']], $data);
        }

        // 2. Seed Badges
        $badgesData = [
            [
                'name' => 'Hari Sempurna',
                'slug' => 'perfect-day',
                'description' => 'Berhasil menyelesaikan 7 kebiasaan penuh dalam satu hari!',
                'icon' => 'sparkle',
                'color' => 'sun',
                'requirement_type' => 'perfect_day',
                'requirement_value' => 1,
            ],
            [
                'name' => 'Penjaga Api',
                'slug' => 'streak-7',
                'description' => 'Mempertahankan streak jurnal selama 7 hari berturut-turut.',
                'icon' => 'fire',
                'color' => 'coral',
                'requirement_type' => 'streak',
                'requirement_value' => 7,
            ],
            [
                'name' => 'Early Bird',
                'slug' => 'early-bird',
                'description' => 'Konsisten bangun pagi sebelum jam 05.00 selama 5 hari.',
                'icon' => 'sun-horizon',
                'color' => 'bangun',
                'requirement_type' => 'habit_streak',
                'requirement_value' => 5,
            ],
            [
                'name' => 'Ksatria XP',
                'slug' => 'xp-300',
                'description' => 'Mengumpulkan lebih dari 300 XP kebiasaan hebat.',
                'icon' => 'trophy',
                'color' => 'lavender',
                'requirement_type' => 'xp',
                'requirement_value' => 300,
            ],
            [
                'name' => 'Atlet Sehat',
                'slug' => 'athlete',
                'description' => 'Rutin berolahraga minimal 30 menit dalam 5 jurnal.',
                'icon' => 'medal',
                'color' => 'mint',
                'requirement_type' => 'habit_streak',
                'requirement_value' => 5,
            ],
        ];

        $badges = [];
        foreach ($badgesData as $b) {
            $badges[$b['slug']] = Badge::updateOrCreate(['slug' => $b['slug']], $b);
        }

        // 3. Seed Academic Year
        $academicYear = AcademicYear::updateOrCreate(
            ['name' => '2026/2027', 'semester' => 'Ganjil'],
            [
                'start_date' => Carbon::parse('2026-07-15'),
                'end_date' => Carbon::parse('2026-12-20'),
                'is_active' => true,
            ]
        );

        // 4. Seed Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@sihebat.id'],
            [
                'name' => 'Admin SiHebat',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'must_change_password' => false,
            ]
        );

        // 5. Seed Teachers
        $teacher1 = User::updateOrCreate(
            ['nip' => '198501152010012001'],
            [
                'name' => 'Bu Ani Suryani, S.Pd.',
                'email' => 'bu.ani@sihebat.id',
                'role' => 'guru',
                'password' => Hash::make('password'),
                'must_change_password' => false,
            ]
        );

        $teacher2 = User::updateOrCreate(
            ['nip' => '198203202008011003'],
            [
                'name' => 'Pak Budi Santoso, M.Pd.',
                'email' => 'pak.budi@sihebat.id',
                'role' => 'guru',
                'password' => Hash::make('password'),
                'must_change_password' => false,
            ]
        );

        // 6. Seed Classrooms
        $classX1 = Classroom::updateOrCreate(
            ['name' => 'X-1', 'academic_year_id' => $academicYear->id],
            [
                'grade' => 'X',
                'homeroom_teacher_id' => $teacher1->id,
            ]
        );

        $classX2 = Classroom::updateOrCreate(
            ['name' => 'X-2', 'academic_year_id' => $academicYear->id],
            [
                'grade' => 'X',
                'homeroom_teacher_id' => $teacher2->id,
            ]
        );

        // 7. Seed Students
        $studentsData = [
            [
                'name' => 'Rina Wulandari',
                'nis' => '20261001',
                'email' => 'rina@siswa.sihebat.id',
                'role' => 'siswa',
                'password' => Hash::make('password'),
                'must_change_password' => false,
                'xp' => 380,
                'level' => 4,
                'current_streak' => 12,
                'longest_streak' => 12,
                'last_journal_date' => Carbon::yesterday(),
                'streak_freezes' => 1,
            ],
            [
                'name' => 'Ahmad Pratama',
                'nis' => '20261002',
                'email' => 'ahmad@siswa.sihebat.id',
                'role' => 'siswa',
                'password' => Hash::make('password'),
                'must_change_password' => false,
                'xp' => 320,
                'level' => 4,
                'current_streak' => 9,
                'longest_streak' => 9,
                'last_journal_date' => Carbon::yesterday(),
                'streak_freezes' => 1,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'nis' => '20261003',
                'email' => 'siti@siswa.sihebat.id',
                'role' => 'siswa',
                'password' => Hash::make('password'),
                'must_change_password' => false,
                'xp' => 290,
                'level' => 3,
                'current_streak' => 7,
                'longest_streak' => 7,
                'last_journal_date' => Carbon::yesterday(),
                'streak_freezes' => 1,
            ],
            [
                'name' => 'Budi Wicaksono',
                'nis' => '20261004',
                'email' => 'budi.w@siswa.sihebat.id',
                'role' => 'siswa',
                'password' => Hash::make('password'),
                'must_change_password' => false,
                'xp' => 180,
                'level' => 2,
                'current_streak' => 3,
                'longest_streak' => 5,
                'last_journal_date' => Carbon::yesterday(),
                'streak_freezes' => 1,
            ],
            [
                'name' => 'Dewi Lestari',
                'nis' => '20261005',
                'email' => 'dewi@siswa.sihebat.id',
                'role' => 'siswa',
                'password' => Hash::make('20261005'), // Default NIS password
                'must_change_password' => true, // Will test first login password change flow!
                'xp' => 50,
                'level' => 1,
                'current_streak' => 0,
                'longest_streak' => 1,
                'last_journal_date' => null,
                'streak_freezes' => 1,
            ],
        ];

        $studentModels = [];
        foreach ($studentsData as $st) {
            $student = User::updateOrCreate(['nis' => $st['nis']], $st);
            $studentModels[] = $student;
            // Attach to class X-1
            $student->classrooms()->syncWithoutDetaching([$classX1->id]);
        }

        // Attach badges to Rina
        $rina = $studentModels[0];
        $rina->badges()->syncWithoutDetaching([
            $badges['perfect-day']->id => ['earned_at' => Carbon::now()->subDays(2)],
            $badges['streak-7']->id => ['earned_at' => Carbon::now()->subDays(5)],
            $badges['xp-300']->id => ['earned_at' => Carbon::now()->subDay()],
        ]);

        // 8. Seed past journals for Rina (last 5 days)
        $today = Carbon::today();
        for ($i = 5; $i >= 1; $i--) {
            $date = $today->copy()->subDays($i);
            $journal = Journal::updateOrCreate(
                ['user_id' => $rina->id, 'date' => $date],
                [
                    'habits_completed' => 7,
                    'is_perfect_day' => true,
                    'xp_earned' => 100,
                    'mood' => 'senang',
                    'notes' => 'Hari yang produktif dan menyenangkan!',
                ]
            );

            foreach ($habits as $slug => $habit) {
                JournalEntry::updateOrCreate(
                    ['journal_id' => $journal->id, 'habit_id' => $habit->id],
                    [
                        'completed' => true,
                        'value' => ['sample' => true],
                        'xp_earned' => 10,
                    ]
                );
            }
        }
    }
}
