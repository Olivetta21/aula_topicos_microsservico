# Estrutura do JSON da API

A API recebe um JSON no corpo da requisição para gerar o arquivo CSV. O campo `titulo` define o nome do arquivo, `dados` contém os registros que serão exportados e `header` é opcional para incluir informações no início do relatório.

## Exemplo

```json
{
  "titulo": "usuarios",
  "header": {
    "titulo_empresa": "Unigran",
    "periodo_relatorio": "Maio/2026",
    "id": "123",
    "titulo_relatorio": "Relatório de Usuários"
  },
  "dados": [
    {
      "nome": "João",
      "idade": 30,
      "cidade": "São Paulo"
    }
  ]
}
```

## Exemplo em JavaScript

```html
<script>
fetch('/relatorio.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    titulo: 'usuarios',
    header: {
      titulo_empresa: 'Unigran',
      periodo_relatorio: 'Maio/2026',
      id: '123',
      titulo_relatorio: 'Relatório de Usuários'
    },
    dados: [
      { nome: 'João', idade: 30, cidade: 'São Paulo' }
    ]
  })
})
  .then(response => response.blob())
  .then(blob => {
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'usuarios.csv';
    a.click();
    window.URL.revokeObjectURL(url);
  });
</script>
```

## Campos

- `titulo`: nome base do arquivo baixado.
- `header`: bloco opcional com dados do relatório.
- `dados`: array obrigatório com as linhas do CSV.

## Campos do `header`

- `titulo_empresa`: nome da empresa.
- `periodo_relatorio`: período do relatório.
- `id`: identificador do relatório.
- `titulo_relatorio`: título exibido no relatório.