@extends('admin.layouts.admin')

@section('title', $location->exists ? 'Ubah lokasi' : 'Tambah lokasi')

@section('content')
    <a href="{{ route('admin.locations.index') }}" class="back-link"><x-admin.icon name="back" :size="16" /> Kembali ke daftar lokasi</a>

    <div class="page-head">
        <div>
            <h2>{{ $location->exists ? 'Ubah lokasi' : 'Tambah lokasi' }}</h2>
            <p>Perubahan langsung tampil di website setelah disimpan.</p>
        </div>
    </div>

    <form method="POST" action="{{ $location->exists ? route('admin.locations.update', $location) : route('admin.locations.store') }}" class="card form form--narrow" novalidate>
        @csrf
        @if ($location->exists)
            @method('PUT')
        @endif

        <div class="form-grid">
            <x-admin.field label="Wilayah" name="area" :value="old('area', $location->area)" placeholder="Surabaya Timur" :required="true" :maxlength="80" />
            <x-admin.field label="Nama kolam" name="pool" :value="old('pool', $location->pool)" placeholder="Kolam Renang Nirwana Eksekutif" :required="true" :maxlength="150" />
        </div>
        <x-admin.field label="Alamat" name="address" :value="old('address', $location->address)" placeholder="Jl. Raya Wonorejo Permai, Wonorejo, Kec. Rungkut" :required="true" :maxlength="250" />
        <x-admin.field label="Link Google Maps (opsional)" name="maps_url" type="url" :value="old('maps_url', $location->maps_url)" placeholder="https://maps.app.goo.gl/..."
            hint="Kalau kosong, tombol Lihat kolam mencari nama kolam di Google Maps." />
        <x-admin.field label="Urutan" name="sort_order" type="number" :value="old('sort_order', $location->sort_order)" hint="Angka kecil tampil lebih dulu." />
        <x-admin.switch name="is_active" label="Tampilkan di website" :checked="old() ? (bool) old('is_active') : $location->is_active" />

        <div class="form-actions">
            <a href="{{ route('admin.locations.index') }}" class="btn btn--ghost">Batal</a>
            <button type="submit" class="btn btn--primary">Simpan lokasi</button>
        </div>
    </form>
@endsection
