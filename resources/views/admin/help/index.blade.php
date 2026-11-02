@extends('admin.layouts.admin')

@section('title', 'Bantuan')
@section('subtitle', 'Panduan langkah demi langkah')

@section('content')
    <div class="grid grid--help">

        <section class="card">
            <h2 class="card-title">Cara konfirmasi pendaftar yang sudah bayar</h2>
            <ol class="howto">
                <li><i>1</i><div><b>Buka menu Pendaftar</b><small>Klik tulisan "Pendaftar" di kiri layar.</small></div></li>
                <li><i>2</i><div><b>Pilih nama anak</b><small>Klik tombol "Lihat" di baris nama anak.</small></div></li>
                <li><i>3</i><div><b>Cek bukti transfer di WhatsApp</b><small>Tekan tombol hijau, lalu centang kotak "Bukti transfer sudah saya cek".</small></div></li>
                <li><i>4</i><div><b>Tekan tombol kuning</b><small>"Sudah bayar: Tandai Terkonfirmasi". Selesai! Slot jadwal otomatis terisi.</small></div></li>
            </ol>
        </section>

        <section class="card">
            <h2 class="card-title">Arti istilah di panel ini</h2>
            <dl class="terms">
                <div><dt>Slot</dt><dd>Satu jam latihan di satu kolam. Contoh: "Araya 2/3" artinya 2 dari 3 tempat sudah terisi.</dd></div>
                <div><dt>Menunggu</dt><dd>Anak sudah mendaftar, tapi belum bayar.</dd></div>
                <div><dt>Terkonfirmasi</dt><dd>Sudah bayar dan jadwal sudah pasti. Anak menjadi murid aktif.</dd></div>
                <div><dt>Selesai</dt><dd>Semua pertemuan paketnya sudah dipakai.</dd></div>
                <div><dt>Dibatalkan</dt><dd>Pendaftaran tidak jadi. Slot jadwal tidak terisi.</dd></div>
            </dl>
        </section>

        <section class="card">
            <h2 class="card-title">Cara mengubah harga</h2>
            <ol class="howto">
                <li><i>1</i><div><b>Buka Paket dan Harga</b><small>Ubah angka di kotak harga.</small></div></li>
                <li><i>2</i><div><b>Lihat contoh di kanan</b><small>Tampilan di website langsung ikut berubah di situ.</small></div></li>
                <li><i>3</i><div><b>Tekan Simpan perubahan</b><small>Baru setelah itu harga di website berubah.</small></div></li>
            </ol>
        </section>

        <section class="card">
            <h2 class="card-title">Cara menutup kolam sementara</h2>
            <ol class="howto">
                <li><i>1</i><div><b>Buka menu Kolam</b><small>Cari kotak kolam yang ingin ditutup.</small></div></li>
                <li><i>2</i><div><b>Geser tombol di kanan atas kotak</b><small>Tulisannya berubah menjadi "Tutup sementara".</small></div></li>
                <li><i>3</i><div><b>Selesai</b><small>Kolam hilang dari website dan form daftar. Datanya tetap aman, geser lagi untuk membuka.</small></div></li>
            </ol>
        </section>

        <section class="card">
            <h2 class="card-title">Murid dan kehadiran</h2>
            <ol class="howto">
                <li><i>1</i><div><b>Atur jam latihan</b><small>Di detail pendaftar, buka "Atur hari dan jam latihan" supaya anak masuk ke slot yang benar.</small></div></li>
                <li><i>2</i><div><b>Catat hadir setiap pertemuan</b><small>Di menu Murid, tekan "Hadir" atau "Tidak" pada kotak "Catat kehadiran hari ini".</small></div></li>
                <li><i>3</i><div><b>Perpanjang paket</b><small>Kalau sisa tinggal 1, nama anak muncul di "Perlu perpanjang paket" dengan tombol pesan WhatsApp.</small></div></li>
            </ol>
        </section>

        <section class="card">
            <h2 class="card-title">Butuh bantuan lain?</h2>
            <p class="muted">Kalau ada yang tidak jalan atau lupa kata sandi, hubungi orang yang membantu mengelola website lewat WhatsApp. Sebutkan halaman yang sedang dibuka dan apa yang terjadi.</p>
        </section>
    </div>
@endsection
