<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixed_expenses', function (Blueprint $table) {
            $table
                ->foreignId('fixed_expense_category_id')
                ->nullable()
                ->after('categoria')
                ->constrained('fixed_expense_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fixed_expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fixed_expense_category_id');
        });
    }
};
