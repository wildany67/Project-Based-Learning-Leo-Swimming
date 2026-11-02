@extends('admin.layouts.admin')

@section('title', 'Detail Pendaftar')
@section('subtitle', $registration->code . ' · masuk ' . $registration->created_label)

@section('content')
    @php
        $r      = $registration;
        $steps  = ['menunggu' => 'Menunggu', 'terkonfirmasi' => 'Terkonfirmasi', 'selesai' => 'Selesai'];
        $order  = array_keys($steps);
        $index  = array_search($r->status, $order, true);
        $cancel = $r->status === 'dibatalkan';
    @endphp

    <a href="{{ route('admin.registrations.index') }}" class="back-link"><x-admin.icon name="chevron-l" :size="16" /> Kembali ke daftar</a>

    <div class="grid grid--detail">

        {{-- Kiri: data --}}
        <div class="stack">
            <section class="card">
                <h2 class="card-title">Data pendaftar</h2>
                <dl class="facts">
                    <div><dt>Kode</dt><dd>{{ $r->code }}</dd></div>
                    <div><dt>Nama anak</dt><dd>{{ $r->child_name }} · {{ strtolower($r->gender) }} · {{ $r->age }}</dd></div>
                    <div><dt>Orang tua</dt><dd>{{ $r->parent_name }} · {{ $r->phone }}</dd></div>
                    <div><dt>Paket</dt><dd>{{ $r->package_label }}</dd></div>
                    <div><dt>Kolam</dt><dd>{{ $r->location }}</dd></div>
                    <div><dt>Jadwal</dt><dd>{{ $r->schedule_text }}</dd></div>
                    <div><dt>Mulai</dt><dd>{{ $r->start_label }}</dd></div>
                    <div><dt>Daftar pada</dt><dd>{{ $r->created_label }}</dd></div>
                </dl>

                {{-- Atur hari dan jam latihan --}}
                <details class="inline-edit" @if (! $r->schedule_time) open @endif>
                    <summary>Atur hari dan jam latihan</summary>
                    <form method="POST" action="{{ route('admin.registrations.schedule', $r) }}" class="inline-form">
                        @csrf
                        @method('PATCH')
                        <p class="step-label">Hari</p>
                        <div class="picks">
                            @foreach ($days as $day)
                                <x-admin.pick type="checkbox" name="schedule[]" :value="$day" :label="$day" :checked="in_array($day, $r->schedule ?? [])" />
                            @endforeach
                        </div>
                        <p class="step-label">Jam</p>
                        <div class="picks picks--time">
                            @foreach ($times as $time)
                                <x-admin.pick name="schedule_time" :value="str_replace(':', '.', $time)" :label="str_replace(':', '.', $time)" :checked="$r->schedule_time === $time" />
                            @endforeach
                        </div>
                        <p class="field-hint">Jam ini yang membuat anak masuk ke slot di halaman Jadwal setelah terkonfirmasi.</p>
                        <button type="submit" class="btn btn--navy btn--sm">Simpan jadwal</button>
                    </form>
                </details>
            </section>

            <section class="card">
                <h2 class="card-title">Catatan {{ auth()->user()->name }}</h2>
                <form method="POST" action="{{ route('admin.registrations.notes', $r) }}">
                    @csrf
                    @method('PATCH')
                    <div class="field">
                        <textarea name="notes" rows="4" maxlength="2000" placeholder="Contoh: anak agak takut air, mulai pelan.">{{ old('notes', $r->notes) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn--outline btn--sm">Simpan catatan</button>
                </form>
            </section>
        </div>

        {{-- Kanan: status dan tindakan --}}
        <div class="stack">
            <section class="card">
                <header class="card-head">
                    <h2 class="card-title">Status pendaftaran</h2>
                    <span class="badge badge--{{ $r->status }}">{{ $r->status_label }}</span>
                </header>

                @if ($cancel)
                    <p class="notice notice--red">Pendaftaran ini dibatalkan. Slot jadwal tidak terisi.</p>
                @else
                    <ol class="steps" aria-label="Tahap pendaftaran">
                        @foreach ($steps as $key => $label)
                            @php $n = array_search($key, $order, true); @endphp
                            <li class="{{ $n < $index ? 'is-done' : '' }} {{ $n === $index ? 'is-current' : '' }}">
                                <i>{{ $n + 1 }}</i>
                                <span>{{ $label }}</span>
                            </li>
                        @endforeach
                    </ol>
                @endif

                <div class="action-row">
                    @if ($r->status === 'menunggu')
                        <form method="POST" action="{{ route('admin.registrations.status', $r) }}" class="grow">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="terkonfirmasi">
                            <button type="submit" class="btn btn--gold btn--block" @disabled(! $r->payment_checked)>Sudah bayar: Tandai Terkonfirmasi</button>
                        </form>
                    @elseif ($r->status === 'terkonfirmasi')
                        <form method="POST" action="{{ route('admin.registrations.status', $r) }}" class="grow">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="selesai">
                            <button type="submit" class="btn btn--gold btn--block">Tandai Selesai</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.registrations.status', $r) }}" class="grow">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $r->status === 'selesai' ? 'terkonfirmasi' : 'menunggu' }}">
                            <button type="submit" class="btn btn--outline btn--block">{{ $r->status === 'selesai' ? 'Buka lagi sebagai murid aktif' : 'Aktifkan lagi (Menunggu)' }}</button>
                        </form>
                    @endif

                    @if ($r->status === 'menunggu' || $r->status === 'terkonfirmasi')
                        <form method="POST" action="{{ route('admin.registrations.status', $r) }}" data-confirm="Batalkan pendaftaran {{ $r->child_name }}?">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="dibatalkan">
                            <button type="submit" class="btn btn--danger-outline">Batalkan</button>
                        </form>
                    @endif
                </div>
                <p class="field-hint">Slot jadwal otomatis terisi setelah dikonfirmasi.</p>
            </section>

            <a href="{{ $r->wa_url }}" class="btn btn--whatsapp btn--block btn--lg" target="_blank" rel="noopener">
                <x-admin.icon name="whatsapp" :size="20" /> Hubungi {{ $r->parent_name }} lewat WhatsApp (pesan sudah terisi)
            </a>

            <section class="card card--tight">
                <form method="POST" action="{{ route('admin.registrations.payment', $r) }}">
                    @csrf
                    @method('PATCH')
                    <label class="check check--box">
                        <input type="hidden" name="payment_checked" value="0">
                        <input type="checkbox" name="payment_checked" value="1" data-autosubmit @checked($r->payment_checked)>
                        <span>Bukti transfer sudah saya cek di WhatsApp</span>
                    </label>
                </form>
            </section>

            <section class="card">
                <h2 class="card-title">Riwayat perubahan</h2>
                <ul class="timeline">
                    @forelse ($r->logs as $log)
                        <li>
                            <b>{{ $log->text }}</b>
                            <small>{{ $log->created_at->locale('id')->translatedFormat('j M, H.i') }}</small>
                        </li>
                    @empty
                        <li><b>Belum ada riwayat.</b></li>
                    @endforelse
                </ul>
            </section>

            <form method="POST" action="{{ route('admin.registrations.destroy', $r) }}" data-confirm="Hapus data {{ $r->child_name }} secara permanen? Ini tidak bisa dibatalkan.">
                @csrf
                @method('DELETE')
                <button type="submit" class="link-danger"><x-admin.icon name="trash" :size="15" /> Hapus data pendaftar ini</button>
            </form>
        </div>
    </div>
@endsection
