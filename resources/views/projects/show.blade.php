<x-layouts::app :title="$project->name">
    <div class="space-y-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <div class="flex flex-wrap items-center gap-2"><x-status-badge :status="$project->status" /><span class="rounded-full bg-[var(--color-primary-muted)] px-2.5 py-1 text-xs font-semibold text-[var(--color-primary)]">{{ __("projects.billing_types.$project->billing_type") }}</span></div>
                <p class="mt-2 text-sm text-[var(--color-text-muted)]">{{ $project->client->name }} @if($project->start_date) - {{ $project->start_date->format('d M Y') }} @endif</p>
            </div>
            <div class="flex gap-2"><a class="btn-secondary" href="{{ route('projects.edit', $project) }}">{{ __('common.edit') }}</a><a class="btn-primary" href="{{ route('project-costs.create', ['project_id' => $project->id]) }}">{{ __('projects.add_cost') }}</a></div>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <x-stat-card :label="__('projects.agreed_amount')" :value="$project->currency.' '.number_format((float) ($project->agreed_amount ?? 0), 2)" />
            <x-stat-card :label="__('projects.income_total')" :value="'MYR '.number_format($incomeTotal, 2)" tone="green" />
            <x-stat-card :label="__('projects.cost_total')" :value="'MYR '.number_format($costTotal, 2)" tone="red" />
            <x-stat-card :label="__('projects.profit')" :value="'MYR '.number_format($incomeTotal - $costTotal, 2)" />
        </div>

        <section class="card grid gap-3 text-sm md:grid-cols-3">
            @foreach (['live_url' => __('projects.live_url'), 'repository_url' => __('projects.repository_url'), 'admin_url' => __('projects.admin_url')] as $field => $label)
                <div><p class="text-[var(--color-text-muted)]">{{ $label }}</p>@if($project->{$field})<a class="text-[var(--color-primary)]" href="{{ $project->{$field} }}" target="_blank" rel="noreferrer">{{ $project->{$field} }}</a>@else<p class="text-[var(--color-text-faint)]">-</p>@endif</div>
            @endforeach
        </section>

        <section class="card overflow-x-auto">
            <div class="mb-4 flex items-center justify-between gap-3"><h2 class="text-lg font-bold">{{ __('projects.costs') }}</h2><p class="font-mono text-sm text-[var(--color-text-muted)]">{{ __('projects.billable_cost_total') }}: MYR {{ number_format($billableCostTotal, 2) }}</p></div>
            <table class="w-full min-w-[820px] text-sm">
                <thead class="text-left text-[var(--color-text-muted)]"><tr><th>{{ __('projects.cost_name') }}</th><th>{{ __('common.type') }}</th><th>{{ __('projects.provider') }}</th><th>{{ __('common.amount') }}</th><th>{{ __('projects.billing_cycle') }}</th><th>{{ __('projects.next_renewal_date') }}</th><th></th></tr></thead>
                <tbody>
                    @forelse ($project->costs as $cost)
                        <tr class="animate-row border-t border-[var(--color-border)]"><td class="py-3 font-semibold">{{ $cost->name }}</td><td>{{ __("projects.cost_types.$cost->type") }}</td><td>{{ $cost->provider ?: '-' }}</td><td><x-currency-badge :amount="$cost->amount" :currency="$cost->currency" /></td><td>{{ __("projects.billing_cycles.$cost->billing_cycle") }}</td><td>{{ $cost->next_renewal_date?->format('d M Y') ?? '-' }}</td><td class="space-x-3 text-right"><a class="text-[var(--color-primary)]" href="{{ route('project-costs.edit', $cost) }}">{{ __('common.edit') }}</a><x-confirm-modal :action="route('project-costs.destroy', $cost)" /></td></tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-[var(--color-text-muted)]">{{ __('common.no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <div class="grid gap-5 lg:grid-cols-2">
            <section class="card"><h2 class="mb-4 text-lg font-bold">{{ __('income.title') }}</h2><div class="space-y-2">@forelse($project->income as $income)<div class="flex justify-between rounded-lg bg-[var(--color-surface-raised)] p-3 text-sm"><span>{{ $income->date->format('d M Y') }} - {{ $income->account->name }}</span><x-currency-badge :amount="$income->amount" :currency="$income->currency" /></div>@empty<p class="text-sm text-[var(--color-text-muted)]">{{ __('common.no_results') }}</p>@endforelse</div></section>
            <section class="card"><h2 class="mb-4 text-lg font-bold">{{ __('invoices.title') }}</h2><div class="space-y-2">@forelse($project->invoices as $invoice)<a class="flex justify-between rounded-lg bg-[var(--color-surface-raised)] p-3 text-sm" href="{{ route('invoices.show', $invoice) }}"><span>{{ $invoice->invoice_number }} - {{ $invoice->project_title }}</span><x-status-badge :status="$invoice->status" /></a>@empty<p class="text-sm text-[var(--color-text-muted)]">{{ __('common.no_results') }}</p>@endforelse</div></section>
        </div>
    </div>
</x-layouts::app>
