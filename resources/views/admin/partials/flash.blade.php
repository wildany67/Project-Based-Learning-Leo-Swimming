{{-- Pesan sukses / gagal setelah menyimpan --}}
@if (session('success'))
    <div class="flash flash--success" role="status" data-flash>
        <x-admin.icon name="check" :size="18" />
        <span>{{ session('success') }}</span>
        <button type="button" class="flash-close" aria-label="Tutup" data-flash-close><x-admin.icon name="close" :size="16" /></button>
    </div>
@endif

@if (session('error'))
    <div class="flash flash--error" role="alert">
        <x-admin.icon name="close" :size="18" />
        <span>{{ session('error') }}</span>
    </div>
@endif
