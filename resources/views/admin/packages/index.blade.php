@extends('admin.layouts.admin')

@section('title', 'Paket dan harga')

@section('content')
    <div class="page-head">
        <div>
            <h2>Paket dan harga</h2>
            <p>Tampil di bagian "Program dan Harga" dan di pilihan paket pada form daftar.</p>
        </div>
        <a href="{{ route('admin.packages.create') }}" class="btn btn--primary"><x-admin.icon name="plus" :size="18" /> Tambah paket</a>
    </div>

    @if (count($packages))
        <div class="pkg-grid">
            @foreach ($packages as $package)
                <article class="pkg pkg--{{ $package->theme }} {{ $package->is_active ? '' : 'is-off' }}">
                    <header class="pkg-head">
                        <div>
                            <h3>{{ $package->name }}</h3>
                            <p>{{ $package->tagline }}@if ($package->highlight) <strong>{{ $package->highlight }}</strong>@endif</p>
                        </div>
                        <span class="pkg-chip">{{ $package->is_active ? 'Tayang' : 'Disembunyikan' }}</span>
                    </header>

                    <ul class="pkg-prices">
                        @foreach ($package->prices as $row)
                            <li>
                                <span>{{ $row['label'] }}@if (! empty($row['note'])) <em>{{ $row['note'] }}</em>@endif</span>
                                <b>{{ $row['price'] }}</b>
                            </li>
                        @endforeach
                    </ul>

                    <p class="pkg-meta">{{ count($package->features ?? []) }} keunggulan · urutan {{ $package->sort_order }} · kode <code>{{ $package->slug }}</code></p>

                    <footer class="pkg-actions">
                        <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn--white btn--sm"><x-admin.icon name="edit" :size="16" /> Ubah</a>
                        <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" data-confirm="Hapus paket {{ $package->name }}?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--outline-light btn--sm"><x-admin.icon name="trash" :size="16" /> Hapus</button>
                        </form>
                    </footer>
                </article>
            @endforeach
        </div>
    @else
        <div class="card empty empty--page">
            <x-admin.icon name="tag" :size="32" />
            <p>Belum ada paket.</p>
            <small>Tambahkan paket pertama, atau jalankan <code>php artisan db:seed --class=ContentSeeder</code> untuk mengisi dari data awal.</small>
        </div>
    @endif
@endsection
