# PDV Sistema

Sistema de ponto de venda (PDV) em [Laravel](https://laravel.com): produtos, estoque, vendas, caixa, comissões, usuários e permissões por tela.

## Requisitos

- PHP 8.2+
- Composer
- Node.js + npm (assets com Vite)

## Instalação local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan storage:link
php artisan serve
```

Ajuste o `.env` (banco SQLite ou MySQL) e credenciais conforme o ambiente.

## Licença

O projeto utiliza o framework Laravel, licenciado sob [MIT](https://opensource.org/licenses/MIT).
