@extends('admin.layouts.admin')

@section('title', $item->exists ? 'Ubah foto' : 'Tambah foto')

@section('content')
    <a href="{{ route('admin.gallery.index') }}" class="back-link"><x-admin.icon name="back" :size="16" /> Kembali ke galeri</a>

    <div class="page-head">
        <div>
            <h2>{{ $item->exists ? 'Ubah foto' : 'Tambah foto' }}</h2>
            <p>Foto JPG, PNG, atau WEBP, maksimal 4 MB.</p>
        </div>
    </div>

    <form method="POST" action="{{ $item->exists ? route('admin.gallery.update', $item) : route('admin.gallery.store') }}" enctype="multipart/form-data" class="card form form--narrow" novalidate>
        @csrf
        @if ($item->exists)
            @method('PUT')
        @endif

        <div class="field {{ $errors->has('image') ? 'field--error' : '' }}">
            <label for="f-image">Foto@if (! $item->exists)<em>*</em>@endif</label>

            <div class="upload" data-upload>
                <div class="upload-preview" data-upload-preview>
                    @if ($item->image_url)
                        <img src="{{ $item->image_url }}" alt="Foto saat ini">
                    @else
                        <span><x-admin.icon name="image" :size="28" /> Pratinjau foto</span>
                    @endif
                </div>
                <input id="f-image" type="file" name="image" accept="image/jpeg,image/png,image/webp" data-upload-input>
            </div>

            <p class="field-hint">
                @if ($item->exists)
                    Kosongkan kalau tidak ingin mengganti foto.
                @else
                    Tips: kecilkan dulu fotonya (sekitar 200 KB) supaya website cepat dibuka.
                @endif
            </p>
            @if ($errors->has('image'))
                <p class="field-error">{{ $errors->first('image') }}</p>
            @endif
        </div>

        <x-admin.field label="Keterangan" name="caption" :value="old('caption', $item->caption)" placeholder="Anak belajar mengapung dibantu Pak Leo" :required="true" :maxlength="150" />

        <div class="form-grid">
            <x-admin.select label="Ukuran di galeri" name="size" :options="\App\Models\GalleryItem::SIZES" :value="old('size', $item->size)" :required="true"
                hint="Besar dan Tinggi memakai 2 baris." />
            <x-admin.select label="Warna cadangan" name="tone" :options="\App\Models\GalleryItem::TONES" :value="old('tone', $item->tone)" :required="true"
                hint="Dipakai kalau foto tidak bisa dimuat." />
        </div>

        <x-admin.field label="Urutan" name="sort_order" type="number" :value="old('sort_order', $item->sort_order)" hint="Angka kecil tampil lebih dulu." />
        <x-admin.switch name="is_active" label="Tampilkan di website" :checked="old() ? (bool) old('is_active') : $item->is_active" />

        <div class="form-actions">
            <a href="{{ route('admin.gallery.index') }}" class="btn btn--ghost">Batal</a>
            <button type="submit" class="btn btn--primary">Simpan foto</button>
        </div>
    </form>
@endsection
