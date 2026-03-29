<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_register_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('valor_abertura', 12, 2);
            $table->decimal('valor_fechamento', 12, 2)->nullable();
            $table->text('observacao_fechamento')->nullable();
            $table->decimal('total_vendas_no_fechamento', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('cash_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_session_id')->constrained('cash_register_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_sales');
        Schema::dropIfExists('cash_register_sessions');
    }
};
