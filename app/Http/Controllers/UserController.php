<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $query = User::query()->with('company')->orderBy('name');
        if (! auth()->user()->isSuperAdmin()) {
            $query->where('company_id', auth()->user()->company_id);
        }
        $usuarios = $query->get();

        return view('paginas.usuarios.index', compact('usuarios'));
    }

    public function create(): View
    {
        $user = new User([
            'role' => 'vendedor',
            'vendedor_rua' => false,
        ]);

        $empresas = auth()->user()->isSuperAdmin()
            ? Company::query()->where('ativo', true)->orderBy('nome')->get()
            : collect();

        return view('paginas.usuarios.create', [
            'user' => $user,
            'empresas' => $empresas,
            'screensConfig' => config('pdv.screens', []),
            'defaultCheckedScreens' => $user->defaultScreensCheckedForForm(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->role = $data['role'];
        $user->vendedor_rua = $request->boolean('vendedor_rua');
        $user->is_active = true;

        if ($data['role'] === 'super_admin') {
            $user->company_id = null;
        } else {
            $user->company_id = $request->user()->isSuperAdmin()
                ? (int) $data['company_id']
                : $request->user()->company_id;
        }

        $user->syncAllowedScreensFromInput($request->input('screens', []), $data['role']);

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
            ->route('modulos.usuarios')
            ->with('status', 'Usuário criado com sucesso.');
    }

    public function edit(User $user): View
    {
        $this->authorizeManageUser($user);

        $empresas = auth()->user()->isSuperAdmin()
            ? Company::query()->where('ativo', true)->orderBy('nome')->get()
            : collect();

        return view('paginas.usuarios.edit', [
            'user' => $user,
            'empresas' => $empresas,
            'screensConfig' => config('pdv.screens', []),
            'checkedScreens' => $user->screensCheckedForForm(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorizeManageUser($user);

        if ($request->user()->is($user) && $request->input('role') === 'vendedor') {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['role' => 'Você não pode alterar seu próprio cargo para Vendedor.']);
        }

        $data = $request->validated();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->vendedor_rua = $request->boolean('vendedor_rua');
        $user->is_active = $request->boolean('is_active', true);

        if ($data['role'] === 'super_admin') {
            $user->company_id = null;
        } elseif ($request->user()->isSuperAdmin()) {
            $user->company_id = (int) $data['company_id'];
        }

        $user->syncAllowedScreensFromInput($request->input('screens', []), $data['role']);

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
            ->route('modulos.usuarios')
            ->with('status', 'Usuário atualizado com sucesso.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeManageUser($user);

        if ($request->user()->is($user)) {
            return redirect()
                ->route('modulos.usuarios')
                ->withErrors(['delete' => 'Você não pode excluir sua própria conta.']);
        }

        User::deleteStoredAvatarFile($user->avatar_path);

        $user->delete();

        return redirect()
            ->route('modulos.usuarios')
            ->with('status', 'Usuário removido.');
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
