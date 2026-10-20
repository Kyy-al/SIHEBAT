# Design System: SiHebat

Dokumen ini adalah acuan desain UI/UX untuk **SiHebat** (Dashboard 7 Kebiasaan Anak Indonesia Hebat). Pelengkap dari `prd.md`.

| Item | Keputusan |
|---|---|
| CSS Framework | **Tailwind CSS** (v3.4+, lewat Vite di Laravel) |
| Font | **Plus Jakarta Sans** (tanpa italic) |
| Warna primer | **Biru muda** (sky) |
| Kesan | Clean, playful, ramah remaja |
| Pendekatan | Mobile-first, responsif sampai desktop |
| Maskot | Ya (spesifikasi di bagian 9) |
| Mode | Terang (default) dan gelap |

**Referensi visual**
- **Admin/Wali Kelas:** dashboard *bento grid*: sidebar kiri, kartu putih bersudut sangat membulat di atas latar abu-biru lembut, kartu statistik berderet di atas, tombol ikon bulat, pemilih Light/Dark di bawah sidebar. Struktur isi mengikuti Rapor Pendidikan (kartu ringkasan, kartu status per kebiasaan).
- **Siswa:** tampilan mobile ala aplikasi ramah anak: sapaan personal, kartu hero berwarna dengan maskot, kalender titik-titik berwarna, kartu konten bersudut membulat, navigasi bawah dengan tab aktif berupa pil berwarna.

---

## 1. Prinsip Desain

1. **Playful tapi rapi.** Warna cerah, sudut membulat, maskot, animasi kecil. Tata letak tetap bersih dengan banyak ruang kosong.
2. **Satu layar, satu tujuan.** Siswa selalu tahu langkah berikutnya (isi jurnal, jaga streak).
3. **Reward terasa.** Setiap aksi benar mendapat respons visual (konfeti, XP naik, maskot bereaksi).
4. **Tidak menghakimi.** Bahasa dan warna untuk capaian rendah bersifat menyemangati, bukan menyalahkan.
5. **Data jelas untuk guru.** Dashboard admin mengutamakan keterbacaan: angka besar, label status, tren.
6. **Mobile-first.** Siswa mengisi lewat HP; desktop adalah perluasan, bukan sebaliknya.
7. **Tanpa italic.** Penekanan memakai `font-semibold` atau `font-bold`, bukan miring.

---

## 2. Palet Warna

### 2.1 Primer: Biru Muda (`primary`)

| Token | Hex | Penggunaan |
|---|---|---|
| `primary-50` | `#F0F9FF` | Latar halus, hover item |
| `primary-100` | `#E0F2FE` | Latar kartu info, chip non-aktif |
| `primary-200` | `#BAE6FD` | Badan maskot, border aksen, track progress |
| `primary-300` | `#7DD3FC` | Ilustrasi, dekorasi |
| **`primary-400`** | **`#38BDF8`** | **Warna utama tombol, item aktif, progress** |
| `primary-500` | `#0EA5E9` | Hover tombol, grafik |
| `primary-600` | `#0284C7` | Bayangan tombol (bagian bawah), pressed |
| `primary-700` | `#0369A1` | Teks tautan, ikon di atas latar terang |
| `primary-800` | `#075985` | Teks kuat di atas `primary-100` |
| `primary-900` | `#0C4A6E` | Banner ringkasan gelap (ala Rapor Pendidikan) |

> **Aturan kontras:** teks di atas `primary-400` memakai **`ink-900`** (navy gelap), bukan putih, karena putih di atas biru muda tidak lolos kontras. Teks putih hanya di atas `primary-600` ke atas.

### 2.2 Aksen Playful

| Token | Hex | Penggunaan |
|---|---|---|
| `sun` | `#FFC93C` | XP, bintang, sorotan reward |
| `sun-soft` | `#FFF4CC` | Latar chip XP |
| `coral` | `#FF7A59` | Api streak, olahraga |
| `coral-soft` | `#FFE6DF` | Latar chip streak |
| `mint` | `#34D399` | Sukses, status Baik |
| `mint-soft` | `#D8F8EB` | Latar status Baik |
| `lavender` | `#A78BFA` | Badge, level, ibadah |
| `lavender-soft` | `#ECE6FF` | Latar badge |
| `bubblegum` | `#FF8FB1` | Aksen hangat, bermasyarakat |
| `bubblegum-soft` | `#FFE4EC` | Latar aksen |

