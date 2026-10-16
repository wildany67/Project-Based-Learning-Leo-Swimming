{{--
    Garis gelombang dekorasi.
    Contoh: <x-wave color="gold" pos="right:0;top:250px;width:42%" />
    color: navy | blue | gold   pos: posisi dan lebar CSS
--}}
@props(['color' => 'gold', 'pos' => ''])

<span {{ $attributes->class(['wave', "wave--{$color}"]) }} style="{{ $pos }}" aria-hidden="true"></span>
