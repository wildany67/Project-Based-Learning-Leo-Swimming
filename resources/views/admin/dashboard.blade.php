@extends('admin.layouts.admin')

@section('title', $greeting . ', ' . auth()->user()->name)
@section('subtitle', now()->locale('id')->translatedFormat('l, j F Y'))

@section('content')

    @if ($waiting)
        <a href="{{ route('admin.registrations.index', ['status' => 'menunggu']) }}" class="banner banner--link">
            <x-admin.icon name="warning" :size="18" />
            <span><b>{{ $waiting }} pendaftar</b> menunggu konfirmasi. Ketuk untuk melihat dan menghubungi orang tuanya.</span>
            <x-admin.icon name="chevron" :size="16" />
        </a>
    @endif

    {{-- Angka ringkas --}}
    <section class="stats" aria-label="Ringkasan">
        <article class="stat">
            <small>Murid aktif</small>
            <strong>{{ $stats['students'] }}</strong>
        </article>
        <article class="stat">
            <small>Pendaftar bulan ini</small>
            <strong>{{ $stats['month'] }}</strong>
        </article>
        <article class="stat">
            <small>Slot kosong minggu ini</small>
            <strong>{{ $stats['free'] }}</strong>
        </article>
        <article class="stat">
            <small>Terkonfirmasi bulan ini</small>
            <strong>{{ $stats['confirmed'] }}</strong>
        </article>
    </section>

    <div class="grid grid--home">
        {{-- Jadwal hari ini --}}
        <section class="card">
            <header class="card-head">
                <h2 class="card-title">Jadwal hari ini</h2>
                <a href="{{ route('admin.schedule.index') }}" class="link-arrow">Lihat jadwal <x-admin.icon name="chevron" :size="14" /></a>
            </header>

            @forelse ($today as $row)
                <div class="today-row">
                    <b>{{ $row['time'] }}</b>
                    <span>{{ $row['text'] }}</span>
                </div>
            @empty
                <p class="empty">Belum ada jadwal latihan hari ini. Jadwal terisi setelah pendaftar terkonfirmasi dan diberi jam latihan.</p>
            @endforelse
        </section>

        {{-- Pendaftar terbaru --}}
        <section class="card">
            <header class="card-head">
                <h2 class="card-title">Pendaftar terbaru</h2>
                <a href="{{ route('admin.registrations.index') }}" class="link-arrow">Semua <x-admin.icon name="chevron" :size="14" /></a>
            </header>

            @forelse ($latest as $r)
                <a href="{{ route('admin.registrations.show', $r) }}" class="latest-row">
                    <span class="latest-name">
                        <b>{{ $r->child_name }}</b>
                        <small>{{ $r->package_short }}</small>
                    </span>
                    <span class="badge badge--{{ $r->status }}">{{ $r->status_label }}</span>
                </a>
            @empty
                <p class="empty">Belum ada pendaftar. Pendaftar dari form website akan muncul di sini.</p>
            @endforelse
        </section>
    </div>

    <div class="quick-actions">
        <button type="button" class="btn btn--gold" data-modal-open="pendaftar-manual"><x-admin.icon name="plus" :size="16" /> Tambah pendaftar manual</button>
        <button type="button" class="btn btn--outline" data-copy="{{ route('daftar') }}" data-copied="Link tersalin">
            <x-admin.icon name="copy" :size="16" /> <span data-copy-label>Salin link pendaftaran</span>
        </button>
    </div>

    @include('admin.registrations._manual', ['form' => $form])
@endsection
