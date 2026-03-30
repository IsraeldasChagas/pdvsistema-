<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashFlowEntry extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'user_id',
        'tipo',
        'data_movimento',
        'valor',
        'cash_flow_category_id',
        'origem',
        'descricao',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'data_movimento' => 'date',
            'valor' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<CashFlowCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CashFlowCategory::class, 'cash_flow_category_id');
    }
}

