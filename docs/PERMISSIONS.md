# Matriz de permissões

Papéis persistidos: `member`, `agent` e `admin`. Visitante representa uma
requisição sem usuário autenticado.

| Ação real | Visitante | Member | Agent | Admin |
|---|---:|---:|---:|---:|
| `GET /` (home) | 200 | 200 | 200 | 200 |
| `GET /arquivos` (`LevelPolicy@viewAny`) | 302 | 403 | 200 | 200 |
| `GET /arquivos/{level}` (`LevelPolicy@view`) | 302 | 403 | 404* | 404* |
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

`*` Agentes e administradores passam pela autorização, mas o nível fictício
continua retornando `404`, pois ainda não há conteúdo persistido para ele.
