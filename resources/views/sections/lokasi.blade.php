<section id="lokasi" class="section lokasi decor-host">
    <x-dot color="gold" :size="28" pos="right:18%;top:20px" />
    <x-dot color="gold" :size="64" pos="right:-10px;top:40px" />
    <x-dot color="blue" :size="50" pos="right:10%;top:90px" />
    <x-dot color="blue" :size="80" pos="right:3%;top:120px" />
    <x-dot color="navy" :size="110" pos="right:22%;top:130px" />
    <x-wave color="gold" pos="right:0;top:180px;width:40%" />
    <x-dot color="navy" :size="150" pos="left:-50px;bottom:60px" />
    <x-dot color="blue" :size="62" pos="left:12%;bottom:30px" />
    <x-dot color="navy" :size="110" pos="left:26%;bottom:10px" />
    <x-wave color="gold" pos="left:0;bottom:20px;width:36%" />

    <div class="container">
        <header class="section-head section-head--center">
            <p class="eyebrow">Lokasi Latihan</p>
            <h2 class="section-title section-title--stack">
                <span>Pilih kolam</span>
                <span class="text-gold">terdekat dan nyaman.</span>
            </h2>
            <p class="section-sub">Pilihlah lokasi terdekat dan kolam ternyaman versi Anda.</p>
        </header>

        <div class="loc-grid">
            @foreach ($locations as $loc)
                <article class="loc-card">
                    <svg class="loc-pin" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.800 12 21 12 21z"/><circle cx="12" cy="9.500" r="2.500"/></svg>
                    <h3>{{ $loc['area'] }}</h3>
                    <p class="loc-pool"><i aria-hidden="true"></i>{{ $loc['pool'] }}</p>
                    <p class="loc-address">{{ $loc['address'] }}</p>
                    <a class="loc-link"
                       href="https://www.google.com/maps/search/?api=1&query={{ urlencode($loc['pool'] . ' ' . $loc['address'] . ' Surabaya') }}"
                       target="_blank" rel="noopener">
                        <span>Lihat kolam</span>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M13 8l4 4-4 4"/></svg>
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
