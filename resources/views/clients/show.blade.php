<x-layouts::app :title="$client->name">
    <div class="space-y-5">
        <div class="grid gap-4 md:grid-cols-3">
            <x-stat-card :label="__('clients.total_billed')" :value="'MYR '.number_format($totalBilled, 2)" />
            <x-stat-card :label="__('clients.total_paid')" :value="'MYR '.number_format($totalPaid, 2)" />
            <x-stat-card :label="__('clients.outstanding')" :value="'MYR '.number_format($outstanding, 2)" tone="red" />
        </div>

        <section class="card">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-2xl font-semibold">{{ __('projects.title') }}</h2>
                <a class="btn-secondary" href="{{ route('projects.create', ['client_id' => $client->id]) }}">{{ __('projects.create') }}</a>
            </div>
            <div class="mt-4 space-y-2">
                @forelse ($client->projects as $project)
                    <a class="flex flex-wrap items-center justify-between gap-3 rounded-lg bg-[var(--color-surface-raised)] p-3" href="{{ route('projects.show', $project) }}">
                        <span>{{ $project->name }}</span>
                        <span class="flex items-center gap-2">
                            <x-status-badge :status="$project->status" />
                            <x-currency-badge :amount="$project->agreed_amount ?? 0" :currency="$project->currency" />
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-[var(--color-text-muted)]">{{ __('common.no_results') }}</p>
                @endforelse
            </div>
        </section>

        <section class="card">
            <h2 class="text-2xl font-semibold">{{ __('quotations.title') }}</h2>
            <div class="mt-4 space-y-2">
                @forelse ($client->quotations as $quotation)
                    <a class="flex justify-between rounded-lg bg-[var(--color-surface-raised)] p-3" href="{{ route('quotations.show', $quotation) }}">
                        <span>{{ $quotation->quotation_number }} - {{ $quotation->project_title }}</span>
                        <x-status-badge :status="$quotation->status" />
                    </a>
                @empty
                    <p class="text-sm text-[var(--color-text-muted)]">{{ __('common.no_results') }}</p>
                @endforelse
            </div>
        </section>

        <section class="card">
            <h2 class="text-2xl font-semibold">{{ __('invoices.title') }}</h2>
            <div class="mt-4 space-y-2">
                @forelse ($client->invoices as $invoice)
                    <a class="flex justify-between rounded-lg bg-[var(--color-surface-raised)] p-3" href="{{ route('invoices.show', $invoice) }}">
                        <span>{{ $invoice->invoice_number }} - {{ $invoice->project_title }}</span>
                        <x-status-badge :status="$invoice->status" />
                    </a>
                @empty
                    <p class="text-sm text-[var(--color-text-muted)]">{{ __('common.no_results') }}</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts::app>
