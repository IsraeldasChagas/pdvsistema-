<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pdv_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('comissao_percentual', 5, 2)->default(5);
            $table->unsignedInteger('estoque_min')->default(10);
            $table->json('formas_pagamento')->nullable();
            $table->string('empresa_nome')->nullable();
            $table->string('empresa_cnpj')->nullable();
            $table->string('empresa_telefone')->nullable();
            $table->string('empresa_email')->nullable();
            $table->string('empresa_endereco')->nullable();
            $table->string('nome_loja')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        $formas = json_encode([
            ['slug' => 'dinheiro', 'label' => 'Dinheiro'],
            ['slug' => 'pix', 'label' => 'PIX'],
            ['slug' => 'cartao_debito', 'label' => 'Cartão Débito'],
            ['slug' => 'cartao_credito', 'label' => 'Cartão Crédito'],
        ]);

        DB::table('pdv_settings')->insert([
            'comissao_percentual' => 5,
            'estoque_min' => 10,
            'formas_pagamento' => $formas,
            'empresa_nome' => null,
            'empresa_cnpj' => null,
            'empresa_telefone' => null,
            'empresa_email' => null,
            'empresa_endereco' => null,
            'nome_loja' => null,
            'logo_path' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdv_settings');
    }
};
