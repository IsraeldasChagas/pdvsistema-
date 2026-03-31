<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Models\SaasPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'cnpj' => ['nullable', 'string', 'max:32'],
            'endereco' => ['nullable', 'string', 'max:500'],
            'telefone' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'saas_plan_id' => ['nullable', 'integer', Rule::exists(SaasPlan::class, 'id')],
            'ativo' => ['sometimes', 'boolean'],
            'billing_blocked' => ['sometimes', 'boolean'],
            'allowed_screens' => ['required', 'array', 'min:1'],
            'allowed_screens.*' => [Rule::in(Company::tenantSelectableScreenKeys())],
        ];
    }
}
