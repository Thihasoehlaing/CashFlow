<div class="space-y-5" wire:loading.class="opacity-60">
    <div class="grid gap-4 md:grid-cols-3">
        <label class="form-field">{{ __('expenses.category') }}
            <select class="field" name="category" wire:model.live="category">
                <option value="">{{ __('common.select') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </select>
        </label>
        <label class="form-field">{{ __('expenses.amount') }}<input class="field font-mono" name="amount" type="number" step="0.01" min="0.01" wire:model.live="amount"></label>
        <label class="form-field">{{ __('expenses.currency') }}<select class="field" name="currency" wire:model.live="currency">@foreach ($currencies as $currencyOption)<option>{{ $currencyOption }}</option>@endforeach</select></label>
    </div>
    <p class="rounded-lg border border-[var(--color-border)] bg-[var(--color-primary-muted)] px-4 py-3 font-mono text-[var(--color-primary)]">{{ __('expenses.myr_preview') }}: {{ $this->myrPreview() }}</p>
</div>