### 2.3 Netral (berwarna navy halus, bukan abu murni)

| Token | Hex | Penggunaan |
|---|---|---|
| `ink-900` | `#0F2A43` | Teks utama, tombol gelap |
| `ink-700` | `#35506B` | Teks sekunder |
| `ink-500` | `#6B829A` | Teks tersier, placeholder (min. 14px) |
| `ink-300` | `#C5D3E0` | Border |
| `ink-100` | `#EAF1F8` | Pemisah, latar input |
| `canvas` | `#F3F8FD` | Latar halaman |
| `surface` | `#FFFFFF` | Kartu |

### 2.4 Status Capaian (ala Rapor Pendidikan)

| Status | Latar | Teks | Ikon |
|---|---|---|---|
| **Baik** | `mint-soft` `#D8F8EB` | `#0B6B47` | Centang |
| **Cukup** | `sun-soft` `#FFF4CC` | `#8A5A00` | Tanda seru lembut |
| **Perlu Perhatian** | `coral-soft` `#FFE6DF` | `#A8381B` | Segitiga |
| Naik | | `#0B6B47` | Panah atas |
| Turun | | `#A8381B` | Panah bawah |

> Status tidak boleh hanya dibedakan lewat warna; selalu sertakan **teks dan ikon**.

### 2.5 Warna per Kebiasaan

| Kebiasaan | Warna | Hex | Soft (latar) |
|---|---|---|---|
| Bangun Pagi | Amber | `#FFB020` | `#FFF1D6` |
| Beribadah | Violet | `#A78BFA` | `#ECE6FF` |
| Berolahraga | Coral | `#FF7A59` | `#FFE6DF` |
| Makan Sehat | Hijau | `#4ADE80` | `#DDF9E7` |
| Gemar Belajar | Teal | `#22C7D6` | `#D6F6F9` |
| Bermasyarakat | Pink | `#FF8FB1` | `#FFE4EC` |
| Tidur Cepat | Indigo | `#6366F1` | `#E2E3FF` |

Warna kebiasaan dipakai konsisten di ikon, kartu jurnal, grafik, dan titik kalender.

### 2.6 Mode Gelap

| Token | Hex |
|---|---|
| `canvas` | `#0B1826` |
| `surface` | `#12263A` |
| `surface-2` | `#1A3350` |
| Teks utama | `#EAF4FF` |
| Teks sekunder | `#9DB4CB` |
| Border | `#27425F` |
| Primer | tetap `primary-400` |

---

## 3. Tipografi

**Font:** Plus Jakarta Sans (bobot 400, 500, 600, 700, 800). **Italic tidak digunakan di mana pun.**

Instalasi (disarankan, tanpa CDN agar cepat dan stabil):

```bash
npm i @fontsource-variable/plus-jakarta-sans
```

```js
// resources/js/app.js
import '@fontsource-variable/plus-jakarta-sans';
```

### Skala Tipografi

| Peran | Mobile | Desktop | Bobot | Line height |
|---|---|---|---|---|
| Display (angka streak/XP) | 48px | 64px | 800 | 1 |
| H1 | 28px | 36px | 800 | 1.15 |
| H2 | 22px | 28px | 700 | 1.2 |
| H3 | 18px | 20px | 700 | 1.3 |
| Body | 16px | 16px | 500 | 1.6 |
| Body kecil | 14px | 14px | 500 | 1.5 |
| Caption/Label | 12px | 12px | 600 | 1.4 |
| Angka statistik | 32px | 40px | 800 | 1 |

Aturan:
- Judul memakai bobot 700-800, isi 500 (bobot 400 terlalu tipis untuk gaya playful).
- Angka memakai `tabular-nums` pada tabel dan leaderboard.
- Teks maksimal ±65 karakter per baris.
- Penekanan: `font-semibold`/`font-bold`. **Larang** `italic`, `<em>`, `<i>` bergaya miring.

---

## 4. Bentuk, Ruang, dan Elevasi

| Elemen | Nilai |
|---|---|
| Radius kartu | `rounded-3xl` (24px); kartu hero `rounded-[28px]` |
| Radius tombol | `rounded-2xl` (16px) atau `rounded-full` untuk chip |
| Radius input | `rounded-2xl` |
| Skala spasi | Kelipatan 4px (`p-4`, `p-5`, `p-6`) |
| Padding kartu | 16px (mobile), 20-24px (desktop) |
| Jarak antar kartu | 12px (mobile), 16-20px (desktop) |
| Border | `border border-ink-100` (tipis), tanpa border tebal |
| Target sentuh | Minimal 44x44px |

