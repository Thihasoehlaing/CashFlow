<x-layouts::app :title="$projectCost->exists ? __('projects.edit_cost') : __('projects.add_cost')">
    <form class="card space-y-5" method="POST" action="{{ $projectCost->exists ? route('project-costs.update', $projectCost) : route('project-costs.store') }}">
        @csrf
        @if ($projectCost->exists) @method('PUT') @endif

        <div class="grid gap-4 md:grid-cols-2">
            <label class="form-field">{{ __('projects.title') }}<select class="field" name="project_id">@foreach ($projects as $project)<option value="{{ $project->id }}" @selected(old('project_id', request('project_id', $projectCost->project_id)) == $project->id)>{{ $project->name }} - {{ $project->client->name }}</option>@endforeach</select></label>
            <label class="form-field">{{ __('projects.cost_name') }}<input class="field" name="name" value="{{ old('name', $projectCost->name) }}">@error('name') <span class="form-error">{{ $message }}</span> @enderror</label>
            <label class="form-field">{{ __('common.type') }}<select class="field" name="type">@foreach (collect(trans('projects.cost_types'))->sort()->keys() as $type)<option value="{{ $type }}" @selected(old('type', $projectCost->type ?: 'other') === $type)>{{ __("projects.cost_types.$type") }}</option>@endforeach</select></label>
            <label class="form-field">{{ __('projects.provider') }}<input class="field" name="provider" value="{{ old('provider', $projectCost->provider) }}"></label>
            <label class="form-field">{{ __('common.amount') }}<input class="field font-mono" name="amount" type="number" step="0.01" min="0.01" value="{{ old('amount', $projectCost->amount) }}"></label>
            <label class="form-field">{{ __('income.currency') }}<select class="field" name="currency">@foreach ($currencies as $currency)<option value="{{ $currency }}" @selected(old('currency', $projectCost->currency ?: 'MYR') === $currency)>{{ $currency }}</option>@endforeach</select></label>
            <label class="form-field">{{ __('projects.billing_cycle') }}<select class="field" name="billing_cycle">@foreach (collect(trans('projects.billing_cycles'))->sort()->keys() as $cycle)<option value="{{ $cycle }}" @selected(old('billing_cycle', $projectCost->billing_cycle ?: 'one_time') === $cycle)>{{ __("projects.billing_cycles.$cycle") }}</option>@endforeach</select></label>
            <label class="form-field">{{ __('projects.next_renewal_date') }}<input class="field" name="next_renewal_date" type="date" value="{{ old('next_renewal_date', optional($projectCost->next_renewal_date)->format('Y-m-d')) }}"></label>
            <label class="form-field">{{ __('accounts.title') }}<select class="field" name="account_id"><option value="">{{ __('common.select') }}</option>@foreach ($accounts as $account)<option value="{{ $account->id }}" @selected(old('account_id', $projectCost->account_id) == $account->id)>{{ $account->name }} - {{ $account->currency }}</option>@endforeach</select></label>
            <div class="grid gap-3 rounded-lg border border-[var(--color-border)] bg-[var(--color-surface-raised)] p-4">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_billable" value="1" @checked(old('is_billable', $projectCost->is_billable))> {{ __('projects.is_billable') }}</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="auto_log_expense" value="1" @checked(old('auto_log_expense', $projectCost->auto_log_expense))> {{ __('projects.auto_log_expense') }}</label>
            </div>
            <label class="form-field md:col-span-2">{{ __('common.notes') }}<textarea class="field" name="notes">{{ old('notes', $projectCost->notes) }}</textarea></label>
        </div>

        @if ($errors->any())
            <div class="rounded-lg border border-[var(--color-danger)] p-4 text-sm text-[var(--color-danger)]">{{ $errors->first() }}</div>
        @endif

        <button class="btn-primary">{{ __('common.save') }}</button>
    </form>
</x-layouts::app>
