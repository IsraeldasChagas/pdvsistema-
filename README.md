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

Ajuste o `.env` com host, usuário e senha do **MySQL remoto** da hospedagem.

### MySQL remoto (obrigatório para este projeto)

1. No painel da hospedagem (cPanel / Locaweb): **MySQL remoto** → libere o **IP público do seu PC**.
2. Senha com `$` ou `@` no `.env`: use **aspas simples**, ex.: `DB_PASSWORD='minha$enha'`.
3. Teste: `testar-banco.bat` ou `php artisan db:show`.
4. Se o banco não conectar, o **login retorna erro 500** (sessão em `database`).


## Licença

O projeto utiliza o framework Laravel, licenciado sob [MIT](https://opensource.org/licenses/MIT).
