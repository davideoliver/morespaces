# Auditoria de segurança

Data da verificação: 2026-11-15.

| Controle | Resultado |
| --- | --- |
| XSS em Blade | **Aprovado.** A busca em `resources/views/**/*.blade.php` não encontrou `{!! !!}`. Valores de entrada e saída usam `{{ }}`; a confirmação de exclusão deixou de usar `onsubmit` inline e passou a usar listener externo. |
| Cabeçalhos | **Corrigido.** `SecurityHeaders` aplica `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`, `Referrer-Policy`, `Permissions-Policy` restritiva e CSP. |
| CSP | **Aprovado.** Views usam apenas CSS, JavaScript, imagens e manifesto locais (`'self'`). `style-src 'unsafe-inline'` é permitido somente porque ainda existem atributos `style` inline no layout da home; `script-src` não permite inline scripts nem handlers. |
| CSRF | **Aprovado.** Todos os formulários POST/PUT/DELETE usam `@csrf`. Não há chamadas `fetch` na aplicação Laravel; portanto não existe token a acrescentar a chamadas AJAX. O tratamento de `TokenMismatchException` retorna HTTP 419 e a view `errors/419` informa a renovação da sessão. |
| Sessão | **Aprovado.** `http_only=true`, `same_site=lax`, duração padrão de 120 minutos e `secure` controlado por `SESSION_SECURE_COOKIE`. O `.env.example` recomenda 60 minutos e habilitar `secure` em HTTPS. |
| Mass assignment | **Corrigido.** `User::$fillable` não expõe `role`, `id` ou `created_by`; `Level::$fillable` não expõe `id` ou `created_by`. `created_by` é atribuído explicitamente no repositório, fora da atribuição em massa. |
| SQL injection | **Aprovado.** O único `whereRaw` está em `EloquentLevelRepository` e usa placeholders, escape de `\%_` e `ESCAPE`; não há `DB::raw`, `selectRaw` ou consultas construídas por interpolação. |
| Rate limiting | **Aprovado.** `login`, `register` e `contact` têm limitadores centralizados em `AppServiceProvider`: 5/ minuto por usuário+IP para autenticação e 3/minuto por IP para contato. |
| Dependências | **Verificar com `composer audit`.** O resultado deve ser registrado junto com esta auditoria. |
| Testes | **Aprovado.** `php artisan test` passou com 72 testes e 191 asserções, incluindo a regressão dos cabeçalhos. O handler de `TokenMismatchException` foi confirmado no bootstrap e na view HTTP 419. |
| Lighthouse | **Não executado neste ambiente.** O CLI foi tentado, mas não há uma instalação Chrome detectável (`No Chrome installations found`). A home respondeu 200 e os cabeçalhos foram verificados via HTTP; reexecute Lighthouse em uma máquina com Chrome para confirmar a meta 100. |
