<?php

namespace App\Models;

use App\Support\CurrentCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Configurações do PDV por empresa.
 *
 * @property int $company_id
 * @property float $comissao_percentual
 * @property int $estoque_min
 * @property list<array{slug: string, label: string}>|null $formas_pagamento
 * @property string|null $empresa_nome
 * @property string|null $empresa_cnpj
 * @property string|null $empresa_telefone
 * @property string|null $empresa_email
 * @property string|null $empresa_endereco
 * @property string|null $nome_loja
 * @property string|null $logo_path
 */
class PdvSetting extends Model
{
    protected $fillable = [
        'company_id',
        'comissao_percentual',
        'estoque_min',
        'formas_pagamento',
        'empresa_nome',
        'empresa_cnpj',
        'empresa_telefone',
        'empresa_email',
        'empresa_endereco',
        'nome_loja',
        'logo_path',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'comissao_percentual' => 'decimal:2',
            'estoque_min' => 'integer',
            'formas_pagamento' => 'array',
        ];
    }

    public static function current(): self
    {
        return once(function () {
            $cid = CurrentCompany::id();
            if ($cid === null) {
                throw new \RuntimeException('Nenhuma empresa no contexto atual.');
            }

            $row = static::query()->where('company_id', $cid)->first();
            if ($row !== null) {
                return $row;
            }

            return static::query()->create([
                'company_id' => $cid,
                'comissao_percentual' => 5,
                'estoque_min' => 10,
                'formas_pagamento' => self::defaultFormasPagamento(),
            ]);
        });
    }

    /**
     * @return list<array{slug: string, label: string}>
     */
    public static function defaultFormasPagamento(): array
    {
        return [
            ['slug' => 'dinheiro', 'label' => 'Dinheiro'],
            ['slug' => 'pix', 'label' => 'PIX'],
            ['slug' => 'cartao_debito', 'label' => 'Cartão Débito'],
            ['slug' => 'cartao_credito', 'label' => 'Cartão Crédito'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function formasPagamentoMap(): array
    {
        $rows = $this->formas_pagamento;
        if (! is_array($rows) || $rows === []) {
            $rows = self::defaultFormasPagamento();
        }

        $map = [];
        foreach ($rows as $row) {
            if (is_array($row) && isset($row['slug'], $row['label'])) {
                $map[(string) $row['slug']] = (string) $row['label'];
            }
        }

        return $map !== [] ? $map : collect(self::defaultFormasPagamento())->mapWithKeys(
            fn (array $r) => [$r['slug'] => $r['label']]
        )->all();
    }

    /**
     * Inclui chaves legadas (ex.: manual no caixa).
     *
     * @return array<string, string>
     */
    public function formasPagamentoParaRelatorios(): array
    {
        $map = $this->formasPagamentoMap();
        if (! array_key_exists('manual', $map)) {
            $map['manual'] = 'Manual';
        }

        return $map;
    }

    public function formasPagamentoCsv(): string
    {
        return implode(', ', array_values($this->formasPagamentoMap()));
    }

    public function displayName(): string
    {
        $n = trim((string) ($this->nome_loja ?? ''));
        if ($n !== '') {
            return $n;
        }
        $e = trim((string) ($this->empresa_nome ?? ''));

        return $e !== '' ? $e : (string) config('app.name', 'Sistema PDV');
    }

    public function logoPublicUrl(): ?string
    {
        $path = $this->logo_path;
        if ($path === null || $path === '') {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
