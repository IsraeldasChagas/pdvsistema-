<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CategoryProductSeeder extends Seeder
{
    public function run(): void
    {
        $companyId = (int) Company::query()->orderBy('id')->value('id');

        $cats = [
            'Acessórios' => 'Resistencias, carregadores, cases',
            'Cigarros Eletrônicos' => 'Pods, vapes, cigarrilhas eletrônicas',
            'Outros' => 'Demais produtos',
            'Refis e Liquids' => 'Refis de nicotina, líquidos, essências',
        ];

        $ids = [];
        foreach ($cats as $nome => $descricao) {
            $cat = Category::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $companyId, 'nome' => $nome],
                ['descricao' => $descricao, 'ativo' => true],
            );
            $ids[$nome] = $cat->id;
        }

        if (Product::withoutGlobalScopes()->where('company_id', $companyId)->where('codigo', 'PROD-0002')->exists()) {
            return;
        }

        Product::withoutGlobalScopes()->create([
            'company_id' => $companyId,
            'codigo' => 'PROD-0002',
            'marca' => 'IGNITE',
            'nome' => 'V120 Black',
            'category_id' => $ids['Cigarros Eletrônicos'],
            'caracteristicas' => 'Limão razz azul: —',
            'preco' => 105.00,
            'estoque' => 4,
            'status' => 'ativo',
        ]);
    }
}
