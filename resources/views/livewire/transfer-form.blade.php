<div class="grid gap-4 md:grid-cols-2" wire:loading.class="opacity-60">
    <label class="form-field">{{ __('transfers.from_account') }}<select class="field" name="from_account_id" wire:model.live="fromAccountId"><option value="">{{ __('common.select') }}</option>@foreach ($accounts as $account)<option value="{{ $account->id }}">{{ $account->name }} ({{ $account->currency }})</option>@endforeach</select></label>
    <label class="form-field">{{ __('transfers.to_account') }}<select class="field" name="to_account_id" wire:model.live="toAccountId"><option value="">{{ __('common.select') }}</option>@foreach ($accounts as $account)<option value="{{ $account->id }}">{{ $account->name }} ({{ $account->currency }})</option>@endforeach</select></label>
    <label class="form-field">{{ __('transfers.from_amount') }}<input class="field font-mono" name="from_amount" type="number" step="0.01" wire:model.live="fromAmount"></label>
    <label class="form-field">{{ __('transfers.to_amount') }}<input class="field font-mono" name="to_amount" type="number" step="0.01" wire:model.live="toAmount"></label>
    <input type="hidden" name="from_currency" value="{{ $fromCurrency }}"><input type="hidden" name="to_currency" value="{{ $toCurrency }}"><input type="hidden" name="exchange_rate" value="{{ $exchangeRate }}">
    <p class="md:col-span-2 rounded-lg border border-[var(--color-border)] bg-[var(--color-primary-muted)] px-4 py-3 font-mono text-[var(--color-primary)]">1 {{ $fromCurrency }} = {{ number_format($exchangeRate, 6) }} {{ $toCurrency }}</p>
</div>
