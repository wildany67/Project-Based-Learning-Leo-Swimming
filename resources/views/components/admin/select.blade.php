{{--
    Dropdown.
    Contoh: <x-admin.select label="Warna" name="theme" :options="['navy' => 'Biru tua', 'blue' => 'Biru']" :value="old('theme', $p->theme)" />
--}}
@props(['label', 'name', 'options' => [], 'value' => '', 'hint' => null, 'required' => false])

<div class="field {{ $errors->has($name) ? 'field--error' : '' }}">
    <label for="f-{{ $name }}">{{ $label }}@if ($required)<em>*</em>@endif</label>
    <select id="f-{{ $name }}" name="{{ $name }}">
        @foreach ($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected((string) $value === (string) $optValue)>{{ $optLabel }}</option>
        @endforeach
    </select>
    @if ($hint)
        <p class="field-hint">{{ $hint }}</p>
    @endif
    @if ($errors->has($name))
        <p class="field-error">{{ $errors->first($name) }}</p>
    @endif
</div>
