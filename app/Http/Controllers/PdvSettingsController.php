<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePdvSettingsRequest;
use App\Models\PdvSetting;
use Illuminate\Http\RedirectResponse;
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
        $settings = PdvSetting::current();

        $formas = $this->parseFormasPagamentoCsv($request->input('formas_pagamento', ''));
        if ($formas === []) {
            $formas = PdvSetting::defaultFormasPagamento();
        }

        if ($request->boolean('remover_logo') && $settings->logo_path) {
            Storage::disk('public')->delete($settings->logo_path);
            $settings->logo_path = null;
        }

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $file = $request->file('logo');
            $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png');
            $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'png';
            $dir = 'pdv/companies/'.$settings->company_id;
            $path = $file->storeAs($dir, 'logo.'.$ext, 'public');
            $settings->logo_path = $path;
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
        $settings->save();

        return redirect()
            ->route('modulos.configuracoes')
            ->with('status', 'Configurações salvas com sucesso.');
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
