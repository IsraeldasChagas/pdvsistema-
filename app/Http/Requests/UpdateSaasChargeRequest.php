<?php

namespace App\Http\Requests;

use App\Models\SaasCharge;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSaasChargeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('saas_plan_id') === '' || $this->input('saas_plan_id') === null) {
            $this->merge(['saas_plan_id' => null]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'saas_plan_id' => ['nullable', 'integer', 'exists:saas_plans,id'],
            'valor' => ['required', 'numeric', 'min:0'],
            'vencimento' => ['required', 'date'],
            'pagamento' => ['nullable', 'date'],
            'status' => ['required', Rule::in([SaasCharge::STATUS_PENDENTE, SaasCharge::STATUS_PAGO])],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @return array{company_id: int, saas_plan_id: int|null, valor: float|string, vencimento: string, pagamento: string|null, status: string}
     */
    public function validatedPayload(): array
    {
        /** @var array{company_id: int, saas_plan_id: int|null, valor: float|string, vencimento: string, pagamento: string|null, status: string} $data */
        $data = $this->validated();
        if ($data['status'] === SaasCharge::STATUS_PAGO && empty($data['pagamento'])) {
            $data['pagamento'] = now()->toDateString();
        }
        if ($data['status'] === SaasCharge::STATUS_PENDENTE) {
            $data['pagamento'] = null;
        }

        return $data;
    }
}
