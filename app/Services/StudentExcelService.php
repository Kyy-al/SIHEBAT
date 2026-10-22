<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleXMLElement;
use ZipArchive;

class StudentExcelService
{
    /**
     * Import students from an uploaded Excel (.xlsx) or CSV file.
     * Expected columns (as in Image 4):
     * No. | Nama Siswa | JK | NIS | NISN | Kelas
     *
     * @return array ['success' => int, 'errors' => array, 'total' => int]
     */
    public function import(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $rows = [];

        if ($extension === 'xlsx') {
            $rows = $this->parseXlsx($file->getPathname());
        } elseif (in_array($extension, ['csv', 'txt'])) {
            $rows = $this->parseCsv($file->getPathname());
        } else {
            throw new Exception('Format berkas tidak didukung. Harap unggah berkas .xlsx atau .csv.');
        }

        if (empty($rows)) {
            throw new Exception('Berkas kosong atau data tidak terbaca.');
        }

        return $this->processRows($rows);
    }

    /**
     * Process parsed rows into User models.
     */
    protected function processRows(array $rows): array
    {
        // First row is usually header: No, Nama Siswa, JK, NIS, NISN, Kelas
        $header = array_shift($rows);
        $headerMap = $this->mapHeaders($header);

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

        $imported = 0;
        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $rowNum = $index + 2;

                $name = trim($row[$headerMap['nama']] ?? '');
                $jk = strtoupper(trim($row[$headerMap['jk']] ?? ''));
                $nis = trim((string) ($row[$headerMap['nis']] ?? ''));
                $nisn = trim((string) ($row[$headerMap['nisn']] ?? ''));
                $kelas = trim($row[$headerMap['kelas']] ?? '');

                // Skip completely empty row
                if (empty($name) && empty($nis)) {
                    continue;
                }

                if (empty($name)) {
                    $errors[] = "Baris {$rowNum}: Nama siswa tidak boleh kosong.";

                    continue;
                }

                if (empty($nis)) {
                    $errors[] = "Baris {$rowNum} ({$name}): NIS tidak boleh kosong.";

                    continue;
                }

                // Normalize gender (L / P)
                $gender = null;
                if (str_starts_with($jk, 'L')) {
                    $gender = 'L';
                } elseif (str_starts_with($jk, 'P')) {
                    $gender = 'P';
                }

                // Handle classroom
                $classroomId = null;
                if (! empty($kelas)) {
                    // Normalize class name, e.g. "X.1" or "X-1"
                    $grade = 'X';
                    if (str_starts_with($kelas, 'XII')) {
                        $grade = 'XII';
                    } elseif (str_starts_with($kelas, 'XI')) {
                        $grade = 'XI';
                    } elseif (str_starts_with($kelas, 'X')) {
                        $grade = 'X';
                    }

                    $classroom = Classroom::firstOrCreate(
                        [
                            'name' => $kelas,
                            'academic_year_id' => $academicYear->id,
                        ],
                        [
                            'grade' => $grade,
                        ]
                    );
                    $classroomId = $classroom->id;
                }

                // Check if student exists by NIS
                $student = User::where('nis', $nis)->first();

                if ($student) {
                    $student->update([
                        'name' => $name,
                        'gender' => $gender ?? $student->gender,
                        'nisn' => ! empty($nisn) ? $nisn : $student->nisn,
                    ]);
                    $updated++;
                } else {
                    $initialPassword = 'Hbt'.strtoupper(Str::random(5));
                    $student = User::create([
                        'name' => $name,
                        'gender' => $gender ?? 'L',
                        'nis' => $nis,
                        'nisn' => $nisn,
                        'role' => 'siswa',
                        'password' => Hash::make($initialPassword),
                        'initial_password' => $initialPassword,
                        'must_change_password' => true,
                        'xp' => 0,
                        'level' => 1,
                        'current_streak' => 0,
                        'longest_streak' => 0,
                        'streak_freezes' => 1,
                    ]);
                    $imported++;
                }

                if ($classroomId) {
                    $student->classrooms()->syncWithoutDetaching([$classroomId]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'imported' => $imported,
            'updated' => $updated,
            'total' => $imported + $updated,
            'errors' => $errors,
        ];
    }

    /**
     * Map header columns to indexes.
     */
    protected function mapHeaders(array $header): array
    {
        $map = [
            'no' => null,
            'nama' => null,
            'jk' => null,
            'nis' => null,
            'nisn' => null,
            'kelas' => null,
        ];

        foreach ($header as $idx => $col) {
            $cleaned = strtolower(trim((string) $col));
            $cleaned = str_replace(['.', '_', '-'], '', $cleaned);

            if (str_contains($cleaned, 'nama')) {
                $map['nama'] = $idx;
            } elseif ($cleaned === 'jk' || str_contains($cleaned, 'kelamin') || str_contains($cleaned, 'gender')) {
                $map['jk'] = $idx;
            } elseif ($cleaned === 'nisn') {
                $map['nisn'] = $idx;
            } elseif ($cleaned === 'nis') {
                $map['nis'] = $idx;
            } elseif (str_contains($cleaned, 'kelas') || str_contains($cleaned, 'rombel')) {
                $map['kelas'] = $idx;
            } elseif ($cleaned === 'no') {
                $map['no'] = $idx;
            }
        }

        // Fallback default index positions if headers didn't match cleanly:
        // Col 0: No, Col 1: Nama, Col 2: JK, Col 3: NIS, Col 4: NISN, Col 5: Kelas
        if ($map['nama'] === null) {
            $map['nama'] = 1;
        }
        if ($map['jk'] === null) {
            $map['jk'] = 2;
        }
        if ($map['nis'] === null) {
            $map['nis'] = 3;
        }
        if ($map['nisn'] === null) {
            $map['nisn'] = 4;
        }
        if ($map['kelas'] === null) {
            $map['kelas'] = 5;
        }

        return $map;
    }

    /**
     * Native parser for .xlsx (OpenXML) files without third-party dependencies.
     */
    public function parseXlsx(string $filePath): array
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            throw new Exception('Tidak dapat membuka berkas XLSX.');
        }

