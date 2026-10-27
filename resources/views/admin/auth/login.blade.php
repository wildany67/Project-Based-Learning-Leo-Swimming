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

        {{-- Sisi kiri: sambutan --}}
        <section class="auth-hero" aria-hidden="true">
            <span class="auth-dot auth-dot--gold"></span>
            <span class="auth-dot auth-dot--blue"></span>
            <span class="auth-dot auth-dot--navy"></span>
            <span class="auth-wave"></span>

            <div class="auth-hero-inner">
                <span class="auth-logo"><img src="{{ asset('images/brand/logo.png') }}" alt=""></span>
                <p class="eyebrow">Dashboard admin</p>
                <h2>Kelola kelas,<br><span>pantau pendaftar.</span></h2>
                <p class="auth-lead">Lihat pendaftar baru, atur paket dan harga, dan perbarui isi website dalam satu tempat.</p>
            </div>
        </section>

        {{-- Sisi kanan: form --}}
        <section class="auth-panel">
            <form method="POST" action="{{ route('admin.login.post') }}" class="auth-card" novalidate>
                @csrf

                <h1>Masuk</h1>
                <p class="auth-sub">Gunakan akun admin yang dibuat lewat terminal.</p>

                @if (session('success'))
                    <div class="flash flash--success" role="status">
                        <x-admin.icon name="check" :size="18" />
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <x-admin.field label="Email" name="email" type="email" :value="old('email')" placeholder="nama@email.com" :required="true" />

                <div class="field {{ $errors->has('password') ? 'field--error' : '' }}">
                    <label for="f-password">Password<em>*</em></label>
                    <input id="f-password" type="password" name="password" autocomplete="current-password" placeholder="Masukkan password">
                    @if ($errors->has('password'))
                        <p class="field-error">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <label class="check">
                    <input type="checkbox" name="remember" value="1">
                    <span>Ingat saya di perangkat ini</span>
                </label>

                <button type="submit" class="btn btn--primary btn--block">Masuk ke dashboard</button>

                <a href="{{ route('home') }}" class="auth-back"><x-admin.icon name="back" :size="16" /> Kembali ke website</a>
            </form>
        </section>

    </main>

</body>
</html>
