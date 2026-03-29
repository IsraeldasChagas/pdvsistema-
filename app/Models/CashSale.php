<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashSale extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cash_register_session_id',
        'user_id',
        'subtotal',
        'desconto',
        'total',
        'forma_pagamento',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'desconto' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function cashRegisterSession(): BelongsTo
    {
        return $this->belongsTo(CashRegisterSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<CashSaleItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(CashSaleItem::class);
    }
}
