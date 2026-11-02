@extends('admin.layouts.admin')

@section('title', 'Pendaftar')
@section('subtitle', $allCount . ' pendaftar tercatat')

@section('content')
    @php $exportQuery = array_filter(['q' => $search, 'status' => $status]); @endphp

    <div class="toolbar">
        <nav class="chips" aria-label="Filter status">
            <a href="{{ route('admin.registrations.index', array_filter(['q' => $search])) }}" class="chip chip--all {{ $status === '' ? 'is-active' : '' }}">
                Semua <b>{{ $allCount }}</b>
            </a>
            @foreach (\App\Models\Registration::STATUSES as $key => $label)
                <a href="{{ route('admin.registrations.index', array_filter(['q' => $search, 'status' => $key])) }}" class="chip chip--{{ $key }} {{ $status === $key ? 'is-active' : '' }}">
                    {{ $label }} <b>{{ $counts[$key] ?? 0 }}</b>
                </a>
            @endforeach
        </nav>

        <div class="toolbar-actions">
            <button type="button" class="btn btn--gold btn--sm" data-modal-open="pendaftar-manual"><x-admin.icon name="plus" :size="16" /> Tambah manual</button>
            <a href="{{ route('admin.registrations.export', $exportQuery) }}" class="btn btn--outline btn--sm"><x-admin.icon name="download" :size="16" /> Unduh Excel</a>
        </div>
    </div>

    <section class="card card--flush">
        <form method="GET" action="{{ route('admin.registrations.index') }}" class="search">
            @if ($status !== '')
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <x-admin.icon name="search" :size="18" />
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama anak, orang tua, kode, atau nomor HP" aria-label="Cari pendaftar">
        </form>

        @if ($registrations->count())
            <div class="table-wrap">
                <table class="table table--cards">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama anak (orang tua)</th>
                            <th>Paket</th>
                            <th>Kolam dan jadwal</th>
                            <th>Status</th>
                            <th class="col-actions"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registrations as $r)
                            <tr>
                                <td data-label="Kode" class="cell-code">{{ $r->code }}</td>
                                <td data-label="Nama anak">
                                    <a href="{{ route('admin.registrations.show', $r) }}" class="cell-title">{{ $r->child_name }}</a>
                                    <small>{{ $r->parent_name }}</small>
                                </td>
                                <td data-label="Paket">{{ $r->package_short }}</td>
                                <td data-label="Kolam dan jadwal">
                                    {{ $r->location_short }} · {{ $r->schedule_short }}
                                </td>
                                <td data-label="Status"><span class="badge badge--{{ $r->status }}">{{ $r->status_label }}</span></td>
                                <td class="col-actions" data-label="Aksi">
                                    <a href="{{ route('admin.registrations.show', $r) }}" class="btn btn--soft btn--sm">Lihat</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @include('admin.partials.pagination', ['paginator' => $registrations])
        @else
            <div class="empty empty--big">
                @if ($search !== '' || $status !== '')
                    <strong>Tidak ada hasil.</strong>
                    <p>Coba kata kunci lain atau pilih "Semua".</p>
                    <a href="{{ route('admin.registrations.index') }}" class="btn btn--outline btn--sm">Hapus filter</a>
                @else
                    <strong>Belum ada pendaftar.</strong>
                    <p>Pendaftar dari form website akan muncul di sini. Orang tua yang mendaftar lewat WhatsApp bisa ditambahkan dengan tombol "Tambah manual".</p>
                @endif
            </div>
        @endif
    </section>

    @include('admin.registrations._manual', ['form' => $form])
@endsection
