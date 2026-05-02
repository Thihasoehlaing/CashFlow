@props(['title' => null])
@php
$pageTitle = $title ?? __('navigation.dashboard');
$sections = [
    ['patterns' => ['dashboard'], 'label' => __('navigation.dashboard'), 'icon' => 'heroicon-o-home', 'route' => 'dashboard'],
    ['patterns' => ['income.*'], 'label' => __('navigation.income'), 'icon' => 'heroicon-o-arrow-trending-up', 'route' => 'income.index'],
    ['patterns' => ['expenses.*'], 'label' => __('navigation.expenses'), 'icon' => 'heroicon-o-arrow-trending-down', 'route' => 'expenses.index'],
    ['patterns' => ['accounts.*'], 'label' => __('navigation.accounts'), 'icon' => 'heroicon-o-banknotes', 'route' => 'accounts.index'],
    ['patterns' => ['transfers.*'], 'label' => __('navigation.transfers'), 'icon' => 'heroicon-o-arrows-right-left', 'route' => 'transfers.index'],
    ['patterns' => ['clients.*'], 'label' => __('navigation.clients'), 'icon' => 'heroicon-o-user-group', 'route' => 'clients.index'],
    ['patterns' => ['projects.*', 'project-costs.*'], 'label' => __('navigation.projects'), 'icon' => 'heroicon-o-briefcase', 'route' => 'projects.index'],
    ['patterns' => ['quotations.*'], 'label' => __('navigation.quotations'), 'icon' => 'heroicon-o-document-text', 'route' => 'quotations.index'],
    ['patterns' => ['invoices.*'], 'label' => __('navigation.invoices'), 'icon' => 'heroicon-o-receipt-percent', 'route' => 'invoices.index'],
    ['patterns' => ['settings.*', 'profile.*', 'appearance.*', 'security.*'], 'label' => __('navigation.settings'), 'icon' => 'heroicon-o-cog-6-tooth', 'route' => 'settings.index'],
];
$currentSection = collect($sections)->first(fn (array $section): bool => request()->routeIs(...$section['patterns'])) ?? $sections[0];
$isSectionIndex = $pageTitle === $currentSection['label'];
@endphp
<header class="fixed inset-x-0 top-0 z-30 border-b border-[var(--color-border)] bg-[var(--color-bg)]/90 backdrop-blur lg:left-[260px]">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
        <nav class="flex min-w-0 items-center gap-2 text-sm" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-2 rounded-lg border border-transparent px-2.5 py-2 text-[var(--color-text-muted)] transition hover:border-[var(--color-border)] hover:bg-[var(--color-surface)] hover:text-[var(--color-primary)]">
                <x-heroicon-o-home class="size-4" />
                <span class="hidden font-semibold sm:inline">{{ __('navigation.dashboard') }}</span>
            </a>
            @unless ($currentSection['label'] === __('navigation.dashboard') && $isSectionIndex)
                <x-heroicon-o-chevron-right class="size-4 shrink-0 text-[var(--color-text-faint)]" />
                <a href="{{ route($currentSection['route']) }}" class="flex min-w-0 shrink-0 items-center gap-2 rounded-lg px-2.5 py-2 text-[var(--color-text-muted)] transition hover:bg-[var(--color-surface)] hover:text-[var(--color-primary)]">
                    <x-dynamic-component :component="$currentSection['icon']" class="size-4 shrink-0" />
                    <span class="truncate font-semibold">{{ $currentSection['label'] }}</span>
                </a>
            @endunless
            @unless ($isSectionIndex)
                <x-heroicon-o-chevron-right class="size-4 shrink-0 text-[var(--color-text-faint)]" />
                <span class="min-w-0 truncate rounded-lg bg-[var(--color-primary-muted)] px-2.5 py-2 font-bold text-[var(--color-primary)]">{{ $pageTitle }}</span>
            @endunless
        </nav>
        <div class="flex items-center gap-3">
            <a href="{{ route('lang.switch', app()->getLocale() === 'en' ? 'my' : 'en') }}" class="shrink-0 rounded-full border border-[var(--color-border)] px-3 py-1 text-sm text-[var(--color-text-muted)]">{{ app()->getLocale() === 'en' ? 'MY' : 'EN' }}</a>
            <div class="grid size-9 place-items-center rounded-full bg-[var(--color-primary)] font-semibold text-black">{{ auth()->user()->initials() }}</div>
        </div>
    </div>
</header>
