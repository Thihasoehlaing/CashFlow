<?php

namespace App\Models;

use Database\Factories\TransferFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['from_account_id', 'to_account_id', 'from_amount', 'from_currency', 'to_amount', 'to_currency', 'exchange_rate', 'fee', 'fee_currency', 'date', 'notes'])]
class Transfer extends Model
{
    /** @use HasFactory<TransferFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'from_amount' => 'decimal:2',
            'to_amount' => 'decimal:2',
            'exchange_rate' => 'decimal:6',
            'fee' => 'decimal:2',
        ];
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }
}
