{{--
    Jendela tambah / ubah kolam. Dipakai: @include('admin.locations._modals', ['loc' => $location])
    Untuk kolam baru kirim: ['loc' => null]
--}}
@php
    $isNew = ! $loc;
    $bag   = $isNew ? 'kolam-new' : 'kolam-' . $loc->id;
    $has   = $errors->hasBag($bag);
    $o     = fn ($key, $default = null) => $has ? old($key, $default) : $default;
    $id    = $isNew ? 'kolam-baru' : 'kolam-ubah-' . $loc->id;
@endphp

<x-admin.modal :id="$id" :title="$isNew ? 'Tambah kolam' : 'Ubah data kolam'" :subtitle="$isNew ? 'Satu kolam = satu tempat latihan. Isi 4 pilihan di bawah, lalu simpan.' : 'Ubah bagian yang perlu saja. Bagian yang diubah ditandai biru.'" :bag="$bag" size="lg">
    <form method="POST" action="{{ $isNew ? route('admin.locations.store') : route('admin.locations.update', $loc) }}" class="modal-form" data-summary-form novalidate>
        @csrf
        @unless ($isNew)
            @method('PUT')
        @endunless

        <div class="step">
            <p class="step-label"><i>1</i> Apa nama kolamnya?</p>
            <div class="field"><input name="pool" value="{{ $o('pool', $loc->pool ?? '') }}" placeholder="Contoh: Kolam Renang Nirwana Eksekutif" maxlength="150" aria-label="Nama kolam" required></div>
        </div>

        <div class="step">
            <p class="step-label"><i>2</i> Di wilayah mana?</p>
            <div class="picks picks--cards picks--areas">
                @foreach (\App\Models\Location::AREAS as $area => $districts)
                    <x-admin.pick name="area" :value="$area" :label="$area" :sub="$districts" :card="true" :checked="$o('area', $loc->area ?? '') === $area" />
                @endforeach
            </div>
        </div>

        <div class="step">
            <p class="step-label"><i>3</i> Alamat lengkap <small>Boleh diisi nanti</small></p>
            <div class="field">
                <textarea name="address" rows="2" maxlength="250" placeholder="Contoh: Jl. Raya Wonorejo Permai No. 12, Wonorejo, Kec. Rungkut" aria-label="Alamat lengkap">{{ $o('address', $loc->address ?? '') }}</textarea>
            </div>
            <div class="field">
                <label for="{{ $id }}-maps">Link Google Maps <small>(boleh kosong)</small></label>
                <input id="{{ $id }}-maps" type="url" name="maps_url" value="{{ $o('maps_url', $loc->maps_url ?? '') }}" placeholder="https://maps.app.goo.gl/...">
                <p class="field-hint">Kalau kosong, tombol "Lihat kolam" di website mencari nama kolam ini di Google Maps.</p>
            </div>
        </div>

        <div class="step">
            <p class="step-label"><i>4</i> Menerima pendaftar baru?</p>
            <div class="picks">
                <x-admin.pick name="is_active" value="1" label="Ya, buka" :checked="(string) $o('is_active', ($loc->is_active ?? true) ? '1' : '0') === '1'" />
                <x-admin.pick name="is_active" value="0" label="Tutup sementara" :checked="(string) $o('is_active', ($loc->is_active ?? true) ? '1' : '0') === '0'" />
            </div>
        </div>

        <p class="summary" data-summary="Ringkasan: {pool}, {area}, {is_active}.">
            <b>Ringkasan:</b> <span data-summary-text></span>
        </p>

        <footer class="modal-foot">
            @unless ($isNew)
                <button type="submit" form="hapus-kolam-{{ $loc->id }}" class="btn btn--danger-outline">Hapus kolam</button>
            @endunless
            <button type="button" class="btn btn--outline" data-modal-close>Batal</button>
            <button type="submit" class="btn btn--gold btn--grow">{{ $isNew ? 'Simpan kolam' : 'Simpan perubahan' }}</button>
        </footer>
    </form>

    @unless ($isNew)
        <form id="hapus-kolam-{{ $loc->id }}" method="POST" action="{{ route('admin.locations.destroy', $loc) }}" data-confirm="Hapus {{ $loc->pool }}? Slot jadwalnya ikut terhapus. Data pendaftar tidak hilang.">
            @csrf
            @method('DELETE')
        </form>
    @endunless
</x-admin.modal>
