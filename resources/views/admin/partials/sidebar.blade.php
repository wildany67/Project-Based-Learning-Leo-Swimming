@php
    $newCount = \App\Models\Registration::where('status', 'baru')->count();

    // [route, label, ikon, pola route aktif, badge]
    $groups = [
        'Utama' => [
            ['admin.dashboard', 'Dashboard', 'home', 'admin.dashboard', null],
        ],
        'Pendaftaran' => [
            ['admin.registrations.index', 'Pendaftar', 'users', 'admin.registrations.*', $newCount ?: null],
        ],
        'Konten website' => [
            ['admin.packages.index', 'Paket dan harga', 'tag', 'admin.packages.*', null],
            ['admin.locations.index', 'Lokasi kolam', 'pin', 'admin.locations.*', null],
            ['admin.testimonials.index', 'Testimoni', 'quote', 'admin.testimonials.*', null],
            ['admin.gallery.index', 'Galeri foto', 'image', 'admin.gallery.*', null],
        ],
    ];
@endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar-head">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <span class="brand-logo"><img src="{{ asset('images/brand/logo.png') }}" alt="Leo Swimming Course"></span>
            <span class="brand-text">
                <strong>Leo Swimming</strong>
                <small>Dashboard admin</small>
            </span>
        </a>
        <button class="icon-btn sidebar-close" type="button" data-nav-toggle aria-label="Tutup menu">
            <x-admin.icon name="close" />
        </button>
    </div>

    <nav class="sidebar-nav" aria-label="Menu admin">
        @foreach ($groups as $heading => $items)
            <p class="nav-heading">{{ $heading }}</p>
            @foreach ($items as [$routeName, $label, $icon, $pattern, $badge])
                <a href="{{ route($routeName) }}" class="nav-link {{ request()->routeIs($pattern) ? 'is-active' : '' }}">
                    <x-admin.icon :name="$icon" />
                    <span>{{ $label }}</span>
                    @if ($badge)
                        <b class="nav-badge">{{ $badge }}</b>
                    @endif
                </a>
            @endforeach
        @endforeach
    </nav>

    <div class="sidebar-foot">
        <a href="{{ route('home') }}" class="nav-link" target="_blank" rel="noopener">
            <x-admin.icon name="external" />
            <span>Lihat website</span>
        </a>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="nav-link nav-link--button">
                <x-admin.icon name="logout" />
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
