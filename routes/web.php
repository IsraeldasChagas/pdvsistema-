<?php

use App\Http\Controllers\CaixaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CompanyContextController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\FinanceiroSaasController;
use App\Http\Controllers\PdvSettingsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\SaasChargeController;
use App\Http\Controllers\SaasPlanController;
use App\Http\Controllers\SaidaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->isSuperAdmin()) {
        return redirect()->route('empresas.index');
    }

    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified', 'super.panel', 'pdv.screen'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('saas')->group(function () {
        Route::get('/empresas', [CompanyController::class, 'index'])->name('empresas.index');
        Route::get('/empresas/nova', [CompanyController::class, 'create'])->name('empresas.create');
        Route::post('/empresas', [CompanyController::class, 'store'])->name('empresas.store');
        Route::get('/empresas/{empresa}/editar', [CompanyController::class, 'edit'])->name('empresas.edit');
        Route::put('/empresas/{empresa}', [CompanyController::class, 'update'])->name('empresas.update');
        Route::delete('/empresas/{empresa}', [CompanyController::class, 'destroy'])->name('empresas.destroy');
        Route::post('/contexto-empresa', [CompanyContextController::class, 'update'])->name('empresa.context');
        Route::get('/financeiro', fn () => redirect()->route('financeiro.saas.dashboard'))->name('financeiro.saas');
        Route::get('/financeiro/dashboard', [FinanceiroSaasController::class, 'dashboard'])->name('financeiro.saas.dashboard');
        Route::get('/financeiro/empresas/{empresa}', [FinanceiroSaasController::class, 'financeiroEmpresaShow'])->name('financeiro.saas.empresas.show');
        Route::get('/financeiro/empresas', [FinanceiroSaasController::class, 'financeiroEmpresas'])->name('financeiro.saas.empresas');
        Route::get('/financeiro/planos/novo', [SaasPlanController::class, 'create'])->name('financeiro.saas.plans.create');
        Route::post('/financeiro/planos', [SaasPlanController::class, 'store'])->name('financeiro.saas.plans.store');
        Route::get('/financeiro/planos/{plano}/editar', [SaasPlanController::class, 'edit'])->name('financeiro.saas.plans.edit');
        Route::put('/financeiro/planos/{plano}', [SaasPlanController::class, 'update'])->name('financeiro.saas.plans.update');
        Route::delete('/financeiro/planos/{plano}', [SaasPlanController::class, 'destroy'])->name('financeiro.saas.plans.destroy');
        Route::get('/financeiro/planos', [SaasPlanController::class, 'index'])->name('financeiro.saas.planos');
        Route::get('/financeiro/cobrancas/nova', [SaasChargeController::class, 'create'])->name('financeiro.saas.charges.create');
        Route::post('/financeiro/cobrancas', [SaasChargeController::class, 'store'])->name('financeiro.saas.charges.store');
        Route::get('/financeiro/cobrancas/{charge}/editar', [SaasChargeController::class, 'edit'])->name('financeiro.saas.charges.edit');
        Route::put('/financeiro/cobrancas/{charge}', [SaasChargeController::class, 'update'])->name('financeiro.saas.charges.update');
        Route::delete('/financeiro/cobrancas/{charge}', [SaasChargeController::class, 'destroy'])->name('financeiro.saas.charges.destroy');
        Route::get('/financeiro/cobrancas', [FinanceiroSaasController::class, 'cobrancas'])->name('financeiro.saas.cobrancas');
    });

    Route::get('/produtos', [ProductController::class, 'index'])->name('modulos.produtos');
    Route::get('/produtos/novo', [ProductController::class, 'create'])->name('produtos.create');
    Route::post('/produtos', [ProductController::class, 'store'])->name('produtos.store');
    Route::get('/produtos/{product}', [ProductController::class, 'show'])->name('produtos.show');
    Route::get('/produtos/{product}/editar', [ProductController::class, 'edit'])->name('produtos.edit');
    Route::put('/produtos/{product}', [ProductController::class, 'update'])->name('produtos.update');
    Route::delete('/produtos/{product}', [ProductController::class, 'destroy'])->name('produtos.destroy');
    Route::get('/categorias', [CategoryController::class, 'index'])->name('modulos.categorias');
    Route::get('/categorias/nova', [CategoryController::class, 'create'])->name('categorias.create');
    Route::post('/categorias', [CategoryController::class, 'store'])->name('categorias.store');
    Route::get('/categorias/{category}/editar', [CategoryController::class, 'edit'])->name('categorias.edit');
    Route::put('/categorias/{category}', [CategoryController::class, 'update'])->name('categorias.update');
    Route::delete('/categorias/{category}', [CategoryController::class, 'destroy'])->name('categorias.destroy');
    Route::get('/estoque', [EstoqueController::class, 'index'])->name('modulos.estoque');
    Route::get('/estoque/historico', [EstoqueController::class, 'historico'])->name('estoque.historico');
    Route::get('/estoque/produto/{product}/movimento/{tipo}', [EstoqueController::class, 'movimentoForm'])->name('estoque.movimento.form');
    Route::post('/estoque/produto/{product}/movimento/{tipo}', [EstoqueController::class, 'movimentoStore'])->name('estoque.movimento.store');
    Route::middleware(['caixa.aberto'])->group(function () {
        Route::get('/venda', [VendaController::class, 'index'])->name('modulos.venda');
        Route::post('/venda/finalizar', [VendaController::class, 'finalizar'])->name('venda.finalizar');
    });
    Route::redirect('/pdv', '/venda');
    Route::get('/entradas', [EntradaController::class, 'index'])->name('modulos.entradas');
    Route::post('/entradas', [EntradaController::class, 'store'])->name('entradas.store');
    Route::get('/saidas', [SaidaController::class, 'index'])->name('modulos.saidas');
    Route::post('/saidas', [SaidaController::class, 'store'])->name('saidas.store');
    Route::get('/entregas', [EntregaController::class, 'index'])->name('modulos.entregas');
    Route::post('/entregas', [EntregaController::class, 'store'])->name('entregas.store');
    Route::get('/comissoes', [CommissionController::class, 'index'])->name('modulos.comissoes');
    Route::get('/comissoes/nova', [CommissionController::class, 'create'])->name('comissoes.create');
    Route::post('/comissoes', [CommissionController::class, 'store'])->name('comissoes.store');
    Route::post('/comissoes/{commission}/pagar', [CommissionController::class, 'marcarPago'])->name('comissoes.pagar');
    Route::post('/comissoes/pagar-lote', [CommissionController::class, 'marcarPagoLote'])->name('comissoes.pagar-lote');
    Route::get('/financeiro/fluxo-caixa', [FinanceiroController::class, 'fluxoCaixa'])->name('financeiro.fluxo_caixa');
    Route::get('/financeiro/despesas-fixas', [FinanceiroController::class, 'despesasFixas'])->name('financeiro.despesas_fixas');
    Route::post('/financeiro/despesas-fixas', [FinanceiroController::class, 'storeDespesasFixas'])->name('financeiro.despesas_fixas.store');
    Route::get('/financeiro/categorias-despesas-fixas', [FinanceiroController::class, 'categoriasDespesasFixas'])->name('financeiro.categorias_despesas_fixas');
    Route::post('/financeiro/categorias-despesas-fixas', [FinanceiroController::class, 'storeCategoriaDespesasFixas'])->name('financeiro.categorias_despesas_fixas.store');
    Route::get('/financeiro/despesas-variaveis', [FinanceiroController::class, 'despesasVariaveis'])->name('financeiro.despesas_variaveis');
    Route::post('/financeiro/despesas-variaveis', [FinanceiroController::class, 'storeDespesasVariaveis'])->name('financeiro.despesas_variaveis.store');
    Route::post('/financeiro/categorias-despesas-variaveis', [FinanceiroController::class, 'storeCategoriaDespesasVariaveis'])->name('financeiro.categorias_despesas_variaveis.store');
    Route::middleware('admin')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('modulos.usuarios');
        Route::get('/usuarios/novo', [UserController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{user}/editar', [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    });
    Route::get('/caixa', [CaixaController::class, 'index'])->name('modulos.caixa');
    Route::post('/caixa/abrir', [CaixaController::class, 'abrir'])->name('caixa.abrir');
    Route::post('/caixa/fechar', [CaixaController::class, 'fechar'])->name('caixa.fechar');
    Route::post('/caixa/venda', [CaixaController::class, 'registrarVenda'])->name('caixa.venda');
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('modulos.relatorios');
    Route::get('/configuracoes', [PdvSettingsController::class, 'edit'])->name('modulos.configuracoes');
    Route::post('/configuracoes/logo', [PdvSettingsController::class, 'uploadLogo'])->name('configuracoes.logo');
    Route::post('/configuracoes', [PdvSettingsController::class, 'update'])->name('configuracoes.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
