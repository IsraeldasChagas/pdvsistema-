<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('stock_movements', 'valor_unitario_repasse')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->dropColumn('valor_unitario_repasse');
            });
        }

        $userCols = [
            'telefone',
            'parceiro_tipo_documento',
            'parceiro_documento',
            'parceiro_razao_social',
            'endereco_logradouro',
            'endereco_numero',
            'endereco_complemento',
            'endereco_bairro',
            'endereco_cidade',
            'endereco_uf',
            'endereco_cep',
        ];

        $toDrop = array_values(array_filter($userCols, fn (string $c) => Schema::hasColumn('users', $c)));

        if ($toDrop !== []) {
            Schema::table('users', function (Blueprint $table) use ($toDrop) {
                $table->dropColumn($toDrop);
            });
        }
    }

    public function down(): void
    {
        //
    }
};
