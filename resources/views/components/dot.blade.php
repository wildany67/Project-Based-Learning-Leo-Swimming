{{--
    Lingkaran dekorasi.
    Contoh: <x-dot color="gold" :size="56" pos="left:46%;top:60px" />
    color: navy | blue | gold   size: diameter (px)   pos: posisi CSS
--}}
@props(['color' => 'gold', 'size' => 40, 'pos' => ''])

<span {{ $attributes->class(['dot', "dot--{$color}"]) }} style="--s:{{ $size }}px;{{ $pos }}" aria-hidden="true"></span>
