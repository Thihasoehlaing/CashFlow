<?php

namespace App\Models;

use Database\Factories\IncomeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['source', 'client_id', 'project_id', 'project_name', 'billing_type', 'hours', 'rate_per_hour', 'amount', 'currency', 'amount_in_myr', 'account_id', 'description', 'date', 'type'])]
class Income extends Model
{
    /** @use HasFactory<IncomeFactory> */
    use HasFactory;

    protected $table = 'income';

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'hours' => 'decimal:2',
            'rate_per_hour' => 'decimal:2',
            'amount' => 'decimal:2',
            'amount_in_myr' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