**Bayangan:**
- Kartu: lembut, hampir tidak terlihat (`shadow-card`).
- Tombol playful: **bayangan keras di bawah** (efek timbul 3D ala game) yang mengecil saat ditekan.

---

## 5. Konfigurasi Tailwind

```js
// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Livewire/**/*.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans Variable"', '"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        primary: {
          50: '#F0F9FF', 100: '#E0F2FE', 200: '#BAE6FD', 300: '#7DD3FC',
          400: '#38BDF8', 500: '#0EA5E9', 600: '#0284C7', 700: '#0369A1',
          800: '#075985', 900: '#0C4A6E',
        },
        ink: {
          100: '#EAF1F8', 300: '#C5D3E0', 500: '#6B829A', 700: '#35506B', 900: '#0F2A43',
        },
        canvas: '#F3F8FD',
        sun: { DEFAULT: '#FFC93C', soft: '#FFF4CC' },
        coral: { DEFAULT: '#FF7A59', soft: '#FFE6DF' },
        mint: { DEFAULT: '#34D399', soft: '#D8F8EB' },
        lavender: { DEFAULT: '#A78BFA', soft: '#ECE6FF' },
        bubblegum: { DEFAULT: '#FF8FB1', soft: '#FFE4EC' },
        habit: {
          bangun:   { DEFAULT: '#FFB020', soft: '#FFF1D6' },
          ibadah:   { DEFAULT: '#A78BFA', soft: '#ECE6FF' },
          olahraga: { DEFAULT: '#FF7A59', soft: '#FFE6DF' },
          makan:    { DEFAULT: '#4ADE80', soft: '#DDF9E7' },
          belajar:  { DEFAULT: '#22C7D6', soft: '#D6F6F9' },
          sosial:   { DEFAULT: '#FF8FB1', soft: '#FFE4EC' },
          tidur:    { DEFAULT: '#6366F1', soft: '#E2E3FF' },
        },
      },
      borderRadius: {
        '2xl': '16px',
        '3xl': '24px',
        '4xl': '28px',
      },
      boxShadow: {
        card: '0 1px 2px rgba(15,42,67,.04), 0 8px 24px -12px rgba(15,42,67,.10)',
        pop: '0 12px 32px -12px rgba(14,165,233,.35)',
        btn: '0 4px 0 0 #0284C7',
        'btn-dark': '0 4px 0 0 #000',
        'btn-soft': '0 4px 0 0 #C5D3E0',
      },
      keyframes: {
        wiggle: { '0%,100%': { transform: 'rotate(-4deg)' }, '50%': { transform: 'rotate(4deg)' } },
        floaty: { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-6px)' } },
        pop: { '0%': { transform: 'scale(.8)', opacity: '0' }, '60%': { transform: 'scale(1.08)' }, '100%': { transform: 'scale(1)', opacity: '1' } },
        flame: { '0%,100%': { transform: 'scale(1) rotate(-2deg)' }, '50%': { transform: 'scale(1.08) rotate(2deg)' } },
        'fill-bar': { from: { transform: 'scaleX(0)' }, to: { transform: 'scaleX(1)' } },
      },
      animation: {
        wiggle: 'wiggle .4s ease-in-out',
        floaty: 'floaty 3s ease-in-out infinite',
        pop: 'pop .35s cubic-bezier(.34,1.56,.64,1) both',
        flame: 'flame 1.2s ease-in-out infinite',
        'fill-bar': 'fill-bar .6s ease-out both',
      },
    },
  },
  plugins: [],
};
```

