<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nome
 * @property string|null $descricao
 * @property string $valor_mensal
 * @property string $periodicidade
 * @property int|null $limite_usuarios
 * @property int|null $limite_unidades
 * @property bool $ativo
 */
class SaasPlan extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'valor_mensal',
        'periodicidade',
        'limite_usuarios',
        'limite_unidades',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'valor_mensal' => 'decimal:2',
            'limite_usuarios' => 'integer',
            'limite_unidades' => 'integer',
            'ativo' => 'boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function periodicidadeOptions(): array
    {
        return [
            'mensal' => 'Mensal',
            'trimestral' => 'Trimestral',
            'semestral' => 'Semestral',
            'anual' => 'Anual',
        ];
    }

    public function limiteUsuariosLabel(): string
    {
        return $this->limite_usuarios === null ? 'Ilimitado' : (string) $this->limite_usuarios;
    }

    public function limiteUnidadesLabel(): string
    {
        return $this->limite_unidades === null ? 'Ilimitado' : (string) $this->limite_unidades;
    }

    public function periodicidadeLabel(): string
    {
        return self::periodicidadeOptions()[$this->periodicidade] ?? $this->periodicidade;
    }

    /**
     * @return HasMany<SaasCharge, $this>
     */
    public function charges(): HasMany
    {
        return $this->hasMany(SaasCharge::class);
    }
}
