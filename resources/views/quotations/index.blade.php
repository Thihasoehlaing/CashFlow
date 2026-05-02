<x-layouts::app :title="__('quotations.title')">
    <div class="space-y-5">
        <div class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-start">
            <form class="grid w-full gap-3 sm:grid-cols-2 xl:grid-cols-4" method="GET">
                <select class="field" name="status">
                    <option value="">{{ __('common.all_statuses') }}</option>
                    @foreach (collect(['accepted', 'draft'])->sortBy(fn ($status) => __("common.status.$status")) as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ __("common.status.$status") }}</option>
                    @endforeach
                </select>

                <select class="field" name="client_id">
                    <option value="">{{ __('clients.all') }}</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected(request('client_id') == $client->id)>{{ $client->name }}</option>
                    @endforeach
                </select>

                <input class="field" name="month" type="number" min="1" max="12" value="{{ request('month') }}" placeholder="{{ __('common.month') }}">
                <button class="btn-secondary">{{ __('common.filter') }}</button>
            </form>

            <a class="btn-primary" href="{{ route('quotations.create') }}">{{ __('quotations.create') }}</a>
        </div>

        <section class="card overflow-x-auto">
            <table class="w-full min-w-[920px] text-sm">
                <thead class="text-left text-[var(--color-text-muted)]">
                    <tr>
                        <th>{{ __('quotations.number') }}</th>
                        <th>{{ __('clients.title') }}</th>
                        <th>{{ __('quotations.project') }}</th>
                        <th>{{ __('quotations.total') }}</th>
                        <th>{{ __('common.status_label') }}</th>
                        <th>{{ __('quotations.valid_until') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($quotations as $row)
                        <tr class="animate-row border-t border-[var(--color-border)]">
                            <td class="py-3 font-mono">{{ $row->quotation_number }}</td>
                            <td>{{ $row->client->name }}</td>
                            <td>{{ $row->project_title }}</td>
                            <td><x-currency-badge :amount="$row->total" :currency="$row->currency" /></td>
                            <td><x-status-badge :status="$row->status" /></td>
                            <td>{{ $row->valid_until->format('d M Y') }}</td>
                            <td class="space-x-3 text-right">
                                <a class="text-[var(--color-primary)]" href="{{ route('quotations.show', $row) }}">{{ __('common.view') }}</a>
                                @if ($row->status === 'draft')
                                    <a class="text-[var(--color-primary)]" href="{{ route('quotations.edit', $row) }}">{{ __('common.edit') }}</a>
                                    <form class="inline" method="POST" action="{{ route('quotations.status', $row) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="accepted">
                                        <button class="text-[var(--color-primary)]">{{ __('quotations.mark_accepted') }}</button>
                                    </form>
                                @elseif ($row->invoice)
                                    <a class="text-[var(--color-primary)]" href="{{ route('invoices.edit', $row->invoice) }}">{{ __('quotations.view_invoice') }}</a>
                                @endif
                                <a class="text-[var(--color-primary)]" href="{{ route('quotations.pdf', $row) }}">PDF</a>
                                <x-confirm-modal :action="route('quotations.destroy', $row)" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <x-pagination :paginator="$quotations" />
        </section>
    </div>
</x-layouts::app>