```css
/* resources/css/app.css */
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
  html { @apply font-sans text-ink-900 bg-canvas antialiased; }
  /* Tanpa italic di seluruh aplikasi */
  em, i, cite, address, dfn, var { font-style: normal; }
  .italic { font-style: normal !important; }
  :focus-visible { @apply outline-none ring-4 ring-primary-300; }
  .dark html, html.dark { @apply bg-[#0B1826] text-[#EAF4FF]; }
}

@layer components {
  .card { @apply rounded-3xl bg-white p-4 shadow-card sm:p-5 dark:bg-[#12263A]; }
  .btn { @apply inline-flex min-h-[48px] items-center justify-center gap-2 rounded-2xl px-6 font-bold transition active:translate-y-1 active:shadow-none disabled:opacity-50; }
  .btn-primary { @apply btn bg-primary-400 text-ink-900 shadow-btn hover:bg-primary-500; }
  .btn-dark { @apply btn bg-ink-900 text-white shadow-btn-dark; }
  .btn-soft { @apply btn bg-white text-ink-900 border border-ink-100 shadow-btn-soft; }
  .chip { @apply inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold; }
}

@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
}
```

---

## 6. Layout dan Responsivitas

**Breakpoint:** `sm 640`, `md 768`, `lg 1024`, `xl 1280`, `2xl 1536`. Desain dimulai dari 360px.

### 6.1 Perilaku Responsif

| Area | < 640 (HP) | 768-1023 (tablet) | ≥ 1024 (desktop) |
|---|---|---|---|
| Navigasi siswa | Bottom nav 4 tab | Bottom nav | Sidebar ramping + konten tengah |
| Navigasi admin | Top bar + drawer menu | Drawer menu | Sidebar tetap 240px |
| Grid kartu statistik | 1 kolom (atau scroll horizontal) | 2 kolom | 3 kolom |
| Grid 7 kebiasaan | 1 kolom | 2 kolom | 3-4 kolom |
| Tabel | Berubah jadi daftar kartu | Tabel scroll horizontal | Tabel penuh |
| Modal | Bottom sheet penuh lebar | Modal tengah | Modal tengah |
| Padding halaman | 16px | 24px | 32px |

Aturan:
- Tidak ada scroll horizontal pada halaman (kecuali carousel yang disengaja).
- Gunakan `min-h-dvh`, bukan `100vh`, agar aman di browser HP.
- Hormati area aman (`pb-[env(safe-area-inset-bottom)]`) untuk bottom nav dan tombol sticky.
- Gambar dan grafik fluid (`w-full`, `max-w-full`).

### 6.2 Layout Admin / Wali Kelas (Desktop)

```
┌──────────┬───────────────────────────────────────────────┐
│ Logo     │ Halo, Bu Ani!                [🔔][🔍][Ekspor] │
│          ├───────────────────────────────────────────────┤
│ Dashboard│ ┌─ Banner Ringkasan Kondisi (primary-900) ──┐ │
│ Kelas    │ │ [Peningkatan][Capaian Terbaik][Terendah]  │ │
│ Siswa    │ └───────────────────────────────────────────┘ │
│ Peringkat│ ┌ Bangun ┐┌ Ibadah ┐┌ Olahraga ┐  7 kartu      │
│ Insight  │ ┌ Makan  ┐┌ Belajar┐┌ Sosial   ┐  status       │
│ Laporan  │ ┌ Tidur  ┐                                      │
│ Pengatur │ ┌─ Tren ──────────┐┌─ Insight AI + maskot ─┐    │
│          │ ┌─ Heatmap ───────┐┌─ Siswa perlu perhatian┐    │
│ [Profil] │                                                 │
│ [Terang|Gelap]                                              │
└──────────┴───────────────────────────────────────────────┘
```

Detail:
- **Sidebar:** logo di atas, menu dengan ikon, item aktif berupa pil `bg-primary-400 text-ink-900` dengan panah kecil di kanan; profil dan pemilih **Terang/Gelap** di bawah.
- **Header:** sapaan personal + subteks ("Ada 3 siswa yang perlu perhatian"), tombol ikon bulat gelap (notifikasi, cari), tombol utama "Ekspor Laporan".
- **Banner Ringkasan Kondisi:** latar `primary-900`, tiga kartu putih (Peningkatan Tertinggi, Capaian Terbaik, Capaian Terendah) berisi ikon berwarna kebiasaan, label kecil, dan nama kebiasaan tebal. Di bawahnya kotak info `primary-100` berisi tips AI.
- **Kartu per kebiasaan:** judul, label status (Baik/Cukup/Perlu Perhatian), persentase besar, panah naik/turun, tombol "Apa arti capaian saya?" (soft) dan **"Pelajari Akar Masalah"** (dark, penuh lebar).
- **Latar kartu:** putih di atas `canvas` abu-biru lembut, jarak lega, radius besar (mengikuti gaya bento di referensi).
- **Filter:** pil di header (Periode, Kelas, Angkatan).

