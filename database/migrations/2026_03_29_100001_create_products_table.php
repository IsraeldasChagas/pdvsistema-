<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('marca')->nullable();
            $table->string('nome');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->text('caracteristicas')->nullable();
            $table->decimal('preco', 12, 2)->default(0);
            $table->unsignedInteger('estoque')->default(0);
            $table->string('status', 20)->default('ativo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
