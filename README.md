# Leo Swimming Course

Website les renang privat dan grup bersama Coach Leo Aswandany, S.Pd di Surabaya.
Memperkenalkan Pak Leo, menampilkan paket dan harga, lokasi kolam, lalu mengarahkan orang tua mendaftar lewat WhatsApp.

- **Teknologi:** Laravel (PHP 8.2+), Blade, CSS dan JavaScript biasa
- **Database:** tidak ada
- **Desain:** Figma (desktop selesai, HP menyusul)
- **Dokumen kebutuhan:** [`docs/PRD.md`](docs/PRD.md)

## Halaman

| URL | Isi |
| --- | --- |
| `/` | Beranda satu halaman: Hero, Profil, Galeri, Testimoni, Program dan Harga, Lokasi, Kontak |
| `/daftar` | Form pendaftaran. Data dikirim ke WhatsApp Pak Leo |

## Kebutuhan

- PHP 8.2 atau lebih baru
- Composer
- VS Code (disarankan ekstensi **Laravel Blade Snippets** dan **PHP Intelephense**)

Tidak perlu Node.js atau `npm install`. CSS dan JS dimuat langsung dari `public/`.

## Instalasi

### A. Dari file zip (project Laravel belum ada)

```bash
composer create-project laravel/laravel leo-swimming
cd leo-swimming
```

Salin isi zip ke dalam project itu, lalu timpa `routes/web.php` kalau ditanya.
Salin folder `app`, `config`, `public`, `resources`, `routes`, `docs`, `assets-source`.
Jangan menimpa file `README.md` Laravel kalau kamu ingin menyimpannya.

### B. Dari repository Git (project sudah lengkap)

```bash
git clone <url-repo> leo-swimming
cd leo-swimming
composer install
cp .env.example .env
php artisan key:generate
```

### Menjalankan

```bash
php artisan serve
```

Buka http://127.0.0.1:8000. Setelah file disimpan, cukup refresh browser.
Jika port 8000 terpakai: `php artisan serve --port=8001`.

## Struktur folder

```
leo-swimming/
├── app/Http/Controllers/PageController.php   halaman dan pengiriman form
├── config/leo.php                            isi yang sering berubah
├── routes/web.php                            daftar URL
├── resources/views/
│   ├── layouts/app.blade.php                 kerangka halaman
│   ├── partials/                             navbar, footer
│   ├── sections/                             hero, profil, galeri, testimoni, program, lokasi, kontak
│   ├── components/                           <x-dot> dan <x-wave> (lingkaran dan gelombang dekorasi)
│   ├── home.blade.php                        beranda
│   └── daftar.blade.php                      form daftar
├── public/
│   ├── css/                                  satu file CSS per bagian
│   │   ├── base.css        warna, reset, tombol, dekorasi, judul section
│   │   ├── layout.css      navbar dan footer
│   │   ├── hero.css  profil.css  galeri.css  testimoni.css
│   │   ├── program.css  lokasi.css  kontak.css  daftar.css
│   │   └── responsive.css  aturan tablet dan HP (dimuat paling akhir)
│   ├── js/app.js                             menu HP dan penanda menu aktif
│   └── images/                               gambar yang tampil di website
│       ├── brand/      logo.png
│       ├── hero/       hero.jpg
│       ├── profil/     leo.jpg
│       ├── gallery/    foto galeri
│       ├── decor/      elemen dekorasi (opsional)
│       └── icons/      ikon SVG (opsional)
├── assets-source/                            file asli resolusi tinggi (tidak tampil di website)
│   ├── figma-export/  foto-asli/  logo-asli/
└── docs/                                     dokumen proyek
    ├── PRD.md
    └── design/  content/  legal/  reference/
```

## Gambar

Taruh gambar siap tayang di `public/images/` dengan nama persis seperti ini:

