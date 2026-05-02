<?php

namespace App\Models;

use App\Services\CurrencyService;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'type', 'currency', 'account_number', 'opening_balance', 'is_active', 'notes'])]
class Account extends Model
{
    /** @use HasFactory<AccountFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function income(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'from_account_id');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'to_account_id');
    }

    protected function currentBalance(): Attribute
    {
        return Attribute::get(function (): float {
            $income = (float) $this->income()->where('currency', $this->currency)->sum('amount');
            $expenses = (float) $this->expenses()->where('currency', $this->currency)->sum('amount');
            $incoming = (float) $this->incomingTransfers()->sum('to_amount');
            $outgoing = (float) $this->outgoingTransfers()->sum('from_amount');
            $fees = (float) $this->outgoingTransfers()->where(function ($query): void {
                $query->where('fee_currency', $this->currency)->orWhereNull('fee_currency');
            })->sum('fee');

            return round((float) $this->opening_balance + $income - $expenses + $incoming - $outgoing - $fees, 2);
        });
    }

    protected function currentBalanceInMyr(): Attribute
    {
        return Attribute::get(fn (): float => app(CurrencyService::class)->convertToMYR($this->current_balance, $this->currency));
    }
}