### 6.3 Layout Siswa (Mobile-first)

```
┌──────────────────────┐
│ Halo, Rina! 👋   [AV]│
│ 🔥 12 hari  ⭐ 340 XP │
│ ┌─ Hero (primary-400)┐│
│ │ Maskot + "Ayo isi  ││
│ │ jurnal hari ini!"  ││
│ │ [Isi Jurnal  3/7]  ││
│ └────────────────────┘│
│ Sen Sel Rab Kam Jum…  │  ← titik mingguan berwarna
│ ● ● ● ● ○ ○ ○         │
│ Kebiasaan hari ini    │
│ ┌ kartu ┐┌ kartu ┐    │  ← kartu berwarna per kebiasaan
│ Misi minggu ini       │
│ Peringkat kelas (Top3)│
├──────────────────────┤
│ 🏠 Beranda 📝 Jurnal 🏆 Peringkat 👤 Profil │
└──────────────────────┘
```

Detail:
- **Header:** sapaan, chip streak (api coral) dan chip XP (bintang kuning).
- **Hero:** kartu `primary-400` dengan dekorasi bentuk lingkaran/kurva lembut dan maskot di kanan; tombol CTA besar. Mengikuti gaya kartu hero berwarna dengan maskot di referensi.
- **Kalender mingguan:** titik bulat per hari; terisi lengkap = warna penuh, sebagian = setengah, kosong = outline, hari ini = cincin tebal. Kalender bulanan di halaman Rapor Saya (grid titik seperti referensi).
- **Kartu kebiasaan:** latar `habit-*-soft`, ikon besar, nama, status centang; dapat discroll horizontal atau grid 2 kolom.
- **Bottom nav:** 4 tab, tab aktif berupa pil `primary-400` dengan ikon dan label; tab lain hanya ikon + label abu.
- **Desktop (≥1024):** konten di tengah (`max-w-5xl`), navigasi pindah ke sidebar ramping kiri, hero dan kalender berdampingan dalam 2 kolom.

---

## 7. Komponen UI

### 7.1 Tombol
| Varian | Kelas | Penggunaan |
|---|---|---|
| Primer | `btn-primary` | Aksi utama (Isi Jurnal, Simpan) |
| Gelap | `btn-dark` | Aksi sekunder tegas (Pelajari Akar Masalah) |
| Soft | `btn-soft` | Aksi tambahan (Apa arti capaian saya?) |
| Ghost | teks `primary-700` + underline hover | Tautan |
| Ikon bulat | `size-11 rounded-full bg-ink-900 text-white` | Notifikasi, cari, chat |

Efek tekan: tombol turun 4px dan bayangan hilang. Status loading: spinner di dalam tombol, teks diganti ("Menyimpan...").

### 7.2 Kartu
- Putih, `rounded-3xl`, `shadow-card`, tanpa border tebal.
- Varian berwarna: `habit-*-soft` untuk kebiasaan; `primary-400` untuk hero; `primary-900` untuk banner ringkasan.

### 7.3 Input Jurnal (ringan, minim mengetik)
| Kebiasaan | Komponen |
|---|---|
| Bangun Pagi | Pemilih jam berupa **roda/stepper besar** + chip cepat (04.30, 05.00, 05.30, ...) |
| Beribadah | Toggle besar Sudah/Belum + chip jenis ibadah |
| Berolahraga | Chip jenis (Lari, Futsal, Senam, ...) + **slider durasi** (0-120 menit) |
| Makan Sehat | 5 checklist ikon besar (sarapan, makan siang, makan malam, sayur/buah, air putih) |
| Gemar Belajar | Slider durasi + input topik singkat (opsional) |
| Bermasyarakat | Chip multi-pilih (Gotong royong, Bantu keluarga, Ekskul, ...) |
| Tidur Cepat | Pemilih jam seperti Bangun Pagi |
| Mood | 5 emoji besar |
| Catatan | Textarea singkat (opsional), hitungan karakter |

Aturan input:
- Tinggi minimal 48px, label selalu terlihat (bukan hanya placeholder).
- Fokus: ring `primary-300` tebal.
- Error: border `coral`, pesan jelas dengan ikon ("Jam bangun belum diisi").
- Semua pilihan tersedia dengan sentuhan; keyboard muncul seminimal mungkin.

