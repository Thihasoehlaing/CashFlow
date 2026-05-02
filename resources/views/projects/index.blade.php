<x-layouts::app :title="__('projects.title')">
    <div class="space-y-5">
        <div class="flex flex-wrap justify-between gap-3">
            <form class="grid gap-3 sm:grid-cols-5" method="GET">
                <input class="field" name="search" value="{{ request('search') }}" placeholder="{{ __('common.search') }}">
                <select class="field" name="status">
                    <option value="">{{ __('common.all_statuses') }}</option>
                    @foreach (collect(trans('projects.statuses'))->sort()->keys() as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ __("projects.statuses.$status") }}</option>
                    @endforeach
                </select>
                <select class="field" name="billing_type">
                    <option value="">{{ __('projects.all_billing_types') }}</option>
                    @foreach (collect(trans('projects.billing_types'))->sort()->keys() as $type)
                        <option value="{{ $type }}" @selected(request('billing_type') === $type)>{{ __("projects.billing_types.$type") }}</option>
                    @endforeach
                </select>
                <select class="field" name="client_id">
                    <option value="">{{ __('clients.all') }}</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected(request('client_id') == $client->id)>{{ $client->name }}</option>
                    @endforeach
                </select>
                <button class="btn-secondary">{{ __('common.filter') }}</button>
            </form>
            <a class="btn-primary" href="{{ route('projects.create') }}">{{ __('projects.create') }}</a>
        </div>

        <section class="card overflow-x-auto">
            <table class="w-full min-w-[920px] text-sm">
                <thead class="text-left text-[var(--color-text-muted)]">
                    <tr>
                        <th class="py-2">{{ __('projects.name') }}</th>
                        <th>{{ __('clients.title') }}</th>
                        <th>{{ __('projects.billing_type') }}</th>
                        <th>{{ __('common.status_label') }}</th>
                        <th>{{ __('projects.agreed_amount') }}</th>
                        <th>{{ __('projects.cost_total') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        <tr class="animate-row border-t border-[var(--color-border)]">
                            <td class="py-3 font-semibold"><a class="text-[var(--color-primary)]" href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></td>
                            <td>{{ $project->client->name }}</td>
                            <td>{{ __("projects.billing_types.$project->billing_type") }}</td>
                            <td><x-status-badge :status="$project->status" /></td>
                            <td><x-currency-badge :amount="$project->agreed_amount ?? 0" :currency="$project->currency" /></td>
                            <td class="font-mono">MYR {{ number_format((float) ($project->costs_total_myr ?? 0), 2) }}</td>
                            <td class="space-x-3 text-right"><a class="text-[var(--color-primary)]" href="{{ route('projects.edit', $project) }}">{{ __('common.edit') }}</a><x-confirm-modal :action="route('projects.destroy', $project)" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-[var(--color-text-muted)]">{{ __('common.no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
            <x-pagination :paginator="$projects" />
        </section>
    </div>
</x-layouts::app>
