<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class FixedExpense extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'descricao',
        'categoria',
        'valor',
        'periodicidade',
        'intervalo',
        'dia_vencimento',
        'data_inicio',
        'status',
        'forma_pagamento',
        'fornecedor_nome',
        'fornecedor_doc',
        'centro_custo',
        'conta',
        'alerta_dias',
        'observacoes',
        'anexo_path',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'intervalo' => 'integer',
            'dia_vencimento' => 'integer',
            'alerta_dias' => 'integer',
            'data_inicio' => 'date',
        ];
    }
}
