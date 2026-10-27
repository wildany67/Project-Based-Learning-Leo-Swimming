@extends('admin.layouts.admin')

@section('title', 'Galeri foto')

@section('content')
    <div class="page-head">
        <div>
            <h2>Galeri foto</h2>
            <p>Foto tampil di bagian "Momen seru di kolam" sesuai urutan dan ukuran yang dipilih.</p>
        </div>
        <a href="{{ route('admin.gallery.create') }}" class="btn btn--primary"><x-admin.icon name="plus" :size="18" /> Tambah foto</a>
    </div>

    @if (count($items))
        <div class="photo-grid">
            @foreach ($items as $item)
                <article class="photo {{ $item->is_active ? '' : 'is-off' }}">
                    <div class="photo-thumb photo-thumb--{{ $item->tone }}">
                        @if ($item->image_url)
                            <img src="{{ $item->image_url }}" alt="{{ $item->caption }}" loading="lazy">
                        @else
                            <span class="photo-empty"><x-admin.icon name="image" :size="26" /> Belum ada foto</span>
                        @endif
                        <span class="photo-chip">{{ \App\Models\GalleryItem::SIZES[$item->size] ?? $item->size }}</span>
                    </div>
                    <div class="photo-body">
                        <p class="photo-caption">{{ $item->caption }}</p>
                        <div class="photo-foot">
                            <span class="badge {{ $item->is_active ? 'badge--aktif' : 'badge--batal' }}">{{ $item->is_active ? 'Tayang' : 'Disembunyikan' }}</span>
                            <div class="row-actions">
                                <a href="{{ route('admin.gallery.edit', $item) }}" class="icon-btn" title="Ubah" aria-label="Ubah foto {{ $item->caption }}"><x-admin.icon name="edit" :size="18" /></a>
                                <form method="POST" action="{{ route('admin.gallery.destroy', $item) }}" data-confirm="Hapus foto ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn icon-btn--danger" title="Hapus" aria-label="Hapus foto {{ $item->caption }}"><x-admin.icon name="trash" :size="18" /></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="card empty empty--page">
            <x-admin.icon name="image" :size="32" />
            <p>Belum ada foto galeri.</p>
            <small>Tambahkan foto pertama, atau jalankan <code>php artisan db:seed --class=ContentSeeder</code>.</small>
        </div>
    @endif
@endsection
