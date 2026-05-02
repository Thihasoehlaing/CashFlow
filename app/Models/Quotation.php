<?php

namespace App\Models;

use Database\Factories\QuotationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['quotation_number', 'client_id', 'project_id', 'project_title', 'status', 'currency', 'subtotal', 'discount_type', 'discount_value', 'discount_amount', 'tax_rate', 'tax_amount', 'total', 'issue_date', 'valid_until', 'notes', 'payment_terms', 'terms_conditions', 'business_snapshot'])]
class Quotation extends Model
{
    /** @use HasFactory<QuotationFactory> */
    use HasFactory;


    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'valid_until' => 'date',
            'business_snapshot' => 'array',
            'subtotal' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }


    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }


    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }


    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }


    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
