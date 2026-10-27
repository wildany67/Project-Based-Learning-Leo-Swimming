@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
    @php
        $hour  = (int) now()->format('G');
        $greet = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
        $statusTotal = max(1, $stats['total']);
    @endphp

    {{-- Sambutan --}}
    <section class="welcome">
        <div>
            <p class="welcome-eyebrow">{{ $greet }}</p>
            <h2>Halo, {{ auth()->user()->name }}</h2>
            <p class="welcome-text">
                @if ($stats['new'] > 0)
                    Ada <strong>{{ $stats['new'] }} pendaftar baru</strong> yang belum dihubungi.
                @else
                    Semua pendaftar sudah ditangani. Kerja bagus!
                @endif
            </p>
        </div>
        <div class="welcome-actions">
            <a href="{{ route('admin.registrations.index', ['status' => 'baru']) }}" class="btn btn--gold">
                <x-admin.icon name="users" :size="18" /> Lihat pendaftar baru
            </a>
            <a href="{{ route('home') }}" class="btn btn--light" target="_blank" rel="noopener">
                <x-admin.icon name="external" :size="18" /> Buka website
            </a>
        </div>
    </section>

    {{-- Angka penting --}}
    <section class="stats" aria-label="Ringkasan">
        <article class="stat">
            <span class="stat-icon stat-icon--blue"><x-admin.icon name="users" /></span>
            <p class="stat-label">Total pendaftar</p>
            <p class="stat-value">{{ $stats['total'] }}</p>
            <p class="stat-note">{{ $stats['thisWeek'] }} masuk minggu ini</p>
        </article>
        <article class="stat">
            <span class="stat-icon stat-icon--gold"><x-admin.icon name="calendar" /></span>
            <p class="stat-label">Bulan ini</p>
            <p class="stat-value">{{ $stats['thisMonth'] }}</p>
            <p class="stat-note">pendaftar sejak tanggal 1</p>
        </article>
        <article class="stat">
            <span class="stat-icon stat-icon--red"><x-admin.icon name="clock" /></span>
            <p class="stat-label">Perlu dihubungi</p>
            <p class="stat-value">{{ $stats['new'] }}</p>
            <p class="stat-note">berstatus Baru</p>
        </article>
        <article class="stat">
            <span class="stat-icon stat-icon--green"><x-admin.icon name="check" /></span>
            <p class="stat-label">Murid aktif</p>
            <p class="stat-value">{{ $stats['active'] }}</p>
            <p class="stat-note">sedang berlatih</p>
        </article>
    </section>

    {{-- Grafik + status --}}
    <section class="grid grid--chart">
        <article class="card">
            <header class="card-head">
                <div>
                    <h3>Pendaftar 14 hari terakhir</h3>
                    <p>{{ $chart['total'] }} pendaftar dalam dua minggu</p>
                </div>
            </header>

            <div class="bars" role="img" aria-label="Grafik pendaftar per hari">
                @foreach ($chart['days'] as $day)
                    <div class="bar {{ $day['today'] ? 'is-today' : '' }}" title="{{ $day['label'] }}: {{ $day['count'] }} pendaftar">
                        <span class="bar-val">{{ $day['count'] ?: '' }}</span>
                        <span class="bar-track"><i style="height: {{ $day['pct'] }}%"></i></span>
                        <span class="bar-day">{{ $day['day'] }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="card">
            <header class="card-head">
                <div>
                    <h3>Status pendaftar</h3>
                    <p>Dari semua pendaftar</p>
                </div>
            </header>

            <div class="segbar" aria-hidden="true">
                @foreach ($statusTotals as $key => $row)
                    @if ($row['total'] > 0)
                        <i class="seg seg--{{ $key }}" style="width: {{ round($row['total'] / $statusTotal * 100, 1) }}%"></i>
                    @endif
                @endforeach
            </div>

            <ul class="legend">
                @foreach ($statusTotals as $key => $row)
                    <li>
                        <span class="legend-dot seg--{{ $key }}"></span>
                        <a href="{{ route('admin.registrations.index', ['status' => $key]) }}">{{ $row['label'] }}</a>
                        <b>{{ $row['total'] }}</b>
                        <small>{{ round($row['total'] / $statusTotal * 100) }}%</small>
                    </li>
                @endforeach
            </ul>
        </article>
    </section>

    {{-- Pendaftar terbaru + peringkat --}}
    <section class="grid grid--latest">
        <article class="card">
            <header class="card-head">
                <div>
                    <h3>Pendaftar terbaru</h3>
                    <p>6 pendaftar terakhir</p>
                </div>
                <a href="{{ route('admin.registrations.index') }}" class="link-arrow">Lihat semua <x-admin.icon name="arrow" :size="16" /></a>
            </header>

            @forelse ($latest as $r)
                <div class="row">
                    <span class="row-avatar">{{ mb_strtoupper(mb_substr($r->child_name, 0, 1)) }}</span>
                    <div class="row-main">
                        <a href="{{ route('admin.registrations.show', $r) }}">{{ $r->child_name }}</a>
                        <small>{{ $r->package_label }}</small>
                    </div>
                    <span class="row-date">{{ $r->created_short }}</span>
                    <span class="badge badge--{{ $r->status }}">{{ $r->status_label }}</span>
                    <a href="{{ $r->wa_url }}" class="icon-btn icon-btn--wa" target="_blank" rel="noopener" aria-label="WhatsApp {{ $r->parent_name }}" title="WhatsApp {{ $r->parent_name }}">
                        <x-admin.icon name="whatsapp" :size="18" />
                    </a>
                </div>
            @empty
                <div class="empty">
                    <x-admin.icon name="users" :size="28" />
                    <p>Belum ada pendaftar.</p>
                    <small>Pendaftar dari form di website akan muncul di sini.</small>
                </div>
            @endforelse
        </article>

        <div class="stack">
            <article class="card">
                <header class="card-head"><div><h3>Paket terlaris</h3><p>Berdasarkan pilihan pendaftar</p></div></header>
                @forelse ($byPackage as $row)
                    <div class="meter">
                        <div class="meter-top"><span>{{ $row['name'] }}</span><b>{{ $row['total'] }}</b></div>
                        <div class="meter-track"><i style="width: {{ $row['pct'] }}%"></i></div>
                    </div>
                @empty
                    <p class="muted">Belum ada data.</p>
                @endforelse
            </article>

            <article class="card">
                <header class="card-head"><div><h3>Kolam favorit</h3><p>Lokasi yang paling banyak dipilih</p></div></header>
                @forelse ($byLocation as $row)
                    <div class="meter meter--gold">
                        <div class="meter-top"><span>{{ $row['name'] }}</span><b>{{ $row['total'] }}</b></div>
                        <div class="meter-track"><i style="width: {{ $row['pct'] }}%"></i></div>
                    </div>
                @empty
                    <p class="muted">Belum ada data.</p>
                @endforelse
            </article>
        </div>
    </section>

    {{-- Pintasan ke konten website --}}
    <section class="card">
        <header class="card-head">
            <div>
                <h3>Konten website</h3>
                <p>Yang sedang tampil untuk pengunjung</p>
            </div>
        </header>

        <div class="shortcuts">
            <a href="{{ route('admin.packages.index') }}" class="shortcut">
                <span class="stat-icon stat-icon--blue"><x-admin.icon name="tag" /></span>
                <div><b>{{ $content['packages'] }}</b><small>Paket tayang</small></div>
            </a>
            <a href="{{ route('admin.locations.index') }}" class="shortcut">
                <span class="stat-icon stat-icon--gold"><x-admin.icon name="pin" /></span>
                <div><b>{{ $content['locations'] }}</b><small>Lokasi kolam</small></div>
            </a>
            <a href="{{ route('admin.testimonials.index') }}" class="shortcut">
                <span class="stat-icon stat-icon--green"><x-admin.icon name="quote" /></span>
                <div><b>{{ $content['testimonials'] }}</b><small>Testimoni tayang</small></div>
            </a>
            <a href="{{ route('admin.gallery.index') }}" class="shortcut">
                <span class="stat-icon stat-icon--red"><x-admin.icon name="image" /></span>
                <div><b>{{ $content['gallery'] }}</b><small>Foto galeri</small></div>
            </a>
        </div>
    </section>
@endsection
