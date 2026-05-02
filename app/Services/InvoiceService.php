<?php

namespace App\Services;

use App\Models\Income;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function generateNumber(): string
    {
        $prefix = 'INV-'.now()->format('Y').'-';
        $last = Invoice::query()->where('invoice_number', 'like', $prefix.'%')->latest('id')->value('invoice_number');
        $next = $last ? ((int) str($last)->afterLast('-')->toString()) + 1 : 1;

        return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    /** @param array<int, array<string, mixed>> $items @return array{subtotal: float, discount_amount: float, tax_amount: float, total: float} */
    public function calculateTotals(array $items, ?string $discountType, float|int|string|null $discountValue, float|int|string|null $taxRate): array
    {
        return app(QuotationService::class)->calculateTotals($items, $discountType, $discountValue, $taxRate);
    }


    public function markAsPaid(Invoice $invoice, int $accountId, string $paidAt, bool $logIncome = false): Invoice
    {
        return DB::transaction(function () use ($invoice, $accountId, $paidAt, $logIncome): Invoice {
            $invoice->update(['status' => 'paid', 'paid_at' => $paidAt, 'payment_account_id' => $accountId]);

            if ($logIncome) {
                Income::query()->create([
                    'source' => 'freelance',
                    'client_id' => $invoice->client_id,
                    'project_id' => $invoice->project_id,
                    'project_name' => $invoice->project_title,
                    'billing_type' => 'fixed',
                    'amount' => $invoice->total,
                    'currency' => $invoice->currency,
                    'amount_in_myr' => app(CurrencyService::class)->convertToMYR($invoice->total, $invoice->currency),
                    'account_id' => $accountId,
                    'description' => 'Invoice '.$invoice->invoice_number,
                    'date' => $paidAt,
                    'type' => 'business',
                ]);
            }

            return $invoice->refresh();
        });
    }


    public function checkOverdue(): int
    {
        return Invoice::query()
            ->where('status', 'sent')
            ->whereDate('due_date', '<', today())
            ->update(['status' => 'overdue']);
    }
}
