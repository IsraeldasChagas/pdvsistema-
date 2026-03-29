<?php

namespace Database\Seeders;

use App\Models\SaasPlan;
use Illuminate\Database\Seeder;

class SaasPlanSeeder extends Seeder
{
    public function run(): void
    {
        SaasPlan::query()->firstOrCreate(
            ['nome' => 'Básico'],
            ['valor_mensal' => 49.90, 'ativo' => true]
        );
        SaasPlan::query()->firstOrCreate(
            ['nome' => 'Pro'],
            ['valor_mensal' => 99.90, 'ativo' => true]
        );
    }
}
