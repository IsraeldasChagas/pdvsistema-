<?php

namespace App\Http\Requests;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $raw = $this->input('allowed_screens');
        $keys = Company::tenantSelectableScreenKeys();
        if (! is_array($raw) || $raw === [] || count(array_filter($raw, fn ($v) => $v !== null && $v !== '')) === 0) {
            $this->merge(['allowed_screens' => $keys]);
        }
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
            'admin_nome' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'allowed_screens' => ['required', 'array', 'min:1'],
            'allowed_screens.*' => [Rule::in(Company::tenantSelectableScreenKeys())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nome' => 'nome da empresa',
            'admin_nome' => 'nome do administrador',
            'admin_email' => 'e-mail do administrador',
        ];
    }
}