        // 1. Read shared strings if available
        $sharedStrings = [];
        $stringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($stringsXml !== false) {
            $xml = new SimpleXMLElement($stringsXml);
            foreach ($xml->si as $si) {
                if (isset($si->t)) {
                    $sharedStrings[] = (string) $si->t;
                } elseif (isset($si->r)) {
                    $text = '';
                    foreach ($si->r as $r) {
                        $text .= (string) $r->t;
                    }
                    $sharedStrings[] = $text;
                } else {
                    $sharedStrings[] = '';
                }
            }
        }

        // 2. Read sheet1.xml
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            // Try finding first sheet in xl/worksheets/
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (str_starts_with($filename, 'xl/worksheets/sheet') && str_ends_with($filename, '.xml')) {
                    $sheetXml = $zip->getFromIndex($i);
                    break;
                }
            }
        }

        $zip->close();

        if ($sheetXml === false) {
            throw new Exception('Lembar kerja Excel (sheet1) tidak ditemukan.');
        }

        $xml = new SimpleXMLElement($sheetXml);
        $rows = [];

        foreach ($xml->sheetData->row as $row) {
            $rowData = [];
            $currentCol = 0;

            foreach ($row->c as $cell) {
                $cellRef = (string) $cell['r']; // e.g. "A1", "B2"
                $colIndex = $this->columnLetterToIndex(preg_replace('/[0-9]/', '', $cellRef));

                // Pad missing empty cells before this column
                while ($currentCol < $colIndex) {
                    $rowData[] = '';
                    $currentCol++;
                }

                $type = (string) $cell['t'];
                $val = isset($cell->v) ? (string) $cell->v : '';

                if ($type === 's' && isset($sharedStrings[(int) $val])) {
                    $val = $sharedStrings[(int) $val];
                } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                    $val = (string) $cell->is->t;
                }

                $rowData[] = trim($val);
                $currentCol++;
            }

            if (! empty(array_filter($rowData))) {
                $rows[] = $rowData;
            }
        }

        return $rows;
    }

    /**
     * Native parser for CSV files.
     */
    public function parseCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            throw new Exception('Gagal membaca berkas CSV.');
        }

        // Detect delimiter: comma, semicolon, tab
        $sample = fgets($handle);
        rewind($handle);

        $delimiters = [',', ';', "\t"];
        $bestDelimiter = ',';
        $maxCount = 0;
        foreach ($delimiters as $delim) {
            $count = count(str_getcsv($sample, $delim));
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelimiter = $delim;
            }
        }

        $rows = [];
        while (($data = fgetcsv($handle, 2000, $bestDelimiter)) !== false) {
            // Trim BOM if present in first cell
            if (! empty($rows) === false && isset($data[0])) {
                $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
            }
            if (! empty(array_filter($data))) {
                $rows[] = array_map('trim', $data);
            }
        }
        fclose($handle);

        return $rows;
    }

    /**
     * Convert Excel column letters (A, B, C... AA, AB) to zero-based index.
     */
    protected function columnLetterToIndex(string $col): int
    {
        $col = strtoupper($col);
        $index = 0;
        $len = strlen($col);
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($col[$i]) - 64);
        }

        return max(0, $index - 1);
    }

    /**
     * Generate template CSV matching Image 4.
     */
    public function generateTemplateCsv(): string
    {
        $output = fopen('php://temp', 'r+');
        // UTF-8 BOM
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header matching Image 4
        fputcsv($output, ['No.', 'Nama Siswa', 'JK', 'NIS', 'NISN', 'Kelas']);

        // Example rows (including the one from Image 4!)
        fputcsv($output, ['1', 'Ahmad Reza Maulana', 'L', '12009', '0118102423', 'X.1']);
        fputcsv($output, ['2', 'Siti Zahra Aulia', 'P', '12010', '0118102424', 'X.1']);
        fputcsv($output, ['3', 'Bima Satria Yudha', 'L', '12011', '0118102425', 'X.2']);
        fputcsv($output, ['4', 'Nayla Putri Clarissa', 'P', '12012', '0118102426', 'X.2']);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
