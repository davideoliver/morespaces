# Testes

A suíte usa PHPUnit via `php artisan test`, com SQLite em memória e
`RefreshDatabase` nos testes que acessam o banco. As migrations são executadas
antes de cada teste, incluindo as colunas `role` e `danger_level` e seus casts
para enums. O arquivo `phpunit.xml` configura `SESSION_DRIVER=array`, portanto
os testes não dependem de um servidor MySQL.

## Matriz de cobertura

| Camada | Área | Cobertura |
| --- | --- | --- |
| Unit | Enums | Valores válidos de `Role` e rejeição de valor inválido em `DangerLevel` |
| Unit | Serviços | Sanitização de atributos antes do repositório |
| Feature | Autenticação | Cadastro, hashing, login, logout, throttle e entradas inválidas |
| Feature | Níveis | CRUD, busca, duplicidade, limites e validação |
| Feature | Contato | Persistência, associação ao usuário, autorização e entradas inválidas |
| Feature | API | Sessão, JSON, CRUD, paginação, busca e payloads inválidos |
| Feature | Autorização | Matriz de perfis member/agent/admin e políticas |
| Feature | Segurança | CSRF, SQL injection, XSS, cookies e headers |
| Feature | Observabilidade | Logs de autenticação, criação, erros e redaction |
| Feature | Integração | Jornada cadastro → login → promoção → criação → busca → logout |

## Execução local

```bash
php artisan test
php artisan test
```

O workflow [`tests.yml`](../.github/workflows/tests.yml) executa a mesma suíte
em PHP 8.2 a cada push e pull request.
