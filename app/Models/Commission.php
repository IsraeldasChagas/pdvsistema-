<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    public const STATUS_PENDENTE = 'pendente';

    public const STATUS_PAGO = 'pago';

    protected $fillable = [
        'company_id',
        'user_id',
        'valor',
        'percentual',
        'status',
        'paid_at',
        'origem',
        'cash_sale_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'percentual' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cashSale(): BelongsTo
    {
        return $this->belongsTo(CashSale::class);
    }

    public function isPendente(): bool
    {
        return $this->status === self::STATUS_PENDENTE;
    }
}
