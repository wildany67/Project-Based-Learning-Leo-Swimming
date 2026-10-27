{{--
    Kolom isian banyak baris.
    Contoh: <x-admin.textarea label="Isi" name="quote" :value="old('quote', $t->quote)" :rows="5" />
--}}
@props(['label', 'name', 'value' => '', 'hint' => null, 'placeholder' => '', 'required' => false, 'rows' => 4, 'mono' => false])

<div class="field {{ $errors->has($name) ? 'field--error' : '' }}">
    <label for="f-{{ $name }}">{{ $label }}@if ($required)<em>*</em>@endif</label>
    <textarea id="f-{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}" class="{{ $mono ? 'is-mono' : '' }}">{{ $value }}</textarea>
    @if ($hint)
        <p class="field-hint">{{ $hint }}</p>
    @endif
    @if ($errors->has($name))
        <p class="field-error">{{ $errors->first($name) }}</p>
    @endif
</div>
