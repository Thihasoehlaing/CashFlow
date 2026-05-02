<?php

namespace App\Livewire;

use App\Models\Quotation;
use App\Models\Setting;
use App\Services\CurrencyService;
use App\Services\QuotationService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class QuotationBuilder extends Component
{
    /** @var array<int, array<string, mixed>> */
    public array $items = [];
    public string $currency = 'MYR';
    public ?string $discountType = null;
    public float $discountValue = 0;
    public float $taxRate = 0;
    public float $subtotal = 0;
    public float $discountAmount = 0;
    public float $taxAmount = 0;
    public float $total = 0;


    public function mount(?Quotation $quotation = null): void
    {
        $this->currency = old('currency', $quotation?->currency ?? 'MYR');
        $this->discountType = old('discount_type', $quotation?->discount_type);
        $this->discountValue = (float) old('discount_value', $quotation?->discount_value ?? 0);
        $this->taxRate = (float) old('tax_rate', $quotation?->tax_rate ?? Setting::get('default_tax_rate', 8));
        $oldItems = old('items');

        if (is_array($oldItems)) {
            $this->items = $oldItems;
        } elseif ($quotation?->exists) {
            $this->items = $quotation->items->map(fn ($item): array => [
                'description' => $item->description,
                'item_type' => $item->item_type,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'amount' => (float) $item->amount,
            ])->all();
        } else {
            $this->items = [['description' => '', 'item_type' => 'fixed', 'quantity' => 1, 'unit_price' => 0, 'amount' => 0]];
        }

        $this->recalculate();
    }


    public function addItem(): void
    {
        $this->items[] = ['description' => '', 'item_type' => 'fixed', 'quantity' => 1, 'unit_price' => 0, 'amount' => 0];
        $this->recalculate();
    }


    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        if ($this->items === []) {
            $this->addItem();
        }
        $this->recalculate();
    }


    public function updated(): void
    {
        $this->recalculate();
    }


    public function recalculate(): void
    {
        foreach ($this->items as $index => $item) {
            $this->items[$index]['quantity'] = (float) ($item['quantity'] ?? 0);
            $this->items[$index]['unit_price'] = (float) ($item['unit_price'] ?? 0);
            $this->items[$index]['amount'] = round($this->items[$index]['quantity'] * $this->items[$index]['unit_price'], 2);
        }

        $totals = app(QuotationService::class)->calculateTotals($this->items, $this->discountType, $this->discountValue, $this->taxRate);
        $this->subtotal = $totals['subtotal'];
        $this->discountAmount = $totals['discount_amount'];
        $this->taxAmount = $totals['tax_amount'];
        $this->total = $totals['total'];
    }


    public function render(): View
    {
        return view('livewire.quotation-builder', [
            'currencies' => app(CurrencyService::class)->getAvailableCurrencies(),
        ]);
    }
}
