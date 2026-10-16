@php $home = route('home'); @endphp

<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <span class="footer-logo">
                <img src="{{ asset('images/brand/logo.png') }}" alt="Leo Swimming Course" width="80" height="50">
            </span>
            <p>Les renang privat dan grup bersama Pak Leo. Seru, aman, dan bermakna, untuk pemula sampai mahir.</p>
        </div>

        <div>
            <h4>Halaman</h4>
            <ul>
                <li><a href="{{ $home }}#beranda">Beranda</a></li>
                <li><a href="{{ $home }}#program">Program dan Harga</a></li>
                <li><a href="{{ $home }}#lokasi">Lokasi dan Jadwal</a></li>
                <li><a href="{{ route('daftar') }}">Daftar Sekarang</a></li>
            </ul>
        </div>

        <div>
            <h4>Wilayah</h4>
            <ul>
                <li>Surabaya Timur</li>
                <li>Surabaya Selatan</li>
            </ul>
        </div>

        <div>
            <h4>Kontak</h4>
            <ul>
                <li><a href="https://wa.me/{{ config('leo.whatsapp') }}" target="_blank" rel="noopener">WhatsApp {{ config('leo.whatsapp_display') }}</a></li>
                @if (config('leo.whatsapp_second'))
                    <li>WhatsApp kedua: {{ config('leo.whatsapp_second') }}</li>
                @endif
                <li><a href="https://instagram.com/{{ config('leo.instagram') }}" target="_blank" rel="noopener">Instagram {{ '@' . config('leo.instagram') }}</a></li>
            </ul>
        </div>
    </div>

    <div class="container footer-bottom">
        <p>© {{ date('Y') }} Leo Swimming Course. Surabaya.</p>
    </div>
</footer>
