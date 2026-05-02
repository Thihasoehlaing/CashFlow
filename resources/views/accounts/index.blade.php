<x-layouts::app :title="__('accounts.title')">
    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <form class="grid w-full gap-3 sm:grid-cols-3 lg:w-auto" method="GET">
                <select class="field" name="type">
                    <option value="">{{ __('accounts.all_types') }}</option>
                    @foreach (collect(trans('accounts.types'))->sort()->keys() as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>{{ __("accounts.types.$type") }}</option>
                    @endforeach
                </select>

                <select class="field" name="currency">
                    <option value="">{{ __('accounts.all_currencies') }}</option>
                    @foreach ($currencies as $currency)
                        <option value="{{ $currency }}" @selected(request('currency') === $currency)>{{ $currency }}</option>
                    @endforeach
                </select>

                <button class="btn-secondary">{{ __('common.filter') }}</button>
            </form>

            <a class="btn-primary" href="{{ route('accounts.create') }}">{{ __('accounts.create') }}</a>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($accounts as $account)
                <article class="card">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-[var(--color-text-muted)]">{{ __("accounts.types.$account->type") }} - {{ $account->currency }}</p>
                            <h2 class="mt-1 text-2xl font-semibold">{{ $account->name }}</h2>
                        </div>
                        <x-status-badge :status="$account->is_active ? 'accepted' : 'draft'" />
                    </div>
                    <p class="mt-6 font-mono text-2xl text-[var(--color-primary)]">{{ $account->currency }} {{ number_format($account->current_balance, 2) }}</p>
                    <p class="font-mono text-sm text-[var(--color-text-muted)]">MYR {{ number_format($account->current_balance_in_myr, 2) }}</p>
                    <div class="mt-5 flex gap-3">
                        <a class="text-[var(--color-primary)]" href="{{ route('accounts.show', $account) }}">{{ __('common.view') }}</a>
                        <a class="text-[var(--color-primary)]" href="{{ route('accounts.edit', $account) }}">{{ __('common.edit') }}</a>
                        <x-confirm-modal :action="route('accounts.destroy', $account)" />
                    </div>
                </article>
            @empty
                <p class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5 text-sm text-[var(--color-text-muted)] md:col-span-2 xl:col-span-3">{{ __('common.no_results') }}</p>
            @endforelse
        </div>

        <x-pagination :paginator="$accounts" />
    </div>
</x-layouts::app>
