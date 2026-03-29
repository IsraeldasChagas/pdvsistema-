<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'codigo',
        'marca',
        'nome',
        'category_id',
        'caracteristicas',
        'preco',
        'estoque',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'preco' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * @return HasMany<VendorStock, $this>
     */
    public function vendorStocks(): HasMany
    {
        return $this->hasMany(VendorStock::class);
    }

    public function isAtivo(): bool
    {
        return $this->status === 'ativo';
    }
}
