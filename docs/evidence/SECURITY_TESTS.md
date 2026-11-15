# Testes de segurança

Data da execução: `15/11/2026`.

## Resultado da execução automatizada

Comando executado:

```powershell
php artisan test tests/Feature/Security tests/Feature/SecurityHeadersTest.php
```

Resultado: **PASS — 8 testes passaram, 37 asserções, duração de 0,84 s**.
Os sete cenários T1–T7 foram exercitados por
`tests/Feature/Security/SecurityTest.php`; a verificação adicional de cabeçalhos
também passou. As evidências abaixo registram o resultado automatizado; as
capturas de tela da reprodução manual não foram necessárias para esta execução.
Após a correção da consulta compatível com MariaDB, T2 foi repetido no navegador
e a captura foi salva em `docs/evidence/img/T2-reflected-xss.png`.
As capturas T1–T7 foram salvas nos caminhos indicados em cada seção. Em T5, a
captura documenta o acesso negado após a sessão ser invalidada; a reprodução
específica do cookie antigo foi confirmada pelo teste automatizado.

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
- **RESULTADO REAL:** **PASS** — o payload de login não autenticou, a busca
  autenticada respondeu com sucesso e o registro de `levels` permaneceu
  disponível. Verificado por
  `test_sql_injection_payloads_do_not_bypass_auth_or_damage_levels`.
- **Evidência:** `docs/evidence/img/T1-sql-injection.png`

## T2 — XSS refletido

- **Objetivo:** confirmar que o termo de busca é codificado antes de voltar ao HTML.
- **Payload:** `<script>alert(1)</script>`.
- **Reprodução manual:** faça login como agente no navegador e abra:
  `http://localhost/morespaces/arquivos?q=%3Cscript%3Ealert(1)%3C%2Fscript%3E`.
- **Esperado:** o corpo contém `&lt;script&gt;alert(1)&lt;/script&gt;` e não
  contém a tag `<script>alert(1)</script>` sem escape.
- **RESULTADO REAL:** **PASS** — após a correção da consulta MariaDB, a busca
  retornou a página normalmente; o payload apareceu como texto escapado
  (`&lt;script&gt;...`) e não como uma tag `<script>` executável. Verificado no
  navegador e por `test_search_escapes_reflected_xss_payload`.
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
- **RESULTADO REAL:** **PASS** — as páginas web redirecionaram para `/#login`
  e a API respondeu HTTP 401. Verificado por
  `test_protected_endpoints_reject_guests_with_expected_responses`.
- **Evidência:** `docs/evidence/img/T3-access-without-login.png`

## T4 — Privilégio insuficiente

- **Objetivo:** confirmar que membro não acessa arquivos e agente não altera níveis.
- **Reprodução manual:** faça login como membro e abra `/arquivos` (esperado 403).
  Depois faça login como agente e tente criar, editar e excluir um nível pelos
  formulários da área de arquivos (cada operação deve retornar 403).
- **Esperado:** membro recebe 403; agente recebe 403 em POST, PUT e DELETE.
- **RESULTADO REAL:** **PASS** — membro recebeu HTTP 403 e as operações POST,
  PUT e DELETE do agente receberam HTTP 403. Verificado por
  `test_members_and_agents_cannot_perform_restricted_actions`.
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
- **RESULTADO REAL:** **PASS** — o cookie capturado antes do logout não
  reautenticou a sessão; o acesso foi redirecionado para `/#login`. Verificado
  por `test_replaying_a_session_cookie_after_logout_is_unauthenticated`.
- **Evidência:** `docs/evidence/img/T5-invalid-session-reuse.png`

## T6 — CSRF

- **Objetivo:** confirmar rejeição de POST sem token CSRF.
- **Reprodução manual (PowerShell):**

  ```powershell
  curl.exe -i -X POST "http://localhost/morespaces/enviar" -d "nome=Teste&contatoEmail=teste%40example.com&mensagem=Sem%20token"
  ```

- **Esperado:** HTTP 419 (`csrf_token_mismatch`), sem criação de mensagem.
- **RESULTADO REAL:** **PASS** — o POST sem token CSRF foi rejeitado com HTTP
  419. Verificado por `test_post_without_csrf_token_returns_419`.
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
- **RESULTADO REAL:** **PASS** — o campo `role=admin` enviado pelo cliente foi
  ignorado e o usuário foi persistido como `member`. Verificado por
  `test_registration_ignores_admin_role_payload`.
- **Evidência:** `docs/evidence/img/T7-register-role-escalation.png`
