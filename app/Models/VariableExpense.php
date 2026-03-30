<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariableExpense extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'descricao',
        'variable_expense_category_id',
        'valor',
        'data_despesa',
        'forma_pagamento',
        'fornecedor_nome',
        'fornecedor_doc',
        'centro_custo',
        'conta',
        'observacoes',
        'anexo_path',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'data_despesa' => 'date',
        ];
    }

    /**
     * @return BelongsTo<VariableExpenseCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(VariableExpenseCategory::class, 'variable_expense_category_id');
    }
}
