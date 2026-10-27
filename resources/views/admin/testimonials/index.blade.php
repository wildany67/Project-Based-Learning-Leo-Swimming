@extends('admin.layouts.admin')

@section('title', 'Testimoni')

@section('content')
    <div class="page-head">
        <div>
            <h2>Testimoni</h2>
            <p>Hanya testimoni berstatus <strong>Tayang</strong> yang muncul di website.</p>
        </div>
        <a href="{{ route('admin.testimonials.create') }}" class="btn btn--primary"><x-admin.icon name="plus" :size="18" /> Tambah testimoni</a>
    </div>

    <div class="note">
        <x-admin.icon name="quote" :size="18" />
        <p>Pakai testimoni asli dan pastikan orangnya sudah mengizinkan namanya ditampilkan. Kalau tidak ada yang tayang, bagian testimoni disembunyikan dari website.</p>
    </div>

    @if (count($testimonials))
        <div class="testi-list">
            @foreach ($testimonials as $t)
                <article class="testi">
                    <header>
                        <span class="avatar avatar--gold">{{ $t->initial }}</span>
                        <div>
                            <strong>{{ $t->name }}</strong>
                            <small>{{ $t->program ?: 'Program tidak diisi' }}</small>
                        </div>
                        <span class="badge {{ $t->is_published ? 'badge--aktif' : 'badge--batal' }}">{{ $t->is_published ? 'Tayang' : 'Draf' }}</span>
                    </header>

                    <p class="testi-quote">“{{ mb_strimwidth($t->quote, 0, 180, '…') }}”</p>

                    <footer>
                        <small>Urutan {{ $t->sort_order }}</small>
                        <div class="row-actions">
                            <a href="{{ route('admin.testimonials.edit', $t) }}" class="icon-btn" title="Ubah" aria-label="Ubah testimoni {{ $t->name }}"><x-admin.icon name="edit" :size="18" /></a>
                            <form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}" data-confirm="Hapus testimoni dari {{ $t->name }}?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="icon-btn icon-btn--danger" title="Hapus" aria-label="Hapus testimoni {{ $t->name }}"><x-admin.icon name="trash" :size="18" /></button>
                            </form>
                        </div>
                    </footer>
                </article>
            @endforeach
        </div>
    @else
        <div class="card empty empty--page">
            <x-admin.icon name="quote" :size="32" />
            <p>Belum ada testimoni.</p>
            <small>Tambahkan testimoni asli dari orang tua atau murid.</small>
        </div>
    @endif
@endsection
