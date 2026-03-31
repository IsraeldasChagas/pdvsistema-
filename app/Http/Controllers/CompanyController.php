<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\PdvSetting;
use App\Models\SaasPlan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        $empresas = Company::query()
            ->with('saasPlan:id,nome')
            ->orderBy('nome')
            ->get();

        return view('paginas.empresas.index', compact('empresas'));
    }

    public function create(): View
    {
        $planos = SaasPlan::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return view('paginas.empresas.create', compact('planos'));
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $telas = Company::normalizeAllowedScreensFromRequest($data['allowed_screens'] ?? []);

        DB::transaction(function () use ($data, $telas): void {
            $company = Company::query()->create([
                'nome' => $data['nome'],
                'cnpj' => $data['cnpj'] ?? null,
                'endereco' => $data['endereco'] ?? null,
                'telefone' => $data['telefone'] ?? null,
                'email' => $data['email'] ?? null,
                'saas_plan_id' => $data['saas_plan_id'] ?? null,
                'ativo' => true,
                'billing_blocked' => false,
                'allowed_screens' => $telas !== [] ? $telas : Company::tenantSelectableScreenKeys(),
            ]);

            User::query()->create([
                'name' => $data['admin_nome'],
                'email' => $data['admin_email'],
                'password' => $data['password'],
                'role' => 'administrador',
                'company_id' => $company->id,
                'vendedor_rua' => false,
                'allowed_screens' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            PdvSetting::query()->where('company_id', $company->id)->update([
                'empresa_nome' => $company->nome,
                'empresa_cnpj' => $company->cnpj,
                'empresa_telefone' => $company->telefone,
                'empresa_email' => $company->email,
                'empresa_endereco' => $company->endereco,
            ]);
        });

        return redirect()
            ->route('empresas.index')
            ->with(
                'status',
                'Empresa cadastrada. O administrador deve entrar no login com o e-mail '.$data['admin_email'].' e a senha definida no cadastro (não existe um e-mail padrão tipo admin@pdvsistema.com).'
            );
    }

    public function edit(Company $empresa): View
    {
        $planos = SaasPlan::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return view('paginas.empresas.edit', ['empresa' => $empresa, 'planos' => $planos]);
    }

    public function update(UpdateCompanyRequest $request, Company $empresa): RedirectResponse
    {
        $data = $request->validated();
        $telas = Company::normalizeAllowedScreensFromRequest($data['allowed_screens'] ?? []);

        $empresa->update([
            'nome' => $data['nome'],
            'cnpj' => $data['cnpj'] ?? null,
            'endereco' => $data['endereco'] ?? null,
            'telefone' => $data['telefone'] ?? null,
            'email' => $data['email'] ?? null,
            'saas_plan_id' => $data['saas_plan_id'] ?? null,
            'ativo' => $request->boolean('ativo'),
            'billing_blocked' => $request->boolean('billing_blocked'),
            'allowed_screens' => $telas,
        ]);

        PdvSetting::query()->where('company_id', $empresa->id)->update([
            'empresa_nome' => $empresa->nome,
            'empresa_cnpj' => $empresa->cnpj,
            'empresa_telefone' => $empresa->telefone,
            'empresa_email' => $empresa->email,
            'empresa_endereco' => $empresa->endereco,
        ]);

        return redirect()
            ->route('empresas.index')
            ->with('status', 'Empresa atualizada.');
    }

    public function destroy(Company $empresa): RedirectResponse
    {
        if ($empresa->users()->exists()) {
            return redirect()
                ->route('empresas.index')
                ->withErrors(['delete' => 'Não é possível excluir: existem usuários vinculados a esta empresa.']);
        }

        $empresa->delete();

        return redirect()
            ->route('empresas.index')
            ->with('status', 'Empresa removida.');
    }
}
