<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\School;
use App\Models\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TeacherExcelService
{
    /**
     * Import teachers and homeroom classes from uploaded Excel (.xlsx) or CSV file.
     * Expected format matching Dapodik Rombel:
     * No | Nama Rombel | Tingkat Kelas | Jumlah Siswa (L, P, Total) | Wali Kelas
     *
     * @return array ['total' => int, 'teachers_created' => int, 'teachers_updated' => int, 'classrooms_created' => int, 'classrooms_updated' => int, 'errors' => array]
     */
    public function import(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $rows = [];

        if (in_array($extension, ['xlsx', 'xls'])) {
            $rows = $this->parseXlsx($file->getPathname());
        } elseif (in_array($extension, ['csv', 'txt'])) {
            $rows = $this->parseCsv($file->getPathname());
        } else {
            throw new Exception('Format berkas tidak didukung. Harap unggah berkas .xlsx atau .csv.');
        }

        if (empty($rows)) {
            throw new Exception('Berkas kosong atau data tabel tidak terbaca.');
        }

        return $this->processRows($rows);
    }

    /**
     * Process parsed 2D rows into Teacher (User) and Classroom models.
     */
    public function processRows(array $rows): array
    {
        $mapping = $this->detectColumnsAndDataStart($rows);
        $colMap = $mapping['colMap'];
        $startIndex = $mapping['startIndex'];

        $academicYear = AcademicYear::active() ?? AcademicYear::latest()->first();
        if (! $academicYear) {
            $academicYear = AcademicYear::create([
                'name' => date('Y').'/'.(date('Y') + 1),
                'semester' => 'Ganjil',
                'start_date' => now()->startOfYear(),
                'end_date' => now()->endOfYear(),
                'is_active' => true,
            ]);
        }

        $school = School::first();

        $teachersCreated = 0;
        $teachersUpdated = 0;
        $classroomsCreated = 0;
        $classroomsUpdated = 0;
        $totalProcessed = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            for ($i = $startIndex; $i < count($rows); $i++) {
                $row = $rows[$i];
                $rowNum = $i + 1;

                // Extract values using detected mapping
                $namaRombel = isset($colMap['rombel']) ? trim((string) ($row[$colMap['rombel']] ?? '')) : '';
                $tingkatRaw = isset($colMap['tingkat']) ? trim((string) ($row[$colMap['tingkat']] ?? '')) : '';
                $namaWali = isset($colMap['wali']) ? trim((string) ($row[$colMap['wali']] ?? '')) : '';
                $nip = isset($colMap['nip']) ? trim((string) ($row[$colMap['nip']] ?? '')) : '';
                $email = isset($colMap['email']) ? trim((string) ($row[$colMap['email']] ?? '')) : '';

                // Skip purely empty lines or header remnants
                if (empty($namaRombel) && empty($namaWali)) {
                    continue;
                }

                if (strcasecmp($namaRombel, 'Nama Rombel') === 0 || strcasecmp($namaWali, 'Wali Kelas') === 0) {
                    continue;
                }

                $totalProcessed++;

                // 1. Process Teacher / Wali Kelas
                $teacher = null;
                if (! empty($namaWali)) {
                    // Search existing teacher by NIP if present
                    if (! empty($nip)) {
                        $teacher = User::where('role', 'guru')->where('nip', $nip)->first();
                    }

                    // Search by exact name
                    if (! $teacher) {
                        $teacher = User::where('role', 'guru')
                            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($namaWali)])
                            ->first();
                    }

                    // Search by clean name (without academic titles e.g. ", S.Pd.")
                    if (! $teacher) {
                        $cleanTitle = trim(preg_replace('/\s*,\s*.*$/', '', $namaWali));
                        if (! empty($cleanTitle) && strlen($cleanTitle) >= 3) {
                            $teacher = User::where('role', 'guru')
                                ->where(function ($q) use ($namaWali, $cleanTitle) {
                                    $q->where('name', 'like', "%{$cleanTitle}%")
                                        ->orWhere('name', 'like', "%{$namaWali}%");
                                });
                            $teacher = $teacher->first();
                        }
                    }

                    if ($teacher) {
                        if (! empty($nip) && empty($teacher->nip)) {
                            $teacher->update(['nip' => $nip]);
                        }
                        $teachersUpdated++;
                    } else {
                        // Generate random initial password
                        $initialPassword = 'Guru'.strtoupper(Str::random(5));

                        // Generate unique email if none provided
                        if (empty($email)) {
                            $baseSlug = Str::slug($namaWali, '.');
                            if (empty($baseSlug)) {
                                $baseSlug = 'guru.'.Str::lower(Str::random(4));
                            }
                            $candidateEmail = $baseSlug.'@guru.sihebat.id';
                            $counter = 1;
                            while (User::where('email', $candidateEmail)->exists()) {
                                $candidateEmail = $baseSlug.$counter.'@guru.sihebat.id';
                                $counter++;
                            }
                            $email = $candidateEmail;
                        }

                        $teacher = User::create([
                            'name' => $namaWali,
                            'role' => 'guru',
                            'nip' => ! empty($nip) ? $nip : null,
                            'email' => $email,
                            'password' => Hash::make($initialPassword),
                            'initial_password' => $initialPassword,
                            'must_change_password' => true,
                            'school_id' => $school?->id,
                        ]);
                        $teachersCreated++;
                    }
                }

                // 2. Process Classroom / Rombel
                if (! empty($namaRombel)) {
                    // Normalize grade (10 -> X, 11 -> XI, 12 -> XII)
                    $grade = match (strtoupper($tingkatRaw)) {
                        '10', 'X' => 'X',
                        '11', 'XI' => 'XI',
                        '12', 'XII' => 'XII',
                        default => ! empty($tingkatRaw) ? $tingkatRaw : 'X',
                    };

                    if (empty($tingkatRaw) && preg_match('/^(XII|XI|X|12|11|10)/i', $namaRombel, $m)) {
                        $grade = match (strtoupper($m[1])) {
                            '10', 'X' => 'X',
                            '11', 'XI' => 'XI',
                            '12', 'XII' => 'XII',
                            default => 'X',
                        };
                    }

                    // Look for existing classroom (handles X.1 <-> X-1 naming conventions)
                    $altName = str_contains($namaRombel, '.')
                        ? str_replace('.', '-', $namaRombel)
                        : (str_contains($namaRombel, '-') ? str_replace('-', '.', $namaRombel) : $namaRombel);

                    $classroom = Classroom::where('academic_year_id', $academicYear->id)
                        ->where(function ($q) use ($namaRombel, $altName) {
                            $q->where('name', $namaRombel)
                                ->orWhere('name', $altName);
                        })->first();

                    if ($classroom) {
                        $updateData = ['grade' => $grade];
                        if ($teacher) {
                            $updateData['homeroom_teacher_id'] = $teacher->id;
                        }
                        $classroom->update($updateData);
                        $classroomsUpdated++;
                    } else {
                        Classroom::create([
                            'name' => $namaRombel,
                            'grade' => $grade,
                            'academic_year_id' => $academicYear->id,
                            'homeroom_teacher_id' => $teacher?->id,
                        ]);
                        $classroomsCreated++;
                    }
                }
            }

            DB::commit();

            return [
                'total' => $totalProcessed,
                'teachers_created' => $teachersCreated,
                'teachers_updated' => $teachersUpdated,
                'classrooms_created' => $classroomsCreated,
                'classrooms_updated' => $classroomsUpdated,
                'errors' => $errors,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Parse .xlsx file using PhpSpreadsheet into 2D array.
     */
    public function parseXlsx(string $filePath): array
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            return $sheet->toArray(null, true, true, false);
        } catch (Exception $e) {
            throw new Exception('Gagal membaca berkas Excel (.xlsx): '.$e->getMessage());
        }
    }

    /**
     * Parse CSV file into 2D array.
     */
    public function parseCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            throw new Exception('Gagal membuka berkas CSV.');
        }

        $sample = fgets($handle);
        rewind($handle);

        $delimiters = [',', ';', "\t"];
        $bestDelimiter = ',';
        $maxCount = 0;
        foreach ($delimiters as $delim) {
            $count = count(str_getcsv((string) $sample, $delim));
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelimiter = $delim;
            }
        }

        $rows = [];
        while (($data = fgetcsv($handle, 2000, $bestDelimiter)) !== false) {
            if (empty($rows) && isset($data[0])) {
                $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
            }
            if (! empty(array_filter($data))) {
                $rows[] = array_map(fn ($v) => trim((string) $v), $data);
            }
        }
        fclose($handle);

        return $rows;
    }

    /**
     * Detect column indices and the data row start index.
     * Supports Dapodik format (2-row header) and standard flat formats.
     */
    public function detectColumnsAndDataStart(array $rows): array
    {
        $colMap = [
            'rombel' => null,
            'tingkat' => null,
            'wali' => null,
            'l' => null,
            'p' => null,
            'total' => null,
            'nip' => null,
            'email' => null,
        ];

        $headerRowsCount = 1;
        $maxCheckRows = min(4, count($rows));

        for ($r = 0; $r < $maxCheckRows; $r++) {
            $row = $rows[$r] ?? [];
            foreach ($row as $colIdx => $val) {
                $clean = strtolower(trim((string) $val));
                if (empty($clean)) {
                    continue;
                }

                if (str_contains($clean, 'rombel') || str_contains($clean, 'nama kelas')) {
                    $colMap['rombel'] = $colIdx;
                } elseif (str_contains($clean, 'tingkat') || str_contains($clean, 'tingkat kelas')) {
                    $colMap['tingkat'] = $colIdx;
                } elseif (str_contains($clean, 'wali') || str_contains($clean, 'wali kelas') || str_contains($clean, 'nama guru')) {
                    $colMap['wali'] = $colIdx;
                } elseif ($clean === 'l' || str_contains($clean, 'laki')) {
                    $colMap['l'] = $colIdx;
                    $headerRowsCount = max($headerRowsCount, $r + 1);
                } elseif ($clean === 'p' || str_contains($clean, 'perempuan')) {
                    $colMap['p'] = $colIdx;
                    $headerRowsCount = max($headerRowsCount, $r + 1);
                } elseif (str_contains($clean, 'total') || str_contains($clean, 'jumlah siswa')) {
                    $colMap['total'] = $colIdx;
                } elseif (str_contains($clean, 'nip')) {
                    $colMap['nip'] = $colIdx;
                } elseif (str_contains($clean, 'email')) {
                    $colMap['email'] = $colIdx;
                }
            }
        }

        // Defaults based on image layout if some wasn't detected by text
        // Image layout: A:No(0), B:Nama Rombel(1), C:Tingkat Kelas(2), D:L(3), E:P(4), F:Total(5), G:Wali Kelas(6)
        if ($colMap['rombel'] === null) {
            $colMap['rombel'] = 1;
        }
        if ($colMap['tingkat'] === null) {
            $colMap['tingkat'] = 2;
        }
        if ($colMap['wali'] === null) {
            // Find rightmost column with text
            $colMap['wali'] = count($rows[0] ?? []) >= 7 ? 6 : 3;
        }

        // Determine where actual data starts (e.g. Row 2 in 0-indexed if 2-row header)
        $dataStartIndex = 1;
        for ($r = 0; $r < min(5, count($rows)); $r++) {
            $row = $rows[$r] ?? [];
            $cell0 = trim((string) ($row[0] ?? ''));
            $cellWali = trim((string) ($row[$colMap['wali']] ?? ''));
            $cellRombel = trim((string) ($row[$colMap['rombel']] ?? ''));

            // If first column has a numeric "No" (e.g. 1, 2) or rombel is X.1, X-1 etc.
            if ((is_numeric($cell0) && (int) $cell0 > 0) || (preg_match('/^(X|XI|XII|[0-9]+)/i', $cellRombel) && ! str_contains(strtolower($cellRombel), 'rombel'))) {
                $dataStartIndex = $r;
                break;
            }
        }

        return [
            'colMap' => $colMap,
            'startIndex' => $dataStartIndex,
        ];
    }

    /**
     * Generate an Excel (.xlsx) template strictly matching the Dapodik format from user's image.
     */
    public function generateTemplateXlsx(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Rombel & Wali Kelas');

        // Row 1 & 2: Headers matching Dapodik exactly
        $sheet->setCellValue('A1', 'No');
        $sheet->mergeCells('A1:A2');

        $sheet->setCellValue('B1', 'Nama Rombel');
        $sheet->mergeCells('B1:B2');

        $sheet->setCellValue('C1', 'Tingkat Kelas');
        $sheet->mergeCells('C1:C2');

        $sheet->setCellValue('D1', 'Jumlah Siswa');
        $sheet->mergeCells('D1:F1');

        $sheet->setCellValue('D2', 'L');
        $sheet->setCellValue('E2', 'P');
        $sheet->setCellValue('F2', 'Total');

        $sheet->setCellValue('G1', 'Wali Kelas');
        $sheet->mergeCells('G1:G2');

        // Sample Rows matching Image
        $sheet->fromArray([1, 'X.1', 10, 15, 21, 36, 'MUHAMMAD RAMADHANI ZUHRI'], null, 'A3');
        $sheet->fromArray([2, 'X.2', 10, 14, 22, 36, 'Lesmiarti'], null, 'A4');
        $sheet->fromArray([3, 'XI.1', 11, 16, 20, 36, 'HENDRA WIJAYA, S.Kom.'], null, 'A5');
        $sheet->fromArray([4, 'XII.1', 12, 15, 19, 34, 'MAYA KUSUMA, M.Pd.'], null, 'A6');

        // Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A'], 'size' => 11],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0F2FE'], // Primary sky blue accent
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '94A3B8'],
                ],
            ],
        ];

        $sheet->getStyle('A1:G2')->applyFromArray($headerStyle);

        // Data row borders and alignments
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A3:G6')->applyFromArray($dataStyle);

        $sheet->getStyle('A3:A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C3:F6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Generate CSV template fallback.
     */
    public function generateTemplateCsv(): string
    {
        $headers = ['No', 'Nama Rombel', 'Tingkat Kelas', 'L', 'P', 'Total', 'Wali Kelas'];
        $rows = [
            $headers,
            [1, 'X.1', 10, 15, 21, 36, 'MUHAMMAD RAMADHANI ZUHRI'],
            [2, 'X.2', 10, 14, 22, 36, 'Lesmiarti'],
            [3, 'XI.1', 11, 16, 20, 36, 'HENDRA WIJAYA, S.Kom.'],
            [4, 'XII.1', 12, 15, 19, 34, 'MAYA KUSUMA, M.Pd.'],
        ];

        $handle = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    /**
     * Export teachers with their initial passwords and homeroom assignments.
     */
    public function exportCredentials(): Spreadsheet
    {
        $teachers = User::where('role', 'guru')
            ->with('homeroomClassrooms')
            ->orderBy('name')
            ->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Akun Guru');

        $headers = ['No', 'Nama Guru & Gelar', 'NIP', 'Email Akun', 'Wali Kelas', 'Kata Sandi Awal', 'Status'];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        foreach ($teachers as $idx => $t) {
            $wali = $t->homeroomClassrooms->pluck('name')->join(', ') ?: '-';
            $pass = $t->initial_password ?: ($t->must_change_password ? 'Sesuai NIP' : '[Sudah Diganti Guru]');
            $status = $t->must_change_password ? 'Wajib Ganti Sandi' : 'Aktif (Mandiri)';

            $sheet->fromArray([
                $idx + 1,
                $t->name,
                $t->nip ?: '-',
                $t->email ?: '-',
                $wali,
                $pass,
                $status,
            ], null, 'A'.$row);
            $row++;
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0F2FE'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '94A3B8'],
                ],
            ],
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
