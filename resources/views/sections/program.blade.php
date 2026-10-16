<section id="program" class="section program decor-host">
    <x-wave color="gold" pos="left:0;top:20px;width:34%" />
    <x-dot color="gold" :size="66" pos="left:3%;top:50px" />
    <x-dot color="navy" :size="28" pos="left:24%;top:60px" />
    <x-dot color="navy" :size="130" pos="left:-60px;top:70px" />
    <x-dot color="blue" :size="54" pos="left:15%;top:150px" />
    <x-dot color="navy" :size="84" pos="left:7%;top:230px" />
    <x-dot color="blue" :size="110" pos="left:24%;top:290px" />
    <x-wave color="navy" pos="right:0;top:110px;width:38%" />
    <x-wave color="blue" pos="right:0;top:330px;width:22%" />
    <x-wave color="navy" pos="right:0;top:400px;width:22%" />
    <x-dot color="gold" :size="60" pos="right:-15px;top:440px" />
    <x-dot color="blue" :size="50" pos="right:12%;top:470px" />
    <x-dot color="navy" :size="90" pos="right:3%;top:520px" />
    <x-wave color="blue" pos="left:0;bottom:90px;width:30%" />
    <x-wave color="navy" pos="left:0;bottom:20px;width:22%" />

    <div class="container">
        <header class="section-head section-head--center">
            <p class="eyebrow">Paket Latihan</p>
            <h2 class="section-title section-title--stack">
                <span class="text-gold">Mulai latihan dengan</span>
                <span>paket yang tepat.</span>
            </h2>
            <p class="section-sub">Sesuaikan harga dan jumlah sesi dengan paket yang ditawarkan.</p>
        </header>

        <div class="price-grid">
            @foreach ($packages as $pkg)
                <article class="price-card price-card--{{ $pkg['theme'] }}">
                    <span class="ghost" aria-hidden="true">{{ $pkg['ghost'] }}</span>

                    <h3>{{ $pkg['name'] }}</h3>
                    <p class="price-tagline">
                        {{ $pkg['tagline'] }}
                        @if ($pkg['highlight'])
                            <br><strong>{{ $pkg['highlight'] }}</strong>
                        @endif
                    </p>

                    <div class="price-box">
                        <p class="price-box-title">{{ $pkg['price_label'] }}</p>
                        <ul>
                            @foreach ($pkg['prices'] as $row)
                                <li>
                                    <span>
                                        {{ $row['label'] }}
                                        @if ($row['note'])
                                            <em>{{ $row['note'] }}</em>
                                        @endif
                                    </span>
                                    <b>{{ $row['price'] }}</b>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <ul class="checklist">
                        @foreach ($pkg['features'] as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>

                    <a href="{{ route('daftar', ['paket' => $pkg['cta_package']]) }}" class="btn btn--white">{{ $pkg['cta'] }}</a>
                </article>
            @endforeach
        </div>
    </div>
</section>
