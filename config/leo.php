<?php

/*
 * Semua isi/teks yang sering berubah ada di sini (harga, lokasi, kontak, testimoni).
 * Ubah di file ini, tidak perlu menyentuh Blade.
 */

return [

    'whatsapp'         => '6283191737789',      // format internasional tanpa + dan tanpa 0 di depan
    'whatsapp_display' => '0831 9173 7789',
    'whatsapp_second'  => null,                 // isi kalau sudah ada nomor kedua, mis. '0812 3456 7890'
    'instagram'        => 'leo_aswandani',

    'packages' => [
        [
            'key'         => 'privat',
            'name'        => 'Kelas Privat/Grup',
            'tagline'     => 'Satu pelatih untuk satu sampai dua anak.',
            'highlight'   => null,
            'ghost'       => '1+',
            'theme'       => 'navy',
            'price_label' => 'Biaya per paket',
            'prices'      => [
                ['label' => '4× pertemuan', 'price' => 'Rp300.000', 'note' => null],
                ['label' => '6× pertemuan', 'price' => 'Rp450.000', 'note' => null],
                ['label' => '8× pertemuan', 'price' => 'Rp600.000', 'note' => '+1× gratis'],
            ],
            'features'    => [
                'Jadwal bisa dipilih',
                'Materi menyesuaikan anak',
                '4, 6, atau 8 pertemuan per bulan',
                'Durasi 90 menit',
            ],
            'cta'         => 'Daftar Kelas Privat/Grup',
            'cta_package' => 'privat-4',
        ],
        [
            'key'         => 'khusus',
            'name'        => 'Kelas Khusus',
            'tagline'     => 'Kelas khusus siswa/siswi',
            'highlight'   => 'SMK Negeri 1 Surabaya',
            'ghost'       => '5+',
            'theme'       => 'blue',
            'price_label' => 'Biaya per anak',
            'prices'      => [
                ['label' => '4× pertemuan', 'price' => 'Rp150.000', 'note' => null],
            ],
            'features'    => [
                'Murid min. 5 anak',
                'Biaya lebih terjangkau',
                '4, 6, atau 8 pertemuan per bulan',
                'Durasi 90 menit',
            ],
            'cta'         => 'Daftar Kelas Khusus',
            'cta_package' => 'khusus-4',
        ],
    ],

    // Pilihan paket di form daftar
    'package_options' => [
        'privat-4' => 'Privat/Grup · 4× pertemuan (Rp300.000)',
        'privat-6' => 'Privat/Grup · 6× pertemuan (Rp450.000)',
        'privat-8' => 'Privat/Grup · 8× pertemuan (Rp600.000, +1× gratis)',
        'khusus-4' => 'Kelas Khusus · 4× pertemuan (Rp150.000 / anak)',
    ],

    'locations' => [
        [
            'area'    => 'Surabaya Timur',
            'pool'    => 'Kolam Renang Nirwana Eksekutif',
            'address' => 'Jl. Raya Wonorejo Permai, Wonorejo, Kec. Rungkut',
        ],
        [
            'area'    => 'Surabaya Selatan',
            'pool'    => 'Kolam Kodam Brawijaya',
            'address' => 'Jl. Kesatriyan, Sawunggaling, Kec. Wonokromo',
        ],
        [
            'area'    => 'Surabaya Selatan',
            'pool'    => 'Kolam Jambangan',
            'address' => 'Karah, Kec. Jambangan',
        ],
    ],

    'days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],

    // 'img' => nama file di public/images/gallery/ (kosong = tampil blok warna)
    // 'size' => big | wide | tall | small  (menentukan bentuk di grid)
    'gallery' => [
        ['caption' => 'Foto asli: anak mengapung dibantu Pak Leo', 'size' => 'big',   'tone' => 'sky',  'img' => null],
        ['caption' => 'Foto: tos selesai latihan',                 'size' => 'small', 'tone' => 'sky',  'img' => null],
        ['caption' => 'Foto: kelas grup',                          'size' => 'small', 'tone' => 'sky',  'img' => null],
        ['caption' => 'Foto: kolam pagi hari',                     'size' => 'tall',  'tone' => 'navy', 'img' => null],
        ['caption' => 'Foto: peralatan',                           'size' => 'small', 'tone' => 'sky',  'img' => null],
        ['caption' => 'Foto: murid dengan sertifikat atau medali', 'size' => 'wide',  'tone' => 'gold', 'img' => null],
        ['caption' => 'Foto: peralatan',                           'size' => 'small', 'tone' => 'sky',  'img' => null],
    ],

    // Ganti placeholder dengan testimoni asli yang sudah mendapat izin ditampilkan.
    'testimonials' => [
        ['quote' => '[Masukkan testimoni asli tentang pengalaman belajar renang.]',      'name' => '[Nama peserta/wali]', 'program' => '[Program yang diikuti]', 'initial' => 'P1'],
        ['quote' => '[Ceritakan perubahan atau kemajuan yang benar-benar dirasakan peserta.]', 'name' => '[Nama peserta/wali]', 'program' => '[Program yang diikuti]', 'initial' => 'P2'],
        ['quote' => '[Gunakan testimoni yang sudah mendapat izin untuk ditampilkan.]',   'name' => '[Nama peserta/wali]', 'program' => '[Program yang diikuti]', 'initial' => 'P3'],
        ['quote' => '[Masukkan testimoni asli tentang pengalaman belajar renang.]',      'name' => '[Nama peserta/wali]', 'program' => '[Program yang diikuti]', 'initial' => 'P4'],
        ['quote' => '[Ceritakan perubahan atau kemajuan yang benar-benar dirasakan peserta.]', 'name' => '[Nama peserta/wali]', 'program' => '[Program yang diikuti]', 'initial' => 'P5'],
        ['quote' => '[Gunakan testimoni yang sudah mendapat izin untuk ditampilkan.]',   'name' => '[Nama peserta/wali]', 'program' => '[Program yang diikuti]', 'initial' => 'P6'],
    ],
];