### 7.4 Halaman Jurnal Harian
- **Progress atas:** bar `primary-400` "3 dari 7 kebiasaan" yang terisi dengan animasi.
- **Satu kartu per kebiasaan**, urut vertikal, bisa dilipat; kartu yang selesai berubah menjadi centang berwarna kebiasaan dan menampilkan "+10 XP" melayang.
- **Tombol sticky bawah** "Simpan Jurnal" (di atas bottom nav, memperhatikan safe area).
- **Layar sukses:** maskot merayakan, konfeti, hitung naik XP, api streak membesar, bonus Perfect Day jika 7/7.

### 7.5 Streak, XP, dan Level
- **Chip streak:** `coral-soft` + ikon api beranimasi `animate-flame`, angka tebal.
- **Chip XP:** `sun-soft` + ikon bintang.
- **Level bar:** track `primary-100`, isi `primary-400`, label level (Pemula, Hebat, Legenda) dengan lencana lavender.
- **Streak freeze:** ikon es biru muda; tampil sebagai kartu kecil "Pelindung streak: 1".
- Streak putus: maskot sedih + pesan menyemangati ("Tidak apa-apa, mulai lagi hari ini!"), tanpa warna merah keras.

### 7.6 Leaderboard
- Daftar baris: peringkat, avatar, nama, XP/persen, perubahan posisi (panah).
- **Podium Top 3** dengan emas (`sun`), perak (`#C5D3E0`), perunggu (`#F2A66B`).
- Baris milik sendiri disorot `primary-100` dan menempel (sticky) di bawah jika di luar Top 10.
- Toggle periode (Mingguan, Bulanan, Semester) berupa segmented control pil.
- Mode privasi: bagian bawah menampilkan "Posisi kamu: #17", bukan daftar penuh.

### 7.7 Badge Status dan Label
- Pil kecil `chip` dengan ikon + teks (lihat 2.4).
- Badge pencapaian: lingkaran/heksagon dengan gradien lembut, terkunci = abu dengan gembok.

### 7.8 Grafik (ApexCharts)
- Warna mengikuti warna kebiasaan atau `primary-400`.
- Batang membulat, grid garis tipis `ink-100`, tooltip kartu putih bersudut membulat.
- Font grafik: Plus Jakarta Sans.
- Selalu ada ringkasan teks di bawah grafik untuk aksesibilitas.
- Heatmap kalender: sel bulat berwarna `primary-100` sampai `primary-500` sesuai kelengkapan.

### 7.9 Tabel (admin)
- Header tipis `ink-500` 12px kapital kecil, baris lega (min 56px), pembatas `ink-100`.
- Hover baris `primary-50`; avatar bulat; status berupa chip.
- Mobile: tiap baris menjadi kartu bertumpuk.

### 7.10 Modal, Bottom Sheet, Toast
- Modal: `rounded-3xl`, overlay `ink-900/40` + blur ringan; di HP berupa **bottom sheet** dengan pegangan geser.
- Modal "Apa arti capaian saya?": ilustrasi maskot + penjelasan singkat + tombol "Mengerti".
- Toast: pojok atas (desktop) atau atas layar (HP), ikon + pesan singkat, hilang otomatis 4 detik.

### 7.11 Keadaan Kosong, Memuat, dan Error
- **Kosong:** maskot + kalimat ramah + 1 tombol aksi ("Belum ada jurnal. Yuk isi yang pertama!").
- **Memuat:** skeleton `animate-pulse` berbentuk kartu (bukan spinner penuh layar).
- **Error:** maskot bingung, pesan jelas, tombol "Coba lagi".

### 7.12 Ikon
- **Phosphor Icons** (gaya *bold* atau *duotone*, ujung membulat) agar selaras dengan kesan playful.
- Ukuran 20-24px di UI, 32-48px di kartu kebiasaan.
- Ikon kebiasaan: matahari terbit, tangan berdoa/masjid-gereja netral, lari/bola, apel/piring, buku, tangan saling bantu, bulan.

---

## 8. Halaman dan Komponen Utama

