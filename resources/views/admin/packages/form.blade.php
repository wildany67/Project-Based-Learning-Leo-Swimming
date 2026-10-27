@extends('admin.layouts.admin')

@section('title', $package->exists ? 'Ubah paket' : 'Tambah paket')

@section('content')
    <a href="{{ route('admin.packages.index') }}" class="back-link"><x-admin.icon name="back" :size="16" /> Kembali ke daftar paket</a>

    <div class="page-head">
        <div>
            <h2>{{ $package->exists ? 'Ubah paket' : 'Tambah paket' }}</h2>
            <p>Perubahan langsung tampil di website setelah disimpan.</p>
        </div>
    </div>

    <form method="POST" action="{{ $package->exists ? route('admin.packages.update', $package) : route('admin.packages.store') }}" class="card form form--wide" novalidate>
        @csrf
        @if ($package->exists)
            @method('PUT')
        @endif

        @if ($errors->any())
            <div class="flash flash--error" role="alert">
                <x-admin.icon name="close" :size="18" />
                <span>Ada isian yang belum benar. Cek kolom yang bertanda merah.</span>
            </div>
        @endif

        <h3 class="form-section">Informasi paket</h3>
        <div class="form-grid">
            <x-admin.field label="Nama paket" name="name" :value="old('name', $package->name)" placeholder="Kelas Privat/Grup" :required="true" :maxlength="100" />
            <x-admin.field label="Kode paket" name="slug" :value="old('slug', $package->slug)" placeholder="privat" :required="true" :maxlength="40"
                hint="Huruf kecil tanpa spasi. Dipakai di link pendaftaran, jangan sering diganti." />
            <x-admin.field label="Deskripsi singkat" name="tagline" :value="old('tagline', $package->tagline)" placeholder="Satu pelatih untuk satu sampai dua anak." :maxlength="150" />
            <x-admin.field label="Teks tebal (opsional)" name="highlight" :value="old('highlight', $package->highlight)" placeholder="SMK Negeri 1 Surabaya" :maxlength="100"
                hint="Tampil di bawah deskripsi, dicetak tebal." />
        </div>

        <h3 class="form-section">Harga</h3>
        <div class="form-grid">
            <x-admin.field label="Judul kotak harga" name="price_label" :value="old('price_label', $package->price_label)" placeholder="Biaya per paket" :required="true" :maxlength="60" />
            <x-admin.field label="Tulisan tombol" name="cta" :value="old('cta', $package->cta)" placeholder="Daftar Kelas Privat/Grup" :required="true" :maxlength="60" />
        </div>
        <x-admin.textarea label="Daftar harga" name="prices_text" :value="old('prices_text', $package->prices_text)" :rows="5" :mono="true" :required="true"
            placeholder="4× pertemuan | Rp300.000"
            hint="Satu baris = satu harga. Format: nama | harga | catatan (catatan boleh kosong). Contoh: 8× pertemuan | Rp600.000 | +1× gratis" />
        <x-admin.textarea label="Keunggulan" name="features_text" :value="old('features_text', $package->features_text)" :rows="5"
            placeholder="Jadwal bisa dipilih"
            hint="Satu baris = satu poin ceklis di kartu paket." />

        <h3 class="form-section">Tampilan</h3>
        <div class="form-grid">
            <x-admin.select label="Warna kartu" name="theme" :options="['navy' => 'Biru tua', 'blue' => 'Biru terang']" :value="old('theme', $package->theme)" :required="true" />
            <x-admin.field label="Angka besar samar" name="ghost" :value="old('ghost', $package->ghost)" placeholder="1+" :maxlength="8"
                hint="Hiasan di pojok kanan atas kartu. Boleh kosong." />
            <x-admin.field label="Urutan" name="sort_order" type="number" :value="old('sort_order', $package->sort_order)" hint="Angka kecil tampil lebih dulu." />
        </div>
        <x-admin.switch name="is_active" label="Tampilkan di website" :checked="old() ? (bool) old('is_active') : $package->is_active" />

        <div class="form-actions">
            <a href="{{ route('admin.packages.index') }}" class="btn btn--ghost">Batal</a>
            <button type="submit" class="btn btn--primary">Simpan paket</button>
        </div>
    </form>
@endsection
