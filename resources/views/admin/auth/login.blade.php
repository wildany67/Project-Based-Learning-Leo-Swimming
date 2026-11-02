<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Masuk | Admin Leo Swimming</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=DM+Sans:wght@400;500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin auth">

    <main class="auth-shell">
        <form method="POST" action="{{ route('admin.login.post') }}" class="auth-card" novalidate>
            @csrf

            <span class="auth-logo"><img src="{{ asset('images/brand/logo.png') }}" alt="Leo Swimming Course"></span>

            <h1>Masuk ke Panel Admin</h1>
            <p class="auth-sub">Khusus Pak Leo dan asisten</p>

            @if (session('success'))
                <div class="flash flash--success" role="status">
                    <x-admin.icon name="check" :size="18" />
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="field {{ $errors->has('email') ? 'field--error' : '' }}">
                <label for="f-email">Email</label>
                <input id="f-email" type="email" name="email" value="{{ old('email') }}" placeholder="leo@contoh.com" autocomplete="username" autofocus>
                @if ($errors->has('email'))
                    <p class="field-error">{{ $errors->first('email') }}</p>
                @endif
            </div>

            <div class="field {{ $errors->has('password') ? 'field--error' : '' }}">
                <label for="f-password">Kata sandi</label>
                <div class="input-affix">
                    <input id="f-password" type="password" name="password" placeholder="••••••••" autocomplete="current-password">
                    <button type="button" class="affix-btn" data-toggle-password="f-password" aria-controls="f-password">Tampilkan</button>
                </div>
                @if ($errors->has('password'))
                    <p class="field-error">{{ $errors->first('password') }}</p>
                @endif
            </div>

            <label class="check">
                <input type="checkbox" name="remember" value="1">
                <span>Tetap masuk di perangkat ini</span>
            </label>

            <button type="submit" class="btn btn--navy btn--block">Masuk</button>

            <p class="auth-note">Lupa kata sandi? Hubungi orang yang membantu mengelola website lewat WhatsApp.</p>
        </form>
    </main>

    <script src="{{ asset('js/admin.js') }}" defer></script>
</body>
</html>
