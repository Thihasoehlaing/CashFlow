<?php

namespace App\Services;

use App\Models\Setting;

class CurrencyService
{
    /** @return array<string, float> */
    public function rates(): array
    {
        return array_merge(['MYR' => 1.0], Setting::get('fx_rates', ['USD' => 0.212766, 'SGD' => 0.285714, 'EUR' => 0.196078, 'GBP' => 0.166667]));
    }


    public function convertToMYR(float|int|string $amount, string $currency): float
    {
        $rates = $this->rates();

        return round((float) $amount / max((float) ($rates[strtoupper($currency)] ?? 1), 0.000001), 2);
    }


    public function formatAmount(float|int|string $amount, string $currency): string
    {
        return strtoupper($currency).' '.number_format((float) $amount, 2);
    }

    /** @return array<int, string> */
    public function getAvailableCurrencies(): array
    {
        $currencies = array_values(array_unique(array_merge(['MYR', 'USD', 'SGD', 'EUR', 'GBP'], array_keys($this->rates()))));
        sort($currencies);

        return $currencies;
    }


    public function exchangeRate(string $fromCurrency, string $toCurrency): float
    {
        $rates = $this->rates();
        $fromRate = (float) ($rates[strtoupper($fromCurrency)] ?? 1);
        $toRate = (float) ($rates[strtoupper($toCurrency)] ?? 1);

        return round($toRate / max($fromRate, 0.000001), 6);
    }
}
