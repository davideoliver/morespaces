# API MoreSpaces

## Convenções

### URL base

Com a configuração padrão do projeto:

```powershell
$base = 'http://localhost/morespaces'
```

Os endpoints documentados neste arquivo usam `$base/api/v1`.
O caminho efetivo depende de `APP_URL` no `.env`.

### Sessão e CSRF

A API usa a sessão web existente (`auth`), não bearer token. Para obter uma sessão
e um token CSRF em uma instalação local, execute no PowerShell:

```powershell
$base = 'http://localhost/morespaces'
$jar = Join-Path $PWD 'morespaces-cookies.txt'
curl.exe -sS -c $jar "$base/" -o "$PWD\morespaces-home.html"
$csrf = [regex]::Match((Get-Content "$PWD\morespaces-home.html" -Raw), 'name="_token" value="([^"]+)"').Groups[1].Value
if ([string]::IsNullOrWhiteSpace($csrf)) { throw 'Token CSRF não encontrado na página inicial.' }
$username = $env:MS_USERNAME
$password = $env:MS_PASSWORD
curl.exe -sS -L -c $jar -b $jar -X POST "$base/login" `
  -H 'Accept: text/html' `
  --data-urlencode "_token=$csrf" `
  --data-urlencode "username=$username" `
  --data-urlencode "password=$password"
```

Defina `$env:MS_USERNAME` e `$env:MS_PASSWORD` antes do login. Reutilize o mesmo
`$jar` e envie `X-CSRF-TOKEN: $csrf` em toda requisição que altera dados:

```powershell
curl.exe -sS -b $jar -H 'Accept: application/json' -H "X-CSRF-TOKEN: $csrf" `
  -H 'Content-Type: application/json' -X POST "$base/api/v1/levels" `
  --data-raw '{"name":"Nível de exemplo","description":"Descrição de exemplo.","danger_level":"low"}'
```

As respostas JSON de erro têm este envelope:

```json
{
  "status": 422,
  "error": "validation_error",
  "message": "Os dados enviados são inválidos.",
  "errors": {
    "name": ["O campo name é obrigatório."]
  }
}
```

O campo `errors` só é incluído para `422`. Com `APP_DEBUG=true`, erros também
incluem `debug` com classe, arquivo e linha da exceção; esses detalhes não devem
ser expostos em produção.

### Paginação

`GET /api/v1/levels` retorna `data` e os metadados do paginator Laravel:
`current_page`, `last_page`, `per_page` e `total`. A implementação atual usa
15 itens por página. O parâmetro `q` pesquisa em `name`, `summary` e
`description`; `page` seleciona a página.

### Limite de requisições

O limiter `api` permite 60 requisições por minuto por usuário autenticado (ou
por IP quando não há usuário). Ao exceder o limite, a resposta é `429` com
`error: "too_many_requests"`.

## Endpoints

### GET `/api/v1/me`

**Papel obrigatório:** usuário autenticado (`member`, `agent` ou `admin`).

| Nome | Local | Tipo | Obrigatório | Regras |
|---|---|---|---|---|
| — | — | — | — | Sem parâmetros. |

**Requisição:**

```powershell
curl.exe -sS -b $jar -H 'Accept: application/json' "$base/api/v1/me"
```

**Sucesso `200` (NÃO VERIFICADO):**

```json
{
  "data": {
    "id": 1,
    "name": "Nome do usuário",
    "username": "usuario",
    "role": "agent"
  }
}
```

**Erros:** `401 unauthenticated`.

**Status possíveis documentados:** `200`, `401`.

### GET `/api/v1/levels`

**Papel obrigatório:** `agent` ou `admin`.

| Nome | Local | Tipo | Obrigatório | Regras |
|---|---|---|---|---|
| `q` | query | string | não | Pesquisa textual; é sanitizado. |
| `page` | query | integer | não | Página do paginator Laravel. |

**Requisição:**

```powershell
curl.exe -sS -b $jar -H 'Accept: application/json' `
  "$base/api/v1/levels?q=especial&page=1"
