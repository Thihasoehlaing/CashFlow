<x-layouts::app :title="$project->exists ? __('projects.edit') : __('projects.create')">
    <form class="card space-y-5" method="POST" action="{{ $project->exists ? route('projects.update', $project) : route('projects.store') }}">
        @csrf
        @if ($project->exists) @method('PUT') @endif

        <div class="grid gap-4 md:grid-cols-2">
            <label class="form-field">{{ __('projects.name') }}<input class="field" name="name" value="{{ old('name', $project->name) }}">@error('name') <span class="form-error">{{ $message }}</span> @enderror</label>
            <label class="form-field">{{ __('clients.title') }}<select class="field" name="client_id">@foreach ($clients as $client)<option value="{{ $client->id }}" @selected(old('client_id', $project->client_id) == $client->id)>{{ $client->name }}</option>@endforeach</select>@error('client_id') <span class="form-error">{{ $message }}</span> @enderror</label>
            <label class="form-field">{{ __('projects.status') }}<select class="field" name="status">@foreach (collect(trans('projects.statuses'))->sort()->keys() as $status)<option value="{{ $status }}" @selected(old('status', $project->status ?: 'planned') === $status)>{{ __("projects.statuses.$status") }}</option>@endforeach</select></label>
            <label class="form-field">{{ __('projects.billing_type') }}<select class="field" name="billing_type">@foreach (collect(trans('projects.billing_types'))->sort()->keys() as $type)<option value="{{ $type }}" @selected(old('billing_type', $project->billing_type ?: 'paid') === $type)>{{ __("projects.billing_types.$type") }}</option>@endforeach</select></label>
            <label class="form-field">{{ __('projects.agreed_amount') }}<input class="field font-mono" name="agreed_amount" type="number" step="0.01" min="0" value="{{ old('agreed_amount', $project->agreed_amount) }}"></label>
            <label class="form-field">{{ __('income.currency') }}<select class="field" name="currency">@foreach ($currencies as $currency)<option value="{{ $currency }}" @selected(old('currency', $project->currency ?: 'MYR') === $currency)>{{ $currency }}</option>@endforeach</select></label>
            <label class="form-field">{{ __('projects.start_date') }}<input class="field" name="start_date" type="date" value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}"></label>
            <label class="form-field">{{ __('projects.end_date') }}<input class="field" name="end_date" type="date" value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}"></label>
            <label class="form-field">{{ __('projects.quotation') }}<select class="field" name="quotation_id"><option value="">{{ __('common.select') }}</option>@foreach ($quotations as $quotation)<option value="{{ $quotation->id }}" @selected(old('quotation_id', $project->quotation_id) == $quotation->id)>{{ $quotation->quotation_number }} - {{ $quotation->project_title }}</option>@endforeach</select></label>
            <label class="form-field">{{ __('projects.invoice') }}<select class="field" name="invoice_id"><option value="">{{ __('common.select') }}</option>@foreach ($invoices as $invoice)<option value="{{ $invoice->id }}" @selected(old('invoice_id', $project->invoice_id) == $invoice->id)>{{ $invoice->invoice_number }} - {{ $invoice->project_title }}</option>@endforeach</select></label>
            <label class="form-field">{{ __('projects.live_url') }}<input class="field" name="live_url" value="{{ old('live_url', $project->live_url) }}"></label>
            <label class="form-field">{{ __('projects.repository_url') }}<input class="field" name="repository_url" value="{{ old('repository_url', $project->repository_url) }}"></label>
            <label class="form-field md:col-span-2">{{ __('projects.admin_url') }}<input class="field" name="admin_url" value="{{ old('admin_url', $project->admin_url) }}"></label>
            <label class="form-field md:col-span-2">{{ __('common.notes') }}<textarea class="field" name="notes">{{ old('notes', $project->notes) }}</textarea></label>
        </div>

        @if ($errors->any())
            <div class="rounded-lg border border-[var(--color-danger)] p-4 text-sm text-[var(--color-danger)]">{{ $errors->first() }}</div>
        @endif

        <button class="btn-primary">{{ __('common.save') }}</button>
    </form>
</x-layouts::app>
