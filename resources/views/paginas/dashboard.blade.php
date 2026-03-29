<x-app-layout>
    <div class="px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Dashboard</h1>
        <p class="mt-1 text-base text-gray-600">Bem-vindo ao {{ config('pdv.brand_name') }}</p>

        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Minhas Vendas Hoje</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">R$ 0,00</p>
                <p class="mt-1 text-sm text-gray-500">0 venda(s)</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Total do Dia</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">R$ 0,00</p>
                <p class="mt-1 text-sm text-gray-500">0 venda(s)</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Estoque</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">3 produtos</p>
                <p class="mt-1 text-sm text-gray-500">28 itens</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Meu Caixa</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">—</p>
                <p class="mt-1 text-sm text-gray-500">Fechado</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:col-span-2 xl:col-span-1">
                <p class="text-sm font-medium text-gray-500">Minhas Comissões</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">R$ 9,56</p>
                <p class="mt-1 text-sm text-gray-500">A receber</p>
            </div>
        </div>
    </div>
</x-app-layout>
