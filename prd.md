# PRD: SiHebat (Dashboard 7 Kebiasaan Anak Indonesia Hebat)

| Item | Keterangan |
|---|---|
| Nama Produk | **SiHebat** |
| Versi Dokumen | 1.0 (Draft) |
| Platform | Aplikasi web responsif (mobile-first), tanpa aplikasi native dan tanpa PWA |
| Stack | Laravel 11, MySQL 8, Livewire + Alpine.js + Tailwind CSS |
| Target Pengguna | Siswa SMA, Wali Kelas, Admin/Kepala Sekolah |
| Asumsi Awal | Satu sekolah (contoh: SMAN 1 Sungailiat), struktur database disiapkan agar bisa dikembangkan ke multi-sekolah |

---

## 1. Latar Belakang

Gerakan 7 Kebiasaan Anak Indonesia Hebat membutuhkan alat pencatatan yang konsisten, mudah, dan menarik bagi siswa. Pencatatan manual (kertas/formulir) sulit direkap dan kurang memotivasi. Produk ini menyediakan jurnal harian digital dengan elemen gamifikasi (streak, XP, leaderboard) serta dashboard ala **Rapor Pendidikan Kemendikbudristek** agar sekolah dapat memantau capaian kebiasaan siswa secara visual dan berbasis data, dibantu analisis AI.

**7 Kebiasaan:**
1. Bangun Pagi
2. Beribadah
3. Berolahraga
4. Makan Sehat dan Bergizi
5. Gemar Belajar
6. Bermasyarakat
7. Tidur Cepat

---

## 2. Tujuan (Goals)

| # | Tujuan | Indikator Keberhasilan (KPI) |
|---|---|---|
| G1 | Membangun kebiasaan mengisi jurnal secara rutin | ≥ 70% siswa aktif mengisi minimal 5 hari/minggu |
| G2 | Menyediakan data capaian kebiasaan yang akurat dan real-time | Rekap kelas/sekolah tersedia otomatis tanpa input manual |
| G3 | Membuat pengalaman pengisian menarik dan cepat | Waktu pengisian jurnal ≤ 3 menit; retensi mingguan ≥ 60% |
| G4 | Membantu guru mengambil keputusan lewat insight AI | Wali kelas membuka insight mingguan ≥ 1x/minggu |
| G5 | Menyediakan dashboard ringkas ala Rapor Pendidikan | 100% kebiasaan punya status (Baik/Cukup/Perlu Perhatian) dan tren |

### Non-Goals (di luar cakupan versi 1)
- Aplikasi mobile native (Android/iOS) dan PWA; SiHebat hanya berbentuk web
- Integrasi otomatis dengan Dapodik atau Rapor Pendidikan resmi
- Verifikasi kebiasaan lewat sensor/wearable
- Fitur sosial penuh (komentar, chat antar siswa)
- Penilaian akademik atau nilai rapor siswa

---

## 3. Ruang Lingkup (Scope)

### 3.1 In Scope (MVP sampai V1)
- Autentikasi dan manajemen peran (Siswa, Wali Kelas, Admin)
- Jurnal harian 7 kebiasaan
- Gamifikasi: streak, XP, level, badge, misi mingguan
- Leaderboard individu dan antar kelas
- Dashboard ala Rapor Pendidikan (kartu ringkasan, status per kebiasaan, tren, drill-down)
- Fitur AI: ringkasan insight, analisis akar masalah, deteksi dini, rekomendasi personal, tanya data
- Manajemen master data (siswa, kelas, tahun ajaran, kebiasaan, badge)
- Ekspor laporan (PDF/Excel)
- Notifikasi pengingat (email dan notifikasi dalam web)

### 3.2 Out of Scope
Lihat bagian Non-Goals di atas.

---

## 4. Persona dan Peran Pengguna

| Peran | Deskripsi | Kebutuhan Utama |
|---|---|---|
| **Siswa** | Pelajar SMA, mengakses lewat HP | Isi jurnal cepat, lihat streak/XP/badge, lihat peringkat, dapat saran personal |
| **Wali Kelas** | Pengampu satu kelas | Pantau kelas, siswa yang jarang mengisi, rekap per kebiasaan, insight AI |
| **Admin/Kepsek** | Pengelola sekolah | Dashboard sekolah, perbandingan antar kelas, ekspor laporan, kelola master data |

---

## 5. Kebutuhan Fungsional

