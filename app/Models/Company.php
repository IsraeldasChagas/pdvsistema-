<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $nome
 * @property string|null $cnpj
 * @property string|null $endereco
 * @property string|null $telefone
 * @property string|null $email
 * @property bool $ativo
 * @property bool $billing_blocked
 * @property list<string>|null $allowed_screens
 * @property int|null $saas_plan_id
 */
class Company extends Model
{
    protected $fillable = [
        'nome',
        'cnpj',
        'endereco',
        'telefone',
        'email',
        'saas_plan_id',
        'ativo',
        'billing_blocked',
        'allowed_screens',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'billing_blocked' => 'boolean',
            'saas_plan_id' => 'integer',
            'allowed_screens' => 'array',
        ];
    }

    /**
     * Telas do PDV que podem ser atribuídas à empresa (exclui módulos só do super admin).
     *
     * @return list<string>
     */
    public static function tenantSelectableScreenKeys(): array
    {
        $exclude = ['empresas', 'financeiro_saas'];

        return collect(config('pdv.screens', []))
            ->pluck('key')
            ->reject(fn (string $k) => in_array($k, $exclude, true))
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, array{key: string, label: string}>
     */
    public static function tenantSelectableScreens(): Collection
    {
        $keys = self::tenantSelectableScreenKeys();

        return collect(config('pdv.screens', []))
            ->filter(fn (array $row) => in_array($row['key'], $keys, true))
            ->values();
    }

    /**
     * @param  list<string>  $input
     * @return list<string>
     */
    public static function normalizeAllowedScreensFromRequest(array $input): array
    {
        $valid = self::tenantSelectableScreenKeys();

        return array_values(array_unique(array_values(array_intersect($input, $valid))));
    }

    /**
     * Chaves marcadas no formulário (null no banco = todas as telas liberadas).
     *
     * @return list<string>
     */
    public function screensCheckedForForm(): array
    {
        $keys = self::tenantSelectableScreenKeys();
        if ($this->allowed_screens === null) {
            return $keys;
        }

        return array_values(array_intersect($keys, $this->allowed_screens));
    }

    public function allowsTenantScreen(string $key): bool
    {
        $selectable = self::tenantSelectableScreenKeys();
        if (! in_array($key, $selectable, true)) {
            return true;
        }

        $allowed = $this->allowed_screens;
        if ($allowed === null) {
            return true;
        }

        if ($allowed === []) {
            return false;
        }

        return in_array($key, $allowed, true);
    }

    protected static function booted(): void
    {
        static::created(function (Company $company): void {
            PdvSetting::query()->firstOrCreate(
                ['company_id' => $company->id],
                [
                    'comissao_percentual' => 5,
                    'estoque_min' => 10,
                    'formas_pagamento' => PdvSetting::defaultFormasPagamento(),
                ]
            );
        });
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<SaasCharge, $this>
     */
    public function saasCharges(): HasMany
    {
        return $this->hasMany(SaasCharge::class);
    }

    /**
     * @return BelongsTo<SaasPlan, $this>
     */
    public function saasPlan(): BelongsTo
    {
        return $this->belongsTo(SaasPlan::class, 'saas_plan_id');
    }
}
