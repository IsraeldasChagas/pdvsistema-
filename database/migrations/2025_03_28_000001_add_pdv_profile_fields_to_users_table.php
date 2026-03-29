<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)->default('vendedor')->after('password');
            $table->boolean('vendedor_rua')->default(false)->after('role');
            $table->json('allowed_screens')->nullable()->after('vendedor_rua');
            $table->boolean('is_active')->default(true)->after('allowed_screens');
        });

        DB::table('users')->update(['role' => 'administrador']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'vendedor_rua', 'allowed_screens', 'is_active']);
        });
    }
};
