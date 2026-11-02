{{--
    Jendela pop-up (<dialog>). Dibuka dengan tombol: <button data-modal-open="id-modal">
    Kalau form di dalamnya gagal validasi, jendela terbuka sendiri dan menampilkan pesan kesalahan
    (pakai nama bag yang sama dengan validateWithBag() di controller).

    <x-admin.modal id="kolam-baru" title="Tambah kolam" subtitle="..." bag="kolam-new"> ... </x-admin.modal>
--}}
@props(['id', 'title', 'subtitle' => null, 'bag' => null, 'size' => 'md'])

@php $hasErrors = $bag && $errors->hasBag($bag); @endphp

<dialog id="{{ $id }}" class="modal modal--{{ $size }}" aria-labelledby="{{ $id }}-title" @if ($hasErrors) data-auto-open @endif>
    <div class="modal-box">
        <header class="modal-head">
            <div>
                <h3 id="{{ $id }}-title">{{ $title }}</h3>
                @if ($subtitle)
                    <p>{{ $subtitle }}</p>
                @endif
            </div>
            <button type="button" class="modal-x" data-modal-close aria-label="Tutup"><x-admin.icon name="close" :size="16" /></button>
        </header>

        @if ($hasErrors)
            <div class="modal-errors" role="alert">
                <strong>Mohon periksa lagi:</strong>
                <ul>
                    @foreach ($errors->getBag($bag)->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </div>
</dialog>