| File | Dipakai di | Ukuran saran |
| --- | --- | --- |
| `brand/logo.png` | Navbar dan footer | PNG transparan, lebar sekitar 300 px |
| `hero/hero.jpg` | Latar Hero | Lebar sekitar 1920 px |
| `profil/leo.jpg` | Foto Pak Leo | Rasio 3:4, sekitar 900 × 1200 px |
| `gallery/<nama>.jpg` | Galeri | Maks. sekitar 200 KB per foto |

Foto galeri didaftarkan di `config/leo.php`, bagian `gallery`, kolom `img`.
Kalau gambar belum ada, Hero tampil gradasi biru dan galeri tampil blok warna.
File asli resolusi tinggi disimpan di `assets-source/`, bukan di `public/`.

Detail lengkap ada di [`public/images/README.md`](public/images/README.md).

## Mengubah isi

Semua teks yang sering berubah ada di **`config/leo.php`**. Tidak perlu membuka Blade.

| Mau ubah | Kunci |
| --- | --- |
| Nomor WhatsApp | `whatsapp` (format `62831...`, tanpa `+` dan tanpa 0 di depan) dan `whatsapp_display` |
| WhatsApp kedua | `whatsapp_second` (kosong = tidak tampil di footer) |
| Instagram | `instagram` |
| Paket dan harga | `packages` |
| Pilihan paket di form | `package_options` |
| Kolam dan alamat | `locations` |
| Hari latihan di form | `days` |
| Foto dan caption galeri | `gallery` |
| Testimoni | `testimonials` |

Warna ada di bagian paling atas `public/css/base.css` (`--navy`, `--blue`, `--gold`).
Ingin mengubah satu bagian saja? Buka CSS bagian itu, misalnya `public/css/hero.css` untuk Hero.

Lingkaran dan gelombang dekorasi dipasang dengan komponen, bukan CSS inline:

```blade
<x-dot color="gold" :size="56" pos="left:46%;top:60px" />
<x-wave color="blue" pos="right:0;top:330px;width:22%" />
```

## Form daftar

1. Orang tua mengisi form di `/daftar`.
2. Server memvalidasi data (nama, nomor HP, anak, paket, lokasi, tanggal mulai).
3. Jika benar, browser dialihkan ke `wa.me` dengan pesan yang sudah terisi.
4. Jika ada yang salah, form tampil kembali dengan pesan kesalahan dan isian tetap terjaga.

Tombol "Daftar" di kartu harga membuka form dengan paket yang sudah terpilih (`/daftar?paket=privat-4`).
Tidak ada data yang disimpan di server.

## Responsif

Breakpoint ada di `public/css/responsive.css`:

| Lebar layar | Perilaku |
| --- | --- |
| > 1024 px | Desktop, seperti desain Figma |
| ≤ 1024 px | Tablet: lokasi dan testimoni 2 kolom |
| ≤ 768 px | HP: menu hamburger, semua menumpuk, galeri 2 kolom, testimoni digeser, form 1 kolom |
| ≤ 480 px | HP kecil: footer 1 kolom |

Cara cek di Chrome: tekan **F12**, lalu klik ikon HP di pojok kiri atas panel (Toggle device toolbar).

## Catatan Git

Jika memakai Git, tambahkan ke `.gitignore`:

```
/assets-source/foto-asli
/docs/legal
```

Foto asli berukuran besar, dan folder `legal` berisi surat izin dari orang tua murid.

## Pengerjaan berikutnya

- [ ] Ekspor logo, foto hero, dan foto Pak Leo dari Figma
- [ ] Foto galeri asli
- [ ] Testimoni asli (sudah mendapat izin)
- [ ] Nomor WhatsApp kedua
- [ ] Cocokkan tampilan HP dengan desain Figma HP
- [ ] Uji di iPhone dan HP Android
- [ ] Domain dan hosting

Daftar pertanyaan terbuka ada di bagian 13 [`docs/PRD.md`](docs/PRD.md).
