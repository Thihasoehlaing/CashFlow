@php
$links = [
    ['route' => 'dashboard', 'label' => __('navigation.dashboard'), 'icon' => 'heroicon-o-home'],
    ['route' => 'income.index', 'label' => __('navigation.income'), 'icon' => 'heroicon-o-arrow-trending-up'],
    ['route' => 'expenses.index', 'label' => __('navigation.expenses'), 'icon' => 'heroicon-o-arrow-trending-down'],
    ['route' => 'accounts.index', 'label' => __('navigation.accounts'), 'icon' => 'heroicon-o-banknotes'],
    ['route' => 'transfers.index', 'label' => __('navigation.transfers'), 'icon' => 'heroicon-o-arrows-right-left'],
    ['route' => 'clients.index', 'label' => __('navigation.clients'), 'icon' => 'heroicon-o-user-group'],
    ['route' => 'projects.index', 'label' => __('navigation.projects'), 'icon' => 'heroicon-o-briefcase'],
    ['route' => 'quotations.index', 'label' => __('navigation.quotations'), 'icon' => 'heroicon-o-document-text'],
    ['route' => 'invoices.index', 'label' => __('navigation.invoices'), 'icon' => 'heroicon-o-receipt-percent'],
    ['route' => 'settings.index', 'label' => __('navigation.settings'), 'icon' => 'heroicon-o-cog-6-tooth'],
];
$mobile = array_slice($links, 0, 3);
@endphp
<aside class="fixed inset-y-0 left-0 z-40 hidden w-[260px] border-r border-[var(--color-border)] bg-[var(--color-surface)] lg:flex lg:flex-col">
    <a href="{{ route('dashboard') }}" class="flex h-24 items-center px-7">
        <span class="group inline-grid gap-1">
            <span class="text-3xl font-extrabold text-[var(--color-primary)]">CashFlow</span>
            <span class="h-px w-16 bg-[var(--color-primary)]/60 transition-all group-hover:w-full"></span>
        </span>
    </a>
    <nav class="flex-1 space-y-1 px-4">
        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}" class="nav-link {{ request()->routeIs(str($link['route'])->before('.')->append('.*')->toString()) || request()->routeIs($link['route']) ? 'active' : '' }}">
                <x-dynamic-component :component="$link['icon']" class="size-5" /> <span>{{ $link['label'] }}</span>
            </a>
        @endforeach
    </nav>
    <div class="space-y-4 border-t border-[var(--color-border)] p-5">
        <div class="flex rounded-lg border border-[var(--color-border)] p-1 text-xs font-semibold">
            <a class="flex-1 rounded-md px-3 py-2 text-center {{ app()->getLocale() === 'en' ? 'bg-[var(--color-primary)] text-black' : 'text-[var(--color-text-muted)]' }}" href="{{ route('lang.switch', 'en') }}">EN</a>
            <a class="flex-1 rounded-md px-3 py-2 text-center {{ app()->getLocale() === 'my' ? 'bg-[var(--color-primary)] text-black' : 'text-[var(--color-text-muted)]' }}" href="{{ route('lang.switch', 'my') }}">MY</a>
        </div>
        <div class="flex items-center justify-between gap-3 text-sm">
            <span class="truncate text-[var(--color-text-muted)]">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-[var(--color-primary)] hover:text-[var(--color-primary-hover)]">{{ __('navigation.logout') }}</button></form>
        </div>
    </div>
</aside>
<nav x-data="{ more: false }" class="fixed inset-x-0 bottom-0 z-50 border-t border-[var(--color-border)] bg-[var(--color-surface)] lg:hidden">
    <div class="grid grid-cols-5">
        @foreach ($mobile as $link)
            <a href="{{ route($link['route']) }}" class="mobile-link"><x-dynamic-component :component="$link['icon']" class="size-6" /><span class="sr-only">{{ $link['label'] }}</span></a>
        @endforeach
        <a href="{{ route('quotations.index') }}" class="mobile-link"><x-heroicon-o-document-text class="size-6" /><span class="sr-only">{{ __('navigation.quotations') }}</span></a>
        <button type="button" @click="more = true" class="mobile-link"><x-heroicon-o-ellipsis-horizontal class="size-6" /><span class="sr-only">{{ __('navigation.more') }}</span></button>
    </div>
    <div x-cloak x-show="more" class="fixed inset-0 z-50 bg-black/60" @click.self="more = false">
        <div x-show="more" x-transition class="absolute inset-x-0 bottom-0 rounded-t-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
            <div class="mb-4 h-1 w-12 rounded-full bg-[var(--color-text-faint)] mx-auto"></div>
            <div class="grid gap-2">@foreach (array_slice($links, 4) as $link)<a href="{{ route($link['route']) }}" class="nav-link"><x-dynamic-component :component="$link['icon']" class="size-5" />{{ $link['label'] }}</a>@endforeach</div>
        </div>
    </div>
</nav>
