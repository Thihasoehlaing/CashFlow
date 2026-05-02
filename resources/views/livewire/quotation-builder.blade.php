<div class="space-y-6" wire:loading.class="opacity-60">
    <input type="hidden" name="currency" value="{{ $currency }}">
    <input type="hidden" name="discount_type" value="{{ $discountType }}">
    <input type="hidden" name="discount_value" value="{{ $discountValue }}">
    <input type="hidden" name="tax_rate" value="{{ $taxRate }}">

    <div class="overflow-x-auto rounded-lg border border-[var(--color-border)]">
        <table class="min-w-[760px] w-full text-sm">
            <thead class="bg-[var(--color-surface-raised)] text-[var(--color-text-muted)]">
                <tr><th class="px-4 py-3 text-left">{{ __('quotations.description') }}</th><th class="px-4 py-3">{{ __('quotations.type') }}</th><th class="px-4 py-3">{{ __('quotations.qty') }}</th><th class="px-4 py-3">{{ __('quotations.unit_price') }}</th><th class="px-4 py-3">{{ __('quotations.amount') }}</th><th></th></tr>
            </thead>
            <tbody>
                @foreach ($items as $index => $item)
                    <tr class="animate-row border-t border-[var(--color-border)]" wire:key="line-{{ $index }}">
                        <td class="p-3"><input class="field" name="items[{{ $index }}][description]" wire:model.live="items.{{ $index }}.description" required></td>
                        <td class="p-3"><select class="field" name="items[{{ $index }}][item_type]" wire:model.live="items.{{ $index }}.item_type"><option value="fixed">{{ __('quotations.fixed') }}</option><option value="hourly">{{ __('quotations.hourly') }}</option></select></td>
                        <td class="p-3"><input class="field font-mono" type="number" step="0.01" min="0.01" name="items[{{ $index }}][quantity]" wire:model.live="items.{{ $index }}.quantity" required></td>
                        <td class="p-3"><input class="field font-mono" type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]" wire:model.live="items.{{ $index }}.unit_price" required></td>
                        <td class="p-3 font-mono text-[var(--color-primary)]">{{ number_format((float) ($item['amount'] ?? 0), 2) }}</td>
                        <td class="p-3 text-right"><button type="button" class="icon-btn danger" wire:click="removeItem({{ $index }})" title="{{ __('common.delete') }}">x</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <button type="button" wire:click="addItem" class="btn-secondary">{{ __('quotations.add_item') }}</button>

    <div class="grid gap-4 md:grid-cols-3">
        <label class="form-field">{{ __('quotations.currency') }}<select class="field" wire:model.live="currency">@foreach ($currencies as $currencyOption)<option>{{ $currencyOption }}</option>@endforeach</select></label>
        <label class="form-field">{{ __('quotations.discount') }}<select class="field" wire:model.live="discountType"><option value="flat">{{ __('quotations.flat') }}</option><option value="">{{ __('quotations.none') }}</option><option value="percentage">{{ __('quotations.percentage') }}</option></select></label>
        <label class="form-field">{{ __('quotations.discount_value') }}<input class="field font-mono" type="number" step="0.01" wire:model.live="discountValue"></label>
        <label class="form-field">{{ __('quotations.tax_rate') }}<input class="field font-mono" type="number" step="0.01" wire:model.live="taxRate"></label>
    </div>

    <div class="ml-auto max-w-md rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-4">
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt>{{ __('quotations.subtotal') }}</dt><dd class="font-mono">{{ $currency }} {{ number_format($subtotal, 2) }}</dd></div>
            <div class="flex justify-between"><dt>{{ __('quotations.discount') }}</dt><dd class="font-mono">{{ $currency }} {{ number_format($discountAmount, 2) }}</dd></div>
            <div class="flex justify-between"><dt>{{ __('quotations.tax') }}</dt><dd class="font-mono">{{ $currency }} {{ number_format($taxAmount, 2) }}</dd></div>
            <div class="flex justify-between border-t border-[var(--color-border)] pt-3 text-xl text-[var(--color-primary)]"><dt>{{ __('quotations.total') }}</dt><dd class="font-mono">{{ $currency }} {{ number_format($total, 2) }}</dd></div>
        </dl>
    </div>
</div>
