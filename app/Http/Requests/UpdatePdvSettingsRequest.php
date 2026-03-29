<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePdvSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $raw = (string) $this->input('comissao_pct', '0');
        $s = trim(str_replace(' ', '', $raw));
        if (str_contains($s, ',')) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        }
        $this->merge([
            'comissao_pct' => $s,
            'remover_logo' => $this->boolean('remover_logo'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'comissao_pct' => ['required', 'numeric', 'min:0', 'max:100'],
            'estoque_min' => ['required', 'integer', 'min:0', 'max:999999'],
            'formas_pagamento' => ['required', 'string', 'max:2000'],
            'empresa_nome' => ['nullable', 'string', 'max:255'],
            'empresa_cnpj' => ['nullable', 'string', 'max:32'],
            'empresa_telefone' => ['nullable', 'string', 'max:32'],
            'empresa_email' => ['nullable', 'email', 'max:255'],
            'empresa_endereco' => ['nullable', 'string', 'max:500'],
            'nome_loja' => ['nullable', 'string', 'max:255'],
            'remover_logo' => ['sometimes', 'boolean'],
        ];
    }
}
