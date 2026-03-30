<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_flow_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('nome');
            $table->string('cor', 16)->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'nome']);
        });

        Schema::table('cash_flow_entries', function (Blueprint $table) {
            $table->dropColumn('categoria');
            $table
                ->foreignId('cash_flow_category_id')
                ->nullable()
                ->after('valor')
                ->constrained('cash_flow_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cash_flow_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cash_flow_category_id');
            $table->string('categoria', 80)->nullable()->after('valor');
        });

        Schema::dropIfExists('cash_flow_categories');
    }
};
