<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixed_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->string('descricao');
            $table->string('categoria')->nullable();
            $table->decimal('valor', 12, 2);

            // mensal, semanal, anual, a_cada_x_dias
            $table->string('periodicidade', 32)->default('mensal');
            $table->unsignedInteger('intervalo')->nullable(); // usado quando periodicidade = a_cada_x_dias

            $table->unsignedTinyInteger('dia_vencimento')->nullable(); // 1-31
            $table->date('data_inicio')->nullable();

            $table->string('status', 16)->default('ativo'); // ativo | pausado | cancelado
            $table->string('forma_pagamento', 32)->nullable();

            $table->string('fornecedor_nome')->nullable();
            $table->string('fornecedor_doc', 32)->nullable();

            $table->string('centro_custo')->nullable();
            $table->string('conta')->nullable();
            $table->unsignedSmallInteger('alerta_dias')->nullable();

            $table->text('observacoes')->nullable();

            // Anexo opcional (nota/boleto/contrato)
            $table->string('anexo_path', 512)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_expenses');
    }
};
