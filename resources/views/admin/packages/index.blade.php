@extends('admin.layouts.admin')

@section('title', 'Paket dan Harga')
@section('subtitle', 'Atur harga paket les')

@section('content')
    @php
        $has = $errors->hasBag('opsi');
        $o   = fn ($key, $default = null) => $has ? old($key, $default) : $default;
        $first = $packages->firstWhere('is_active', true) ?? $packages->first();
    @endphp

    <p class="banner">
        <x-admin.icon name="warning" :size="18" />
        <span>Ubah angka di kotak, lalu tekan <b>Simpan perubahan</b> di bawah. Harga di website ikut berubah otomatis. Di sebelah kanan ada contoh tampilannya, jadi Bapak bisa lihat dulu sebelum disimpan.</span>
    </p>

    @if ($errors->any() && ! $errors->hasBag('opsi'))
        <div class="modal-errors" role="alert">
            <strong>Mohon periksa lagi:</strong>
            <ul>@foreach ($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
        </div>
    @endif

    @if ($packages->isEmpty())
        <section class="card">
            <div class="empty empty--big">
                <strong>Belum ada paket di database.</strong>
                <p>Jalankan <code>php artisan db:seed --class=ContentSeeder</code> untuk mengisi paket awal dari config/leo.php.</p>
            </div>
        </section>
    @else
    <form method="POST" action="{{ route('admin.packages.update') }}" class="packages-layout" data-savebar-form novalidate>
        @csrf
        @method('PUT')

        <div class="stack">
            @foreach ($packages as $package)
                <section class="card pkg-card" data-package="{{ $package->id }}">
                    <header class="pkg-head">
                        <div class="pkg-title">
                            <input class="pkg-name" name="packages[{{ $package->id }}][name]" value="{{ old('packages.' . $package->id . '.name', $package->name) }}" maxlength="80" aria-label="Nama kelas" data-preview-name>
                            <input class="pkg-tagline" name="packages[{{ $package->id }}][tagline]" value="{{ old('packages.' . $package->id . '.tagline', $package->tagline) }}" maxlength="150" placeholder="Keterangan singkat kelas" aria-label="Keterangan singkat">
                        </div>
                        <div class="loc-toggle">
                            <span>Tampil di website</span>
                            <x-admin.switch :name="'packages[' . $package->id . '][is_active]'" :checked="$package->is_active" />
                        </div>
                    </header>

                    <div class="price-table" data-rows>
                        <div class="price-row price-row--head">
                            <span>Pertemuan</span><span>Harga</span><span>Gratis</span><span></span>
                        </div>
                        @foreach ($rows[$package->id] as $i => $row)
                            <div class="price-row" data-row>
                                <label class="price-meet">
                                    <input type="number" min="1" max="99" name="packages[{{ $package->id }}][rows][{{ $i }}][meetings]" value="{{ $row['meetings'] }}" aria-label="Jumlah pertemuan" data-preview-meet>
                                    <small>pertemuan</small>
                                </label>
                                <label class="price-rp">
                                    <small>Rp</small>
                                    <input type="number" min="0" step="1000" name="packages[{{ $package->id }}][rows][{{ $i }}][price]" value="{{ $row['price'] }}" aria-label="Harga" data-preview-price>
                                </label>
                                <label class="price-bonus">
                                    <small>+</small>
                                    <input type="number" min="0" max="20" name="packages[{{ $package->id }}][rows][{{ $i }}][bonus]" value="{{ $row['bonus'] }}" aria-label="Pertemuan gratis" data-preview-bonus>
                                </label>
                                <span class="price-tools">
                                    <label class="mini-switch" title="Tampilkan pilihan ini di website">
                                        <input type="hidden" name="packages[{{ $package->id }}][rows][{{ $i }}][visible]" value="0">
                                        <input type="checkbox" name="packages[{{ $package->id }}][rows][{{ $i }}][visible]" value="1" @checked($row['visible'])>
                                        <span class="mini-switch-ui" aria-hidden="true"></span>
                                        <span class="sr-only">Tampil di website</span>
                                    </label>
                                    <button type="button" class="icon-btn icon-btn--sm" data-remove-row aria-label="Hapus pilihan ini"><x-admin.icon name="trash" :size="15" /></button>
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn--outline btn--sm" data-modal-open="paket-opsi" data-select="package_id={{ $package->id }}">
                        <x-admin.icon name="plus" :size="15" /> Tambah pilihan paket
                    </button>

                    <details class="inline-edit">
                        <summary>Tampilan kartu di website (lanjutan)</summary>
                        <div class="form-grid">
                            <div class="field">
                                <label>Label di bawah judul <small>(mis. SMK Negeri 1 Surabaya)</small></label>
                                <input name="packages[{{ $package->id }}][highlight]" value="{{ old('packages.' . $package->id . '.highlight', $package->highlight) }}" maxlength="80">
                            </div>
                            <div class="field">
                                <label>Judul harga</label>
                                <input name="packages[{{ $package->id }}][price_label]" value="{{ old('packages.' . $package->id . '.price_label', $package->price_label) }}" maxlength="60">
                            </div>
                            <div class="field">
                                <label>Teks tombol daftar</label>
                                <input name="packages[{{ $package->id }}][cta]" value="{{ old('packages.' . $package->id . '.cta', $package->cta) }}" maxlength="60">
                            </div>
                            <div class="field">
                                <label>Angka besar samar <small>(hiasan)</small></label>
                                <input name="packages[{{ $package->id }}][ghost]" value="{{ old('packages.' . $package->id . '.ghost', $package->ghost) }}" maxlength="8">
                            </div>
                            <div class="field">
                                <label>Warna kartu</label>
                                <select name="packages[{{ $package->id }}][theme]">
                                    <option value="navy" @selected($package->theme === 'navy')>Biru tua</option>
                                    <option value="blue" @selected($package->theme === 'blue')>Biru terang</option>
                                </select>
                            </div>
                        </div>
                        <div class="field">
                            <label>Keunggulan <small>(satu baris = satu poin)</small></label>
                            <textarea name="packages[{{ $package->id }}][features]" rows="4" maxlength="800">{{ old('packages.' . $package->id . '.features', $package->features_text) }}</textarea>
                        </div>
                    </details>
                </section>
            @endforeach
        </div>

        {{-- Contoh tampilan di website --}}
        <aside class="preview" aria-label="Contoh tampilan di website">
            <p class="preview-label">Contoh tampilan di website</p>
            <div class="preview-card" data-preview-card="{{ $first->id }}">
                <h3 data-preview-title>{{ $first->name }}</h3>
                <ul data-preview-list>
                    @foreach ($rows[$first->id] as $row)
                        @if ($row['visible'])
                            <li><span>{{ $row['meetings'] }}× pertemuan@if ($row['bonus']) <em>+{{ $row['bonus'] }}× gratis</em>@endif</span><b>Rp{{ number_format($row['price'], 0, ',', '.') }}</b></li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <p class="preview-note">Contoh ini mengikuti kelas pertama dan berubah saat angka diketik.</p>
        </aside>

        <div class="savebar" data-savebar>
            <p data-savebar-clean><x-admin.icon name="check" :size="16" /> Semua perubahan sudah tersimpan</p>
            <p data-savebar-dirty hidden><x-admin.icon name="warning" :size="16" /> Ada perubahan yang belum disimpan</p>
            <span class="savebar-actions">
                <button type="button" class="btn btn--outline-light btn--sm" data-savebar-reset hidden>Batalkan</button>
                <button type="submit" class="btn btn--gold btn--sm">Simpan perubahan</button>
            </span>
        </div>
    </form>

    {{-- Tambah pilihan paket --}}
    <x-admin.modal id="paket-opsi" title="Tambah pilihan paket" subtitle="Paket = jumlah pertemuan + harganya. Isi 4 pilihan di bawah, lalu simpan." bag="opsi" size="md">
        <form method="POST" action="{{ route('admin.packages.option') }}" class="modal-form" data-summary-form novalidate>
            @csrf

            <div class="step">
                <p class="step-label"><i>1</i> Untuk kelas mana?</p>
                <div class="picks picks--cards">
                    @foreach ($packages as $package)
                        <x-admin.pick name="package_id" :value="$package->id" :label="$package->name" :sub="$package->tagline" :card="true" :checked="(int) $o('package_id', $packages->first()->id) === $package->id" />
                    @endforeach
                </div>
            </div>

            <div class="step step--row">
                <div>
                    <p class="step-label"><i>2</i> Berapa pertemuan?</p>
                    <div class="number-stepper" data-stepper>
                        <button type="button" data-step="-1" aria-label="Kurangi">−</button>
                        <input type="number" name="meetings" value="{{ $o('meetings', 8) }}" min="1" max="99" aria-label="Jumlah pertemuan">
                        <button type="button" data-step="1" aria-label="Tambah">+</button>
                    </div>
                </div>
                <div>
                    <p class="step-label"><i>3</i> Berapa harganya?</p>
                    <label class="price-rp price-rp--lg"><small>Rp</small><input type="number" name="price" value="{{ $o('price') }}" min="0" step="1000" placeholder="600000" aria-label="Harga"></label>
                </div>
            </div>

            <div class="step step--row">
                <div>
                    <p class="step-label"><i>4</i> Bonus pertemuan gratis? <small>0 kalau tidak ada</small></p>
                    <div class="number-stepper" data-stepper>
                        <button type="button" data-step="-1" aria-label="Kurangi">−</button>
                        <input type="number" name="bonus" value="{{ $o('bonus', 0) }}" min="0" max="20" aria-label="Bonus gratis">
                        <button type="button" data-step="1" aria-label="Tambah">+</button>
                    </div>
                </div>
                <div>
                    <p class="step-label">Tampil di website?</p>
                    <div class="picks">
                        <x-admin.pick name="visible" value="1" label="Tampilkan" :checked="(string) $o('visible', '1') === '1'" />
                        <x-admin.pick name="visible" value="0" label="Sembunyikan dulu" :checked="(string) $o('visible', '1') === '0'" />
                    </div>
                </div>
            </div>

            <p class="summary" data-summary="Ringkasan: akan dibuat 1 paket {package_id}, {meetings} pertemuan (+{bonus}× gratis), harga Rp{price}, {visible}.">
                <b>Ringkasan:</b> <span data-summary-text></span>
            </p>

            <footer class="modal-foot">
                <button type="button" class="btn btn--outline" data-modal-close>Batal</button>
                <button type="submit" class="btn btn--gold btn--grow">Simpan paket</button>
            </footer>
        </form>
    </x-admin.modal>
    @endif
@endsection
