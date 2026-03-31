<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageUsers() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (($this->user()->isAdministrador() || $this->user()->isGerente()) && ! $this->user()->isSuperAdmin()) {
            $this->merge(['company_id' => $this->user()->company_id]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $keys = collect(config('pdv.screens', []))->pluck('key')->all();

        $roles = ['administrador', 'gerente', 'vendedor'];
        if ($this->user()->isSuperAdmin()) {
            $roles[] = 'super_admin';
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', Rule::in($roles)],
            'company_id' => [
                Rule::requiredIf(fn () => $this->user()->isSuperAdmin() && $this->input('role') !== 'super_admin'),
                'nullable',
                'integer',
                Rule::exists('companies', 'id'),
            ],
            'vendedor_rua' => ['sometimes', 'boolean'],
            'screens' => [
                Rule::requiredIf(fn () => $this->input('role') !== 'super_admin'),
                'array',
                'min:1',
            ],
            'screens.*' => ['string', Rule::in($keys)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'file', 'max:2048', 'mimes:jpeg,jpg,png,gif,webp'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'screens.required_if' => 'Selecione ao menos uma tela permitida para o usuário.',
            'screens.min' => 'Selecione ao menos uma tela permitida para o usuário.',
            'company_id.required_if' => 'Selecione a empresa.',
        ];
    }
}
