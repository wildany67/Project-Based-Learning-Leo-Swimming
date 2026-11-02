{{-- Jendela "Tambah pendaftar manual". Dipakai di Beranda dan halaman Pendaftar. Butuh $form dari RegistrationForm::options(). --}}
@php
    $has  = $errors->hasBag('manual');
    $o    = fn ($key, $default = null) => $has ? old($key, $default) : $default;
    $oldDays = (array) $o('schedule', []);
@endphp

<x-admin.modal id="pendaftar-manual" title="Tambah pendaftar manual" subtitle="Untuk orang tua yang mendaftar lewat WhatsApp atau datang langsung. Isi 7 pilihan di bawah, lalu simpan." bag="manual" size="lg">
    <form method="POST" action="{{ route('admin.registrations.store') }}" class="modal-form" data-summary-form novalidate>
        @csrf

        <div class="step">
            <p class="step-label"><i>1</i> Siapa nama anak?</p>
            <div class="form-grid">
                <div class="field"><label for="m-child">Nama anak</label><input id="m-child" name="child_name" value="{{ $o('child_name') }}" placeholder="Contoh: Rafi" required></div>
                <div class="field"><label for="m-age">Usia</label><input id="m-age" name="age" value="{{ $o('age') }}" placeholder="7 tahun" required></div>
            </div>
            <div class="picks">
                <x-admin.pick name="gender" value="Laki-laki" label="Laki-laki" :checked="$o('gender') === 'Laki-laki'" />
                <x-admin.pick name="gender" value="Perempuan" label="Perempuan" :checked="$o('gender') === 'Perempuan'" />
            </div>
        </div>

        <div class="step">
            <p class="step-label"><i>2</i> Siapa orang tuanya?</p>
            <div class="form-grid">
                <div class="field"><label for="m-parent">Nama orang tua</label><input id="m-parent" name="parent_name" value="{{ $o('parent_name') }}" placeholder="Bu Sari" required></div>
                <div class="field"><label for="m-phone">Nomor WhatsApp</label><input id="m-phone" name="phone" value="{{ $o('phone') }}" placeholder="0812 3456 7890" inputmode="tel" required></div>
            </div>
        </div>

        <div class="step">
            <p class="step-label"><i>3</i> Pilih paket</p>
            <div class="picks picks--stack">
                @foreach ($form['packages'] as $value => $label)
                    <x-admin.pick name="package" :value="$value" :label="$label" :checked="$o('package') === $value" />
                @endforeach
            </div>
        </div>

        <div class="step">
            <p class="step-label"><i>4</i> Di kolam mana?</p>
            <div class="picks picks--cards">
                @foreach ($form['locations'] as $loc)
                    <x-admin.pick name="location" :value="$loc->pool" :label="$loc->pool" :sub="$loc->area" :card="true" :checked="$o('location') === $loc->pool" />
                @endforeach
            </div>
        </div>

        <div class="step">
            <p class="step-label"><i>5</i> Hari dan jam berapa? <small>Boleh pilih lebih dari satu hari</small></p>
            <div class="picks">
                @foreach ($form['days'] as $day)
                    <x-admin.pick type="checkbox" name="schedule[]" :value="$day" :label="$day" :checked="in_array($day, $oldDays)" />
                @endforeach
            </div>
            <div class="picks picks--time">
                @foreach ($form['times'] as $time)
                    <x-admin.pick name="schedule_time" :value="str_replace(':', '.', $time)" :label="str_replace(':', '.', $time)" :checked="$o('schedule_time') === str_replace(':', '.', $time)" />
                @endforeach
            </div>
        </div>

        <div class="step">
            <p class="step-label"><i>6</i> Kapan mulai? <small>Boleh dikosongkan, otomatis hari ini</small></p>
            <div class="field"><input type="date" name="start_date" value="{{ $o('start_date') }}" aria-label="Tanggal mulai"></div>
        </div>

        <div class="step">
            <p class="step-label"><i>7</i> Status pendaftaran</p>
            <div class="picks">
                <x-admin.pick name="status" value="terkonfirmasi" label="Terkonfirmasi" :checked="$o('status', 'terkonfirmasi') === 'terkonfirmasi'" />
                <x-admin.pick name="status" value="menunggu" label="Menunggu" :checked="$o('status') === 'menunggu'" />
            </div>
            <p class="field-hint">Pilih "Terkonfirmasi" kalau orang tua sudah membayar. Slot jadwal langsung terisi.</p>
        </div>

        <p class="summary" data-summary="Ringkasan: {child_name} ({age}) daftar {package}, di {location}, {schedule[]} jam {schedule_time}, status {status}.">
            <b>Ringkasan:</b> <span data-summary-text></span>
        </p>

        <footer class="modal-foot">
            <button type="button" class="btn btn--outline" data-modal-close>Batal</button>
            <button type="submit" class="btn btn--gold btn--grow">Simpan pendaftar</button>
        </footer>
    </form>
</x-admin.modal>
