<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\PdvSetting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PdvLimparDadosEmpresasCommand extends Command
{
    protected $signature = 'pdv:limpar-dados-empresas
                            {--ids= : IDs das empresas separados por vírgula (ex: 1,2)}
                            {--todas : Inclui todas as empresas cadastradas}
                            {--remover-usuarios : Remove também os usuários vinculados a essas empresas (super admin não é afetado)}
                            {--force : Executa sem pedir confirmação}';

    protected $description = 'Remove histórico operacional (vendas, caixa, comissões, estoque, produtos, categorias, cobranças SaaS) e zera configurações do PDV por empresa.';

    public function handle(): int
    {
        $companyIds = $this->resolveCompanyIds();
        if ($companyIds === []) {
            $this->error('Nenhuma empresa selecionada. Use --ids=1,2 ou --todas.');

            return self::FAILURE;
        }

        sort($companyIds);
        $this->info('Empresas alvo: '.implode(', ', $companyIds));

        if ($this->option('remover-usuarios')) {
            $this->warn('Serão removidos os usuários (exceto super admin) dessas empresas.');
        }

        if (! $this->option('force') && ! $this->confirm('Apagar todo o histórico operacional dessas empresas?', false)) {
            return self::FAILURE;
        }

        DB::transaction(function () use ($companyIds): void {
            foreach ($companyIds as $cid) {
                $this->limparEmpresa((int) $cid);
            }

            if ($this->option('remover-usuarios')) {
                $this->removerUsuariosDasEmpresas($companyIds);
            }
        });

        $this->info('Histórico limpo com sucesso.');

        return self::SUCCESS;
    }

    /**
     * @return list<int>
     */
    private function resolveCompanyIds(): array
    {
        if ($this->option('todas')) {
            return Company::query()->orderBy('id')->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        $raw = $this->option('ids');
        if (! is_string($raw) || trim($raw) === '') {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            static fn (string $s): int => (int) trim($s),
            explode(',', $raw)
        ), static fn (int $id): bool => $id > 0)));
    }

    /**
     * @param  list<int>  $companyIds
     */
    private function removerUsuariosDasEmpresas(array $companyIds): void
    {
        $userIds = User::query()
            ->whereIn('company_id', $companyIds)
            ->where('role', '!=', 'super_admin')
            ->pluck('id');

        if ($userIds->isEmpty()) {
            $this->line('Nenhum usuário para remover.');

            return;
        }

        DB::table('sessions')->whereIn('user_id', $userIds)->delete();

        $deleted = User::query()
            ->whereIn('id', $userIds)
            ->delete();

        $this->line("Usuários removidos: {$deleted}");
    }

    private function limparEmpresa(int $companyId): void
    {
        if (! Company::query()->whereKey($companyId)->exists()) {
            $this->warn("Ignorando ID {$companyId} (empresa inexistente).");

            return;
        }

        $this->line("Limpando empresa ID {$companyId}…");

        DB::table('commissions')->where('company_id', $companyId)->delete();

        DB::table('cash_sales')->where('company_id', $companyId)->delete();

        DB::table('cash_register_sessions')->where('company_id', $companyId)->delete();

        DB::table('stock_movements')->where('company_id', $companyId)->delete();

        DB::table('vendor_stocks')->where('company_id', $companyId)->delete();

        DB::table('products')->where('company_id', $companyId)->delete();

        DB::table('categories')->where('company_id', $companyId)->delete();

        DB::table('saas_charges')->where('company_id', $companyId)->delete();

        PdvSetting::query()->where('company_id', $companyId)->get()->each(function (PdvSetting $row): void {
            $row->update([
                'comissao_percentual' => 5,
                'estoque_min' => 10,
                'formas_pagamento' => PdvSetting::defaultFormasPagamento(),
                'empresa_nome' => null,
                'empresa_cnpj' => null,
                'empresa_telefone' => null,
                'empresa_email' => null,
                'empresa_endereco' => null,
                'nome_loja' => null,
                'logo_path' => null,
            ]);
        });
    }
}
