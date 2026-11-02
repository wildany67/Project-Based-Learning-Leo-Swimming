@extends('admin.layouts.admin')

@section('title', 'Konten Website')
@section('subtitle', 'Ubah tulisan dan foto di website')

@section('content')
    <p class="banner">
        <x-admin.icon name="warning" :size="18" />
        <span>Di sini Bapak mengubah tulisan dan foto yang tampil di website. Pilih bagian di kiri, ubah di kanan. <b>Jangan khawatir salah:</b> foto dan testimoni yang disembunyikan tidak tampil di website.</span>
    </p>

    <div class="grid grid--content">

        <nav class="card card--tight content-tabs" aria-label="Bagian konten">
            <a href="{{ route('admin.content.index', ['tab' => 'galeri']) }}" class="content-tab {{ $tab === 'galeri' ? 'is-active' : '' }}">
                <x-admin.icon name="image" :size="18" />
                <span><b>Galeri foto</b><small>{{ count($gallery) }} foto</small></span>
            </a>
            <a href="{{ route('admin.content.index', ['tab' => 'testimoni']) }}" class="content-tab {{ $tab === 'testimoni' ? 'is-active' : '' }}">
                <x-admin.icon name="quote" :size="18" />
                <span><b>Testimoni</b><small>{{ count($testimonials) }} cerita</small></span>
                @if ($pending)
                    <em class="tag-low">Belum izin {{ $pending }}</em>
                @endif
            </a>
        </nav>

        <div class="stack">
            @if ($tab === 'galeri')
                <section class="card">
                    <header class="card-head">
                        <div>
                            <h2 class="card-title">Galeri foto</h2>
                            <p class="muted">Foto paling bagus tampil di bagian pertama. Disarankan 6–7 foto supaya tampilan di website rapi.</p>
                        </div>
                    </header>

                    <div class="photo-grid">
                        @foreach ($gallery as $item)
                            <article class="photo {{ $item->is_active ? '' : 'is-hidden' }}">
                                <div class="photo-img photo-img--{{ $item->tone }}">
                                    @if ($item->image_url)
                                        <img src="{{ $item->image_url }}" alt="{{ $item->caption }}" loading="lazy">
                                    @else
                                        <span><x-admin.icon name="camera" :size="24" /> Belum ada foto</span>
                                    @endif
                                    <button type="button" class="photo-edit" data-modal-open="foto-{{ $item->id }}">Ganti</button>
                                </div>
                                <p>{{ $item->caption }}@unless ($item->is_active) <em class="tag-low">Disembunyikan</em>@endunless</p>
                            </article>
                        @endforeach

                        <button type="button" class="photo photo--add" data-modal-open="foto-baru">
                            <x-admin.icon name="plus" :size="22" />
                            <span>Tambah foto</span>
                        </button>
                    </div>
                </section>
            @else
                <section class="card">
                    <header class="card-head">
                        <div>
                            <h2 class="card-title">Testimoni</h2>
                            <p class="muted">Tulis kata-kata orang tua murid. Hanya yang sudah memberi izin yang akan ditampilkan.</p>
                        </div>
                        <button type="button" class="btn btn--gold btn--sm" data-modal-open="testi-baru"><x-admin.icon name="plus" :size="16" /> Tambah testimoni</button>
                    </header>

                    @forelse ($testimonials as $t)
                        <article class="testi">
                            <span class="testi-avatar">{{ $t->initial }}</span>
                            <div class="testi-body">
                                <p>“{{ $t->quote }}”</p>
                                <small><b>{{ $t->name }}</b>@if ($t->program) · {{ $t->program }}@endif</small>
                            </div>
                            <div class="testi-side">
                                <span class="badge {{ $t->is_published ? 'badge--ok' : 'badge--warn' }}">{{ $t->is_published ? 'Sudah izin' : 'Belum izin' }}</span>
                                <button type="button" class="btn btn--outline btn--xs" data-modal-open="testi-{{ $t->id }}">Ubah</button>
                            </div>
                        </article>
                    @empty
                        <div class="empty empty--big">
                            <strong>Belum ada testimoni.</strong>
                            <p>Bagian testimoni di website baru muncul setelah ada testimoni yang sudah izin tampil.</p>
                        </div>
                    @endforelse
                </section>
            @endif
        </div>
    </div>

    {{-- Jendela foto --}}
    @foreach ($gallery as $item)
        @include('admin.content._photo-modal', ['item' => $item])
    @endforeach
    @include('admin.content._photo-modal', ['item' => null])

    {{-- Jendela testimoni --}}
    @foreach ($testimonials as $t)
        @include('admin.content._testi-modal', ['t' => $t])
    @endforeach
    @include('admin.content._testi-modal', ['t' => null])
@endsection
