{{--
    Ikon garis untuk dashboard.
    Contoh: <x-admin.icon name="home" /> atau <x-admin.icon name="trash" :size="16" />
--}}
@props(['name' => 'dot', 'size' => 20])

@php
    $paths = [
        'home'      => '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h5v-6h4v6h5V10"/>',
        'users'     => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><circle cx="17.5" cy="9" r="2.5"/><path d="M17 14c2.7 0 4.5 1.8 4.5 4.5"/>',
        'tag'       => '<path d="M3 12V4h8l10 10-8 8L3 12z"/><circle cx="7.5" cy="8.5" r="1.5"/>',
        'pin'       => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0114 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
        'quote'     => '<path d="M4 5h16v11H9l-5 4V5z"/>',
        'image'     => '<rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="9" cy="10" r="1.8"/><path d="M4 18l5-5 4 4 3-3 4 4"/>',
        'logout'    => '<path d="M9 4H5v16h4"/><path d="M16 8l4 4-4 4M20 12H9"/>',
        'external'  => '<path d="M14 4h6v6"/><path d="M20 4l-9 9"/><path d="M18 14v5H5V6h5"/>',
        'plus'      => '<path d="M12 5v14M5 12h14"/>',
        'edit'      => '<path d="M4 20h4L19 9l-4-4L4 16v4z"/>',
        'trash'     => '<path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/>',
        'search'    => '<circle cx="11" cy="11" r="6.5"/><path d="M20 20l-4-4"/>',
        'menu'      => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'download'  => '<path d="M12 4v11M7 11l5 5 5-5M5 20h14"/>',
        'whatsapp'  => '<path d="M3 21l1.65-4.9A8.5 8.5 0 1 1 8 19.4L3 21z"/><path d="M9 10c0 3 2 5 5 5l1-1.5-2-1-1 .5c-.7-.4-1.2-.9-1.5-1.5l.5-1-1-2L9 10z"/>',
        'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="3"/><path d="M3 10h18M8 3v4M16 3v4"/>',
        'check'     => '<path d="M5 12.5l4.5 4.5L19 7"/>',
        'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'trend'     => '<path d="M4 16l5-5 4 4 7-8"/><path d="M15 7h5v5"/>',
        'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'back'      => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
        'eye'       => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
        'close'     => '<path d="M6 6l12 12M18 6L6 18"/>',
        'lock'      => '<rect x="5" y="11" width="14" height="9" rx="2.5"/><path d="M8 11V8a4 4 0 018 0v3"/>',
        'dot'       => '<circle cx="12" cy="12" r="3"/>',
    ];
@endphp

<svg class="icon" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths[$name] ?? $paths['dot'] !!}</svg>
