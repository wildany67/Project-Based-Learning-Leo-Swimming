{{--
    Saklar nyala/mati.
    Contoh: <x-admin.switch name="is_active" label="Tampilkan di website" :checked="old('is_active', $p->is_active)" />
--}}
@props(['name', 'label', 'checked' => false, 'hint' => null])

<div class="field">
    <label class="switch">
        <input type="checkbox" name="{{ $name }}" value="1" @checked($checked)>
        <span class="switch-ui" aria-hidden="true"></span>
        <span class="switch-text">{{ $label }}</span>
    </label>
    @if ($hint)
        <p class="field-hint">{{ $hint }}</p>
    @endif
</div>