### 5.1 Autentikasi dan Akses (FR-AUTH)
| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-AUTH-01 | Siswa login dengan **NIS** dan kata sandi; wali kelas dan admin login dengan email/username dan kata sandi | Must |
| FR-AUTH-02 | Role-based access control (Siswa, Wali Kelas, Admin) | Must |
| FR-AUTH-03 | Reset kata sandi | Should |
| FR-AUTH-04 | Impor akun siswa massal (berbasis NIS) lewat Excel/CSV | Must |
| FR-AUTH-05 | Kata sandi awal dibuat sistem/admin, siswa **wajib ganti kata sandi saat login pertama** (NIS mudah ditebak, jadi tidak boleh dipakai sebagai kata sandi tetap) | Must |
| FR-AUTH-06 | Pembatasan percobaan login (rate limiting) dan admin dapat mereset kata sandi siswa | Should |

### 5.2 Jurnal Harian (FR-JRN)
| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-JRN-01 | Satu halaman jurnal dengan satu kartu per kebiasaan | Must |
| FR-JRN-02 | Input per kebiasaan sesuai tabel 5.2.1 | Must |
| FR-JRN-03 | Isi mundur maksimal H-1; tidak bisa mengisi tanggal mendatang | Must |
| FR-JRN-04 | Simpan draf otomatis dan bisa edit sebelum batas waktu harian | Should |
| FR-JRN-05 | Mood harian dan catatan refleksi singkat (opsional) | Could |
| FR-JRN-06 | Unggah foto bukti (opsional) | Could |
| FR-JRN-07 | Verifikasi opsional oleh wali kelas | Could |

**5.2.1 Rancangan Input per Kebiasaan**

| Kebiasaan | Input | Kriteria "Tercapai" (dapat dikonfigurasi admin) |
|---|---|---|
| Bangun Pagi | Jam bangun | ≤ 05.00 (default) |
| Beribadah | Sudah/belum, jenis ibadah (pilihan inklusif) | Sudah beribadah |
| Berolahraga | Jenis dan durasi (menit) | ≥ 30 menit |
| Makan Sehat | Sarapan, makan siang, makan malam, sayur/buah, air putih | Minimal 3 dari 5 centang terpenuhi |
| Gemar Belajar | Durasi dan topik/buku | ≥ 30 menit |
| Bermasyarakat | Jenis kegiatan (gotong royong, bantu keluarga, ekskul, dll.) | Minimal 1 kegiatan |
| Tidur Cepat | Jam tidur | ≤ 21.00 (default) |

> Nilai ambang batas bersifat default dan dapat diubah oleh Admin sesuai kebijakan sekolah.

### 5.3 Gamifikasi (FR-GAM)
| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-GAM-01 | **Streak** harian (hari beruntun jurnal terisi) dengan indikator api | Must |
| FR-GAM-02 | **Streak freeze**: 1x per minggu untuk mengamankan streak | Should |
| FR-GAM-03 | **XP** per kebiasaan tercapai + bonus *Perfect Day* (7/7) | Must |
| FR-GAM-04 | **Level** (Pemula, Hebat, Legenda, dst.) berdasarkan total XP | Should |
| FR-GAM-05 | **Badge** (7 hari beruntun, Early Bird, Atlet, Si Rajin, dll.) | Should |
| FR-GAM-06 | **Misi mingguan** (contoh: olahraga 4 hari minggu ini) | Could |
| FR-GAM-07 | Maskot dengan ekspresi reaktif, konfeti dan animasi saat jurnal selesai | Should |

**Aturan XP (usulan awal):**
- Tiap kebiasaan tercapai: +10 XP
- Perfect Day (7/7): bonus +30 XP
- Bonus streak: +5 XP untuk setiap kelipatan 7 hari
- XP tidak diberikan untuk isian yang ditandai tidak valid oleh wali kelas

### 5.4 Leaderboard (FR-LDB)
| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-LDB-01 | Peringkat individu per kelas | Must |
| FR-LDB-02 | Peringkat antar kelas berdasarkan **persentase partisipasi dan capaian** (adil untuk kelas kecil) | Must |
| FR-LDB-03 | Peringkat angkatan | Should |
| FR-LDB-04 | Periode: mingguan, bulanan, semester | Must |
| FR-LDB-05 | Mode privasi: tampilkan Top 10, siswa lain hanya melihat posisinya sendiri | Must |
| FR-LDB-06 | Admin dapat menonaktifkan leaderboard individu | Should |

