<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFixedExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'descricao' => ['required', 'string', 'max:255'],
            'categoria' => ['nullable', 'string', 'max:80'],
            'valor' => ['required', 'string', 'max:32'],

            'periodicidade' => ['required', 'string', Rule::in(['mensal', 'semanal', 'anual', 'a_cada_x_dias'])],
            'intervalo' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'dia_vencimento' => ['nullable', 'integer', 'min:1', 'max:31'],
            'data_inicio' => ['nullable', 'date'],

            'status' => ['required', 'string', Rule::in(['ativo', 'pausado', 'cancelado'])],
            'forma_pagamento' => ['nullable', 'string', 'max:32'],

            'fornecedor_nome' => ['nullable', 'string', 'max:255'],
            'fornecedor_doc' => ['nullable', 'string', 'max:32'],

            'centro_custo' => ['nullable', 'string', 'max:80'],
            'conta' => ['nullable', 'string', 'max:80'],
            'alerta_dias' => ['nullable', 'integer', 'min:0', 'max:365'],
            'observacoes' => ['nullable', 'string', 'max:4000'],

            'anexo' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpeg,jpg,png,gif,webp'],
        ];
    }

    public function messages(): array
    {
        return [
            'descricao.required' => 'Informe a descrição.',
            'valor.required' => 'Informe o valor.',
            'periodicidade.in' => 'Selecione uma periodicidade válida.',
            'status.in' => 'Selecione um status válido.',
            'anexo.mimes' => 'O anexo deve ser PDF ou imagem.',
            'anexo.max' => 'O anexo não pode passar de 10 MB.',
        ];
    }
}
