<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Quotation;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function generateNumber(): string
    {
        $prefix = 'QUO-'.now()->format('Y').'-';
        $last = Quotation::query()->where('quotation_number', 'like', $prefix.'%')->latest('id')->value('quotation_number');
        $next = $last ? ((int) str($last)->afterLast('-')->toString()) + 1 : 1;

        return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    /** @param array<int, array<string, mixed>> $items @return array{subtotal: float, discount_amount: float, tax_amount: float, total: float} */
    public function calculateTotals(array $items, ?string $discountType, float|int|string|null $discountValue, float|int|string|null $taxRate): array
    {
        $subtotal = collect($items)->sum(fn (array $item): float => round((float) ($item['quantity'] ?? 1) * (float) ($item['unit_price'] ?? 0), 2));
        $discountValue = (float) ($discountValue ?? 0);
        $discountAmount = match ($discountType) {
            'percentage' => round($subtotal * min($discountValue, 100) / 100, 2),
            'flat' => min($subtotal, $discountValue),
            default => 0.0,
        };
        $taxable = max($subtotal - $discountAmount, 0);
        $taxAmount = round($taxable * (float) ($taxRate ?? 0) / 100, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'tax_amount' => $taxAmount,
            'total' => round($taxable + $taxAmount, 2),
        ];
    }

    public function convertToInvoice(Quotation $quotation): Invoice
    {
        return DB::transaction(function () use ($quotation): Invoice {
            $quotation->loadMissing('items');
            $invoice = Invoice::query()->create([
                'invoice_number' => app(InvoiceService::class)->generateNumber(),
                'quotation_id' => $quotation->id,
                'client_id' => $quotation->client_id,
                'project_id' => $quotation->project_id,
                'project_title' => $quotation->project_title,
                'status' => 'draft',
                'currency' => $quotation->currency,
                'subtotal' => $quotation->subtotal,
                'discount_type' => $quotation->discount_type,
                'discount_value' => $quotation->discount_value,
                'discount_amount' => $quotation->discount_amount,
                'tax_rate' => $quotation->tax_rate,
                'tax_amount' => $quotation->tax_amount,
                'total' => $quotation->total,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'notes' => $quotation->notes,
                'payment_terms' => $quotation->payment_terms,
                'terms_conditions' => $quotation->terms_conditions,
                'business_snapshot' => $quotation->business_snapshot,
            ]);

            foreach ($quotation->items as $item) {
                $invoice->items()->create($item->only(['description', 'item_type', 'quantity', 'unit_price', 'amount', 'sort_order']));
            }

            return $invoice;
        });
    }
}
