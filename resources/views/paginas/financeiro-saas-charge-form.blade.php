<x-app-layout>
    @php
        $contextoNome = \App\Support\CurrentCompany::model()?->nome ?? 'Empresa Padrão';
        $isEdit = $charge->exists;
        $backQuery = array_filter(
            request()->only(['empresa', 'situacao', 'status', 'plano', 'vencimento_de', 'vencimento_ate']),
            static fn ($v) => $v !== null && $v !== ''
        );
    @endphp
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-xl">
            <p class="text-sm text-gray-500">{{ $contextoNome }}</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ $isEdit ? 'Editar cobrança' : 'Nova cobrança' }}</h1>

            <form
                action="{{ $isEdit ? route('financeiro.saas.charges.update', $charge) : route('financeiro.saas.charges.store') }}"
                method="post"
                class="mt-8 space-y-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
            >
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif
                @foreach ($backQuery as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}" />
                @endforeach

                <div>
                    <label for="company_id" class="block text-sm font-medium text-gray-700">Empresa <span class="text-red-500">*</span></label>
                    <select name="company_id" id="company_id" required class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('company_id') border-red-500 @enderror">
                        <option value="">Selecione…</option>
                        @foreach ($companies as $c)
                            <option value="{{ $c->id }}" @selected(old('company_id', $charge->company_id) == $c->id)>{{ $c->nome }}</option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="saas_plan_id" class="block text-sm font-medium text-gray-700">Plano</label>
                    <select name="saas_plan_id" id="saas_plan_id" class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('saas_plan_id') border-red-500 @enderror">
                        <option value="">—</option>
                        @foreach ($planos as $p)
                            <option value="{{ $p->id }}" @selected(old('saas_plan_id', $charge->saas_plan_id) == $p->id)>{{ $p->nome }}</option>
                        @endforeach
                    </select>
                    @error('saas_plan_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="valor" class="block text-sm font-medium text-gray-700">Valor (R$) <span class="text-red-500">*</span></label>
                    <input
                        type="number"
                        name="valor"
                        id="valor"
                        step="0.01"
                        min="0"
                        required
                        value="{{ old('valor', $charge->valor) }}"
                        class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('valor') border-red-500 @enderror"
                    />
                    @error('valor')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="vencimento" class="block text-sm font-medium text-gray-700">Vencimento <span class="text-red-500">*</span></label>
                    <input
                        type="date"
                        name="vencimento"
                        id="vencimento"
                        required
                        value="{{ old('vencimento', $charge->vencimento?->format('Y-m-d')) }}"
                        class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('vencimento') border-red-500 @enderror"
                    />
                    @error('vencimento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                        <option value="{{ \App\Models\SaasCharge::STATUS_PENDENTE }}" @selected(old('status', $charge->status) === \App\Models\SaasCharge::STATUS_PENDENTE)>Pendente</option>
                        <option value="{{ \App\Models\SaasCharge::STATUS_PAGO }}" @selected(old('status', $charge->status) === \App\Models\SaasCharge::STATUS_PAGO)>Pago</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pagamento" class="block text-sm font-medium text-gray-700">Data do pagamento</label>
                    <input
                        type="date"
                        name="pagamento"
                        id="pagamento"
                        value="{{ old('pagamento', $charge->pagamento?->format('Y-m-d')) }}"
                        class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('pagamento') border-red-500 @enderror"
                    />
                    <p class="mt-1 text-xs text-gray-500">Obrigatório ao marcar como pago; se vazio, usa a data de hoje.</p>
                    @error('pagamento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-6">
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">{{ $isEdit ? 'Salvar' : 'Cadastrar' }}</button>
                    <a href="{{ route('financeiro.saas.cobrancas', $backQuery) }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
