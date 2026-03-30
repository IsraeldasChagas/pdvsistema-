<?php

namespace App\Http\Requests;

use App\Models\VariableExpenseCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVariableExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('variable_expense_category_id') === '') {
            $this->merge(['variable_expense_category_id' => null]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'descricao' => ['required', 'string', 'max:255'],
            'variable_expense_category_id' => ['nullable', 'integer', Rule::exists(VariableExpenseCategory::class, 'id')],
            'valor' => ['required', 'string', 'max:32'],
            'data_despesa' => ['required', 'date'],

            'forma_pagamento' => ['nullable', 'string', 'max:32'],

            'fornecedor_nome' => ['nullable', 'string', 'max:255'],
            'fornecedor_doc' => ['nullable', 'string', 'max:32'],

            'centro_custo' => ['nullable', 'string', 'max:80'],
            'conta' => ['nullable', 'string', 'max:80'],
            'observacoes' => ['nullable', 'string', 'max:4000'],

            'anexo' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpeg,jpg,png,gif,webp'],
        ];
    }

    public function messages(): array
    {
        return [
            'descricao.required' => 'Informe a descrição.',
            'valor.required' => 'Informe o valor.',
            'data_despesa.required' => 'Informe a data da despesa.',
            'anexo.mimes' => 'O anexo deve ser PDF ou imagem.',
            'anexo.max' => 'O anexo não pode passar de 10 MB.',
        ];
    }
}
