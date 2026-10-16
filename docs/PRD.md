# PRD: Website Leo Swimming Course

| | |
| --- | --- |
| Versi | 1.0 (draf) |
| Tanggal | 14 Oktober 2026 |
| Pemilik | Leo Aswandany, S.Pd (Coach Leo) |
| Teknologi | Laravel (PHP 8.2+), Blade, CSS dan JS biasa |
| Status | Desain desktop selesai di Figma, desain HP menyusul, kode awal sudah dibuat |

---

## 1. Ringkasan

Leo Swimming Course adalah les renang privat dan grup di Surabaya yang dipegang langsung oleh Coach Leo. Website ini menjadi etalase online: memperkenalkan Pak Leo, menampilkan paket dan harga, menunjukkan lokasi kolam, lalu mengarahkan orang tua mendaftar lewat WhatsApp.

Satu kalimat tujuannya: **orang tua yang membuka website ini bisa percaya, memahami paketnya, dan mengirim pendaftaran ke WhatsApp Pak Leo dalam kurang dari 2 menit.**

## 2. Latar belakang dan masalah

- Calon murid saat ini mengenal Pak Leo lewat WhatsApp dan Instagram. Harga, lokasi, dan jadwal harus dijelaskan satu per satu lewat chat.
- Tidak ada satu tempat yang memuat informasi lengkap dan terlihat profesional.
- Data pendaftaran (nama anak, usia, paket, lokasi) sering harus ditanyakan berulang.

## 3. Tujuan dan indikator keberhasilan

| Tujuan | Cara mengukur |
| --- | --- |
| Menaikkan jumlah pendaftar lewat website | Jumlah pesan WhatsApp yang masuk dari form daftar per bulan |
| Mengurangi chat tanya-jawab berulang | Jumlah pertanyaan soal harga dan lokasi yang turun |
| Membangun kepercayaan | Ada foto asli, testimoni asli, dan profil Pak Leo |
| Nyaman dibuka di HP | Halaman terbaca dan form bisa diisi tanpa zoom di layar HP |

Target awal (bisa disesuaikan): halaman termuat di bawah 3 detik pada 4G, dan skor Lighthouse mobile di atas 80.

## 4. Pengguna

**Pengguna utama: orang tua murid** (usia sekitar 28–45), membuka dari HP, mencari les renang untuk anak, peduli keamanan, harga, dan lokasi yang dekat.

**Pengguna kedua:**
- Orang dewasa pemula yang ingin belajar renang.
- Pihak sekolah (kelas khusus SMK Negeri 1 Surabaya).

**Admin:** Pak Leo sendiri. Mengubah harga, lokasi, dan testimoni lewat satu file konfigurasi.

## 5. Ruang lingkup

### Termasuk (versi 1)

- Halaman beranda satu halaman dengan 7 bagian.
- Halaman form daftar.
- Form daftar yang mengirim data ke WhatsApp Pak Leo.
- Tampilan responsif (desktop, tablet, HP).
- SEO dasar dan tombol WhatsApp.

### Belum termasuk (kemungkinan versi berikutnya)

- Login murid atau panel admin.
- Database dan riwayat pendaftar.
- Pembayaran online.
- Jadwal dan kuota real-time.
- Blog atau artikel.
- Multi bahasa.

## 6. Struktur halaman

```
/                 Beranda (satu halaman, menu menggulung ke bagian)
 ├─ #beranda      Hero → Profil → Galeri → Testimoni
 ├─ #program      Program dan Harga
 ├─ #lokasi       Lokasi
 └─ #kontak       Ajakan daftar + footer
/daftar           Form pendaftaran
```

Menu atas: **Beranda · Program dan Harga · Lokasi · Kontak** + tombol **Daftar Sekarang**.

## 7. Kebutuhan fitur

### 7.1 Navbar
- Logo di kiri, menu di kanan, tombol "Daftar Sekarang" berwarna biru.
- Menu tetap menempel di atas saat scroll.
- Menu aktif diberi garis bawah mengikuti posisi scroll.
- HP: menu menjadi hamburger yang membuka daftar menu penuh.

### 7.2 Hero
- Label "Private & Personal Swimming Lesson".
- Judul besar: **"Berani mulai. Jago berenang."**
- Teks singkat, dua tombol: **Lihat Program** (ke bagian harga) dan **WhatsApp** (buka chat Pak Leo).
- Tiga tag: Pemula, Anak & Dewasa, Persiapan Tes.
- Strip biru 4 poin: Belajar dari dasar, Program sesuai kemampuan, Fokus teknik & keselamatan, Area layanan Surabaya.

