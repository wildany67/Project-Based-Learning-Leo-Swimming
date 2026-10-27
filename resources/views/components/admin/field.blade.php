{{--
    Kolom isian satu baris.
    Contoh: <x-admin.field label="Nama" name="name" :value="old('name', $item->name)" :required="true" />
    Tipe lain: type="url" | "number" | "date"
--}}
@props(['label', 'name', 'value' => '', 'type' => 'text', 'hint' => null, 'placeholder' => '', 'required' => false, 'maxlength' => null])

<div class="field {{ $errors->has($name) ? 'field--error' : '' }}">
    <label for="f-{{ $name }}">{{ $label }}@if ($required)<em>*</em>@endif</label>
    <input id="f-{{ $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $placeholder }}"@if ($maxlength) maxlength="{{ $maxlength }}"@endif>
    @if ($hint)
        <p class="field-hint">{{ $hint }}</p>
    @endif
    @if ($errors->has($name))
        <p class="field-error">{{ $errors->first($name) }}</p>
    @endif
</div>