| Peran | Halaman |
|---|---|
| Siswa | Masuk (NIS), Ganti Kata Sandi Pertama, Beranda, Jurnal Hari Ini, Sukses Jurnal, Rapor Saya (kalender + grafik), Peringkat, Badge dan Misi, Profil |
| Wali Kelas | Dashboard Kelas, Detail Kebiasaan, Daftar Siswa, Detail Siswa, Akar Masalah, Insight AI, Peringkat Kelas, Ekspor |
| Admin | Dashboard Sekolah, Perbandingan Kelas, Master Data (Siswa, Kelas, Tahun Ajaran), Pengaturan Kebiasaan dan XP, Laporan, Audit Log |

**Halaman Masuk:** latar `primary-100` dengan bentuk lembut, kartu putih di tengah, maskot melambai di atas kartu, input NIS (angka) dan kata sandi, tombol primer penuh lebar. Pesan error ramah ("NIS atau kata sandi belum cocok").

**Struktur komponen Blade/Livewire (usulan):**
```
resources/views/components/
  ui/ button, card, chip, input, toggle, slider, modal, toast, progress, avatar
  habit/ card, icon, input-*
  gamification/ streak-chip, xp-chip, level-bar, badge, confetti
  layout/ sidebar, topbar, bottom-nav, page-header
  mascot/ mascot (prop: state, size)
```

---

## 9. Maskot

> Karakter maskot belum final. Bagian ini adalah **spesifikasi** agar aset yang dibuat nanti konsisten dan siap dipakai di UI.

### 9.1 Karakter (usulan)
- **Bentuk:** makhluk bulat lembut (blob/awan) dengan proporsi kepala besar, mudah dikenali dari siluet.
- **Wajah:** mata bulat hitam sederhana, pipi merona kecil, mulut ekspresif; tanpa detail rumit.
- **Warna:** badan `primary-200` dengan highlight putih dan pipi `bubblegum`; aksesori kecil berwarna `sun` (misalnya jambul/bintang) agar menonjol di latar biru.
- **Kepribadian:** ceria, suportif, tidak menggurui.
- **Nama:** belum ditentukan (disarankan pendek dan mudah diucapkan, 2-3 suku kata, selaras dengan nama SiHebat).

### 9.2 Ekspresi / State

| State | Dipakai di |
|---|---|
| `idle` (melayang pelan) | Beranda, hero |
| `senang` | Jurnal tersimpan, capaian Baik |
| `semangat` (kepalan tangan) | Pengingat isi jurnal, misi baru |
| `merayakan` (konfeti, melompat) | Perfect Day, naik level, badge baru |
| `sedih` | Streak putus, capaian Perlu Perhatian |
| `tidur` | Pengingat tidur cepat, mode gelap, malam |
| `berpikir` | AI sedang menganalisis |
| `bingung` | Error, halaman tidak ditemukan |
| `melambai` | Halaman masuk, sapaan |
| `olahraga`, `belajar`, `makan` | Kartu kebiasaan terkait (varian opsional) |

### 9.3 Format dan Penggunaan
- **Format:** SVG per ekspresi (statis) + Lottie/CSS untuk animasi ringan; simpan di `resources/images/mascot/`.
- **Ukuran:** 48px (avatar kecil), 96px (kartu), 160-240px (hero, layar sukses, keadaan kosong).
- Komponen: `<x-mascot state="senang" size="lg" />`.
- Animasi: melayang (`animate-floaty`), mengedip, memantul saat reaksi; **nonaktif** bila `prefers-reduced-motion`.
- Maskot **tidak boleh menutupi** konten atau tombol, dan tidak muncul di setiap layar agar tidak berlebihan (hero, sukses, kosong, error, modal, insight AI).
- Pesan maskot: singkat (maksimal 2 kalimat), ramah, bahasa sehari-hari yang sopan.

### 9.4 Maskot di Dashboard Admin
- Tampil kecil di kartu **Insight AI** (state `berpikir`/`senang`) dan di keadaan kosong. Tampilan admin tetap profesional: maskot tidak muncul di tabel atau laporan ekspor.

---

## 10. Gerak dan Interaksi (Micro-interaction)

| Momen | Efek | Durasi |
|---|---|---|
| Tekan tombol | Turun 4px, bayangan hilang | 100 ms |
| Toggle aktif | Bergeser + sedikit bergetar (`wiggle`) | 250 ms |
| Kebiasaan terisi | Kartu berubah warna, centang `pop`, "+10 XP" melayang naik | 400 ms |
| Progress bar | Terisi mulus (`fill-bar`) | 600 ms |
| Angka XP/streak | Hitung naik (count-up) | 800 ms |
| Jurnal selesai | Konfeti + maskot merayakan | 1,5 detik |
| Perfect Day | Konfeti penuh layar + kartu bonus | 2 detik |
| Pindah halaman | Fade + geser 8px | 200 ms |
| Kartu muncul | Berurutan (stagger 50 ms) | 300 ms |

