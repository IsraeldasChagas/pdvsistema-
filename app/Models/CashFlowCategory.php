<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CashFlowCategory extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'nome',
        'cor',
    ];
}
