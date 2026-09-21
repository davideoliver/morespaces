# MoreSpaces

Aplicação Laravel da página institucional MoreSpaces.

## Execução com XAMPP

1. Inicie Apache no XAMPP.
2. Confirme que o projeto está em `C:\xampp\htdocs\morespaces`.
3. Execute `composer install` na raiz do projeto.
4. Crie o arquivo `.env` a partir de `.env.example` e execute `php artisan key:generate`.
5. Acesse `http://localhost/morespaces`.

O arquivo raiz `index.php` encaminha as requisições para Laravel, então não é necessário executar `php artisan serve`.

Para desenvolvimento alternativo, `php artisan serve` continua disponível.

## Estrutura MVC

- `routes/web.php`: rotas públicas e endpoints dos formulários.
- `app/Http/Controllers/HomeController.php`: dados da página e validação das requisições.
- `resources/views`: layout e view Blade da página.
- `public/css`, `public/js` e `public/imgs/`: assets públicos.
