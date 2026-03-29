<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saas_plans', function (Blueprint $table) {
            $table->text('descricao')->nullable()->after('nome');
            $table->string('periodicidade', 32)->default('mensal')->after('valor_mensal');
            $table->unsignedInteger('limite_usuarios')->nullable()->after('periodicidade');
            $table->unsignedInteger('limite_unidades')->nullable()->after('limite_usuarios');
        });
    }

    public function down(): void
    {
        Schema::table('saas_plans', function (Blueprint $table) {
            $table->dropColumn(['descricao', 'periodicidade', 'limite_usuarios', 'limite_unidades']);
        });
    }
};
