<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegisterSession extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'user_id',
        'opened_at',
        'closed_at',
        'valor_abertura',
        'valor_fechamento',
        'observacao_fechamento',
        'total_vendas_no_fechamento',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'valor_abertura' => 'decimal:2',
            'valor_fechamento' => 'decimal:2',
            'total_vendas_no_fechamento' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cashSales(): HasMany
    {
        return $this->hasMany(CashSale::class);
    }

    public function isOpen(): bool
    {
        return $this->closed_at === null;
    }
}
