<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_sales', function (Blueprint $table) {
            $table->decimal('subtotal', 12, 2)->nullable()->after('user_id');
            $table->decimal('desconto', 12, 2)->default(0)->after('subtotal');
            $table->string('forma_pagamento', 40)->nullable()->after('total');
        });

        DB::table('cash_sales')->whereNull('subtotal')->update(['subtotal' => DB::raw('total')]);

        Schema::create('cash_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_sale_id')->constrained('cash_sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantidade');
            $table->decimal('preco_unitario', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_sale_items');

        Schema::table('cash_sales', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'desconto', 'forma_pagamento']);
        });
    }
};
