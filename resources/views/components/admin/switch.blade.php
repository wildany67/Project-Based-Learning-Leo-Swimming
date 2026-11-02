{{--
    Saklar nyala/mati. Selalu mengirim 0 atau 1 (ada input hidden), jadi bisa dipakai di validasi "boolean".
    Contoh: <x-admin.switch name="is_active" label="Tampilkan di website" :checked="$p->is_active" />
    Tambah auto-submit: <x-admin.switch ... :auto="true" /> (form langsung terkirim saat digeser)
--}}
@props(['name', 'label' => '', 'checked' => false, 'hint' => null, 'auto' => false, 'id' => null])

<div class="field field--switch">
    <label class="switch">
        <input type="hidden" name="{{ $name }}" value="0">
        <input type="checkbox" name="{{ $name }}" value="1" @if ($id) id="{{ $id }}" @endif @checked($checked) @if ($auto) data-autosubmit @endif>
        <span class="switch-ui" aria-hidden="true"></span>
        @if ($label !== '')
            <span class="switch-text">{{ $label }}</span>
        @endif
    </label>
    @if ($hint)
        <p class="field-hint">{{ $hint }}</p>
    @endif
</div>
