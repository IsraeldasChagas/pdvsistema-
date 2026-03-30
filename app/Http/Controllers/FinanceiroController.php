<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class FinanceiroController extends Controller
{
    public function despesasFixas(): View
    {
        return view('paginas.financeiro.despesas-fixas');
    }

    public function despesasVariaveis(): View
    {
        return view('paginas.financeiro.despesas-variaveis');
    }
}

