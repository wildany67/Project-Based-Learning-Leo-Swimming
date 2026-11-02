@extends('admin.layouts.admin')

@section('title', 'Murid')
@section('subtitle', 'Anak-anak yang sedang les')

@section('content')
    <p class="banner">
        <x-admin.icon name="warning" :size="18" />
        <span>Di sini daftar anak yang sedang les. Kolom <b>Pertemuan</b> menunjukkan berapa kali sudah datang. Kalau tinggal 1 kali, muncul tanda kuning supaya Bapak ingat menawarkan paket baru.</span>
    </p>

    <div class="toolbar">
        <nav class="chips" aria-label="Filter murid">
            <a href="{{ route('admin.students.index', array_filter(['q' => $search])) }}" class="chip chip--all {{ $filter === '' ? 'is-active' : '' }}">Semua <b>{{ $counts['all'] }}</b></a>
            <a href="{{ route('admin.students.index', array_filter(['q' => $search, 'filter' => 'aktif'])) }}" class="chip chip--terkonfirmasi {{ $filter === 'aktif' ? 'is-active' : '' }}">Aktif <b>{{ $counts['active'] }}</b></a>
            <a href="{{ route('admin.students.index', array_filter(['q' => $search, 'filter' => 'habis'])) }}" class="chip chip--menunggu {{ $filter === 'habis' ? 'is-active' : '' }}">Paket hampir habis <b>{{ $counts['almost'] }}</b></a>
            <a href="{{ route('admin.students.index', array_filter(['q' => $search, 'filter' => 'selesai'])) }}" class="chip chip--selesai {{ $filter === 'selesai' ? 'is-active' : '' }}">Tidak aktif <b>{{ $counts['done'] }}</b></a>
        </nav>
    </div>

    <div class="grid grid--students">

        <section class="card card--flush">
            <form method="GET" action="{{ route('admin.students.index') }}" class="search">
                @if ($filter !== '')
                    <input type="hidden" name="filter" value="{{ $filter }}">
                @endif
                <x-admin.icon name="search" :size="18" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Ketik nama anak di sini..." aria-label="Cari murid">
            </form>

            @if (count($students))
                <div class="table-wrap">
                    <table class="table table--cards">
                        <thead>
                            <tr>
                                <th>Nama anak</th>
                                <th>Paket</th>
                                <th>Pertemuan</th>
                                <th>Kolam dan jadwal</th>
                                <th class="col-actions">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $s)
                                @php
                                    $total = max($s->meetings_total, 1);
                                    $used  = min($s->meetings_used, $total);
                                    $low   = $s->status === 'terkonfirmasi' && $s->meetings_left <= 1;
                                @endphp
                                <tr>
                                    <td data-label="Nama anak">
                                        <a href="{{ route('admin.registrations.show', $s) }}" class="cell-title">{{ $s->child_name }}</a>
                                        <small>{{ $s->parent_name }}</small>
                                    </td>
                                    <td data-label="Paket">{{ $s->package_short }}</td>
                                    <td data-label="Pertemuan">
                                        <div class="meter {{ $low ? 'meter--low' : '' }}" role="img" aria-label="{{ $used }} dari {{ $total }} pertemuan">
                                            <span style="width: {{ round($used / $total * 100) }}%"></span>
                                        </div>
                                        <small>{{ $used }}/{{ $total }}@if ($low) · <em class="tag-low">Sisa {{ $s->meetings_left }}</em>@endif</small>
                                    </td>
                                    <td data-label="Kolam dan jadwal">{{ $s->location_short }} · {{ $s->schedule_short }}</td>
                                    <td class="col-actions" data-label="Aksi">
                                        <a href="{{ $s->wa_url }}" class="btn btn--whatsapp btn--sm" target="_blank" rel="noopener">Hubungi</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty empty--big">
                    <strong>Belum ada murid di sini.</strong>
                    <p>Anak menjadi murid setelah pendaftarannya ditandai Terkonfirmasi di halaman Pendaftar.</p>
                </div>
            @endif
        </section>

        <div class="stack">
            <section class="card">
                <h2 class="card-title">Perlu perpanjang paket</h2>
                @if (count($almost))
                    <p class="muted">{{ count($almost) }} anak tinggal 1 pertemuan atau kurang.</p>
                    @foreach ($almost as $s)
                        <div class="mini-row">
                            <b>{{ $s->child_name }}</b>
                            <a href="{{ $s->renew_url }}" class="btn btn--whatsapp btn--xs" target="_blank" rel="noopener">Kirim pesan WA</a>
                        </div>
                    @endforeach
                @else
                    <p class="empty">Belum ada. Semua paket masih cukup.</p>
                @endif
            </section>

            <section class="card">
                <h2 class="card-title">Catat kehadiran hari ini</h2>
                @forelse ($attendance as $group)
                    <p class="group-label">{{ $group['label'] }}</p>
                    @foreach ($group['students'] as $item)
                        <div class="mini-row">
                            <b>{{ $item['student']->child_name }}</b>
                            <span class="attend">
                                @foreach ([1 => 'Hadir', 0 => 'Tidak'] as $value => $label)
                                    <form method="POST" action="{{ route('admin.students.attend', $item['student']) }}">
                                        @csrf
                                        <input type="hidden" name="present" value="{{ $value }}">
                                        <button type="submit" class="attend-btn attend-btn--{{ $value ? 'yes' : 'no' }} {{ $item['present'] === (bool) $value ? 'is-on' : '' }}">@if ($item['present'] === (bool) $value && $value)✓ @endif{{ $label }}</button>
                                    </form>
                                @endforeach
                            </span>
                        </div>
                    @endforeach
                @empty
                    <p class="empty">Tidak ada murid yang berlatih hari ini.</p>
                @endforelse
            </section>
        </div>
    </div>
@endsection
