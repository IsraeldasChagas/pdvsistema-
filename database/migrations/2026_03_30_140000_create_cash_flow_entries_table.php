<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_flow_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // entrada | saida
            $table->string('tipo', 10);
            $table->date('data_movimento');
            $table->decimal('valor', 12, 2);

            $table->string('categoria', 80)->nullable();
            $table->string('origem', 40)->nullable(); // caixa, banco, cartão, etc.
            $table->string('descricao', 255);
            $table->text('observacoes')->nullable();

            $table->timestamps();

            $table->index(['company_id', 'data_movimento']);
            $table->index(['company_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_flow_entries');
    }
};

