<section class="section galeri decor-host">
    <x-dot color="navy" :size="200" pos="left:-80px;top:20px" />
    <x-dot color="gold" :size="60" pos="left:60px;top:10px" />
    <x-dot color="blue" :size="50" pos="left:200px;top:40px" />
    <x-dot color="navy" :size="26" pos="left:230px;top:0" />
    <x-dot color="navy" :size="150" pos="right:30%;bottom:-40px" />
    <x-dot color="blue" :size="120" pos="right:-30px;bottom:-30px" />
    <x-wave color="gold" pos="left:0;bottom:30px;width:40%" />
    <x-wave color="gold" pos="right:0;bottom:10px;width:40%" />

    <div class="container">
        <h2 class="section-title">Momen <span class="text-gold">seru</span> di kolam.</h2>

        <div class="gallery-grid">
            @foreach ($gallery as $item)
                @php
                    $path = $item['img'] ? 'images/gallery/' . $item['img'] : null;
                    $hasImg = $path && file_exists(public_path($path));
                @endphp
                <figure class="tile tile--{{ $item['size'] }} tile--{{ $item['tone'] }}">
                    @if ($hasImg)
                        <img src="{{ asset($path) }}" alt="{{ $item['caption'] }}" loading="lazy">
                    @endif
                    <figcaption>{{ $item['caption'] }}</figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
