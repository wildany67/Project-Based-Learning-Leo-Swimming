@php
    $waiting = \App\Models\Registration::where('status', \App\Models\Registration::WAITING)->count();

    // [route, label, ikon, pola route aktif, badge]
    $groups = [
        'Utama' => [
            ['admin.dashboard',          'Beranda',         'home',     'admin.dashboard',      null],
            ['admin.registrations.index', 'Pendaftar',      'users',    'admin.registrations.*', $waiting ?: null],
            ['admin.students.index',      'Murid',          'user',     'admin.students.*',     null],
        ],
        'Operasional' => [
            ['admin.schedule.index',      'Jadwal',         'calendar', 'admin.schedule.*',     null],
            ['admin.locations.index',     'Kolam',          'wave',     'admin.locations.*',    null],
            ['admin.packages.index',      'Paket dan Harga', 'tag',     'admin.packages.*',     null],
        ],
        'Website' => [
            ['admin.content.index',       'Konten Website', 'image',    'admin.content.*',      null],
        ],
        'Sistem' => [
            ['admin.settings.index',      'Pengaturan',     'settings', 'admin.settings.*',     null],
            ['admin.help.index',          'Bantuan',        'help',     'admin.help.*',         null],
        ],
    ];
@endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar-head">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-logo" aria-label="Leo Swimming Course, ke beranda dashboard">
            <img src="{{ asset('images/brand/logo.png') }}" alt="Leo Swimming Course">
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
                    <x-admin.icon :name="$icon" :size="18" />
                    <span>{{ $label }}</span>
                    @if ($badge)
                        <b class="nav-badge">{{ $badge }}</b>
                    @endif
                </a>
            @endforeach
        @endforeach
    </nav>

    <div class="sidebar-user">
        <span class="avatar avatar--gold">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
        <div>
            <strong>{{ auth()->user()->name }}</strong>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="link-btn">Keluar</button>
            </form>
        </div>
    </div>
</aside>
