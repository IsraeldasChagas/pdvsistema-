<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Validation\Rule;

trait ValidatesPartnerUserFields
{
    protected function preparePartnerFieldsForValidation(): void
    {
        if ($this->has('parceiro_documento')) {
            $digits = preg_replace('/\D/', '', (string) $this->input('parceiro_documento'));
            $this->merge(['parceiro_documento' => $digits !== '' ? $digits : null]);
        }
        if ($this->has('endereco_cep')) {
            $cep = preg_replace('/\D/', '', (string) $this->input('endereco_cep'));
            $this->merge(['endereco_cep' => $cep !== '' ? $cep : null]);
        }
        if ($this->has('endereco_uf')) {
            $uf = strtoupper(trim((string) $this->input('endereco_uf')));
            $this->merge(['endereco_uf' => $uf !== '' ? $uf : null]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function partnerFieldRules(): array
    {
        return [
            'telefone' => ['nullable', 'string', 'max:32'],
            'parceiro_tipo_documento' => [
                Rule::requiredIf(fn () => $this->boolean('vendedor_rua')),
                'nullable',
                Rule::in(['cpf', 'cnpj']),
            ],
            'parceiro_documento' => [
                Rule::requiredIf(fn () => $this->boolean('vendedor_rua')),
                'nullable',
                'string',
                'max:20',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! $this->boolean('vendedor_rua')) {
                        return;
                    }
                    $digits = preg_replace('/\D/', '', (string) $value);
                    $tipo = $this->input('parceiro_tipo_documento');
                    if ($tipo === 'cpf' && strlen($digits) !== 11) {
                        $fail('Informe um CPF válido (11 dígitos).');
                    }
                    if ($tipo === 'cnpj' && strlen($digits) !== 14) {
                        $fail('Informe um CNPJ válido (14 dígitos).');
                    }
                },
            ],
            'parceiro_razao_social' => ['nullable', 'string', 'max:255'],
            'endereco_logradouro' => ['nullable', 'string', 'max:255'],
            'endereco_numero' => ['nullable', 'string', 'max:32'],
            'endereco_complemento' => ['nullable', 'string', 'max:120'],
            'endereco_bairro' => ['nullable', 'string', 'max:120'],
            'endereco_cidade' => ['nullable', 'string', 'max:120'],
            'endereco_uf' => ['nullable', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'endereco_cep' => ['nullable', 'string', 'max:12'],
        ];
    }
}
