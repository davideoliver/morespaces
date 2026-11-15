# Testes de segurança

Data da execução: `____/____/________`.

Os testes automatizados podem ser executados com `php artisan test tests/Feature/Security`.
Os comandos abaixo assumem a aplicação disponível em `http://localhost/morespaces`.
Não substitua os campos **RESULTADO REAL** por resultados presumidos: preencha-os
somente após executar o teste e salve a captura no caminho indicado.

## T1 — Injeção SQL

- **Objetivo:** confirmar que entradas de login e busca não permitem autenticação
  indevida, erro 500 ou remoção da tabela `levels`.
- **Payload:** username `' OR '1'='1' --`; busca `1; DROP TABLE levels;--`.
- **Reprodução manual (PowerShell):**

  ```powershell
  curl.exe -i -X POST "http://localhost/morespaces/login" -d "username=%27%20OR%20%271%27%3D%271%27%20--&password=qualquer-senha"
  ```

  Faça login como agente no navegador, copie o cookie `morespaces-session` e
  execute, substituindo `VALOR_DO_COOKIE`:

  ```powershell
  curl.exe -i -b "morespaces-session=VALOR_DO_COOKIE" "http://localhost/morespaces/api/v1/levels?q=1%3B%20DROP%20TABLE%20levels%3B--"
  ```

- **Esperado:** o payload de login não autentica; as respostas não são 500; a
  busca autenticada retorna 200; a tabela `levels` permanece disponível.
- **RESULTADO REAL:** ________________________________________________
- **Evidência:** `docs/evidence/img/T1-sql-injection.png`

## T2 — XSS refletido

- **Objetivo:** confirmar que o termo de busca é codificado antes de voltar ao HTML.
- **Payload:** `<script>alert(1)</script>`.
- **Reprodução manual:** faça login como agente no navegador e abra:
  `http://localhost/morespaces/arquivos?q=%3Cscript%3Ealert(1)%3C%2Fscript%3E`.
- **Esperado:** o corpo contém `&lt;script&gt;alert(1)&lt;/script&gt;` e não
  contém a tag `<script>alert(1)</script>` sem escape.
- **RESULTADO REAL:** ________________________________________________
- **Evidência:** `docs/evidence/img/T2-reflected-xss.png`

## T3 — Acesso sem login

- **Objetivo:** confirmar proteção das páginas web e da API.
- **Reprodução manual (PowerShell):**

  ```powershell
  curl.exe -i "http://localhost/morespaces/arquivos"
  curl.exe -i "http://localhost/morespaces/admin/mensagens"
  curl.exe -i -H "Accept: application/json" "http://localhost/morespaces/api/v1/levels"
  ```

- **Esperado:** `/arquivos` e `/admin/mensagens` redirecionam para `/#login`;
  `/api/v1/levels` retorna HTTP 401.
- **RESULTADO REAL:** ________________________________________________
- **Evidência:** `docs/evidence/img/T3-access-without-login.png`

## T4 — Privilégio insuficiente

- **Objetivo:** confirmar que membro não acessa arquivos e agente não altera níveis.
- **Reprodução manual:** faça login como membro e abra `/arquivos` (esperado 403).
  Depois faça login como agente e tente criar, editar e excluir um nível pelos
  formulários da área de arquivos (cada operação deve retornar 403).
- **Esperado:** membro recebe 403; agente recebe 403 em POST, PUT e DELETE.
- **RESULTADO REAL:** ________________________________________________
- **Evidência:** `docs/evidence/img/T4-insufficient-role.png`

## T5 — Reutilização de sessão invalidada

- **Objetivo:** confirmar que um cookie capturado antes do logout não continua autenticando.
- **Reprodução manual (PowerShell):**

  ```powershell
  curl.exe -i "http://localhost/morespaces/arquivos"
  ```

  No navegador, faça login, copie o cookie `morespaces-session` nas ferramentas
  de desenvolvimento, faça logout, restaure esse valor no cookie e abra
  `/arquivos`.
- **Esperado:** o cookie antigo não concede acesso; a resposta redireciona para `/#login`.
- **RESULTADO REAL:** ________________________________________________
- **Evidência:** `docs/evidence/img/T5-invalid-session-reuse.png`

## T6 — CSRF

- **Objetivo:** confirmar rejeição de POST sem token CSRF.
- **Reprodução manual (PowerShell):**

  ```powershell
  curl.exe -i -X POST "http://localhost/morespaces/enviar" -d "nome=Teste&contatoEmail=teste%40example.com&mensagem=Sem%20token"
  ```

- **Esperado:** HTTP 419 (`csrf_token_mismatch`), sem criação de mensagem.
- **RESULTADO REAL:** ________________________________________________
- **Evidência:** `docs/evidence/img/T6-csrf.png`

## T7 — Escalada de privilégio no cadastro

- **Objetivo:** confirmar que o campo `role` enviado pelo cliente não cria administrador.
- **Payload:** `role=admin`.
- **Reprodução manual:** no navegador, abra a home, preencha o cadastro com
  `Pessoa Membro`, `payload_admin`, `senha1234` e confirmação `senha1234`.
  Envie também `role=admin` pelo DevTools ou por uma requisição reproduzida
  após copiar o token CSRF. Consulte o usuário no banco ou tente acessar
  `/arquivos` com essa conta.
- **Esperado:** o cadastro é criado como `member`, nunca como `admin`; `/arquivos`
  retorna 403 para essa conta.
- **RESULTADO REAL:** ________________________________________________
- **Evidência:** `docs/evidence/img/T7-register-role-escalation.png`
