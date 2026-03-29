<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSaasPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'limite_usuarios' => $this->normalizeLimit($this->input('limite_usuarios')),
            'limite_unidades' => $this->normalizeLimit($this->input('limite_unidades')),
        ]);
    }

    private function normalizeLimit(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $n = (int) $value;

        return $n > 0 ? $n : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:5000'],
            'valor_mensal' => ['required', 'numeric', 'min:0'],
            'periodicidade' => ['required', Rule::in(['mensal', 'trimestral', 'semestral', 'anual'])],
            'limite_usuarios' => ['nullable', 'integer', 'min:1'],
            'limite_unidades' => ['nullable', 'integer', 'min:1'],
            'ativo' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function planPayload(): array
    {
        $desc = $this->validated('descricao');

        return [
            'nome' => $this->validated('nome'),
            'descricao' => $desc !== null && $desc !== '' ? $desc : null,
            'valor_mensal' => $this->validated('valor_mensal'),
            'periodicidade' => $this->validated('periodicidade'),
            'limite_usuarios' => $this->validated('limite_usuarios'),
            'limite_unidades' => $this->validated('limite_unidades'),
            'ativo' => $this->boolean('ativo'),
        ];
    }
}
