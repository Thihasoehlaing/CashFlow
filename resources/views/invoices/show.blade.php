<x-layouts::app :title="$invoice->invoice_number">
    <div class="space-y-5">
        <section class="card">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="font-mono text-[var(--color-primary)]">{{ $invoice->invoice_number }}</p>
                    <h2 class="text-3xl font-semibold">{{ $invoice->project_title }}</h2>
                    <p class="mt-2 text-[var(--color-text-muted)]">
                        {{ $invoice->client->name }} - {{ $invoice->issue_date->format('d M Y') }}
                    </p>
                    @if ($invoice->project)
                        <a class="mt-3 inline-flex rounded-full bg-[var(--color-primary-muted)] px-3 py-1 text-sm font-semibold text-[var(--color-primary)]" href="{{ route('projects.show', $invoice->project) }}">
                            {{ __('projects.title') }}: {{ $invoice->project->name }}
                        </a>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-status-badge :status="$invoice->status" />
                    <a class="btn-secondary" href="{{ route('invoices.pdf', $invoice) }}">PDF</a>
                </div>
            </div>
            <p class="mt-6 font-mono text-3xl text-[var(--color-primary)]">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</p>
        </section>

        <section class="card overflow-x-auto">
            <table class="w-full min-w-[760px] text-sm">
                <thead class="text-left text-[var(--color-text-muted)]">
                    <tr><th>#</th><th>{{ __('invoices.description') }}</th><th>{{ __('invoices.type') }}</th><th>{{ __('invoices.qty') }}</th><th>{{ __('invoices.unit_price') }}</th><th>{{ __('invoices.amount') }}</th></tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $item)
                        <tr class="border-t border-[var(--color-border)]"><td class="py-3">{{ $loop->iteration }}</td><td>{{ $item->description }}</td><td>{{ $item->item_type }}</td><td class="font-mono">{{ $item->quantity }}</td><td><x-currency-badge :amount="$item->unit_price" :currency="$invoice->currency" /></td><td><x-currency-badge :amount="$item->amount" :currency="$invoice->currency" /></td></tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="card ml-auto max-w-md">
            <div class="flex justify-between"><span>{{ __('invoices.subtotal') }}</span><x-currency-badge :amount="$invoice->subtotal" :currency="$invoice->currency" /></div>
            <div class="flex justify-between"><span>{{ __('invoices.discount') }}</span><x-currency-badge :amount="$invoice->discount_amount" :currency="$invoice->currency" /></div>
            <div class="flex justify-between"><span>{{ __('invoices.tax') }}</span><x-currency-badge :amount="$invoice->tax_amount" :currency="$invoice->currency" /></div>
            <div class="mt-3 flex justify-between border-t border-[var(--color-border)] pt-3 text-xl text-[var(--color-primary)]"><span>{{ __('invoices.total') }}</span><x-currency-badge :amount="$invoice->total" :currency="$invoice->currency" /></div>
        </section>

        <section class="card space-y-4">
            <div class="flex flex-wrap gap-2 text-sm text-[var(--color-text-muted)]">
                <span>{{ __('invoices.created') }}: {{ $invoice->created_at->format('d M Y') }}</span>
                <span>-</span>
                <span>{{ __('common.status.sent') }}</span>
                <span>-</span>
                <span>{{ $invoice->paid_at ? __('common.status.paid').': '.$invoice->paid_at->format('d M Y') : __('common.status.pending') }}</span>
            </div>
            @if ($invoice->status !== 'paid')
                <form class="grid gap-3 md:grid-cols-4" method="POST" action="{{ route('invoices.paid', $invoice) }}">
                    @csrf
                    <input class="field" type="date" name="paid_at" value="{{ today()->toDateString() }}">
                    <select class="field" name="payment_account_id">
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="log_income" value="1">{{ __('invoices.log_income') }}</label>
                    <button class="btn-primary">{{ __('invoices.mark_paid') }}</button>
                </form>
            @endif
        </section>
    </div>
</x-layouts::app>
