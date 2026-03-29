<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cnpj')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        $companyId = (int) DB::table('companies')->insertGetId([
            'nome' => 'Empresa Padrão',
            'cnpj' => null,
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
        DB::table('users')->update(['company_id' => $companyId]);

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['nome']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
        });
        DB::table('categories')->whereNull('company_id')->update(['company_id' => $companyId]);
        Schema::table('categories', function (Blueprint $table) {
            $table->unique(['company_id', 'nome']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['codigo']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
        });
        DB::table('products')->whereNull('company_id')->update(['company_id' => $companyId]);
        Schema::table('products', function (Blueprint $table) {
            $table->unique(['company_id', 'codigo']);
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
        });
        DB::statement('UPDATE stock_movements SET company_id = (
            SELECT company_id FROM products WHERE products.id = stock_movements.product_id
        )');

        Schema::table('vendor_stocks', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
        });
        DB::statement('UPDATE vendor_stocks SET company_id = (
            SELECT company_id FROM products WHERE products.id = vendor_stocks.product_id
        )');

        Schema::table('cash_register_sessions', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
        });
        DB::statement('UPDATE cash_register_sessions SET company_id = (
            SELECT company_id FROM users WHERE users.id = cash_register_sessions.user_id
        )');

        Schema::table('cash_sales', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
        });
        DB::statement('UPDATE cash_sales SET company_id = (
            SELECT company_id FROM cash_register_sessions WHERE cash_register_sessions.id = cash_sales.cash_register_session_id
        )');

        Schema::table('commissions', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
        });
        DB::statement('UPDATE commissions SET company_id = (
            SELECT company_id FROM users WHERE users.id = commissions.user_id
        )');

        Schema::table('pdv_settings', function (Blueprint $table) use ($companyId) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });
        DB::table('pdv_settings')->update(['company_id' => $companyId]);
        Schema::table('pdv_settings', function (Blueprint $table) {
            $table->unique('company_id');
        });
    }

    public function down(): void
    {
        Schema::table('pdv_settings', function (Blueprint $table) {
            $table->dropUnique(['company_id']);
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('commissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('cash_sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('cash_register_sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('vendor_stocks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'codigo']);
            $table->dropConstrainedForeignId('company_id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->unique('codigo');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'nome']);
            $table->dropConstrainedForeignId('company_id');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->unique('nome');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::dropIfExists('companies');
    }
};
