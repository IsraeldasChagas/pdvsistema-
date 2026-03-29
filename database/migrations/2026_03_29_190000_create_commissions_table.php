<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('valor', 12, 2);
            $table->decimal('percentual', 6, 2)->nullable();
            $table->string('status', 20)->default('pendente');
            $table->timestamp('paid_at')->nullable();
            $table->string('origem', 20)->default('manual');
            $table->foreignId('cash_sale_id')->nullable()->constrained('cash_sales')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
