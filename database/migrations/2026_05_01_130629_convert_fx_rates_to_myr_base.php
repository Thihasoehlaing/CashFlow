<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->invertFxRates();
    }

    public function down(): void
    {
        $this->invertFxRates();
    }

    private function invertFxRates(): void
    {
        $setting = DB::table('settings')->where('key', 'fx_rates')->first();

        if (! $setting || ! is_string($setting->value)) {
            return;
        }

        $rates = json_decode($setting->value, true);

        if (! is_array($rates)) {
            return;
        }

        $converted = collect($rates)
            ->filter(fn ($rate, string $currency): bool => strtoupper($currency) !== 'MYR' && is_numeric($rate) && (float) $rate > 0)
            ->mapWithKeys(fn ($rate, string $currency): array => [strtoupper($currency) => round(1 / (float) $rate, 6)])
            ->all();

        DB::table('settings')
            ->where('key', 'fx_rates')
            ->update([
                'value' => json_encode($converted, JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]);
    }
};
