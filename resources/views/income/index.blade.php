<x-layouts::app :title="__('income.title')">
    <div class="space-y-5">
        <div class="flex flex-wrap justify-between gap-3">
            <form class="grid gap-3 sm:grid-cols-4" method="GET">
                <input class="field" name="month" type="number" min="1" max="12" value="{{ request('month') }}" placeholder="{{ __('common.month') }}">
                <input class="field" name="year" type="number" value="{{ request('year', now()->year) }}">
                <select class="field" name="source">
                    <option value="">{{ __('income.all_sources') }}</option>
                    @foreach (collect(['family' => __('income.family'), 'freelance' => __('income.freelance'), 'job' => __('income.job')])->sort()->keys() as $source)
                        <option value="{{ $source }}" @selected(request('source') === $source)>{{ __('income.'.$source) }}</option>
                    @endforeach
                </select>
                <button class="btn-secondary">{{ __('common.filter') }}</button>
            </form>
            <a class="btn-primary" href="{{ route('income.create') }}">{{ __('income.create') }}</a>
        </div>

        <div class="card">
            <p class="text-[var(--color-text-muted)]">{{ __('income.monthly_total') }}</p>
            <p class="mt-2 font-mono text-2xl text-[var(--color-primary)]">MYR {{ number_format($monthlyTotal, 2) }}</p>
        </div>

        <section class="card overflow-x-auto">
            <table class="w-full min-w-[980px] text-sm">
                <thead class="text-left text-[var(--color-text-muted)]">
                    <tr>
                        <th>{{ __('common.date') }}</th>
                        <th>{{ __('income.source') }}</th>
                        <th>{{ __('income.counterparty') }}</th>
                        <th>{{ __('income.project') }}</th>
                        <th>{{ __('common.description') }}</th>
                        <th>{{ __('accounts.account') }}</th>
                        <th>{{ __('income.amount') }}</th>
                        <th>{{ __('income.myr') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($income as $row)
                        <tr class="animate-row border-t border-[var(--color-border)]">
                            <td class="py-3">{{ $row->date->format('d M Y') }}</td>
                            <td>{{ __('income.'.$row->source) }}</td>
                            <td>
                                @if ($row->source === 'freelance')
                                    {{ $row->client?->name ?: '-' }}
                                @else
                                    {{ $row->project_name ?: '-' }}
                                @endif
                            </td>
                            <td>
                                @if ($row->project)
                                    <a class="text-[var(--color-primary)]" href="{{ route('projects.show', $row->project) }}">{{ $row->project->name }}</a>
                                @elseif ($row->source === 'freelance')
                                    {{ $row->project_name ?: '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $row->description ?: '-' }}</td>
                            <td>{{ $row->account->name }}</td>
                            <td><x-currency-badge :amount="$row->amount" :currency="$row->currency" /></td>
                            <td><x-currency-badge :amount="$row->amount_in_myr" currency="MYR" /></td>
                            <td class="space-x-3 text-right">
                                <a class="text-[var(--color-primary)]" href="{{ route('income.edit', $row) }}">{{ __('common.edit') }}</a>
                                <x-confirm-modal :action="route('income.destroy', $row)" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <x-pagination :paginator="$income" />
        </section>
    </div>
</x-layouts::app>
