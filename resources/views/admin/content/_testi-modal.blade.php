{{-- Jendela tambah / ubah testimoni. Kirim ['t' => null] untuk testimoni baru. --}}
@php
    $isNew = ! $t;
    $bag   = $isNew ? 'testi-new' : 'testi-' . $t->id;
    $has   = $errors->hasBag($bag);
    $o     = fn ($key, $default = null) => $has ? old($key, $default) : $default;

    // Program yang diketik manual sebelumnya tetap muncul sebagai pilihan.
    $choices = $programs;
    if (! $isNew && $t->program && ! in_array($t->program, $choices, true)) {
        $choices[] = $t->program;
    }
@endphp

<x-admin.modal :id="$isNew ? 'testi-baru' : 'testi-' . $t->id" :title="$isNew ? 'Tambah testimoni' : 'Ubah testimoni'" subtitle="Tulis kata-kata orang tua murid. Isi 4 pilihan di bawah, lalu simpan." :bag="$bag" size="lg">
    <form method="POST" action="{{ $isNew ? route('admin.testimonials.store') : route('admin.testimonials.update', $t) }}" class="modal-form" data-summary-form novalidate>
        @csrf
        @unless ($isNew)
            @method('PUT')
        @endunless

        <div class="step">
            <p class="step-label"><i>1</i> Apa kata orang tua? <small>Maksimal 600 huruf. Pakai kata-kata asli dari orangnya.</small></p>
            <div class="field"><textarea name="quote" rows="4" maxlength="600" placeholder="Contoh: Anak saya awalnya takut air, sekarang berani dan senang ke kolam." aria-label="Kata-kata orang tua">{{ $o('quote', $t->quote ?? '') }}</textarea></div>
        </div>

        <div class="step">
            <p class="step-label"><i>2</i> Nama orang tua / murid</p>
            <div class="field"><input name="name" value="{{ $o('name', $t->name ?? '') }}" maxlength="100" placeholder="Contoh: Bu Dewi" aria-label="Nama"></div>
        </div>

        <div class="step">
            <p class="step-label"><i>3</i> Program yang diikuti</p>
            <div class="picks">
                @foreach ($choices as $program)
                    <x-admin.pick name="program" :value="$program" :label="$program" :checked="$o('program', $t->program ?? '') === $program" />
                @endforeach
            </div>
        </div>

        <div class="step">
            <p class="step-label"><i>4</i> Sudah boleh ditampilkan? <small>Hanya yang sudah memberi izin yang tampil di website.</small></p>
            <div class="picks">
                <x-admin.pick name="allowed" value="1" label="Sudah izin" :checked="(string) $o('allowed', ($t->is_published ?? false) ? '1' : '0') === '1'" />
                <x-admin.pick name="allowed" value="0" label="Belum izin" :checked="(string) $o('allowed', ($t->is_published ?? false) ? '1' : '0') === '0'" />
            </div>
        </div>

        <p class="summary" data-summary="Ringkasan: testimoni dari {name}, program {program}, {allowed}.">
            <b>Ringkasan:</b> <span data-summary-text></span>
        </p>

        <footer class="modal-foot">
            @unless ($isNew)
                <button type="submit" form="hapus-testi-{{ $t->id }}" class="btn btn--danger-outline">Hapus</button>
            @endunless
            <button type="button" class="btn btn--outline" data-modal-close>Batal</button>
            <button type="submit" class="btn btn--gold btn--grow">Simpan testimoni</button>
        </footer>
    </form>

    @unless ($isNew)
        <form id="hapus-testi-{{ $t->id }}" method="POST" action="{{ route('admin.testimonials.destroy', $t) }}" data-confirm="Hapus testimoni dari {{ $t->name }}?">
            @csrf
            @method('DELETE')
        </form>
    @endunless
</x-admin.modal>
