@extends('admin.layouts.admin')

@section('title', 'Lokasi kolam')

@section('content')
    <div class="page-head">
        <div>
            <h2>Lokasi kolam</h2>
            <p>Tampil di bagian "Lokasi" dan di pilihan kolam pada form daftar.</p>
        </div>
        <a href="{{ route('admin.locations.create') }}" class="btn btn--primary"><x-admin.icon name="plus" :size="18" /> Tambah lokasi</a>
    </div>

    <div class="card card--flush">
        @if (count($locations))
            <div class="table-wrap">
                <table class="table table--cards">
                    <thead>
                        <tr>
                            <th>Kolam</th>
                            <th>Alamat</th>
                            <th>Urutan</th>
                            <th>Status</th>
                            <th class="col-actions"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($locations as $location)
                            <tr>
                                <td data-label="Kolam">
                                    <span class="cell-title">{{ $location->pool }}</span>
                                    <small>{{ $location->area }}</small>
                                </td>
                                <td data-label="Alamat">
                                    <span class="cell-text">{{ $location->address }}</span>
                                    @if ($location->maps_url)
                                        <small><a href="{{ $location->maps_url }}" target="_blank" rel="noopener">Link Maps khusus</a></small>
                                    @endif
                                </td>
                                <td data-label="Urutan">{{ $location->sort_order }}</td>
                                <td data-label="Status">
                                    <span class="badge {{ $location->is_active ? 'badge--aktif' : 'badge--batal' }}">{{ $location->is_active ? 'Tayang' : 'Disembunyikan' }}</span>
                                </td>
                                <td class="col-actions" data-label="Aksi">
                                    <a href="{{ route('admin.locations.edit', $location) }}" class="icon-btn" title="Ubah" aria-label="Ubah {{ $location->pool }}"><x-admin.icon name="edit" :size="18" /></a>
                                    <form method="POST" action="{{ route('admin.locations.destroy', $location) }}" data-confirm="Hapus lokasi {{ $location->pool }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-btn icon-btn--danger" title="Hapus" aria-label="Hapus {{ $location->pool }}"><x-admin.icon name="trash" :size="18" /></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty empty--page">
                <x-admin.icon name="pin" :size="32" />
                <p>Belum ada lokasi.</p>
                <small>Tambahkan kolam pertama, atau jalankan <code>php artisan db:seed --class=ContentSeeder</code>.</small>
            </div>
        @endif
    </div>
@endsection
