<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('endereco', 500)->nullable()->after('cnpj');
            $table->string('telefone', 32)->nullable()->after('endereco');
            $table->string('email', 255)->nullable()->after('telefone');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['endereco', 'telefone', 'email']);
        });
    }
};
