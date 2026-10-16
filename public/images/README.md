# Folder public/images

Gambar yang **tampil di website**. Wajib sudah dikompres (foto maks. sekitar 200 KB).
Gunakan huruf kecil dan tanda hubung pada nama file, tanpa spasi: `anak-mengapung.jpg`.

| Folder | Nama file yang dipakai kode | Keterangan |
| --- | --- | --- |
| `brand/` | `logo.png` | Logo untuk navbar dan footer (PNG transparan, lebar sekitar 300 px) |
| `hero/` | `hero.jpg` | Foto latar bagian paling atas (lebar sekitar 1920 px) |
| `profil/` | `leo.jpg` | Foto Pak Leo, rasio 3:4 (sekitar 900 × 1200 px) |
| `gallery/` | bebas | Foto galeri. Daftarkan nama filenya di `config/leo.php` bagian `gallery` → `img` |
| `decor/` | opsional | Elemen dekorasi tambahan (lingkaran, gelombang) bila nanti dipindah dari CSS ke gambar |
| `icons/` | opsional | Ikon SVG (pin lokasi, centang, dll.) |

Kode saat ini membaca:

- `images/brand/logo.png`
- `images/hero/hero.jpg`
- `images/profil/leo.jpg`
- `images/gallery/<nama-file>`
