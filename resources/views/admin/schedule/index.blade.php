@extends('admin.layouts.admin')

@section('title', 'Jadwal')
@section('subtitle', 'Jadwal latihan per minggu')

@section('content')
    @php
        $times = collect(array_merge($quickTimes, array_keys($grid)))->unique()->sort()->values()->all();
        $label = fn ($t) => str_replace(':', '.', $t);
        $query = fn (array $extra = []) => array_filter(array_merge(['kolam' => $locationId ?: null], $extra));
    @endphp

    <div class="toolbar">
        <div class="toolbar-group">
            <form method="GET" action="{{ route('admin.schedule.index') }}" class="select-inline">
                <input type="hidden" name="minggu" value="{{ $monday->toDateString() }}">
                <select name="kolam" data-autosubmit aria-label="Pilih kolam">
                    <option value="">Semua kolam</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}" @selected($locationId === $loc->id)>{{ $loc->pool }}</option>
                    @endforeach
                </select>
            </form>

            <div class="week-nav" aria-label="Pilih minggu">
                <a href="{{ route('admin.schedule.index', $query(['minggu' => $prevWeek])) }}" class="icon-btn" aria-label="Minggu sebelumnya"><x-admin.icon name="chevron-l" :size="16" /></a>
                <strong>{{ $weekLabel }}</strong>
                <a href="{{ route('admin.schedule.index', $query(['minggu' => $nextWeek])) }}" class="icon-btn" aria-label="Minggu berikutnya"><x-admin.icon name="chevron" :size="16" /></a>
            </div>
        </div>

        <button type="button" class="btn btn--gold btn--sm" data-modal-open="slot-baru"><x-admin.icon name="plus" :size="16" /> Tambah slot</button>
    </div>

    <section class="card card--flush">
        @if ($hasSlots)
            <div class="week-scroll">
                <div class="week">
                    <div class="week-head"></div>
                    @foreach ($days as $dayName)
                        <div class="week-head">{{ $dayName }}</div>
                    @endforeach

                    @foreach ($grid as $time => $byDay)
                        <div class="week-time">{{ $label($time) }}</div>
                        @foreach ($days as $dayNo => $dayName)
                            <div class="week-cell">
                                @foreach ($byDay[$dayNo] ?? [] as $slot)
                                    <button type="button" class="slot slot--{{ $slot->state }}" data-modal-open="slot-{{ $slot->id }}">
                                        {{ $slot->location->short }} {{ $slot->filled }}/{{ $slot->capacity }}
                                    </button>
                                @endforeach
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

            <ul class="legend">
                <li><i class="dot dot--free"></i> Masih kosong</li>
                <li><i class="dot dot--almost"></i> Hampir penuh</li>
                <li><i class="dot dot--full"></i> Penuh</li>
                <li><i class="dot dot--off"></i> Dihentikan</li>
            </ul>
        @else
            <div class="empty empty--big">
                <strong>Belum ada slot jadwal.</strong>
                <p>Slot = satu jam latihan di satu kolam, dengan batas jumlah murid. Tekan "Tambah slot" untuk membuat yang pertama.</p>
            </div>
        @endif
    </section>

    {{-- Detail tiap slot --}}
    @foreach ($grid as $byDay)
        @foreach ($byDay as $slots)
            @foreach ($slots as $slot)
                <x-admin.modal :id="'slot-' . $slot->id" :title="$slot->day_name . ', ' . $slot->time_label" :subtitle="$slot->location->pool . ' · ' . \App\Support\Schedule::STATE_LABELS[$slot->state]">
                    <div class="modal-form">
                        <p class="muted">Terisi <b>{{ $slot->filled }} dari {{ $slot->capacity }}</b> murid.@if ($slot->week_start) Slot ini hanya untuk minggu {{ $slot->week_start->locale('id')->translatedFormat('j M') }}.@else Berlaku setiap minggu.@endif</p>

                        @forelse ($slot->students as $student)
                            <div class="mini-row">
                                <a href="{{ route('admin.registrations.show', $student) }}"><b>{{ $student->child_name }}</b></a>
                                <small>{{ $student->parent_name }}</small>
                            </div>
                        @empty
                            <p class="empty">Belum ada murid di slot ini.</p>
                        @endforelse

                        <form method="POST" action="{{ route('admin.schedule.update', $slot) }}" class="form-inline">
                            @csrf
                            @method('PATCH')
                            <div class="field">
                                <label for="cap-{{ $slot->id }}">Maksimal murid</label>
                                <input id="cap-{{ $slot->id }}" type="number" name="capacity" min="1" max="30" value="{{ $slot->capacity }}">
                            </div>
                            <button type="submit" class="btn btn--navy btn--sm">Simpan</button>
                        </form>

                        <footer class="modal-foot">
                            <form method="POST" action="{{ route('admin.schedule.destroy', $slot) }}" data-confirm="Hapus slot ini? Murid tidak ikut terhapus.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn--danger-outline">Hapus slot</button>
                            </form>
                            <form method="POST" action="{{ route('admin.schedule.update', $slot) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_active" value="{{ $slot->is_active ? 0 : 1 }}">
                                <button type="submit" class="btn btn--outline">{{ $slot->is_active ? 'Hentikan slot' : 'Aktifkan lagi' }}</button>
                            </form>
                            <button type="button" class="btn btn--gold btn--grow" data-modal-close>Tutup</button>
                        </footer>
                    </div>
                </x-admin.modal>
            @endforeach
        @endforeach
    @endforeach

    {{-- Tambah slot --}}
    @php
        $has = $errors->hasBag('slot');
        $o   = fn ($key, $default = null) => $has ? old($key, $default) : $default;
    @endphp
    <x-admin.modal id="slot-baru" title="Tambah slot jadwal" subtitle="Slot = satu jam latihan di satu kolam. Isi 5 pilihan di bawah, lalu simpan." bag="slot" size="lg">
        <form method="POST" action="{{ route('admin.schedule.store') }}" class="modal-form" data-summary-form novalidate>
            @csrf
            <input type="hidden" name="minggu" value="{{ $monday->toDateString() }}">

            <div class="step">
                <p class="step-label"><i>1</i> Di kolam mana?</p>
                <div class="picks picks--cards">
                    @foreach ($locations as $loc)
                        <x-admin.pick name="location_id" :value="$loc->id" :label="$loc->pool" :sub="$loc->area" :card="true" :checked="(int) $o('location_id', $locationId ?: ($locations->first()->id ?? 0)) === $loc->id" />
                    @endforeach
                </div>
            </div>

            <div class="step">
                <p class="step-label"><i>2</i> Hari apa? <small>Boleh pilih lebih dari satu</small></p>
                <div class="picks">
                    @foreach ($days as $dayNo => $dayName)
                        <x-admin.pick type="checkbox" name="days[]" :value="$dayNo" :label="$dayName" :checked="in_array($dayNo, (array) $o('days', []))" />
                    @endforeach
                </div>
            </div>

            <div class="step">
                <p class="step-label"><i>3</i> Jam berapa? <small>Boleh pilih lebih dari satu</small></p>
                <div class="picks picks--time">
                    @foreach ($times as $time)
                        <x-admin.pick type="checkbox" name="times[]" :value="$time" :label="$label($time)" :checked="in_array($time, (array) $o('times', []))" />
                    @endforeach
                    <span class="time-extra"><input type="text" name="time_extra" value="{{ $o('time_extra') }}" placeholder="Jam lain, mis. 18.30" inputmode="numeric" maxlength="5" aria-label="Jam lain"></span>
                </div>
            </div>

            <div class="step step--row">
                <div>
                    <p class="step-label"><i>4</i> Maksimal berapa murid?</p>
                    <div class="number-stepper" data-stepper>
                        <button type="button" data-step="-1" aria-label="Kurangi">−</button>
                        <input type="number" name="capacity" value="{{ $o('capacity', 3) }}" min="1" max="30" aria-label="Maksimal murid">
                        <button type="button" data-step="1" aria-label="Tambah">+</button>
                    </div>
                </div>
                <div>
                    <p class="step-label"><i>5</i> Berlaku untuk</p>
                    <div class="picks">
                        <x-admin.pick name="scope" value="week" label="Minggu ini saja" :checked="$o('scope') === 'week'" />
                        <x-admin.pick name="scope" value="always" label="Setiap minggu" :checked="$o('scope', 'always') === 'always'" />
                    </div>
                </div>
            </div>

            <p class="summary" data-summary="Ringkasan: akan dibuat slot hari {days[]}, jam {times[]}, di {location_id}, maksimal {capacity} murid, {scope}.">
                <b>Ringkasan:</b> <span data-summary-text></span>
            </p>

            <footer class="modal-foot">
                <button type="button" class="btn btn--outline" data-modal-close>Batal</button>
                <button type="submit" class="btn btn--gold btn--grow">Simpan slot</button>
            </footer>
        </form>
    </x-admin.modal>
@endsection
