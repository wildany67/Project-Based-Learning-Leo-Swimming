<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Leo Swimming Course | Les Renang Privat & Grup di Surabaya')</title>
    <meta name="description" content="Les renang privat dan grup bersama Pak Leo di Surabaya. Seru, aman, dan bermakna, untuk pemula sampai mahir.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=DM+Sans:wght@400;500;700&family=Fraunces:ital,wght@1,600;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Urutan CSS: dasar → kerangka → khusus halaman → responsif (harus paling akhir) --}}
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body class="@yield('body_class')">

    @include('partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
