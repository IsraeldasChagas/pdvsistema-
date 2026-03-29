<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\PdvSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        $empresas = Company::query()->orderBy('nome')->get();

        return view('paginas.empresas.index', compact('empresas'));
    }

    public function create(): View
    {
        return view('paginas.empresas.create');
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
            ->with('status', 'Empresa e administrador cadastrados com sucesso.');
    }

    public function edit(Company $empresa): View
    {
        return view('paginas.empresas.edit', ['empresa' => $empresa]);
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
