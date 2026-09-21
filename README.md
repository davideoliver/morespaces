# MoreSpaces

Site institucional da MoreSpaces desenvolvido em **Laravel** (arquitetura **MVC**), com uma landing page completa (Home, Sobre, FAQ, Contato), um fluxo simplificado de autenticação por sessão (Login / Cadastro / Logout) e uma área restrita de "Arquivos" (M.G.E) liberada apenas para usuários identificados como *Spaces User*.

> Autor: Davi De Oliveira Costa — Sistemas Computacionais

---

## 🧱 Estrutura do projeto

```
app/
  Http/
    Controllers/
      Controller.php        # Controller base abstrato
      HomeController.php    # Toda a lógica de rotas do site
bootstrap/
  app.php                   # Bootstrap da aplicação (rotas, middlewares, exceções)
public/
  index.php                 # Front controller (ponto de entrada HTTP)
  css/style.css             # Estilos globais do site
  js/scripts.js             # Menu mobile, scroll spy, carrossel de FAQ, validações
resources/
  views/
    layouts/app.blade.php   # Layout HTML base (head, css, js)
    home.blade.php          # Página inicial (Home/Sobre/FAQ/Contato/Login/Cadastro)
    files/
      files.blade.php       # Listagem de níveis de arquivos (M.G.E)
      not-found.blade.php   # Página de nível de arquivo ainda não disponível
routes/
  web.php                   # Rotas HTTP da aplicação
  console.php               # Comandos Artisan customizados
```

> ⚠️ Este pacote de arquivos representa apenas as partes centrais da aplicação. Para rodar o projeto de verdade é necessário criar/gerar um projeto Laravel completo (com `vendor/`, `.env`, `artisan`, migrations etc.) e substituir os arquivos correspondentes pelos deste repositório, conforme os passos abaixo.

---

## ✅ Pré-requisitos

- **PHP** 8.2 ou superior
- **Composer** (gerenciador de dependências do PHP)
- Extensões PHP padrão do Laravel (`mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`)
- Node.js (opcional — só é necessário caso deseje usar um bundler como Vite; o projeto atual referencia `css` e `js` já compilados/estáticos em `public/`)

---

## 🚀 Passo a passo para rodar o projeto

### 1. Criar o projeto Laravel (caso ainda não exista)

```bash
composer create-project laravel/laravel morespaces
cd morespaces
```

### 2. Copiar os arquivos deste pacote para o projeto

Copie cada arquivo para o caminho equivalente dentro do projeto Laravel recém-criado, respeitando a estrutura mostrada acima (ex.: `HomeController.php` vai para `app/Http/Controllers/HomeController.php`, `web.php` vai para `routes/web.php`, e assim por diante).

Os assets estáticos referenciados nas views (`public/imgs/mswhite.png`, `public/imgs/backroomshd.webp`, `public/imgs/parking.webp`, `public/imgs/pipes.webp`, `public/imgs/eletricstation.webp` e os favicons em `public/imgs/favcons/`) também precisam existir dentro de `public/` para que as imagens do site apareçam corretamente.

### 3. Instalar as dependências PHP

```bash
composer install
```

### 4. Configurar o arquivo de ambiente

```bash
cp .env.example .env
php artisan key:generate
```

O arquivo `.env` controla configurações como nome da aplicação, URL, driver de sessão e de banco de dados. Para este projeto (que ainda não usa banco de dados — nem para usuários, nem para os "Arquivos"), a configuração padrão de `.env.example` já é suficiente para subir o site.

### 5. Subir o servidor de desenvolvimento

```bash
php artisan serve
```

Por padrão o site ficará disponível em:

```
http://127.0.0.1:8000
```

---

## 🗺️ Rotas disponíveis

| Método | URI                 | Nome da rota   | Ação                                              |
|--------|---------------------|----------------|----------------------------------------------------|
| GET    | `/`                  | `home`         | Página inicial (landing page completa)             |
| POST   | `/enviar`            | `contact`      | Envio do formulário de contato                     |
| POST   | `/login`             | `login`        | Login simplificado (baseado em sessão)             |
| POST   | `/cadastro`          | `register`     | Cadastro simplificado (sem persistência em banco)  |
| POST   | `/logout`            | `logout`       | Logout / encerramento de sessão                    |
| GET    | `/arquivos`          | `files`        | Lista os níveis da área restrita (M.G.E)           |
| GET    | `/arquivos/{level}`  | `files.level`  | Exibe um nível específico (atualmente sempre 404)  |

---

## 🔐 Sobre a autenticação

O login/cadastro **não usa** o sistema de autenticação nativo do Laravel (`Auth`) nem tabela de usuários no banco de dados — é um fluxo de demonstração:

- Qualquer combinação válida de usuário/senha é aceita no login.
- O `username` informado é apenas armazenado na sessão (`session('username')`).
- Um usuário é considerado **"Spaces User"** (e ganha acesso à área de Arquivos) sempre que o texto `"spaces"` estiver contido no seu username (ex.: `moreSpacesAdmin`).

Para testar a área de Arquivos, basta fazer login com um usuário cujo nome contenha `spaces`.

---

## 🧩 Próximos passos sugeridos

- Conectar a área de "Arquivos" (`/arquivos/{level}`) a um banco de dados real, criando o Model/Migration correspondente.
- Substituir a autenticação simplificada por sessão pelo sistema de `Auth` nativo do Laravel (com tabela `users`, hashing de senha, etc.), caso o projeto avance para produção.
- Persistir o formulário de contato (ex.: salvar em banco ou enviar e-mail via `Mail::send`).

---

## 📄 Licença

Projeto acadêmico/educacional desenvolvido para fins de estudo em Sistemas Computacionais. Todos os direitos reservados © 2026 MoreSpaces.