```

**Sucesso `200` (NÃO VERIFICADO):**

```json
{
  "data": [
    {
      "id": 1,
      "number": 0,
      "name": "Área especial",
      "summary": "Resumo",
      "description": "Descrição",
      "danger_level": "low",
      "created_by": 1,
      "created_at": "2026-01-01T00:00:00.000000Z",
      "updated_at": "2026-01-01T00:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

**Erros:** `401 unauthenticated`, `403 forbidden`.

**Status possíveis documentados:** `200`, `401`, `403`.

### POST `/api/v1/levels`

**Papel obrigatório:** `admin`.

| Nome | Local | Tipo | Obrigatório | Regras |
|---|---|---|---|---|
| `number` | JSON | integer/null | não | `0..9999`, único; se omitido, usa o próximo número. |
| `name` | JSON | string | sim | 3–120 caracteres. |
| `summary` | JSON | string/null | não | Máximo 255 caracteres. |
| `description` | JSON | string | sim | Máximo 5000 caracteres. |
| `danger_level` | JSON | string | sim | `safe`, `low`, `moderate`, `high` ou `lethal`. |

**Requisição:**

```powershell
curl.exe -sS -b $jar -H 'Accept: application/json' -H "X-CSRF-TOKEN: $csrf" `
  -H 'Content-Type: application/json' -X POST "$base/api/v1/levels" `
  --data-raw '{"number":123,"name":"Nível API","summary":"Resumo da API","description":"Descrição da API","danger_level":"low"}'
```

**Sucesso `201` (NÃO VERIFICADO):** o corpo é `{"data": ...}` no formato de
`LevelResource`, e o cabeçalho `Location` aponta para
`/api/v1/levels/{number}`.

**Erros:** `401 unauthenticated`, `403 forbidden`, `419 csrf_token_mismatch`,
`422 validation_error`, `429 too_many_requests`.

**Status possíveis documentados:** `201`, `401`, `403`, `419`, `422`, `429`.

### GET `/api/v1/levels/{level}`

**Papel obrigatório:** `agent` ou `admin`.

| Nome | Local | Tipo | Obrigatório | Regras |
|---|---|---|---|---|
| `level` | URL | integer | sim | Número do nível; deve existir. |

**Requisição:**

```powershell
$number = 123
curl.exe -sS -b $jar -H 'Accept: application/json' "$base/api/v1/levels/$number"
```

**Sucesso `200` (NÃO VERIFICADO):** retorna `{"data": ...}` no formato de
`LevelResource`.

**Erros:** `401 unauthenticated`, `403 forbidden`, `404 not_found`.

**Status possíveis documentados:** `200`, `401`, `403`, `404`.

### PUT `/api/v1/levels/{level}`

### PATCH `/api/v1/levels/{level}`

Os dois métodos executam a mesma ação e têm o mesmo contrato.

**Papel obrigatório:** `admin`.

| Nome | Local | Tipo | Obrigatório | Regras |
|---|---|---|---|---|
| `level` | URL | integer | sim | Número do nível; deve existir. |
| `number` | JSON | integer/null | não | `0..9999`, único entre os outros níveis. |
| `name` | JSON | string | sim | 3–120 caracteres. |
| `summary` | JSON | string/null | não | Máximo 255 caracteres. |
| `description` | JSON | string | sim | Máximo 5000 caracteres. |
| `danger_level` | JSON | string | sim | `safe`, `low`, `moderate`, `high` ou `lethal`. |

**Requisição:**

```powershell
$number = 123
curl.exe -sS -b $jar -H 'Accept: application/json' -H "X-CSRF-TOKEN: $csrf" `
  -H 'Content-Type: application/json' -X PATCH "$base/api/v1/levels/$number" `
  --data-raw '{"number":123,"name":"Nível atualizado","summary":"Resumo atualizado","description":"Descrição atualizada","danger_level":"moderate"}'
```

**Sucesso `200` (NÃO VERIFICADO):** retorna `{"data": ...}` no formato de
`LevelResource`.

**Erros:** `401 unauthenticated`, `403 forbidden`, `404 not_found`,
`419 csrf_token_mismatch`, `422 validation_error`, `429 too_many_requests`.

**Status possíveis documentados:** `200`, `401`, `403`, `404`, `419`, `422`, `429`.

### DELETE `/api/v1/levels/{level}`

**Papel obrigatório:** `admin`.

| Nome | Local | Tipo | Obrigatório | Regras |
|---|---|---|---|---|
| `level` | URL | integer | sim | Número do nível; deve existir. |

**Requisição:**

```powershell
$number = 123
curl.exe -sS -b $jar -H 'Accept: application/json' -H "X-CSRF-TOKEN: $csrf" `
  -X DELETE "$base/api/v1/levels/$number"
```

**Sucesso `204`:** corpo vazio.

**Erros:** `401 unauthenticated`, `403 forbidden`, `404 not_found`,
`419 csrf_token_mismatch`, `429 too_many_requests`.

**Status possíveis documentados:** `204`, `401`, `403`, `404`, `419`, `429`.

## Resumo de status

| Status | Significado | Envelope/caso |
|---:|---|---|
| 200 | Sucesso de leitura/atualização | Corpo de sucesso |
| 201 | Nível criado | Corpo de sucesso + `Location` |
| 204 | Nível removido | Corpo vazio |
| 401 | Sessão ausente | `unauthenticated` |
| 403 | Papel sem permissão | `forbidden` |
| 404 | Nível inexistente | `not_found` |
| 419 | CSRF ausente ou inválido | `csrf_token_mismatch` |
| 422 | Payload inválido | `validation_error` + `errors` |
| 429 | Limite excedido | `too_many_requests` |

Os comportamentos `401`, `403`, `200`, `201`, `204`, `404` e `422` são exercitados
em [ApiLevelsTest.php](../tests/Feature/ApiLevelsTest.php). O tratamento
padronizado de `419` e `429` é coberto pelos testes de segurança e autenticação
existentes; as respostas JSON ilustrativas acima não foram executadas contra um
servidor HTTP local e, por isso, estão marcadas como **NÃO VERIFICADO**.
