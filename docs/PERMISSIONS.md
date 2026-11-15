# Matriz de permissões

Papéis persistidos: `member`, `agent` e `admin`. Visitante representa uma
requisição sem usuário autenticado.

| Ação real | Visitante | Member | Agent | Admin |
|---|---:|---:|---:|---:|
| `GET /` (home) | 200 | 200 | 200 | 200 |
| `GET /arquivos` (`LevelPolicy@viewAny`) | 302 | 403 | 200 | 200 |
| `GET /arquivos/{level}` (`LevelPolicy@view`) | 302 | 403 | 200** | 200** |
| `LevelPolicy@create` | 403 | 403 | 403 | 200 |
| `LevelPolicy@update` | 403 | 403 | 403 | 200 |
| `LevelPolicy@delete` | 403 | 403 | 403 | 200 |
| `ContactMessagePolicy@viewAny` | 403 | 403 | 403 | 200 |
| `ContactMessagePolicy@delete` | 403 | 403 | 403 | 200 |

`302` é o redirecionamento de visitante para `home#login`. O middleware
`role` bloqueia membros com `403`; essa resposta usa a página temática
“Não há arquivos disponíveis”. As ações de policy sem rota HTTP são
representadas pelo resultado de autorização (`200` autorizado e `403`
negado).

`**` Para um nível sem o número informado, o route model binding retorna `404`.
Com os seeders executados, os níveis 0 a 3 existem e retornam `200` para
agentes e administradores.
