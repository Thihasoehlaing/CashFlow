<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('settings.index', [
            'settings' => [
                'business_name' => Setting::get('business_name', 'CashFlow'),
                'business_email' => Setting::get('business_email', ''),
                'business_phone' => Setting::get('business_phone', ''),
                'business_address' => Setting::get('business_address', ''),
                'business_reg_no' => Setting::get('business_reg_no', ''),
                'business_logo' => Setting::get('business_logo', ''),
                'bank_details' => Setting::get('bank_details', []),
                'fx_rates' => Setting::get('fx_rates', []),
                'default_tax_rate' => Setting::get('default_tax_rate', 8),
                'default_payment_terms' => Setting::get('default_payment_terms', ''),
                'default_validity_days' => Setting::get('default_validity_days', 30),
                'expense_categories' => collect(Setting::get('expense_categories', []))->sort()->values()->all(),
            ],
            'accounts' => Account::query()->where('is_active', true)->orderBy('name')->get(),
            'currencies' => Account::query()->distinct()->orderBy('currency')->pluck('currency')->values(),
            'usedCategories' => Expense::query()->distinct()->pluck('category')->all(),
        ]);
    }


    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'business_email' => ['nullable', 'email', 'max:255'],
            'business_phone' => ['nullable', 'string', 'max:255'],
            'business_address' => ['nullable', 'string'],
            'business_reg_no' => ['nullable', 'string', 'max:255'],
            'bank_details' => ['nullable', 'array'],
            'bank_details.*' => ['nullable', 'integer', 'exists:accounts,id'],
            'fx_rates' => ['nullable', 'array'],
            'fx_rates.*' => ['nullable', 'numeric', 'min:0.000001'],
            'default_tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'default_payment_terms' => ['nullable', 'string'],
            'default_validity_days' => ['required', 'integer', 'min:1', 'max:365'],
            'expense_categories' => ['nullable', 'array'],
            'expense_categories.*' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'expense_categories') {
                $usedCategories = Expense::query()->distinct()->pluck('category')->all();
                $value = collect($value ?? [])
                    ->map(fn (?string $category): string => trim((string) $category))
                    ->merge($usedCategories)
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values()
                    ->all();
            }

            if ($key === 'fx_rates') {
                $value = collect($value ?? [])
                    ->filter(fn ($rate): bool => $rate !== null && $rate !== '')
                    ->mapWithKeys(fn ($rate, string $currency): array => [strtoupper($currency) => (float) $rate])
                    ->all();
            }

            if ($key === 'bank_details') {
                $value = collect($value ?? [])
                    ->filter(fn ($accountId): bool => $accountId !== null && $accountId !== '')
                    ->map(fn ($accountId): int => (int) $accountId)
                    ->all();
            }

            Setting::set($key, $value);
        }

        return back()->with('success', __('settings.updated'));
    }


    public function uploadLogo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'logo' => ['required', File::image()->max(2048)],
        ]);
        $path = $validated['logo']->store('logo', 'public');
        Setting::set('business_logo', 'storage/'.$path);

        return back()->with('success', __('settings.logo_updated'));
    }
}
