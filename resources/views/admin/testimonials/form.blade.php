@extends('admin.layouts.admin')

@section('title', $testimonial->exists ? 'Ubah testimoni' : 'Tambah testimoni')

@section('content')
    <a href="{{ route('admin.testimonials.index') }}" class="back-link"><x-admin.icon name="back" :size="16" /> Kembali ke daftar testimoni</a>

    <div class="page-head">
        <div>
            <h2>{{ $testimonial->exists ? 'Ubah testimoni' : 'Tambah testimoni' }}</h2>
            <p>Aktifkan saklar "Tayang" kalau sudah siap ditampilkan di website.</p>
        </div>
    </div>

    <form method="POST" action="{{ $testimonial->exists ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}" class="card form form--narrow" novalidate>
        @csrf
        @if ($testimonial->exists)
            @method('PUT')
        @endif

        <x-admin.textarea label="Isi testimoni" name="quote" :value="old('quote', $testimonial->quote)" :rows="5" :required="true"
            placeholder="Ceritakan pengalaman belajar renang bersama Pak Leo."
            hint="Maksimal 600 huruf. Gunakan kata-kata asli dari orangnya." />

        <div class="form-grid">
            <x-admin.field label="Nama" name="name" :value="old('name', $testimonial->name)" placeholder="Ibu Sari" :required="true" :maxlength="100"
                hint="Inisial avatar dibuat otomatis dari nama." />
            <x-admin.field label="Program yang diikuti" name="program" :value="old('program', $testimonial->program)" placeholder="Kelas Privat 8× pertemuan" :maxlength="100" />
        </div>

        <x-admin.field label="Urutan" name="sort_order" type="number" :value="old('sort_order', $testimonial->sort_order)" hint="Angka kecil tampil lebih dulu." />
        <x-admin.switch name="is_published" label="Tayang di website" :checked="old() ? (bool) old('is_published') : $testimonial->is_published"
            hint="Pastikan yang bersangkutan sudah mengizinkan testimoninya ditampilkan." />

        <div class="form-actions">
            <a href="{{ route('admin.testimonials.index') }}" class="btn btn--ghost">Batal</a>
            <button type="submit" class="btn btn--primary">Simpan testimoni</button>
        </div>
    </form>
@endsection
