@extends('admin.layouts.admin')

@section('title', 'Pendaftar')

@section('content')
    @php
        $exportQuery = array_filter(['q' => $search, 'status' => $status]);
    @endphp

    <div class="page-head">
        <div>
            <h2>Data pendaftar</h2>
            <p>{{ $allCount }} pendaftar tercatat. Ubah status langsung dari tabel.</p>
        </div>
        <a href="{{ route('admin.registrations.export', $exportQuery) }}" class="btn btn--ghost">
            <x-admin.icon name="download" :size="18" /> Export CSV
        </a>
    </div>

    <div class="card card--flush">

        {{-- Tab status + pencarian --}}
        <div class="toolbar">
            <nav class="tabs" aria-label="Filter status">
                <a href="{{ route('admin.registrations.index', array_filter(['q' => $search])) }}" class="tab {{ $status === '' ? 'is-active' : '' }}">
                    Semua <b>{{ $allCount }}</b>
                </a>
                @foreach (\App\Models\Registration::STATUSES as $key => $label)
                    <a href="{{ route('admin.registrations.index', array_filter(['q' => $search, 'status' => $key])) }}" class="tab {{ $status === $key ? 'is-active' : '' }}">
                        {{ $label }} <b>{{ $counts[$key] ?? 0 }}</b>
                    </a>
                @endforeach
            </nav>

            <form method="GET" action="{{ route('admin.registrations.index') }}" class="search">
                @if ($status !== '')
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <x-admin.icon name="search" :size="18" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama anak, orang tua, atau nomor HP" aria-label="Cari pendaftar">
            </form>
        </div>

        @if ($registrations->count())
            <div class="table-wrap">
                <table class="table table--cards">
                    <thead>
                        <tr>
                            <th>Anak</th>
                            <th>Orang tua</th>
                            <th>Paket dan kolam</th>
                            <th>Mulai</th>
                            <th>Status</th>
                            <th class="col-actions"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registrations as $r)
                            <tr>
                                <td data-label="Anak">
                                    <a href="{{ route('admin.registrations.show', $r) }}" class="cell-title">{{ $r->child_name }}</a>
                                    <small>{{ $r->age }} · {{ $r->gender }}</small>
                                </td>
                                <td data-label="Orang tua">
                                    <span class="cell-title">{{ $r->parent_name }}</span>
                                    <small>{{ $r->phone }}</small>
                                </td>
                                <td data-label="Paket dan kolam">
                                    <span class="cell-title">{{ $r->package_label }}</span>
                                    <small>{{ $r->location }}</small>
                                </td>
                                <td data-label="Mulai">
                                    <span class="cell-title">{{ $r->start_label }}</span>
                                    <small>Daftar {{ $r->created_short }}</small>
                                </td>
                                <td data-label="Status">
                                    <form method="POST" action="{{ route('admin.registrations.status', $r) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="status-select status-select--{{ $r->status }}" data-autosubmit aria-label="Status {{ $r->child_name }}">
                                            @foreach (\App\Models\Registration::STATUSES as $key => $label)
                                                <option value="{{ $key }}" @selected($r->status === $key)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="col-actions" data-label="Aksi">
                                    <a href="{{ $r->wa_url }}" class="icon-btn icon-btn--wa" target="_blank" rel="noopener" title="Chat orang tua di WhatsApp" aria-label="WhatsApp {{ $r->parent_name }}">
                                        <x-admin.icon name="whatsapp" :size="18" />
                                    </a>
                                    <a href="{{ route('admin.registrations.show', $r) }}" class="icon-btn" title="Lihat detail" aria-label="Detail {{ $r->child_name }}">
                                        <x-admin.icon name="eye" :size="18" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @include('admin.partials.pagination', ['paginator' => $registrations])
        @else
            <div class="empty empty--page">
                <x-admin.icon name="users" :size="32" />
                <p>{{ $search !== '' || $status !== '' ? 'Tidak ada pendaftar yang cocok.' : 'Belum ada pendaftar.' }}</p>
                <small>
                    @if ($search !== '' || $status !== '')
                        Coba kata kunci lain atau <a href="{{ route('admin.registrations.index') }}">tampilkan semua</a>.
                    @else
                        Pendaftar dari form di website akan muncul di sini.
                    @endif
                </small>
            </div>
        @endif
    </div>
@endsection
