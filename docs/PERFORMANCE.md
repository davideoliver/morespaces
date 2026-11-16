# Medições de desempenho

As medições abaixo devem ser executadas com Apache e MySQL do XAMPP ativos,
com a aplicação configurada para o banco local e com um usuário `agent`
autenticado. O endpoint da API usa sessão; por isso, copie um cookie válido
para o arquivo `morespaces-cookies.txt` antes do teste.

## Cenário

- Endpoint: `GET /api/v1/levels`
- Concorrência: 5
- Requisições: 50
- Limite da API: 60 requisições por minuto
- Ferramenta: `ab.exe` distribuído com o Apache do XAMPP

As 50 requisições ficam abaixo do limite de 60 por minuto para evitar que o
resultado seja contaminado por respostas `429`.

## Comando

No PowerShell, na raiz do projeto:

```powershell
$ab = 'C:\xampp\apache\bin\ab.exe'
$cookie = 'morespaces-session=COLE_O_VALOR_AQUI'
& $ab -n 50 -c 5 -H "Cookie: $cookie" `
  'http://localhost/morespaces/api/v1/levels'
```

Registre os valores exibidos pelo `ab.exe`:

| Data | Concorrência | Requisições | Falhas | Tempo total (ms) | Requisições/s | Tempo médio (ms) |
| --- | ---: | ---: | ---: | ---: | ---: | ---: |
| a preencher | 5 | 50 | a preencher | a preencher | a preencher | a preencher |

O resultado é dependente da máquina, do estado do XAMPP e da quantidade de
dados no banco; não use os valores desta tabela como garantia de produção.
