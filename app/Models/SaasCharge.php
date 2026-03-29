<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $company_id
 * @property int|null $saas_plan_id
 * @property string $valor
 * @property \Illuminate\Support\Carbon $vencimento
 * @property \Illuminate\Support\Carbon|null $pagamento
 * @property string $status
 */
class SaasCharge extends Model
{
    public const STATUS_PENDENTE = 'pendente';

    public const STATUS_PAGO = 'pago';

    protected $table = 'saas_charges';

    protected $fillable = [
        'company_id',
        'saas_plan_id',
        'valor',
        'vencimento',
        'pagamento',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'vencimento' => 'date',
            'pagamento' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<SaasPlan, $this>
     */
    public function saasPlan(): BelongsTo
    {
        return $this->belongsTo(SaasPlan::class, 'saas_plan_id');
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_PENDENTE
            && $this->vencimento->lt(now()->startOfDay());
    }

    public function isDueToday(): bool
    {
        return $this->status === self::STATUS_PENDENTE
            && $this->vencimento->isSameDay(today());
    }

    public function displayStatusLabel(): string
    {
        if ($this->status === self::STATUS_PAGO) {
            return 'Pago';
        }

        return $this->isOverdue() ? 'Vencido' : 'Pendente';
    }

    /** Competência de faturamento (mês/ano do vencimento). */
    public function competenciaLabel(): string
    {
        return $this->vencimento->format('m/Y');
    }
}
