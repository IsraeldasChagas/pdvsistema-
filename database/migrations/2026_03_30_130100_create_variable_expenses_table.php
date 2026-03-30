<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variable_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->string('descricao');
            $table
                ->foreignId('variable_expense_category_id')
                ->nullable()
                ->constrained('variable_expense_categories')
                ->nullOnDelete();

            $table->decimal('valor', 12, 2);
            $table->date('data_despesa');

            $table->string('forma_pagamento', 32)->nullable();

            $table->string('fornecedor_nome')->nullable();
            $table->string('fornecedor_doc', 32)->nullable();

            $table->string('centro_custo')->nullable();
            $table->string('conta')->nullable();

            $table->text('observacoes')->nullable();
            $table->string('anexo_path', 512)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variable_expenses');
    }
};
