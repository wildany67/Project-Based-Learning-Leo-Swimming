<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Admin Leo Swimming</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=DM+Sans:wght@400;500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin">

    <div class="shell">
        @include('admin.partials.sidebar')

        <div class="main">
            <header class="topbar">
                <button class="icon-btn menu-btn" type="button" data-nav-toggle aria-label="Buka menu">
                    <x-admin.icon name="menu" />
                </button>

                <div class="topbar-title">
                    <h1>@yield('title', 'Dashboard')</h1>
                    <p>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                </div>

                <div class="topbar-user">
                    <span class="avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="user-meta">
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>Admin</small>
                    </span>
                </div>
            </header>

            <div class="content">
                @include('admin.partials.flash')
                @yield('content')
            </div>
        </div>
    </div>

    <div class="scrim" data-nav-toggle></div>

    <script src="{{ asset('js/admin.js') }}" defer></script>
</body>
</html>
