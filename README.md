# MoreSpaces

[![Testes](https://github.com/davideoliver/morespaces/actions/workflows/tests.yml/badge.svg)](https://github.com/davideoliver/morespaces/actions/workflows/tests.yml)

MoreSpaces é um site acadêmico sobre exploração de níveis fictícios no estilo
Backrooms. A aplicação oferece uma landing page pública, autenticação por
sessão, uma área protegida de arquivos (M.G.E), CRUD de níveis e uma API JSON
para os mesmos serviços. O projeto foi desenvolvido para a disciplina
**Sistemas Computacionais**.

## Stack

- PHP 8.2+
- Laravel 12
- Blade, HTML, CSS e JavaScript estáticos
- MySQL/MariaDB para desenvolvimento com XAMPP
- SQLite em memória para os testes
- PHPUnit 11 via `php artisan test`

## Requisitos

- Windows com [XAMPP](https://www.apachefriends.org/) (Apache e MySQL
  disponíveis)
- PHP 8.2+ com `mbstring`, `openssl`, `pdo_mysql`, `pdo_sqlite`, `tokenizer`,
  `xml`, `ctype` e `json`
- [Composer](https://getcomposer.org/)
- Git (opcional, para clonar o repositório)

O Node.js não é necessário para executar esta versão: os arquivos CSS e
JavaScript usados pelo site já ficam em `public/`.

## Instalação no Windows/XAMPP

Execute os comandos a partir da raiz do projeto (`C:\xampp\htdocs\morespaces`).

### 1. Inicie o XAMPP

Abra o painel do XAMPP e inicie **Apache** e **MySQL**.

### 2. Crie o banco

No phpMyAdmin ou no console do MySQL, execute:

```sql
CREATE DATABASE morespaces
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

### 3. Instale as dependências e crie o ambiente

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Abra `.env` e confirme `DB_DATABASE=morespaces`, `DB_USERNAME=root` e a senha
do MySQL local. A configuração padrão do XAMPP usa usuário `root` sem senha.
Se o MySQL local exigir senha, altere apenas o `.env`; nunca publique esse
arquivo.

Antes de semear, defina no `.env` um usuário e uma senha locais para a conta
administradora:

```dotenv
SEED_ADMIN_USERNAME=admin_local
SEED_ADMIN_PASSWORD=troque-esta-senha-local
```

### 4. Execute migrations e seeders

```powershell
php artisan migrate --seed
```

Esse comando cria usuários, níveis e sessões. Os dados de demonstração são:

| Usuário | Papel | Uso |
|---|---|---|
| valor de `SEED_ADMIN_USERNAME` | `admin` | CRUD de níveis e mensagens |
| `demo_agent` | `agent` | visualizar e pesquisar níveis |
| `demo_member` | `member` | testar acesso sem permissão à área M.G.E |

As credenciais do administrador são somente as definidas no seu `.env`. As
contas `demo_*` são contas locais de demonstração criadas pelo seeder; não use
essas contas em produção nem reutilize senhas reais.

### 5. Inicie o site

```powershell
php artisan serve
```

Abra <http://127.0.0.1:8000>. Para confirmar as rotas registradas:

```powershell
php artisan route:list
```

### 6. Execute os testes

Os testes usam SQLite em memória e não alteram o banco MySQL local:

```powershell
php artisan test
```

## Estrutura

```text
app/
  Http/Controllers/       Entrada HTTP e respostas
  Http/Requests/          Validação de entrada
  Http/Resources/         Formato das respostas JSON
  Models/                 Entidades Eloquent
  Policies/               Autorização por papel
  Repositories/           Consultas e persistência
  Services/               Regras de negócio e sanitização
  Enums/                  Papéis e níveis de perigo
bootstrap/app.php         Rotas, middleware e respostas de exceção
config/                   Configuração baseada no .env
database/                 Migrations, factories e seeders
docs/                     Arquitetura, banco, API, permissões e testes
public/                   Front controller, CSS, JS e imagens
resources/views/          Templates Blade
routes/                   Rotas web e API v1
tests/                    Testes unitários e de integração
```

## Documentação

- [Arquitetura](docs/ARCHITECTURE.md)
- [Banco de dados](docs/DATABASE.md)
- [API](docs/API.md)
- [Permissões](docs/PERMISSIONS.md)
- [Observabilidade](docs/OBSERVABILITY.md)
- [Testes](docs/TESTING.md)

## Licença

Projeto acadêmico/educacional desenvolvido para fins de estudo em Sistemas
Computacionais. Todos os direitos reservados © 2026 MoreSpaces.