### 5.5 Dashboard ala Rapor Pendidikan (FR-DSH)
| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-DSH-01 | Header profil satuan pendidikan (nama sekolah, NPSN, alamat, akreditasi) dan pemilih periode | Must |
| FR-DSH-02 | **3 kartu ringkasan**: Peningkatan Tertinggi, Capaian Terbaik, Capaian Terendah (otomatis) | Must |
| FR-DSH-03 | Kartu per kebiasaan: label status **Baik / Cukup / Perlu Perhatian**, % siswa mencapai target, panah naik/turun dibanding periode lalu | Must |
| FR-DSH-04 | Tombol **"Apa arti capaian saya?"** (modal penjelasan) | Should |
| FR-DSH-05 | Tombol **"Pelajari Akar Masalah"** (drill-down per kelas, siswa, dan hari) | Must |
| FR-DSH-06 | Filter: periode, kelas, angkatan | Must |
| FR-DSH-07 | Grafik tren (garis/batang), heatmap kalender partisipasi | Should |
| FR-DSH-08 | Tampilan berbeda per peran (sekolah, kelas, pribadi) | Must |

**Ambang status (usulan, dapat dikonfigurasi):**
| Status | Persentase siswa mencapai target |
|---|---|
| Baik | ≥ 75% |
| Cukup | 50% sampai < 75% |
| Perlu Perhatian | < 50% |

### 5.6 Fitur AI (FR-AI)
| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-AI-01 | **Ringkasan insight otomatis** mingguan/bulanan (contoh: "Tidur cepat turun 8% di kelas X-2") | Must |
| FR-AI-02 | **Analisis akar masalah**: korelasi antar kebiasaan (contoh: tidur larut vs bangun terlambat) | Should |
| FR-AI-03 | **Deteksi dini**: siswa dengan tren menurun atau streak sering putus (untuk wali kelas) | Should |
| FR-AI-04 | **Rekomendasi personal** untuk siswa dengan bahasa ramah dan memotivasi | Should |
| FR-AI-05 | **Tanya Data** (chat bahasa natural, hasil dari data terotorisasi) | Could |
| FR-AI-06 | **Draft laporan** untuk wali kelas/kepsek | Could |

**Prinsip AI:**
- Panggilan LLM dilakukan dari **sisi server** (kunci API tidak pernah ke klien)
- Data yang dikirim berupa **agregat atau teranonimisasi** (tanpa nama/NIS)
- Hasil **di-cache** di tabel `ai_insights` dan dijalankan lewat queue/scheduler
- Setiap insight diberi label "dibuat oleh AI" dan bersifat saran, bukan keputusan
- "Tanya Data" hanya mengakses data sesuai peran pengguna (tidak ada query SQL bebas dari LLM)

### 5.7 Manajemen Master Data (FR-ADM)
| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-ADM-01 | CRUD tahun ajaran, kelas, siswa, wali kelas | Must |
| FR-ADM-02 | Konfigurasi ambang kebiasaan, bobot XP, dan jam batas pengisian | Should |
| FR-ADM-03 | Kelola badge dan misi | Could |
| FR-ADM-04 | Ekspor laporan PDF/Excel (per kelas, per siswa, per periode) | Must |
| FR-ADM-05 | Audit log perubahan data penting | Should |

### 5.8 Notifikasi (FR-NTF)
| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-NTF-01 | Pengingat harian jika belum mengisi (email dan notifikasi web) | Should |
| FR-NTF-02 | Peringatan streak akan putus | Could |
| FR-NTF-03 | Notifikasi ke wali kelas untuk siswa berisiko (hasil deteksi dini) | Could |

---

## 6. Kebutuhan Non-Fungsional

| Kategori | Kebutuhan |
|---|---|
| **Performa** | Halaman utama dimuat ≤ 2 detik pada jaringan 4G; query dashboard dioptimalkan dengan indeks dan tabel agregat |
| **Skalabilitas** | Mendukung ≥ 2.000 siswa aktif dan ≥ 500 pengisian bersamaan pada jam sibuk |
| **Keamanan** | Hash kata sandi (bcrypt/argon2), proteksi CSRF/XSS/SQL injection, rate limiting, HTTPS, validasi di server |
| **Privasi** | Akses data per peran; data ke AI dianonimkan; kepatuhan pada UU Pelindungan Data Pribadi (UU PDP) |
| **Aksesibilitas** | Kontras warna WCAG AA, ukuran sentuh ≥ 44px, label form jelas |
| **Kompatibilitas** | Chrome, Edge, Firefox, Safari versi terbaru; layar 360px sampai desktop |
| **Ketersediaan** | Target uptime 99% selama jam sekolah; backup database harian |
| **Maintainability** | Kode mengikuti PSR-12, ada seeder dan test untuk logika streak/XP |
| **Bahasa** | Antarmuka Bahasa Indonesia |

