<?php

namespace App\Http\Requests;

use App\Models\CashFlowCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCashFlowEntryRequest extends FormRequest
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
            'tipo' => ['required', 'string', Rule::in(['entrada', 'saida'])],
            'data_movimento' => ['required', 'date'],
            'valor' => ['required', 'string', 'max:32'],
            'descricao' => ['required', 'string', 'max:255'],
            'cash_flow_category_id' => ['nullable', 'integer', Rule::exists(CashFlowCategory::class, 'id')],
            'origem' => ['nullable', 'string', 'max:40'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('cash_flow_category_id') === '') {
            $this->merge(['cash_flow_category_id' => null]);
        }
    }

    public function messages(): array
    {
        return [
            'tipo.in' => 'Selecione um tipo válido.',
            'data_movimento.required' => 'Informe a data do movimento.',
            'valor.required' => 'Informe o valor.',
            'descricao.required' => 'Informe a descrição.',
        ];
    }
}

