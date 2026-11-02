{{-- Jendela tambah / ubah foto galeri. Kirim ['item' => null] untuk foto baru. --}}
@php
    $isNew = ! $item;
    $bag   = $isNew ? 'foto-new' : 'foto-' . $item->id;
    $has   = $errors->hasBag($bag);
    $o     = fn ($key, $default = null) => $has ? old($key, $default) : $default;
@endphp

<x-admin.modal :id="$isNew ? 'foto-baru' : 'foto-' . $item->id" :title="$isNew ? 'Tambah foto' : 'Ubah foto'" subtitle="Foto JPG, PNG, atau WEBP, maksimal 4 MB. Kecilkan dulu fotonya (sekitar 200 KB) supaya website cepat dibuka." :bag="$bag">
    <form method="POST" action="{{ $isNew ? route('admin.gallery.store') : route('admin.gallery.update', $item) }}" enctype="multipart/form-data" class="modal-form" novalidate>
        @csrf
        @unless ($isNew)
            @method('PUT')
        @endunless

        <div class="step">
            <p class="step-label"><i>1</i> Pilih foto @unless ($isNew)<small>Kosongkan kalau tidak ingin mengganti</small>@endunless</p>
            <div class="upload" data-upload>
                <div class="upload-preview" data-upload-preview>
                    @if (! $isNew && $item->image_url)
                        <img src="{{ $item->image_url }}" alt="Foto saat ini">
                    @else
                        <span><x-admin.icon name="image" :size="26" /> Pratinjau foto</span>
                    @endif
                </div>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" data-upload-input aria-label="File foto">
            </div>
        </div>

        <div class="step">
            <p class="step-label"><i>2</i> Keterangan foto</p>
            <div class="field"><input name="caption" value="{{ $o('caption', $item->caption ?? '') }}" maxlength="150" placeholder="Contoh: Anak belajar mengapung dibantu Pak Leo" aria-label="Keterangan foto"></div>
        </div>

        <div class="step">
            <p class="step-label"><i>3</i> Bentuk di galeri website</p>
            <div class="picks">
                @foreach (\App\Models\GalleryItem::SIZES as $value => $label)
                    <x-admin.pick name="size" :value="$value" :label="$label" :checked="$o('size', $item->size ?? 'small') === $value" />
                @endforeach
            </div>
        </div>

        <div class="step">
            <p class="step-label"><i>4</i> Tampil di website?</p>
            <div class="picks">
                <x-admin.pick name="is_active" value="1" label="Tampilkan" :checked="(string) $o('is_active', ($item->is_active ?? true) ? '1' : '0') === '1'" />
                <x-admin.pick name="is_active" value="0" label="Sembunyikan" :checked="(string) $o('is_active', ($item->is_active ?? true) ? '1' : '0') === '0'" />
            </div>
        </div>

        <footer class="modal-foot">
            @unless ($isNew)
                <button type="submit" form="hapus-foto-{{ $item->id }}" class="btn btn--danger-outline">Hapus foto</button>
            @endunless
            <button type="button" class="btn btn--outline" data-modal-close>Batal</button>
            <button type="submit" class="btn btn--gold btn--grow">{{ $isNew ? 'Simpan foto' : 'Simpan perubahan' }}</button>
        </footer>
    </form>

    @unless ($isNew)
        <form id="hapus-foto-{{ $item->id }}" method="POST" action="{{ route('admin.gallery.destroy', $item) }}" data-confirm="Hapus foto ini dari galeri?">
            @csrf
            @method('DELETE')
        </form>
    @endunless
</x-admin.modal>
