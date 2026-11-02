@extends('admin.layouts.admin')

@section('title', 'Pengaturan')
@section('subtitle', 'Nomor, pesan otomatis, dan akun')

@section('content')
    @if ($errors->any() && ! $errors->hasBag('sandi'))
        <div class="modal-errors" role="alert">
            <strong>Mohon periksa lagi:</strong>
            <ul>@foreach ($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="settings-layout" data-savebar-form novalidate>
        @csrf
        @method('PUT')

        <div class="stack">
            <section class="card">
                <h2 class="card-title">Nomor dan kontak</h2>

                <div class="field">
                    <label for="s-wa">WhatsApp utama</label>
                    <input id="s-wa" name="wa_main" value="{{ old('wa_main', $wa_main) }}" placeholder="0831 9173 7789" inputmode="tel">
                    <p class="field-hint">Nomor ini yang menerima pendaftar dari website.</p>
                </div>
                <div class="field">
                    <label for="s-wa2">WhatsApp kedua</label>
                    <input id="s-wa2" name="wa_second" value="{{ old('wa_second', $wa_second) }}" placeholder="Belum diisi" inputmode="tel">
                    <p class="field-hint">Boleh dikosongkan.</p>
                </div>
                <div class="field">
                    <label for="s-ig">Instagram</label>
                    <input id="s-ig" name="instagram" value="{{ old('instagram', $instagram) }}" placeholder="leo_aswandani">
                </div>
            </section>

            <section class="card">
                <h2 class="card-title">Pendaftaran</h2>
                <div class="switch-row">
                    <div>
                        <b>Terima pendaftar baru</b>
                        <p class="field-hint">Matikan kalau kelas sedang penuh. Tombol "Daftar" di website akan menampilkan pemberitahuan "Pendaftaran sedang ditutup".</p>
                    </div>
                    <x-admin.switch name="accept_registrations" :checked="(bool) old('accept_registrations', $accepting)" />
                </div>
            </section>
        </div>

        <div class="stack">
            <section class="card">
                <h2 class="card-title">Pesan WhatsApp otomatis</h2>
                <p class="muted">Pesan ini muncul sudah terisi saat Bapak menekan tombol WhatsApp di data pendaftar.</p>
                <div class="field">
                    <textarea id="s-template" name="wa_template" rows="5" maxlength="600" aria-label="Isi pesan">{{ old('wa_template', $template) }}</textarea>
                </div>
                <div class="chips chips--insert" aria-label="Sisipkan">
                    <button type="button" class="chip" data-insert="{nama_ortu}" data-target="s-template">+ Nama orang tua</button>
                    <button type="button" class="chip" data-insert="{nama_anak}" data-target="s-template">+ Nama anak</button>
                    <button type="button" class="chip" data-insert="{paket}" data-target="s-template">+ Paket</button>
                </div>
            </section>

            <section class="card">
                <h2 class="card-title">Ukuran tulisan di panel ini</h2>
                <div class="picks picks--sizes" data-fontsize>
                    @foreach ($fontSizes as $value => $label)
                        <x-admin.pick name="font_size" :value="$value" :label="$label" :checked="old('font_size', $fontSize) === $value" />
                    @endforeach
                </div>
                <p class="field-hint">Ukuran baru dipakai di seluruh panel setelah disimpan.</p>
            </section>

            <section class="card">
                <h2 class="card-title">Akun</h2>
                <div class="field">
                    <label for="s-email">Email masuk</label>
                    <input id="s-email" value="{{ auth()->user()->email }}" readonly>
                </div>
                <button type="button" class="btn btn--outline btn--sm" data-modal-open="ganti-sandi">Ganti kata sandi</button>
            </section>
        </div>

        <div class="savebar" data-savebar>
            <p data-savebar-clean><x-admin.icon name="check" :size="16" /> Semua pengaturan sudah tersimpan</p>
            <p data-savebar-dirty hidden><x-admin.icon name="warning" :size="16" /> Ada perubahan yang belum disimpan</p>
            <span class="savebar-actions">
                <button type="button" class="btn btn--outline-light btn--sm" data-savebar-reset hidden>Batalkan</button>
                <button type="submit" class="btn btn--gold btn--sm">Simpan</button>
            </span>
        </div>
    </form>

    <x-admin.modal id="ganti-sandi" title="Ganti kata sandi" subtitle="Kata sandi baru minimal 8 karakter." bag="sandi">
        <form method="POST" action="{{ route('admin.settings.password') }}" class="modal-form" novalidate>
            @csrf
            @method('PUT')

            <div class="field">
                <label for="p-old">Kata sandi lama</label>
                <div class="input-affix">
                    <input id="p-old" type="password" name="current_password" autocomplete="current-password">
                    <button type="button" class="affix-btn" data-toggle-password="p-old">Tampilkan</button>
                </div>
            </div>
            <div class="field">
                <label for="p-new">Kata sandi baru</label>
                <div class="input-affix">
                    <input id="p-new" type="password" name="password" autocomplete="new-password">
                    <button type="button" class="affix-btn" data-toggle-password="p-new">Tampilkan</button>
                </div>
            </div>
            <div class="field">
                <label for="p-new2">Ulangi kata sandi baru</label>
                <input id="p-new2" type="password" name="password_confirmation" autocomplete="new-password">
            </div>

            <footer class="modal-foot">
                <button type="button" class="btn btn--outline" data-modal-close>Batal</button>
                <button type="submit" class="btn btn--gold btn--grow">Ganti kata sandi</button>
            </footer>
        </form>
    </x-admin.modal>
@endsection
