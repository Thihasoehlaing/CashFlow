<?php

namespace App\Models;

use Database\Factories\ProjectCostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'account_id', 'expense_id', 'name', 'type', 'provider', 'amount', 'currency', 'amount_in_myr', 'billing_cycle', 'next_renewal_date', 'is_billable', 'auto_log_expense', 'notes'])]
class ProjectCost extends Model
{
    /** @use HasFactory<ProjectCostFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'amount_in_myr' => 'decimal:2',
            'next_renewal_date' => 'date',
            'is_billable' => 'boolean',
            'auto_log_expense' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
