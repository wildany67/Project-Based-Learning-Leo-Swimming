@extends('admin.layouts.admin')

@section('title', 'Detail pendaftar')

@section('content')
    <a href="{{ route('admin.registrations.index') }}" class="back-link"><x-admin.icon name="back" :size="16" /> Kembali ke daftar</a>

    <div class="page-head">
        <div>
            <h2>{{ $registration->child_name }}</h2>
            <p>Mendaftar {{ $registration->created_label }}</p>
        </div>
        <span class="badge badge--lg badge--{{ $registration->status }}">{{ $registration->status_label }}</span>
    </div>

    <div class="grid grid--detail">

        {{-- Data lengkap --}}
        <div class="stack">
            <article class="card">
                <header class="card-head"><div><h3>Data anak</h3></div></header>
                <dl class="details">
                    <div><dt>Nama anak</dt><dd>{{ $registration->child_name }}</dd></div>
                    <div><dt>Jenis kelamin</dt><dd>{{ $registration->gender }}</dd></div>
                    <div><dt>Usia</dt><dd>{{ $registration->age }}</dd></div>
                </dl>
            </article>

            <article class="card">
                <header class="card-head"><div><h3>Orang tua</h3></div></header>
                <dl class="details">
                    <div><dt>Nama</dt><dd>{{ $registration->parent_name }}</dd></div>
                    <div><dt>Nomor HP</dt><dd>{{ $registration->phone }}</dd></div>
                </dl>
            </article>

            <article class="card">
                <header class="card-head"><div><h3>Pilihan kelas</h3></div></header>
                <dl class="details">
                    <div><dt>Paket</dt><dd>{{ $registration->package_label }}</dd></div>
                    <div><dt>Lokasi kolam</dt><dd>{{ $registration->location }}</dd></div>
                    <div><dt>Jadwal</dt><dd>{{ $registration->schedule_text }}</dd></div>
                    <div><dt>Ingin mulai</dt><dd>{{ $registration->start_label }}</dd></div>
                </dl>
            </article>
        </div>

        {{-- Tindak lanjut --}}
        <div class="stack">
            <article class="card">
                <header class="card-head"><div><h3>Tindak lanjut</h3><p>Status dan catatan hanya terlihat oleh admin</p></div></header>

                <form method="POST" action="{{ route('admin.registrations.update', $registration) }}" class="form">
                    @csrf
                    @method('PUT')

                    <x-admin.select label="Status" name="status" :options="\App\Models\Registration::STATUSES" :value="old('status', $registration->status)" />
                    <x-admin.textarea label="Catatan" name="notes" :value="old('notes', $registration->notes)" :rows="5" placeholder="Mis. sudah dihubungi Senin, minta jadwal sore." />

                    <button type="submit" class="btn btn--primary btn--block">Simpan</button>
                </form>
            </article>

            <article class="card">
                <a href="{{ $registration->wa_url }}" class="btn btn--whatsapp btn--block" target="_blank" rel="noopener">
                    <x-admin.icon name="whatsapp" :size="18" /> Chat {{ $registration->parent_name }}
                </a>
                <p class="field-hint">Pesan pembuka sudah terisi, tinggal dikirim.</p>
            </article>

            <article class="card card--danger">
                <header class="card-head"><div><h3>Hapus data</h3><p>Data yang dihapus tidak bisa dikembalikan.</p></div></header>
                <form method="POST" action="{{ route('admin.registrations.destroy', $registration) }}" data-confirm="Hapus data {{ $registration->child_name }}? Ini tidak bisa dibatalkan.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn--danger btn--block"><x-admin.icon name="trash" :size="18" /> Hapus pendaftar ini</button>
                </form>
            </article>
        </div>
    </div>
@endsection
