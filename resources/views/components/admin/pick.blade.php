{{--
    Pilihan berbentuk chip atau kartu (radio / checkbox).
    Chip:  <x-admin.pick type="radio" name="status" value="menunggu" label="Menunggu" :checked="true" />
    Kartu: <x-admin.pick type="radio" name="area" value="Surabaya Timur" label="Surabaya Timur" sub="Rungkut, Sukolilo" card="1" />
    Untuk checkbox banyak pilihan pakai name="days[]".
--}}
@props(['name', 'value', 'label', 'type' => 'radio', 'checked' => false, 'sub' => null, 'card' => false, 'required' => false])

<label class="pick {{ $card ? 'pick--card' : '' }}">
    <input type="{{ $type }}" name="{{ $name }}" value="{{ $value }}" data-label="{{ $label }}" @checked($checked) @if ($required) required @endif>
    <span class="pick-ui">
        <b>{{ $label }}</b>
        @if ($sub)
            <small>{{ $sub }}</small>
        @endif
    </span>
</label>
