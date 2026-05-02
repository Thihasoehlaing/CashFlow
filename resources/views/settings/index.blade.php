<x-layouts::app :title="__('settings.title')">
    <form
        x-data="{
            tab: 'business',
            categories: @js(old('expense_categories', $settings['expense_categories'])),
            fx: @js(old('fx_rates', $settings['fx_rates'])),
            newCurrency: '',
            addCurrency() {
                const code = this.newCurrency.trim().toUpperCase();

                if (! code) {
                    return;
                }

                this.fx[code] = this.fx[code] || 1;
                this.newCurrency = '';
            },
            sortCategories() {
                this.categories = this.categories.sort((first, second) => first.localeCompare(second));
            },
        }"
        class="space-y-5"
        method="POST"
        action="{{ route('settings.update') }}"
    >
        @csrf
        @method('PUT')

        <div class="flex flex-wrap gap-2">
            @foreach (['business', 'accounts', 'fx', 'defaults'] as $tab)
                <button
                    type="button"
                    class="btn-secondary"
                    :class="tab === '{{ $tab }}' ? 'text-[var(--color-primary)] border-[var(--color-primary)]' : ''"
                    @click="tab = '{{ $tab }}'"
                >
                    {{ __('settings.tabs.'.$tab) }}
                </button>
            @endforeach
        </div>

        <section x-cloak x-show="tab === 'business'" class="card grid gap-4 md:grid-cols-2">
            <label class="form-field">
                {{ __('settings.business_name') }}
                <input class="field" name="business_name" value="{{ old('business_name', $settings['business_name']) }}">
                @error('business_name') <span class="form-error">{{ $message }}</span> @enderror
            </label>

            <label class="form-field">
                {{ __('settings.business_email') }}
                <input class="field" name="business_email" value="{{ old('business_email', $settings['business_email']) }}">
                @error('business_email') <span class="form-error">{{ $message }}</span> @enderror
            </label>

            <label class="form-field">
                {{ __('settings.business_phone') }}
                <input class="field" name="business_phone" value="{{ old('business_phone', $settings['business_phone']) }}">
                @error('business_phone') <span class="form-error">{{ $message }}</span> @enderror
            </label>

            <label class="form-field">
                {{ __('settings.business_reg_no') }}
                <input class="field" name="business_reg_no" value="{{ old('business_reg_no', $settings['business_reg_no']) }}">
                @error('business_reg_no') <span class="form-error">{{ $message }}</span> @enderror
            </label>

            <label class="form-field md:col-span-2">
                {{ __('settings.business_address') }}
                <textarea class="field" name="business_address">{{ old('business_address', $settings['business_address']) }}</textarea>
                @error('business_address') <span class="form-error">{{ $message }}</span> @enderror
            </label>
        </section>

        <section x-cloak x-show="tab === 'accounts'" class="card space-y-4">
            @forelse ($currencies as $currency)
                <label class="form-field">
                    {{ __('settings.pdf_account_for', ['currency' => $currency]) }}
                    <select class="field" name="bank_details[{{ $currency }}]">
                        <option value="">{{ __('common.select') }}</option>
                        @foreach ($accounts->where('currency', $currency) as $account)
                            <option value="{{ $account->id }}" @selected((string) old('bank_details.'.$currency, $settings['bank_details'][$currency] ?? '') === (string) $account->id)>
                                {{ $account->name }} {{ $account->account_number }}
                            </option>
                        @endforeach
                    </select>
                    @error('bank_details.'.$currency) <span class="form-error">{{ $message }}</span> @enderror
                </label>
            @empty
                <p class="text-sm text-[var(--color-text-muted)]">{{ __('settings.no_accounts') }}</p>
            @endforelse
        </section>

        <section x-cloak x-show="tab === 'fx'" class="card space-y-3">
            <template x-for="(rate, code) in fx" :key="code">
                <label class="form-field">
                    <span x-text="'1 MYR = ' + code"></span>
                    <input class="field font-mono" type="number" step="0.000001" x-bind:name="'fx_rates[' + code + ']'" x-model="fx[code]">
                </label>
            </template>
            @error('fx_rates') <span class="form-error">{{ $message }}</span> @enderror
            @error('fx_rates.*') <span class="form-error">{{ $message }}</span> @enderror

            <div class="flex flex-col gap-2 sm:flex-row">
                <input
                    class="field uppercase sm:max-w-36"
                    x-model="newCurrency"
                    maxlength="3"
                    placeholder="{{ __('settings.currency_code') }}"
                    @keydown.enter.prevent="addCurrency()"
                >
                <button type="button" class="btn-secondary" @click="addCurrency()">{{ __('settings.add_currency') }}</button>
            </div>
        </section>

        <section x-cloak x-show="tab === 'defaults'" class="card grid gap-4 md:grid-cols-2">
            <label class="form-field">
                {{ __('settings.default_tax_rate') }}
                <input class="field font-mono" name="default_tax_rate" type="number" step="0.01" value="{{ old('default_tax_rate', $settings['default_tax_rate']) }}">
                @error('default_tax_rate') <span class="form-error">{{ $message }}</span> @enderror
            </label>

            <label class="form-field">
                {{ __('settings.default_validity_days') }}
                <input class="field font-mono" name="default_validity_days" type="number" value="{{ old('default_validity_days', $settings['default_validity_days']) }}">
                @error('default_validity_days') <span class="form-error">{{ $message }}</span> @enderror
            </label>

            <label class="form-field md:col-span-2">
                {{ __('settings.default_payment_terms') }}
                <textarea class="field" name="default_payment_terms">{{ old('default_payment_terms', $settings['default_payment_terms']) }}</textarea>
                @error('default_payment_terms') <span class="form-error">{{ $message }}</span> @enderror
            </label>

            <div class="md:col-span-2 space-y-2">
                <input type="hidden" name="expense_categories[]" value="">
                <template x-for="(category, index) in categories" :key="index">
                    <div class="flex gap-2">
                        <input class="field" x-bind:name="'expense_categories[' + index + ']'" x-model="categories[index]">
                        <button type="button" class="btn-secondary" @click="categories.splice(index, 1); sortCategories()">x</button>
                    </div>
                </template>
                @error('expense_categories') <span class="form-error">{{ $message }}</span> @enderror
                @error('expense_categories.*') <span class="form-error">{{ $message }}</span> @enderror
                <button type="button" class="btn-secondary" @click="categories.push('')">{{ __('settings.add_category') }}</button>
            </div>
        </section>

        <button class="btn-primary">{{ __('common.save') }}</button>
    </form>

    <form class="card mt-5" method="POST" action="{{ route('settings.logo') }}" enctype="multipart/form-data">
        @csrf
        <label class="form-field">
            {{ __('settings.logo') }}
            <input class="field" type="file" name="logo" accept="image/*">
            @error('logo') <span class="form-error">{{ $message }}</span> @enderror
        </label>
        <button class="btn-secondary mt-3">{{ __('settings.upload_logo') }}</button>
        @if ($settings['business_logo'])
            <img class="mt-4 h-20" src="{{ asset($settings['business_logo']) }}" alt="Logo">
        @endif
    </form>
</x-layouts::app>
