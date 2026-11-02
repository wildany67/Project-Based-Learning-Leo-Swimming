@extends('admin.layouts.admin')

@section('title', 'Kolam')
@section('subtitle', 'Tempat latihan murid')

@section('content')
    <p class="banner">
        <x-admin.icon name="warning" :size="18" />
        <span>Satu kotak = satu kolam renang. <b>Tombol geser</b> di kanan atas dipakai untuk menutup kolam sementara tanpa menghapusnya. Pendaftar baru tidak bisa memilih kolam yang ditutup.</span>
    </p>

    <div class="section-bar">
        <strong>{{ count($locations) }} kolam</strong>
        <button type="button" class="btn btn--gold btn--sm" data-modal-open="kolam-baru"><x-admin.icon name="plus" :size="16" /> Tambah kolam</button>
    </div>

    @if (count($locations))
        <div class="loc-grid">
            @foreach ($locations as $loc)
                <article class="card loc-card {{ $loc->is_active ? '' : 'is-closed' }}">
                    <header class="loc-head">
                        <h2>{{ $loc->pool }}</h2>
                        <form method="POST" action="{{ route('admin.locations.toggle', $loc) }}" class="loc-toggle">
                            @csrf
                            @method('PATCH')
                            <span>{{ $loc->is_active ? 'Menerima pendaftar' : 'Tutup sementara' }}</span>
                            <x-admin.switch name="is_active" :checked="$loc->is_active" :auto="true" />
                        </form>
                    </header>

                    <p class="loc-address">{{ $loc->address ?: '(alamat belum diisi)' }} · {{ $loc->area }}</p>

                    @if ($loc->address)
                        <span class="badge badge--ok">Data lengkap</span>
                    @else
                        <span class="badge badge--warn">Alamat belum diisi</span>
                    @endif

                    <dl class="loc-stats">
                        <div><dt>{{ $students[$loc->pool] ?? 0 }}</dt><dd>murid</dd></div>
                        <div><dt>{{ $loc->active_slots }}</dt><dd>jadwal aktif</dd></div>
                    </dl>

                    <div class="loc-actions">
                        <button type="button" class="btn btn--navy btn--sm" data-modal-open="kolam-ubah-{{ $loc->id }}">Ubah data</button>
                        <button type="button" class="btn btn--outline btn--sm" data-modal-open="kolam-jadwal-{{ $loc->id }}">Lihat jadwal</button>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <section class="card">
            <div class="empty empty--big">
                <strong>Belum ada kolam.</strong>
                <p>Tambahkan kolam tempat Pak Leo mengajar. Kolam yang buka akan tampil di website dan form pendaftaran.</p>
            </div>
        </section>
    @endif

    {{-- Jendela tiap kolam --}}
    @foreach ($locations as $loc)
        @include('admin.locations._modals', ['loc' => $loc])

        @php
            $slotsByDay = $loc->slots->where('is_active', true)->groupBy('weekday');
            $studentCount = $students[$loc->pool] ?? 0;
        @endphp
        <x-admin.modal :id="'kolam-jadwal-' . $loc->id" :title="'Jadwal ' . $loc->pool" subtitle="Semua slot latihan di kolam ini. Untuk mengubah atau menambah, buka halaman Jadwal." size="lg">
            <div class="modal-form">
                <div class="mini-stats">
                    <div><strong>{{ $loc->active_slots }}</strong><small>slot aktif</small></div>
                    <div><strong>{{ $studentCount }}</strong><small>murid terdaftar</small></div>
                    <div><strong>90 menit</strong><small>durasi tiap slot</small></div>
                </div>

                @foreach ($days as $dayNo => $dayName)
                    @if (isset($slotsByDay[$dayNo]))
                        <div class="day-row">
                            <b>{{ $dayName }}</b>
                            <div class="day-slots">
                                @foreach ($slotsByDay[$dayNo]->sortBy('time') as $slot)
                                    @php
                                        $filled = count($occupancy[\App\Support\Schedule::key($loc->pool, $dayName, $slot->time)] ?? []);
                                        $state  = \App\Support\Schedule::state($slot, $filled);
                                    @endphp
                                    <span class="slot slot--{{ $state }}"><b>{{ $slot->time_label }}</b> {{ $filled }}/{{ $slot->capacity }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                @if (! $loc->active_slots)
                    <p class="empty">Belum ada slot aktif di kolam ini.</p>
                @endif

                <footer class="modal-foot">
                    <button type="button" class="btn btn--outline" data-modal-close>Tutup</button>
                    <a href="{{ route('admin.schedule.index', ['kolam' => $loc->id]) }}" class="btn btn--gold btn--grow">Buka halaman Jadwal</a>
                </footer>
            </div>
        </x-admin.modal>
    @endforeach

    @include('admin.locations._modals', ['loc' => null])
@endsection
