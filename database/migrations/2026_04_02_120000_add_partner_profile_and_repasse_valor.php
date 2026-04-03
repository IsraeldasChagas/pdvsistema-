<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telefone', 32)->nullable()->after('avatar_path');
            $table->string('parceiro_tipo_documento', 8)->nullable()->after('telefone');
            $table->string('parceiro_documento', 20)->nullable()->after('parceiro_tipo_documento');
            $table->string('parceiro_razao_social', 255)->nullable()->after('parceiro_documento');
            $table->string('endereco_logradouro', 255)->nullable()->after('parceiro_razao_social');
            $table->string('endereco_numero', 32)->nullable()->after('endereco_logradouro');
            $table->string('endereco_complemento', 120)->nullable()->after('endereco_numero');
            $table->string('endereco_bairro', 120)->nullable()->after('endereco_complemento');
            $table->string('endereco_cidade', 120)->nullable()->after('endereco_bairro');
            $table->string('endereco_uf', 2)->nullable()->after('endereco_cidade');
            $table->string('endereco_cep', 12)->nullable()->after('endereco_uf');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->decimal('valor_unitario_repasse', 14, 2)->nullable()->after('observacao');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn('valor_unitario_repasse');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
