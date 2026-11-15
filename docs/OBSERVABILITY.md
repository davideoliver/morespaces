# Observabilidade

Os logs da aplicação usam o canal `daily`, com retenção configurável por
`LOG_DAILY_DAYS` (14 dias por padrão). Cada linha inclui timestamp e contexto
de requisição (`user_id`, `ip`, `method` e `path`). Valores cujas chaves
contêm `password`, `token`, `cookie` ou `csrf` são redigidos, inclusive em
estruturas aninhadas.

## Eventos

Eventos emitidos pelo código:

```text
[2026-11-15 20:34:57] local.INFO: auth.login.success {"user_id":7,"ip":"127.0.0.1","method":"POST","path":"login"} 
[timestamp] local.WARNING: auth.login.failed {"ip":"127.0.0.1","method":"POST","path":"login","username":"sample_user"}
[timestamp] local.INFO: level.created {"user_id":7,"ip":"127.0.0.1","method":"POST","path":"arquivos","level_id":42}
[timestamp] local.ERROR: internal.error {"user_id":7,"ip":"127.0.0.1","method":"GET","path":"up","url":"http://localhost/morespaces/up","exception":"RuntimeException"}
```

Eventos de logout e criação de mensagens de contato usam o mesmo formato e
nomes `auth.logout` e `contact.message.created`.
