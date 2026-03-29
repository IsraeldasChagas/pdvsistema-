<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('saas_plan_id')->nullable()->constrained('saas_plans')->nullOnDelete();
            $table->decimal('valor', 12, 2);
            $table->date('vencimento');
            $table->date('pagamento')->nullable();
            $table->string('status', 20);
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['vencimento', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_charges');
    }
};
