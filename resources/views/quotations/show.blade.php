<x-layouts::app :title="$quotation->quotation_number">
    <div class="space-y-5">
        <section class="card">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="font-mono text-[var(--color-primary)]">{{ $quotation->quotation_number }}</p>
                    <h2 class="text-3xl font-semibold">{{ $quotation->project_title }}</h2>
                    <p class="mt-2 text-[var(--color-text-muted)]">{{ $quotation->client->name }} - {{ $quotation->issue_date->format('d M Y') }}</p>

                    @if ($quotation->project)
                        <a class="mt-3 inline-flex rounded-full bg-[var(--color-primary-muted)] px-3 py-1 text-sm font-semibold text-[var(--color-primary)]" href="{{ route('projects.show', $quotation->project) }}">
                            {{ __('projects.title') }}: {{ $quotation->project->name }}
                        </a>
                    @endif
                </div>

                <div class="flex flex-wrap gap-2">
                    <x-status-badge :status="$quotation->status" />
                    <a class="btn-secondary" href="{{ route('quotations.pdf', $quotation) }}">PDF</a>

                    @if ($quotation->status === 'draft')
                        <a class="btn-secondary" href="{{ route('quotations.edit', $quotation) }}">{{ __('common.edit') }}</a>
                        <form method="POST" action="{{ route('quotations.status', $quotation) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="accepted">
                            <button class="btn-primary">{{ __('quotations.mark_accepted') }}</button>
                        </form>
                    @elseif ($quotation->invoice)
                        <a class="btn-primary" href="{{ route('invoices.edit', $quotation->invoice) }}">{{ __('quotations.view_invoice') }}</a>
                    @endif
                </div>
            </div>

            <p class="mt-6 font-mono text-3xl text-[var(--color-primary)]">{{ $quotation->currency }} {{ number_format($quotation->total, 2) }}</p>
        </section>

        <section class="card overflow-x-auto">
            <table class="w-full min-w-[760px] text-sm">
                <thead class="text-left text-[var(--color-text-muted)]">
                    <tr>
                        <th>#</th>
                        <th>{{ __('quotations.description') }}</th>
                        <th>{{ __('quotations.type') }}</th>
                        <th>{{ __('quotations.qty') }}</th>
                        <th>{{ __('quotations.unit_price') }}</th>
                        <th>{{ __('quotations.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($quotation->items as $item)
                        <tr class="border-t border-[var(--color-border)]">
                            <td class="py-3">{{ $loop->iteration }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->item_type }}</td>
                            <td class="font-mono">{{ $item->quantity }}</td>
                            <td><x-currency-badge :amount="$item->unit_price" :currency="$quotation->currency" /></td>
                            <td><x-currency-badge :amount="$item->amount" :currency="$quotation->currency" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="card ml-auto max-w-md">
            <div class="flex justify-between"><span>{{ __('quotations.subtotal') }}</span><x-currency-badge :amount="$quotation->subtotal" :currency="$quotation->currency" /></div>
            <div class="flex justify-between"><span>{{ __('quotations.discount') }}</span><x-currency-badge :amount="$quotation->discount_amount" :currency="$quotation->currency" /></div>
            <div class="flex justify-between"><span>{{ __('quotations.tax') }}</span><x-currency-badge :amount="$quotation->tax_amount" :currency="$quotation->currency" /></div>
            <div class="mt-3 flex justify-between border-t border-[var(--color-border)] pt-3 text-xl text-[var(--color-primary)]"><span>{{ __('quotations.total') }}</span><x-currency-badge :amount="$quotation->total" :currency="$quotation->currency" /></div>
        </section>
    </div>
</x-layouts::app>
