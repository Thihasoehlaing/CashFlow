@props(['paginator'])
@if ($paginator->hasPages())
    <div class="mt-6 flex flex-col items-center justify-between gap-4 border-t border-[var(--color-border)] pt-4 text-sm text-[var(--color-text-muted)] sm:flex-row">
        <p>{{ __('common.showing', ['first' => $paginator->firstItem(), 'last' => $paginator->lastItem(), 'total' => $paginator->total()]) }}</p>
        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())<span class="page-btn opacity-40"><x-heroicon-o-chevron-left class="size-4" /></span>@else<a class="page-btn" href="{{ $paginator->previousPageUrl() }}"><x-heroicon-o-chevron-left class="size-4" /></a>@endif
            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                <a class="page-btn {{ $page === $paginator->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
            @endforeach
            @if ($paginator->hasMorePages())<a class="page-btn" href="{{ $paginator->nextPageUrl() }}"><x-heroicon-o-chevron-right class="size-4" /></a>@else<span class="page-btn opacity-40"><x-heroicon-o-chevron-right class="size-4" /></span>@endif
        </div>
    </div>
@endif
