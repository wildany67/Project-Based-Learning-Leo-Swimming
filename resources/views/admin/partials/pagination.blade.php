{{-- Halaman sebelumnya / berikutnya. Pakai: @include('admin.partials.pagination', ['paginator' => $items]) --}}
@if ($paginator->hasPages())
    <nav class="pager" aria-label="Halaman">
        @if ($paginator->onFirstPage())
            <span class="btn btn--ghost is-disabled"><x-admin.icon name="back" :size="16" /> Sebelumnya</span>
        @else
            <a class="btn btn--ghost" href="{{ $paginator->previousPageUrl() }}"><x-admin.icon name="back" :size="16" /> Sebelumnya</a>
        @endif

        <span class="pager-info">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a class="btn btn--ghost" href="{{ $paginator->nextPageUrl() }}">Berikutnya <x-admin.icon name="arrow" :size="16" /></a>
        @else
            <span class="btn btn--ghost is-disabled">Berikutnya <x-admin.icon name="arrow" :size="16" /></span>
        @endif
    </nav>
@endif
