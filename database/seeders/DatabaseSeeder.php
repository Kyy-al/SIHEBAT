<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Badge;
use App\Models\Classroom;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Mission;
use App\Models\School;
use App\Models\Setting;
use App\Models\User;
use App\Models\XpLog;
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
        // 0. Seed School
        $school = School::updateOrCreate(
            ['npsn' => '10900232'],
            [
                'name' => 'SMAN 1 SUNGAILIAT',
                'address' => 'JL. PEMUDA SUNGAILIAT, Kelurahan Bukit Betung, Kec. Sungailiat',
                'city' => 'Kab. Bangka, Prov. Kepulauan Bangka Belitung',
                'accreditation' => 'A',
                'is_active' => true,
            ]
        );

        // 0.0 Seed Default System XP Settings
        Setting::set('xp_perfect_day', '30', 'Bonus XP saat semua 7 kebiasaan tercapai dalam satu hari');
        Setting::set('xp_streak_bonus', '5', 'Bonus XP untuk setiap kelipatan 7 hari streak');
        Setting::set('xp_per_habit', '10', 'XP dasar per kebiasaan yang tercapai');

        // 0.1 Seed Missions
        $missionsData = [
            [
                'title' => 'Prajurit Olahraga',
                'description' => 'Lakukan olahraga minimal 30 menit sebanyak 4 hari minggu ini.',
                'habit_slug' => 'berolahraga',
                'target_count' => 4,
                'xp_reward' => 50,
                'icon' => 'barbell',
                'is_active' => true,
            ],
            [
                'title' => 'Bangun Pagi Juara',
                'description' => 'Disiplin bangun pagi sebelum jam 05.00 sebanyak 5 hari minggu ini.',
                'habit_slug' => 'bangun-pagi',
                'target_count' => 5,
                'xp_reward' => 50,
                'icon' => 'sun',
                'is_active' => true,
            ],
            [
                'title' => 'Kutu Buku Hebat',
                'description' => 'Luangkan waktu belajar atau membaca mandiri minimal 4 hari minggu ini.',
                'habit_slug' => 'gemar-belajar',
                'target_count' => 4,
                'xp_reward' => 40,
                'icon' => 'book-open',
                'is_active' => true,
            ],
            [
                'title' => 'Ksatria Jurnal 5 Hari',
                'description' => 'Isi jurnal kebiasaan harian secara konsisten minimal 5 hari minggu ini.',
                'habit_slug' => null,
                'target_count' => 5,
                'xp_reward' => 60,
                'icon' => 'trophy',
                'is_active' => true,
            ],
        ];

        foreach ($missionsData as $m) {
            Mission::updateOrCreate(['title' => $m['title']], $m);
        }

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
                'name' => 'Ksatria Api 14',
                'slug' => 'streak-14',
                'description' => 'Mempertahankan streak jurnal selama 14 hari berturut-turut.',
                'icon' => 'flame',
                'color' => 'coral',
                'requirement_type' => 'streak',
                'requirement_value' => 14,
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
        $defaultPassword = Hash::make('password');
        $admin = User::updateOrCreate(
            ['email' => 'admin@sihebat.id'],
            [
                'name' => 'Admin SiHebat',
                'school_id' => $school->id,
                'role' => 'admin',
                'password' => $defaultPassword,
                'must_change_password' => false,
            ]
        );

        // 5. Seed Homeroom Teachers (Wali Kelas)
        $teachersData = [
            [
                'nip' => '198501152010012001',
                'name' => 'Bu Ani Suryani, S.Pd.',
                'email' => 'bu.ani@sihebat.id',
                'gol_ruang' => 'IV/a',
                'jabatan' => 'Guru Pembina',
            ],
            [
                'nip' => '198203202008011003',
                'name' => 'Pak Budi Santoso, M.Pd.',
                'email' => 'pak.budi@sihebat.id',
                'gol_ruang' => 'IV/b',
                'jabatan' => 'Guru Madya',
            ],
        ];

        $teachers = [];
        foreach ($teachersData as $tData) {
            $teachers[] = User::updateOrCreate(
                ['nip' => $tData['nip']],
                [
                    'name' => $tData['name'],
                    'school_id' => $school->id,
                    'email' => $tData['email'],
                    'role' => 'guru',
                    'password' => $defaultPassword,
                    'must_change_password' => false,
                    'gol_ruang' => $tData['gol_ruang'],
                    'jabatan' => $tData['jabatan'],
                ]
            );
        }

        // 6. Seed Classrooms (X-1 and X-2)
        $classroomsData = [
            ['name' => 'X-1', 'grade' => 'X', 'teacher_idx' => 0],
            ['name' => 'X-2', 'grade' => 'X', 'teacher_idx' => 1],
        ];

        $classes = [];
        foreach ($classroomsData as $c) {
            $classes[] = Classroom::updateOrCreate(
                ['name' => $c['name'], 'academic_year_id' => $academicYear->id],
                [
                    'school_id' => $school->id,
                    'grade' => $c['grade'],
                    'homeroom_teacher_id' => $teachers[$c['teacher_idx']]->id,
                ]
            );
        }

        // 7. Seed Core Demo Students
        $studentsList = [
            [
                'name' => 'Ahmad Reza Maulana',
                'gender' => 'L',
                'nis' => '12009',
                'nisn' => '0118102423',
                'email' => 'reza@siswa.sihebat.id',
                'consistency' => 0.95,
                'class_idx' => 0, // X-1
                'must_change_password' => false,
            ],
            [
                'name' => 'Rina Wulandari',
                'gender' => 'P',
                'nis' => '20261001',
                'nisn' => '0118102420',
                'email' => 'rina@siswa.sihebat.id',
                'consistency' => 0.90,
                'class_idx' => 0, // X-1
                'must_change_password' => false,
            ],
            [
                'name' => 'Ahmad Pratama',
                'gender' => 'L',
                'nis' => '20261002',
                'nisn' => '0118102421',
                'email' => 'ahmad@siswa.sihebat.id',
                'consistency' => 0.85,
                'class_idx' => 0, // X-1
                'must_change_password' => false,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'gender' => 'P',
                'nis' => '20261003',
                'nisn' => '0118102422',
                'email' => 'siti@siswa.sihebat.id',
                'consistency' => 0.75,
                'class_idx' => 0, // X-1
                'must_change_password' => false,
            ],
            [
                'name' => 'Budi Wicaksono',
                'gender' => 'L',
                'nis' => '20261004',
                'nisn' => '0118102427',
                'email' => 'budi.w@siswa.sihebat.id',
                'consistency' => 0.50,
                'class_idx' => 1, // X-2
                'must_change_password' => false,
            ],
            [
                'name' => 'Dewi Lestari',
                'gender' => 'P',
                'nis' => '20261005',
                'nisn' => '0118102428',
                'email' => 'dewi@siswa.sihebat.id',
                'consistency' => 0.35,
                'class_idx' => 1, // X-2
                'must_change_password' => true, // Demo for first-login password change!
            ],
        ];

        $isTesting = app()->runningUnitTests() || app()->environment('testing');

        $allStudents = [];
        $consistencyMap = [];

        foreach ($studentsList as $st) {
            $pwd = $st['must_change_password'] ? Hash::make($st['nis']) : $defaultPassword;

            $user = User::updateOrCreate(
                ['nis' => $st['nis']],
                [
                    'name' => $st['name'],
                    'gender' => $st['gender'],
                    'nisn' => $st['nisn'],
                    'school_id' => $school->id,
                    'email' => $st['email'],
                    'role' => 'siswa',
                    'password' => $pwd,
                    'must_change_password' => $st['must_change_password'],
                    'streak_freezes' => 1,
                ]
            );

            $cls = $classes[$st['class_idx']];
            $user->classrooms()->syncWithoutDetaching([$cls->id]);

            $consistencyMap[$user->id] = $st['consistency'];
            $allStudents[] = $user;
        }

        // 8. Seed Journals for the Past 30 Days (5 days during automated test runs)
        $historyDays = $isTesting ? 5 : 30;
        $startDate = Carbon::today()->subDays($historyDays);
        $endDate = Carbon::today();

        $habitModels = array_values($habits);
        $worshipOptions = ['Shalat 5 Waktu & Dhuha', 'Kebaktian / Doa Pagi', 'Puja Bakti & Meditasi', 'Doa Bersama'];
        $sportsOptions = ['Lari Pagi', 'Badminton', 'Futsal', 'Senam Kebugaran', 'Bersepeda', 'Basket'];
        $studyTopics = ['Matematika - Aljabar', 'Biologi - Sel & Jaringan', 'Bahasa Inggris - Reading', 'Fisika - Gerak Lurus', 'Sejarah Indonesia', 'Kimia - Ikatan Kimia'];
        $socialOptions = ['Gotong royong membersihkan kelas', 'Membantu orang tua di rumah', 'Kegiatan OSIS / Pramuka', 'Bakti sosial warga'];

        foreach ($allStudents as $student) {
            $totalXp = 0;
            $currentStreak = 0;
            $longestStreak = 0;
            $lastJournalDate = null;
            $streakChain = 0;

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $isToday = $date->isToday();
                // Random based on student's consistency profile
                $hashVal = crc32($student->nis.$date->toDateString());
                $prob = ($hashVal % 100) / 100.0;
                $studentConsistency = $consistencyMap[$student->id] ?? 0.75;

                // Don't auto-fill today for demo/test students so they can experience filling today's journal
                if ($isToday && in_array($student->nis, ['20261001', '20261002', '20261003', '12009'])) {
                    continue;
                }

                if ($prob <= $studentConsistency) {
                    // Student filled journal this day
                    $habitsCompletedCount = 0;
                    $entriesData = [];

                    // 1. Bangun Pagi
                    $wakeEarly = (($hashVal % 10) < 8);
                    $wakeTime = $wakeEarly ? sprintf('04:%02d', 15 + ($hashVal % 30)) : sprintf('05:%02d', 10 + ($hashVal % 25));
                    $wakeCompleted = ($wakeTime <= '05:00');
                    if ($wakeCompleted) {
                        $habitsCompletedCount++;
                    }
                    $entriesData[] = [
                        'habit_id' => $habits['bangun-pagi']->id,
                        'completed' => $wakeCompleted,
                        'value' => json_encode(['time' => $wakeTime]),
                        'xp_earned' => $wakeCompleted ? 10 : 0,
                    ];

                    // 2. Beribadah
                    $worshipDone = true;
                    $habitsCompletedCount++;
                    $entriesData[] = [
                        'habit_id' => $habits['beribadah']->id,
                        'completed' => true,
                        'value' => json_encode(['done' => true, 'activity' => $worshipOptions[$hashVal % count($worshipOptions)]]),
                        'xp_earned' => 10,
                    ];

                    // 3. Berolahraga
                    $sportDuration = (($hashVal % 5) === 0) ? 20 : 35 + ($hashVal % 25);
                    $sportCompleted = ($sportDuration >= 30);
                    if ($sportCompleted) {
                        $habitsCompletedCount++;
                    }
                    $entriesData[] = [
                        'habit_id' => $habits['berolahraga']->id,
                        'completed' => $sportCompleted,
                        'value' => json_encode(['duration' => $sportDuration, 'sport' => $sportsOptions[$hashVal % count($sportsOptions)]]),
                        'xp_earned' => $sportCompleted ? 10 : 0,
                    ];

                    // 4. Makan Sehat
                    $meals = [
                        'sarapan' => true,
                        'siang' => true,
                        'malam' => true,
                        'buah' => (($hashVal % 4) !== 0),
                        'air' => true,
                    ];
                    $checkedCount = count(array_filter($meals));
                    $eatCompleted = ($checkedCount >= 3);
                    if ($eatCompleted) {
                        $habitsCompletedCount++;
                    }
                    $entriesData[] = [
                        'habit_id' => $habits['makan-sehat']->id,
                        'completed' => $eatCompleted,
                        'value' => json_encode($meals),
                        'xp_earned' => $eatCompleted ? 10 : 0,
                    ];

                    // 5. Gemar Belajar
                    $studyDuration = (($hashVal % 7) === 0) ? 20 : 30 + ($hashVal % 40);
                    $studyCompleted = ($studyDuration >= 30);
                    if ($studyCompleted) {
                        $habitsCompletedCount++;
                    }
                    $entriesData[] = [
                        'habit_id' => $habits['gemar-belajar']->id,
                        'completed' => $studyCompleted,
                        'value' => json_encode(['duration' => $studyDuration, 'topic' => $studyTopics[$hashVal % count($studyTopics)]]),
                        'xp_earned' => $studyCompleted ? 10 : 0,
                    ];

                    // 6. Bermasyarakat
                    $socialCompleted = true;
                    $habitsCompletedCount++;
                    $entriesData[] = [
                        'habit_id' => $habits['bermasyarakat']->id,
                        'completed' => true,
                        'value' => json_encode(['done' => true, 'activity' => $socialOptions[$hashVal % count($socialOptions)]]),
                        'xp_earned' => 10,
                    ];

                    // 7. Tidur Cepat
                    $sleepEarly = (($hashVal % 6) < 4);
                    $sleepTime = $sleepEarly ? sprintf('20:%02d', 40 + ($hashVal % 15)) : sprintf('21:%02d', 10 + ($hashVal % 30));
                    $sleepCompleted = ($sleepTime <= '21:00');
                    if ($sleepCompleted) {
                        $habitsCompletedCount++;
                    }
                    $entriesData[] = [
                        'habit_id' => $habits['tidur-cepat']->id,
                        'completed' => $sleepCompleted,
                        'value' => json_encode(['time' => $sleepTime]),
                        'xp_earned' => $sleepCompleted ? 10 : 0,
                    ];

                    $isPerfect = ($habitsCompletedCount === 7);
                    $dayXp = ($habitsCompletedCount * 10) + ($isPerfect ? 30 : 0);

                    // Streak chain calculation
                    $streakChain++;
                    if ($streakChain % 7 === 0) {
                        $dayXp += 5; // Streak bonus
                    }
                    if ($streakChain > $longestStreak) {
                        $longestStreak = $streakChain;
                    }
                    $currentStreak = $streakChain;
                    $lastJournalDate = $date->copy();
                    $totalXp += $dayXp;

                    $moodList = ['sangat_senang', 'senang', 'biasa'];
                    $mood = $moodList[$hashVal % 3];

                    $journal = Journal::updateOrCreate(
                        ['user_id' => $student->id, 'date' => $date->toDateString()],
                        [
                            'habits_completed' => $habitsCompletedCount,
                            'is_perfect_day' => $isPerfect,
                            'xp_earned' => $dayXp,
                            'mood' => $mood,
                            'notes' => $isPerfect ? 'Hari yang luar biasa, semua target kebiasaan tercapai!' : 'Tetap semangat berproses menjadi anak Indonesia hebat.',
                        ]
                    );

                    XpLog::updateOrCreate(
                        ['user_id' => $student->id, 'date' => $date->toDateString(), 'source' => $isPerfect ? 'perfect_day' : 'habit'],
                        [
                            'amount' => $dayXp,
                            'description' => "Capaian {$habitsCompletedCount} dari 7 kebiasaan".($isPerfect ? ' (Perfect Day)' : ''),
                        ]
                    );

                    foreach ($entriesData as $entry) {
                        JournalEntry::updateOrCreate(
                            ['journal_id' => $journal->id, 'habit_id' => $entry['habit_id']],
                            [
                                'completed' => $entry['completed'],
                                'value' => $entry['value'],
                                'xp_earned' => $entry['xp_earned'],
                            ]
                        );
                    }
                } else {
                    // Day missed: reset streak chain unless saved by streak freeze
                    $streakChain = 0;
                }
            }

            // Calculate level
            $level = 1;
            if ($totalXp >= 1500) {
                $level = 6;
            } elseif ($totalXp >= 1000) {
                $level = 5;
            } elseif ($totalXp >= 600) {
                $level = 4;
            } elseif ($totalXp >= 300) {
                $level = 3;
            } elseif ($totalXp >= 100) {
                $level = 2;
            }

            $student->update([
                'xp' => $totalXp,
                'level' => $level,
                'current_streak' => $currentStreak,
                'longest_streak' => $longestStreak,
                'last_journal_date' => $lastJournalDate,
            ]);

            // Assign badges based on achievements
            $earnedBadgeIds = [];
            if ($longestStreak >= 7 && isset($badges['streak-7'])) {
                $earnedBadgeIds[$badges['streak-7']->id] = ['earned_at' => Carbon::now()->subDays(5)];
            }
            if ($longestStreak >= 14 && isset($badges['streak-14'])) {
                $earnedBadgeIds[$badges['streak-14']->id] = ['earned_at' => Carbon::now()->subDays(2)];
            }
            if ($totalXp >= 300 && isset($badges['xp-300'])) {
                $earnedBadgeIds[$badges['xp-300']->id] = ['earned_at' => Carbon::now()->subDays(3)];
            }
            if (($consistencyMap[$student->id] ?? 0) >= 0.8 && isset($badges['perfect-day'])) {
                $earnedBadgeIds[$badges['perfect-day']->id] = ['earned_at' => Carbon::now()->subDays(1)];
            }

            if (! empty($earnedBadgeIds)) {
                $student->badges()->syncWithoutDetaching($earnedBadgeIds);
            }
        }
    }
}
