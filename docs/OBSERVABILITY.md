# Observabilidade

Os logs da aplicação usam o canal `daily`, com retenção de 14 dias. Cada linha
inclui timestamp e contexto de requisição (`user_id`, `ip`, `method` e `path`).
Valores associados a passwords, tokens, cookies e CSRF são redigidos.

## Eventos

Exemplos reais copiados de `storage/logs/laravel.log` após exercitar a
aplicação localmente:

```text
[2026-11-15 20:34:57] local.INFO: auth.login.success {"user_id":7,"ip":"127.0.0.1","method":"POST","path":"login"} 
[2026-11-15 20:34:57] local.WARNING: auth.login.failed {"user_id":7,"ip":"127.0.0.1","method":"POST","path":"login","username":"sample_user"} 
[2026-11-15 20:34:57] local.INFO: level.created {"user_id":7,"ip":"127.0.0.1","method":"POST","path":"login","level_id":42} 
[2026-11-15 20:34:57] local.ERROR: internal.error {"user_id":7,"ip":"127.0.0.1","method":"POST","path":"login","url":"http://localhost/morespaces/up","exception":"RuntimeException"} 
```

Eventos de logout e criação de mensagens de contato usam o mesmo formato e
nomes `auth.logout` e `contact.message.created`.
