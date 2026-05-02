<div class="space-y-5" wire:loading.class="opacity-60">
    <div class="grid gap-4 md:grid-cols-3">
        <label class="form-field">{{ __('income.source') }}
            <select class="field" name="source" wire:model.live="source">@foreach (collect(['family' => __('income.family'), 'freelance' => __('income.freelance'), 'job' => __('income.job')])->sort()->keys() as $sourceOption)<option value="{{ $sourceOption }}">{{ __('income.'.$sourceOption) }}</option>@endforeach</select>
        </label>
        @if ($source === 'freelance')
            <label class="form-field">{{ __('income.client') }}<select class="field" name="client_id" wire:model.live="clientId"><option value="">{{ __('common.select') }}</option>@foreach ($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach</select></label>
            <label class="form-field">{{ __('income.billing_type') }}<select class="field" name="billing_type" wire:model.live="billingType"><option value="fixed">{{ __('income.fixed') }}</option><option value="hourly">{{ __('income.hourly') }}</option></select></label>
        @endif
        @if ($source === 'freelance' && $billingType === 'hourly')
            <label class="form-field">{{ __('income.hours') }}<input class="field font-mono" name="hours" type="number" step="0.01" wire:model.live="hours"></label>
            <label class="form-field">{{ __('income.rate_per_hour') }}<input class="field font-mono" name="rate_per_hour" type="number" step="0.01" wire:model.live="ratePerHour"></label>
        @endif
        @if ($source === 'freelance')
            <label class="form-field md:col-span-3">{{ __('projects.title') }}<select class="field" name="project_id" wire:model.live="projectId"><option value="">{{ __('common.select') }}</option>@foreach ($projects as $project)<option value="{{ $project->id }}">{{ $project->name }}</option>@endforeach</select></label>
        @endif
        <label class="form-field">{{ __('income.amount') }}<input class="field font-mono" name="amount" type="number" step="0.01" min="0.01" wire:model.live="amount" @readonly($source === 'freelance' && $billingType === 'hourly')></label>
        <label class="form-field">{{ __('income.currency') }}<select class="field" name="currency" wire:model.live="currency">@foreach ($currencies as $currencyOption)<option>{{ $currencyOption }}</option>@endforeach</select></label>
    </div>
    <p class="rounded-lg border border-[var(--color-border)] bg-[var(--color-primary-muted)] px-4 py-3 font-mono text-[var(--color-primary)]">{{ __('income.myr_preview') }}: {{ $this->myrPreview() }}</p>
    <label class="form-field">{{ $this->projectNameLabel() }}<input class="field" name="project_name" wire:model.live="projectName"></label>
</div>