### 7.3 Profil
- Perkenalan Pak Leo dengan foto, kalimat pribadi, dan tiga ringkasan (level, usia, wilayah).
- Nama lengkap dan gelar: Coach Leo Aswandany, S.Pd.
- Kartu "Belajar step by step. Sesuai kemampuanmu!" di atas foto.

### 7.4 Galeri
- Judul "Momen seru di kolam."
- 7 kotak foto dengan ukuran berbeda (besar, lebar, tinggi, kecil).
- Foto diisi dari `public/images/gallery/`. Jika belum ada foto, tampil blok warna.
- Setiap foto punya keterangan singkat.

### 7.5 Testimoni
- 6 kartu testimoni (nama, program, kutipan).
- HP: kartu bisa digeser ke samping.
- **Hanya testimoni asli dan sudah mendapat izin untuk ditampilkan.** Selama belum ada, isi tetap placeholder dan section boleh disembunyikan.

### 7.6 Program dan Harga

| Paket | Isi | Harga |
| --- | --- | --- |
| **Kelas Privat/Grup** (1 pelatih untuk 1–2 anak) | 4× pertemuan | Rp300.000 |
| | 6× pertemuan | Rp450.000 |
| | 8× pertemuan (+1× gratis) | Rp600.000 |
| **Kelas Khusus** (SMK Negeri 1 Surabaya, min. 5 murid) | 4× pertemuan, per anak | Rp150.000 |

Keunggulan: jadwal bisa dipilih, materi menyesuaikan anak, durasi 90 menit.
Tombol "Daftar" di tiap kartu membuka form dengan paket yang sudah terpilih.

### 7.7 Lokasi

| Wilayah | Kolam | Alamat |
| --- | --- | --- |
| Surabaya Timur | Kolam Renang Nirwana Eksekutif | Jl. Raya Wonorejo Permai, Wonorejo, Kec. Rungkut |
| Surabaya Selatan | Kolam Kodam Brawijaya | Jl. Kesatriyan, Sawunggaling, Kec. Wonokromo |
| Surabaya Selatan | Kolam Jambangan | Karah, Kec. Jambangan |

Tombol "Lihat kolam" membuka lokasi di Google Maps.

### 7.8 Kontak (ajakan daftar) dan footer
- Kartu biru: "Mulai sekarang, raih kemampuan terbaikmu." dengan tombol **Daftar Sekarang** dan **Tanya dulu lewat WhatsApp**.
- Footer: logo, deskripsi singkat, tautan halaman, wilayah, kontak (WhatsApp 0831 9173 7789, Instagram @leo_aswandani), hak cipta.

### 7.9 Form Daftar

| Bagian | Isian | Jenis | Wajib |
| --- | --- | --- | --- |
| Data Orang Tua | Nama orang tua | Teks | Ya |
| | Nomor HP | Telepon (8–16 digit) | Ya |
| Data Anak | Nama anak | Teks | Ya |
| | Jenis kelamin | Pilih satu (Laki-laki / Perempuan) | Ya |
| | Usia anak | Teks | Ya |
| | Pilih paket | Dropdown | Ya |
| Pilihan Kelas | Lokasi kolam | Dropdown | Ya |
| | Jadwal | Pilih beberapa hari (Senin–Jumat) | Tidak |
| | Kapan ingin mulai | Tanggal (tidak boleh sebelum hari ini) | Ya |

**Alur:** pengguna mengisi form → server memvalidasi → jika ada salah, form tampil lagi dengan pesan dan isian tetap terjaga → jika benar, browser dialihkan ke `wa.me` dengan pesan terformat ke nomor Pak Leo.

Pesan WhatsApp berisi tiga blok: Data Orang Tua, Data Anak, Pilihan Kelas.

## 8. Kebutuhan desain

**Warna**

| Nama | Kode | Penggunaan |
| --- | --- | --- |
| Navy | `#0A2C5E` | Teks utama, kartu, footer |
| Biru | `#1E88E0` | Tombol, aksen, kartu Kelas Khusus |
| Emas | `#C9A227` | Judul aksen, lingkaran, gelombang |
| Putih | `#FFFFFF` | Latar |

**Huruf:** Barlow Condensed (judul besar), Plus Jakarta Sans (teks dan menu), DM Sans (keterangan kecil), Fraunces Italic (kata penekanan seperti *membantu*, *terbaikmu*).

**Elemen visual:** lingkaran berwarna dan garis gelombang sebagai dekorasi. Dekorasi tidak boleh menutupi teks.

## 9. Kebutuhan teknis

