<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesPartnerUserFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateParceiroRequest extends FormRequest
{
    use ValidatesPartnerUserFields;

    public function authorize(): bool
    {
        return $this->user()?->canManageUsers() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (($this->user()->isAdministrador() || $this->user()->isGerente()) && ! $this->user()->isSuperAdmin()) {
            $this->merge(['company_id' => $this->user()->company_id]);
        }
        $this->preparePartnerFieldsForValidation();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user?->id ?? 0;
        $keys = collect(config('pdv.screens', []))->pluck('key')->all();

        return array_merge([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'company_id' => [
                Rule::requiredIf(fn () => $this->user()->isSuperAdmin()),
                'nullable',
                'integer',
                Rule::exists('companies', 'id'),
            ],
            'is_active' => ['sometimes', 'boolean'],
            'screens' => ['required', 'array', 'min:1'],
            'screens.*' => ['string', Rule::in($keys)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'file', 'max:2048', 'mimes:jpeg,jpg,png,gif,webp'],
            'remover_foto' => ['sometimes', 'boolean'],
        ], $this->partnerModuleFieldRules());
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'screens.required' => 'Selecione ao menos uma tela permitida.',
            'screens.min' => 'Selecione ao menos uma tela permitida.',
            'company_id.required_if' => 'Selecione a empresa.',
        ];
    }
}
