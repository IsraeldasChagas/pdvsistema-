<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePdvSettingsRequest;
use App\Http\Requests\UploadPdvLogoRequest;
use App\Models\PdvSetting;
use App\Support\CurrentCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PdvSettingsController extends Controller
{
    public function edit(): View
    {
        $settings = PdvSetting::current();

        return view('paginas.configuracoes', [
            'settings' => $settings,
            'comissaoPctDisplay' => number_format((float) $settings->comissao_percentual, 2, ',', '.'),
        ]);
    }

    public function update(UpdatePdvSettingsRequest $request): RedirectResponse
    {
        $companyId = CurrentCompany::id();
        if ($companyId === null) {
            abort(403, 'Nenhuma empresa no contexto.');
        }

        /** @var PdvSetting $settings */
        $settings = PdvSetting::query()->where('company_id', $companyId)->first();
        if ($settings === null) {
            $settings = PdvSetting::query()->create([
                'company_id' => $companyId,
                'comissao_percentual' => 5,
                'estoque_min' => 10,
                'formas_pagamento' => PdvSetting::defaultFormasPagamento(),
            ]);
        }

        $formas = $this->parseFormasPagamentoCsv($request->input('formas_pagamento', ''));
        if ($formas === []) {
            $formas = PdvSetting::defaultFormasPagamento();
        }

        if ($request->boolean('remover_logo') && $settings->logo_path) {
            Storage::disk('public')->delete($settings->logo_path);
            $settings->logo_path = null;
        }

        $settings->comissao_percentual = round((float) $request->input('comissao_pct'), 2);
        $settings->estoque_min = (int) $request->input('estoque_min');
        $settings->formas_pagamento = $formas;
        $settings->empresa_nome = $request->input('empresa_nome');
        $settings->empresa_cnpj = $request->input('empresa_cnpj');
        $settings->empresa_telefone = $request->input('empresa_telefone');
        $settings->empresa_email = $request->input('empresa_email');
        $settings->empresa_endereco = $request->input('empresa_endereco');
        $settings->nome_loja = $request->input('nome_loja');
        $settings->saveOrFail();

        return redirect()
            ->route('modulos.configuracoes')
            ->with('status', 'Configurações salvas com sucesso.');
    }

    public function uploadLogo(UploadPdvLogoRequest $request): RedirectResponse
    {
        $companyId = CurrentCompany::id();
        if ($companyId === null) {
            abort(403, 'Nenhuma empresa no contexto.');
        }

        $settings = PdvSetting::query()->where('company_id', $companyId)->first();
        if ($settings === null) {
            $settings = PdvSetting::query()->create([
                'company_id' => $companyId,
                'comissao_percentual' => 5,
                'estoque_min' => 10,
                'formas_pagamento' => PdvSetting::defaultFormasPagamento(),
            ]);
        }

        $file = $request->file('logo');
        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            return back()->withErrors([
                'logo' => 'O arquivo não foi recebido corretamente. Aumente upload_max_filesize e post_max_size no PHP (ex.: 10M).',
            ]);
        }

        try {
            $path = $this->storeLogoOnDisk($file, $settings);
        } catch (\Throwable $e) {
            return back()->withErrors(['logo' => 'Não foi possível salvar o logo: '.$e->getMessage()]);
        }

        $settings->logo_path = $path;
        $settings->saveOrFail();

        return redirect()
            ->route('modulos.configuracoes')
            ->with('status', 'Logo salvo com sucesso.');
    }

    private function storeLogoOnDisk(UploadedFile $file, PdvSetting $settings): string
    {
        if ($settings->logo_path) {
            Storage::disk('public')->delete($settings->logo_path);
        }
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png');
        $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'png';
        $dir = 'pdv/companies/'.$settings->company_id;
        $path = $file->storeAs($dir, 'logo.'.$ext, 'public');
        if ($path === false || $path === '') {
            throw new \RuntimeException('Falha ao gravar em storage/app/public (permissões?).');
        }

        return $path;
    }

    /**
     * @return list<array{slug: string, label: string}>
     */
    private function parseFormasPagamentoCsv(string $raw): array
    {
        $parts = array_filter(array_map('trim', explode(',', $raw)), fn (string $s) => $s !== '');
        $used = [];
        $out = [];

        foreach ($parts as $label) {
            $ascii = Str::ascii($label);
            $base = Str::slug($ascii);
            if ($base === '') {
                $base = 'forma';
            }
            $slug = $base;
            $i = 1;
            while (in_array($slug, $used, true)) {
                $slug = $base.'-'.$i;
                $i++;
            }
            $used[] = $slug;
            $out[] = ['slug' => $slug, 'label' => $label];
        }

        return $out;
    }
}