Easing: `cubic-bezier(.34,1.56,.64,1)` untuk efek memantul (reward), `ease-out` untuk lainnya.
Semua animasi dinonaktifkan atau dikurangi bila `prefers-reduced-motion: reduce`.
Haptic (getar) opsional lewat `navigator.vibrate` saat jurnal tersimpan di HP yang mendukung.

---

## 11. Gaya Bahasa (Microcopy)

- Sapaan personal, ringkas, positif. Tidak menghakimi.
- Hindari kata menyalahkan ("malas", "gagal"). Pakai ajakan ("Yuk coba lagi besok!").
- Tombol berupa kata kerja: "Isi Jurnal", "Simpan", "Lihat Peringkat".

| Situasi | Contoh |
|---|---|
| Sapaan pagi | "Selamat pagi, Rina! Siap mulai hari hebat?" |
| Jurnal belum diisi | "Jurnal hari ini menunggu kamu. Cuma 2 menit!" |
| Jurnal selesai | "Mantap! 12 hari beruntun. Terus semangat!" |
| Streak putus | "Streak-mu istirahat dulu. Yuk mulai lagi hari ini!" |
| Capaian rendah (admin) | "Tidur Cepat perlu perhatian di kelas X-2." |
| Kosong | "Belum ada data. Mulai dari jurnal pertama, yuk!" |

---

## 12. Aksesibilitas

- Kontras teks minimal **4.5:1** (3:1 untuk teks besar dan ikon); teks di atas `primary-400` memakai `ink-900`.
- Target sentuh minimal 44x44px; jarak antar target minimal 8px.
- Fokus keyboard selalu terlihat (ring `primary-300` 4px).
- Info tidak hanya lewat warna: status memakai teks + ikon.
- Semua gambar/ikon bermakna punya `alt`/`aria-label`; ikon dekoratif `aria-hidden="true"`.
- Form: `label` terhubung ke input, pesan error di-link lewat `aria-describedby`.
- Dukung zoom 200% tanpa kehilangan konten; ukuran teks memakai `rem`.
- Hormati `prefers-reduced-motion` dan `prefers-color-scheme`.
- Konfeti tidak berkedip cepat (aman untuk sensitivitas cahaya).

---

## 13. Performa Desain

- Font variabel lokal (1 berkas), `font-display: swap`.
- Maskot dan ilustrasi berformat SVG; hindari gambar raster besar.
- Lazy load grafik dan gambar di bawah lipatan.
- Skeleton untuk data yang dimuat; hindari layout shift (tetapkan tinggi kartu/gambar).
- Target LCP ≤ 2,5 detik di 4G pada halaman Beranda dan Jurnal.

---

## 14. Daftar Periksa Desain (Definition of Done)

- [ ] Plus Jakarta Sans dimuat; **tidak ada** teks italic
- [ ] Warna primer biru muda konsisten; teks di atas `primary-400` berwarna `ink-900`
- [ ] Semua halaman diuji di 360px, 768px, 1024px, dan 1440px tanpa scroll horizontal
- [ ] Target sentuh ≥ 44px dan fokus keyboard terlihat
- [ ] Mode terang dan gelap tampil benar
- [ ] Status capaian memakai teks + ikon, bukan warna saja
- [ ] Animasi reward berjalan dan dapat dinonaktifkan (`prefers-reduced-motion`)
- [ ] Maskot memakai komponen `<x-mascot>` dengan state yang sesuai
- [ ] Bottom nav (siswa) dan sidebar (admin) berfungsi sesuai breakpoint
- [ ] Microcopy ramah, tanpa kata menyalahkan

---

## 15. Keputusan Terbuka

1. Karakter, nama, dan gaya final maskot (saya dapat membuatkan konsep SVG awal).
2. Nama dan identitas logo SiHebat.
3. Apakah mode gelap dirilis di versi 1 atau fase berikutnya?
4. Apakah banner ringkasan admin memakai `primary-900` (kontras kuat) atau tetap terang?