- Laravel 11/12, PHP 8.2+, tanpa database, tanpa Vite (CSS dan JS dimuat dari `public/`).
- Semua isi yang berubah (harga, lokasi, kontak, testimoni, galeri) ada di `config/leo.php`.
- Routing: `GET /`, `GET /daftar`, `POST /daftar`.
- Validasi form di sisi server dan perlindungan CSRF bawaan Laravel.
- Hosting: perlu PHP 8.2+. Pilihan: shared hosting dengan cPanel, VPS, atau layanan cloud. Domain belum ditentukan.

## 10. Responsif

| Lebar layar | Perilaku |
| --- | --- |
| Di atas 1024 px (desktop) | Susunan seperti desain Figma |
| 769–1024 px (tablet) | Lokasi dan testimoni 2 kolom, dekorasi mengecil |
| 768 px ke bawah (HP) | Menu hamburger, semua bagian menumpuk 1 kolom, galeri 2 kolom, testimoni digeser, form 1 kolom, tombol penuh lebar |
| 480 px ke bawah (HP kecil) | Footer 1 kolom, huruf judul lebih kecil |

Teks input di HP minimal 16 px agar iPhone tidak melakukan zoom otomatis. Target sentuh minimal 44 px. Tidak boleh ada scroll ke samping pada halaman.

## 11. Non-fungsional

- **Kecepatan:** gambar dikompres (foto maksimal sekitar 200 KB, format WebP atau JPG), `loading="lazy"` untuk galeri.
- **Aksesibilitas:** teks alternatif pada gambar, kontras warna cukup, form bisa dipakai dengan keyboard.
- **SEO:** judul dan deskripsi halaman, tag bahasa `id`, nama dan alamat kolam jelas.
- **Privasi:** data form tidak disimpan di server. Foto anak dan testimoni hanya ditampilkan dengan izin orang tua.

## 12. Konten yang masih dibutuhkan

| Kebutuhan | Pemilik | Status |
| --- | --- | --- |
| Logo (PNG transparan) | Pak Leo / Figma | Perlu diekspor |
| Foto hero perenang | Pak Leo / Figma | Perlu diekspor |
| Foto Pak Leo | Pak Leo | Perlu diekspor |
| 7 foto galeri asli | Pak Leo | Belum ada |
| Testimoni asli (sudah izin) | Pak Leo | Belum ada |
| Nomor WhatsApp kedua | Pak Leo | Kosong (`______` di desain) |
| Desain tampilan HP | Figma | Perlu dikirim agar cocok |

## 13. Pertanyaan terbuka

1. Apakah "Kelas Khusus" hanya untuk SMK Negeri 1 Surabaya, atau dibuka untuk sekolah lain?
2. Apakah 6× dan 8× pertemuan juga tersedia di Kelas Khusus? (Harga hanya 4× yang tercantum, tetapi daftar fitur menyebut 4, 6, atau 8.)
3. Apakah ejaan "Jl. Kesatriyan" sudah benar?
4. Apakah jadwal hanya Senin–Jumat? Bagaimana Sabtu dan Minggu?
5. Apakah foto Pak Leo dan murid sudah ada izin untuk dipublikasikan?
6. Domain apa yang akan dipakai, dan di mana hostingnya?
7. Apakah ingin memasang Google Analytics atau Meta Pixel untuk mengukur pendaftar?

## 14. Risiko

| Risiko | Dampak | Penanganan |
| --- | --- | --- |
| Foto belum tersedia saat rilis | Galeri tampak kosong | Sembunyikan galeri sementara atau pakai foto kolam |
| Testimoni masih placeholder | Terlihat tidak meyakinkan | Sembunyikan bagian testimoni sampai ada yang asli |
| Pendaftaran hanya lewat WhatsApp | Tidak ada arsip data | Pesan WhatsApp jadi catatan. Tambah database di versi 2 bila perlu |
| Harga berubah | Teks lama masih tampil | Ubah di `config/leo.php`, satu tempat |

## 15. Tahapan kerja

| Tahap | Isi | Status |
| --- | --- | --- |
| 1. Desain desktop | Figma | Selesai |
| 2. Kode awal | Laravel, semua halaman, responsif perkiraan | Selesai |
| 3. Isi aset | Logo dan foto ke `public/images/` | Menunggu |
| 4. Desain HP | Cocokkan dengan Figma HP | Menunggu screenshot |
| 5. Isi konten asli | Testimoni, foto galeri, nomor kedua | Menunggu |
| 6. Uji | Chrome, Safari iPhone, HP Android, form WhatsApp | Belum |
| 7. Rilis | Domain dan hosting | Belum |
