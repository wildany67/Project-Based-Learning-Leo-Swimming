<section class="section testimoni decor-host">
    <x-wave color="gold" pos="left:0;top:110px;width:34%" />
    <x-wave color="blue" pos="right:0;top:70px;width:34%" />

    <div class="container">
        <header class="section-head section-head--center">
            <p class="eyebrow eyebrow--light">Cerita Peserta</p>
            <h2 class="section-title section-title--stack">
                <span class="text-blue">Pengalaman belajar</span>
                <span class="text-gold">yang berarti.</span>
            </h2>
        </header>

        <div class="testi-grid">
            @foreach ($testimonials as $t)
                <article class="testi-card">
                    <span class="quote-mark" aria-hidden="true">”</span>
                    <p class="testi-quote">{{ $t['quote'] }}</p>
                    <footer>
                        <span class="avatar">{{ $t['initial'] }}</span>
                        <div>
                            <strong>{{ $t['name'] }}</strong>
                            <small>{{ $t['program'] }}</small>
                        </div>
                    </footer>
                </article>
            @endforeach
        </div>
    </div>
</section>
