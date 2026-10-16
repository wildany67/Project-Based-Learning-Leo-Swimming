@php $home = route('home'); @endphp

<header class="site-header">
    <div class="container nav">
        <a href="{{ $home }}#beranda" class="brand" aria-label="Leo Swimming Course, ke beranda">
            <img src="{{ asset('images/brand/logo.png') }}" alt="Leo Swimming Course" width="100" height="62">
        </a>

        <button class="nav-toggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="nav-menu">
            <span></span><span></span><span></span>
        </button>

        <nav id="nav-menu" class="nav-menu" aria-label="Navigasi utama">
            <ul>
                <li><a href="{{ $home }}#beranda" data-spy="beranda">Beranda</a></li>
                <li><a href="{{ $home }}#program" data-spy="program">Program dan Harga</a></li>
                <li><a href="{{ $home }}#lokasi" data-spy="lokasi">Lokasi</a></li>
                <li><a href="{{ $home }}#kontak" data-spy="kontak">Kontak</a></li>
            </ul>
            <a href="{{ route('daftar') }}" class="btn btn--primary nav-cta">Daftar Sekarang</a>
        </nav>
    </div>
</header>
