<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashSaleItem extends Model
{
    protected $fillable = [
        'cash_sale_id',
        'product_id',
        'quantidade',
        'preco_unitario',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'preco_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function cashSale(): BelongsTo
    {
        return $this->belongsTo(CashSale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