---

## 7. Desain UI/UX

**Prinsip:** clean, playful, mobile-first, ramah remaja.

- Palet cerah dengan 1 warna utama dan warna aksen per kebiasaan; sudut membulat; ikon dan ilustrasi per kebiasaan
- Micro-interaction: toggle bergetar, progress bar terisi, angka XP naik, konfeti saat jurnal lengkap
- Jurnal selesai dalam **maksimal 1 layar gulir**; input berupa toggle, slider, dan pilihan cepat (minim mengetik)
- Maskot sebagai pendamping (senang, sedih, merayakan)
- Dashboard sekolah mengikuti pola Rapor Pendidikan: kartu ringkasan, kartu status per kebiasaan, tombol drill-down
- Mode gelap (opsional)

**Halaman Utama:**
| Peran | Halaman |
|---|---|
| Siswa | Beranda (streak, misi), Jurnal Hari Ini, Rapor Saya, Leaderboard, Badge, Profil |
| Wali Kelas | Dashboard Kelas, Daftar Siswa, Akar Masalah, Insight AI, Ekspor |
| Admin | Dashboard Sekolah, Perbandingan Kelas, Master Data, Pengaturan, Ekspor, Audit Log |

---

## 8. Arsitektur dan Teknologi

| Lapisan | Teknologi |
|---|---|
| Backend | Laravel 11 (PHP 8.2+) |
| Database | MySQL 8 |
| Frontend | Blade + Livewire 3 + Alpine.js + Tailwind CSS |
| Grafik | ApexCharts atau Chart.js |
| Auth & Role | Laravel Breeze + Spatie Permission |
| Queue & Scheduler | Laravel Queue (database/Redis) + Task Scheduling |
| AI | API LLM (dipanggil dari service class di server) |
| Ekspor | Laravel Excel, DomPDF |
| Deployment | VPS/hosting dengan Nginx, PHP-FPM, HTTPS |

**Struktur database (ringkas):**
`users`, `roles`, `school_years`, `classes`, `students`, `habits`, `journal_entries`, `journal_entry_details`, `streaks`, `xp_logs`, `levels`, `badges`, `user_badges`, `missions`, `user_missions`, `leaderboard_snapshots`, `habit_stats_daily` (agregat), `ai_insights`, `notifications`, `audit_logs`

> Catatan: tabel `schools` dan kolom `school_id` ditambahkan sejak awal (nullable/default) agar migrasi ke multi-sekolah mudah.

---

## 9. Aturan Bisnis Utama

1. Satu siswa hanya memiliki **satu jurnal per hari**.
2. Jurnal dapat diisi untuk hari ini dan **H-1**; selebihnya dikunci.
3. **Streak** bertambah jika jurnal hari tersebut terkirim (minimal 1 kebiasaan terisi); putus jika satu hari terlewat tanpa freeze.
4. **Perfect Day** jika ke-7 kebiasaan memenuhi kriteria tercapai.
5. Peringkat antar kelas menggunakan **persentase**, bukan total, agar adil.
6. Perhitungan leaderboard dan agregat dijalankan terjadwal (snapshot harian).
7. Data siswa hanya dapat dilihat oleh siswa itu sendiri, wali kelasnya, dan admin.

---

## 10. Metrik Keberhasilan

| Metrik | Target |
|---|---|
| Tingkat adopsi (siswa aktif/total) | ≥ 80% dalam 1 bulan |
| Rata-rata pengisian per minggu | ≥ 5 hari |
| Rata-rata durasi pengisian | ≤ 3 menit |
| Retensi mingguan | ≥ 60% |
| Rata-rata streak siswa aktif | ≥ 7 hari |
| Insight AI dibuka wali kelas | ≥ 1x/minggu |
| Kepuasan pengguna (survei) | ≥ 4 dari 5 |

---

## 11. Roadmap dan Fase Pengembangan

