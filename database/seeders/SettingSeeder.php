<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'business_name' => 'CashFlow',
            'business_email' => '',
            'business_phone' => '',
            'business_address' => '',
            'business_reg_no' => '',
            'business_logo' => 'images/logo.png',
            'bank_details' => [],
            'fx_rates' => ['USD' => 0.212766, 'SGD' => 0.285714, 'EUR' => 0.196078, 'GBP' => 0.166667],
            'default_tax_rate' => 8,
            'default_payment_terms' => 'Payment due within 14 days',
            'default_validity_days' => 30,
            'expense_categories' => ['Food & Drinks', 'Transport', 'Infrastructure', 'Subscription', 'AI Tools', 'Entertainment', 'Utilities', 'Shopping', 'Health', 'Other'],
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
