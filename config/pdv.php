<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Nome da marca (menu lateral, cabeçalho, domínio pdvsistema)
    |--------------------------------------------------------------------------
    */
    'brand_name' => env('PDV_BRAND_NAME', 'PDV Sistema'),

    /*
    |--------------------------------------------------------------------------
    | Telas do PDV (permissões por chave)
    |--------------------------------------------------------------------------
    | Ordem e rótulos alinhados ao cadastro de usuário.
    */
    'screens' => [
        ['key' => 'dashboard', 'label' => 'Dashboard'],
        ['key' => 'empresas', 'label' => 'Empresas'],
        ['key' => 'produtos', 'label' => 'Produtos'],
        ['key' => 'categorias', 'label' => 'Categorias'],
        ['key' => 'estoque', 'label' => 'Estoque'],
        ['key' => 'entradas', 'label' => 'Entradas'],
        ['key' => 'saidas', 'label' => 'Saídas'],
        ['key' => 'entregas', 'label' => 'Entregas'],
        ['key' => 'mini_pdv', 'label' => 'Mini PDV'],
        ['key' => 'caixa', 'label' => 'Caixa'],
        ['key' => 'comissoes', 'label' => 'Comissões'],
        ['key' => 'relatorios', 'label' => 'Relatórios'],
        ['key' => 'usuarios', 'label' => 'Usuários'],
        ['key' => 'configuracoes', 'label' => 'Configurações'],
        ['key' => 'financeiro_saas', 'label' => 'Financeiro SaaS'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mapeamento rota nomeada → chave de tela (vendedor)
    |--------------------------------------------------------------------------
    */
    'route_screen_map' => [
        ['patterns' => ['dashboard'], 'screen' => 'dashboard'],
        ['patterns' => ['modulos.produtos', 'produtos.*'], 'screen' => 'produtos'],
        ['patterns' => ['modulos.categorias', 'categorias.*'], 'screen' => 'categorias'],
        ['patterns' => ['modulos.estoque', 'estoque.*'], 'screen' => 'estoque'],
        ['patterns' => ['modulos.entradas', 'entradas.*'], 'screen' => 'entradas'],
        ['patterns' => ['modulos.saidas', 'saidas.*'], 'screen' => 'saidas'],
        ['patterns' => ['modulos.entregas', 'entregas.*'], 'screen' => 'entregas'],
        ['patterns' => ['modulos.venda', 'venda.*'], 'screen' => 'mini_pdv'],
        ['patterns' => ['modulos.caixa', 'caixa.*'], 'screen' => 'caixa'],
        ['patterns' => ['modulos.comissoes', 'comissoes.*'], 'screen' => 'comissoes'],
        ['patterns' => ['modulos.relatorios'], 'screen' => 'relatorios'],
        ['patterns' => ['modulos.configuracoes'], 'screen' => 'configuracoes'],
        ['patterns' => ['empresas.*'], 'screen' => 'empresas'],
        ['patterns' => ['financeiro.saas', 'financeiro.saas.dashboard', 'financeiro.saas.empresas', 'financeiro.saas.empresas.show', 'financeiro.saas.planos', 'financeiro.saas.plans.*', 'financeiro.saas.cobrancas', 'financeiro.saas.charges.*'], 'screen' => 'financeiro_saas'],
    ],
];