| Fase | Fokus | Output | Estimasi |
|---|---|---|---|
| **0. Persiapan** | Finalisasi PRD, ERD, wireframe, desain sistem UI | Dokumen ERD, mockup | 1 minggu |
| **1. MVP Inti** | Auth, role, master data, jurnal harian, rekap dasar | Siswa bisa mengisi, admin bisa melihat rekap | 3 minggu |
| **2. Dashboard** | Dashboard ala Rapor Pendidikan, filter, drill-down | Dashboard sekolah dan kelas | 2 minggu |
| **3. Gamifikasi** | Streak, XP, level, badge, misi, maskot | Pengalaman ala Duolingo | 2 minggu |
| **4. Leaderboard** | Individu, kelas, angkatan, mode privasi | Peringkat berjalan | 1 minggu |
| **5. AI** | Insight otomatis, akar masalah, deteksi dini, rekomendasi, tanya data | Fitur AI terintegrasi | 2 minggu |
| **6. Finalisasi** | Notifikasi, ekspor, uji coba, perbaikan, deploy | Rilis uji coba di 1 kelas, lalu seluruh sekolah | 2 minggu |

---

## 12. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Data self-report tidak jujur | Analisis bias | Verifikasi opsional wali kelas, foto bukti opsional, tekankan jurnal sebagai refleksi, bukan penilaian |
| Leaderboard menimbulkan minder/tekanan | Siswa enggan berpartisipasi | Mode privasi (Top 10), peringkat berbasis konsistensi, opsi nonaktif |
| Kebocoran data pribadi siswa | Pelanggaran privasi | Akses berbasis peran, anonimisasi ke AI, HTTPS, audit log |
| Biaya API AI membengkak | Anggaran | Cache hasil, jadwalkan batch, kirim data agregat, batasi "Tanya Data" per pengguna |
| Siswa bosan dengan gamifikasi | Retensi turun | Misi mingguan bervariasi, badge baru berkala |
| Beban server saat jam sibuk | Aplikasi lambat | Tabel agregat, cache, queue, indeks database |
| Kebiasaan sensitif (ibadah) | Kesan menghakimi | Pilihan inklusif, tanpa penilaian benar/salah, tidak ditampilkan di leaderboard per kebiasaan |
| Sinyal internet siswa terbatas | Gagal mengisi | Halaman ringan, aset dioptimalkan, simpan draf otomatis |

---

## 13. Asumsi dan Ketergantungan

**Asumsi**
- Sekolah menyediakan data siswa dan kelas (Excel/CSV).
- Siswa memiliki akses HP dan internet.
- Kriteria "tercapai" disepakati oleh pihak sekolah.

**Ketergantungan**
- Akses API LLM (kunci API dan anggaran)
- Layanan email/SMTP untuk pengingat
- Hosting dan domain

---

## 14. Keputusan dan Pertanyaan Terbuka

**Sudah diputuskan:** nama produk **SiHebat**; platform **web saja**; siswa login memakai **NIS**.

**Masih terbuka:**

1. Apakah sistem untuk **satu sekolah** atau **multi-sekolah**? (PRD ini mengasumsikan satu sekolah, siap dikembangkan.)
2. Siapa yang menentukan kriteria "tercapai" tiap kebiasaan (sekolah atau mengikuti pedoman resmi)?
3. Apakah wali kelas wajib memverifikasi jurnal siswa?
4. Apakah leaderboard individu ditampilkan secara terbuka atau hanya Top 10?
5. Penyedia LLM yang akan digunakan dan batas anggaran bulanan?

---

## 15. Kriteria Penerimaan (Acceptance Criteria) MVP

- [ ] Siswa dapat login dengan NIS (dan wajib ganti kata sandi pertama kali), mengisi jurnal 7 kebiasaan, dan melihat data tersimpan
- [ ] Jurnal tidak dapat diisi untuk tanggal mendatang atau lebih dari H-1
- [ ] Streak dan XP terhitung otomatis dan benar
- [ ] Dashboard menampilkan 3 kartu ringkasan dan status per kebiasaan sesuai data
- [ ] Wali kelas hanya dapat melihat data kelasnya
- [ ] Leaderboard menampilkan peringkat sesuai periode dan mode privasi
- [ ] Insight AI mingguan tergenerasi dan tersimpan tanpa data identitas siswa
- [ ] Ekspor rekap kelas ke Excel/PDF berfungsi
- [ ] Tampilan nyaman di layar 360px dan desktop
