<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Usuário local para testar sem MySQL (SQLite em database/database.sqlite).
     * E-mail: admin@sistema.pdv  |  Senha: password
     */
    public function run(): void
    {
        $companyId = (int) Company::query()->orderBy('id')->value('id');

        User::query()->firstOrCreate(
            ['email' => 'admin@sistema.pdv'],
            [
                'name' => 'Administrador',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => 'administrador',
                'company_id' => $companyId,
                'vendedor_rua' => false,
                'allowed_screens' => null,
                'is_active' => true,
            ],
        );

        User::query()->firstOrCreate(
            ['email' => 'super@sistema.pdv'],
            [
                'name' => 'Super administrador',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => 'super_admin',
                'company_id' => null,
                'vendedor_rua' => false,
                'allowed_screens' => null,
                'is_active' => true,
            ],
        );

        $this->call(CategoryProductSeeder::class);
        $this->call(SaasPlanSeeder::class);
    }
}
