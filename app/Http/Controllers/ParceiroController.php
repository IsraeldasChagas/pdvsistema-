<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParceiroRequest;
use App\Http\Requests\UpdateParceiroRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParceiroController extends Controller
{
    public function index(): View
    {
        $query = User::query()
            ->with('company')
            ->parceiros()
            ->orderBy('name');

        if (! auth()->user()->isSuperAdmin()) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $parceiros = $query->get();

        return view('paginas.parceiros.index', compact('parceiros'));
    }

    public function create(): View
    {
        $user = new User([
            'role' => 'vendedor',
            'vendedor_rua' => true,
        ]);

        $empresas = auth()->user()->isSuperAdmin()
            ? Company::query()->where('ativo', true)->orderBy('nome')->get()
            : collect();

        return view('paginas.parceiros.create', [
            'user' => $user,
            'empresas' => $empresas,
            'screensConfig' => config('pdv.screens', []),
            'defaultCheckedScreens' => $user->defaultScreensForParceiro(),
        ]);
    }

    public function store(StoreParceiroRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->role = 'vendedor';
        $user->vendedor_rua = true;
        $user->is_active = true;

        $user->company_id = $request->user()->isSuperAdmin()
            ? (int) $data['company_id']
            : $request->user()->company_id;

        $user->syncAllowedScreensFromInput($request->input('screens', []), 'vendedor');

        $this->fillParceiroPerfil($user, $data);

        if ($request->hasFile('avatar')) {
            try {
                $user->avatar_path = User::storeAvatarFile($request->file('avatar'));
            } catch (\Throwable $e) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['avatar' => 'Não foi possível salvar a foto: '.$e->getMessage()]);
            }
        }

        $user->save();

        return redirect()
            ->route('modulos.parceiros')
            ->with('status', 'Parceiro cadastrado com sucesso.');
    }

    public function edit(User $user): View
    {
        $this->assertParceiroUser($user);

        $empresas = auth()->user()->isSuperAdmin()
            ? Company::query()->where('ativo', true)->orderBy('nome')->get()
            : collect();

        return view('paginas.parceiros.edit', [
            'user' => $user,
            'empresas' => $empresas,
            'screensConfig' => config('pdv.screens', []),
            'checkedScreens' => $user->screensCheckedForForm(),
        ]);
    }

    public function update(UpdateParceiroRequest $request, User $user): RedirectResponse
    {
        $this->assertParceiroUser($user);

        $data = $request->validated();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->vendedor_rua = true;
        $user->is_active = $request->boolean('is_active', true);

        if ($request->user()->isSuperAdmin()) {
            $user->company_id = (int) $data['company_id'];
        }

        $user->syncAllowedScreensFromInput($request->input('screens', []), 'vendedor');

        $this->fillParceiroPerfil($user, $data);

        if ($request->hasFile('avatar')) {
            try {
                User::deleteStoredAvatarFile($user->avatar_path);
                $user->avatar_path = User::storeAvatarFile($request->file('avatar'));
            } catch (\Throwable $e) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['avatar' => 'Não foi possível salvar a foto: '.$e->getMessage()]);
            }
        } elseif ($request->boolean('remover_foto')) {
            User::deleteStoredAvatarFile($user->avatar_path);
            $user->avatar_path = null;
        }

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        return redirect()
            ->route('modulos.parceiros')
            ->with('status', 'Parceiro atualizado com sucesso.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->assertParceiroUser($user);

        if ($request->user()->is($user)) {
            return redirect()
                ->route('modulos.parceiros')
                ->withErrors(['delete' => 'Você não pode excluir sua própria conta.']);
        }

        User::deleteStoredAvatarFile($user->avatar_path);

        $user->delete();

        return redirect()
            ->route('modulos.parceiros')
            ->with('status', 'Parceiro removido.');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function fillParceiroPerfil(User $user, array $data): void
    {
        $user->telefone = $data['telefone'] ?? null;
        $user->endereco_logradouro = $data['endereco_logradouro'] ?? null;
        $user->endereco_numero = $data['endereco_numero'] ?? null;
        $user->endereco_complemento = $data['endereco_complemento'] ?? null;
        $user->endereco_bairro = $data['endereco_bairro'] ?? null;
        $user->endereco_cidade = $data['endereco_cidade'] ?? null;
        $user->endereco_uf = $data['endereco_uf'] ?? null;
        $user->endereco_cep = $data['endereco_cep'] ?? null;
        $user->parceiro_tipo_documento = $data['parceiro_tipo_documento'] ?? null;
        $user->parceiro_documento = $data['parceiro_documento'] ?? null;
        $user->parceiro_razao_social = $data['parceiro_razao_social'] ?? null;
    }

    private function assertParceiroUser(User $user): void
    {
        $this->authorizeManageUser($user);

        if ($user->role !== 'vendedor') {
            abort(404);
        }

        if ($user->parceiro_tipo_documento === null && ! $user->vendedor_rua) {
            abort(404);
        }
    }

    private function authorizeManageUser(User $target): void
    {
        $auth = auth()->user();
        if ($auth->isSuperAdmin()) {
            return;
        }
        if ($target->isSuperAdmin()) {
            abort(403);
        }
        if ($target->company_id !== $auth->company_id) {
            abort(403);
        }
    }
}
