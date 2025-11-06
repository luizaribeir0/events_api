# Como Testar a API de Eventos

## 🔐 Autenticação

**Importante:** Todas as rotas da API requerem autenticação via token Bearer.

### Como usar a autenticação:

A API aceita **ambos os formatos** de autenticação:

1. **Formato com Bearer (recomendado):**
   ```
   Authorization: Bearer 12345
   ```

2. **Formato apenas com o token:**
   ```
   Authorization: 12345
   ```

**Token de exemplo para testes:** `12345`

**Nota:** Ambos os formatos funcionam. O middleware aceita automaticamente:
- `Authorization: Bearer 12345`
- `Authorization: Bearer12345` (sem espaço)
- `Authorization: 12345`

### O que acontece sem autenticação?

Se você tentar fazer uma requisição sem o token ou com um token inválido, receberá um erro **401 (Unauthorized)** com a seguinte resposta:

```json
{
  "success": false,
  "message": "Token de autenticação inválido ou não fornecido.",
  "data": null
}
```

### Como testar autenticação no Swagger UI:

1. Acesse `http://localhost:8000/api/documentation`
2. Clique no botão **"Authorize"** (🔒) no topo da página
3. No campo de autenticação, digite: `12345`
4. Clique em **"Authorize"** e depois em **"Close"**
5. Agora todas as requisições serão feitas automaticamente com o token

## 1. Usando Swagger UI (Interface Gráfica)

Acesse: `http://localhost:8000/api/documentation`

### Primeiro passo: Autenticar

1. Clique no botão **"Authorize"** (🔒) no topo da página
2. No campo **"Value"**, digite: `12345`
3. Clique em **"Authorize"** e depois em **"Close"**

### Segundo passo: Testar endpoints

1. Expanda o endpoint `POST /api/eventos`
2. Clique em "Try it out"
3. Cole o JSON abaixo no campo "Request body"
4. Clique em "Execute"

**Nota:** Com a autenticação configurada, o Swagger UI adiciona automaticamente o header `Authorization: Bearer 12345` em todas as requisições.

### Exemplo de JSON para criar evento:

```json
{
  "descricao": "Workshop de Laravel API",
  "data_inicio": "2024-12-15 10:00:00",
  "data_final": "2024-12-15 18:00:00",
  "cancelado": false
}
```

## 2. Usando cURL (Terminal)

### Criar um evento:

```bash
curl -X POST http://localhost:8000/api/eventos \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 12345" \
  -d '{
    "descricao": "Workshop de Laravel API",
    "data_inicio": "2024-12-15 10:00:00",
    "data_final": "2024-12-15 18:00:00",
    "cancelado": false
  }'
```

### Listar todos os eventos:

```bash
curl -X GET http://localhost:8000/api/eventos \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 12345"
```

### Consultar um evento por ID:

```bash
curl -X GET http://localhost:8000/api/eventos/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 12345"
```

### Atualizar um evento:

```bash
curl -X PUT http://localhost:8000/api/eventos/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 12345" \
  -d '{
    "descricao": "Workshop de Laravel API - Atualizado",
    "data_inicio": "2024-12-15 10:00:00",
    "data_final": "2024-12-15 18:00:00",
    "cancelado": false
  }'
```

### Remover um evento:

**Formato com Bearer (recomendado):**
```bash
curl -X DELETE http://localhost:8000/api/eventos/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 12345"
```

**Formato apenas com token (também funciona):**
```bash
curl -X DELETE http://localhost:8000/api/eventos/1 \
  -H "Accept: application/json" \
  -H "Authorization: 12345"
```

## 3. Usando Postman ou Insomnia

### Criar requisição POST:

- **URL**: `http://localhost:8000/api/eventos`
- **Method**: `POST`
- **Headers**:
  - `Content-Type: application/json`
  - `Accept: application/json`
  - `Authorization: Bearer 12345` (ou apenas `Authorization: 12345`)
- **Body** (raw JSON):

**Nota:** Você pode usar tanto `Authorization: Bearer 12345` quanto `Authorization: 12345` - ambos funcionam!
```json
{
  "descricao": "Workshop de Laravel API",
  "data_inicio": "2024-12-15 10:00:00",
  "data_final": "2024-12-15 18:00:00",
  "cancelado": false
}
```

## 4. Validação de Erros

### Teste com dados inválidos (deve retornar erro 422):

```bash
curl -X POST http://localhost:8000/api/eventos \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 12345" \
  -d '{
    "descricao": "",
    "data_inicio": "2024-12-15 10:00:00",
    "data_final": "2024-12-14 18:00:00"
  }'
```

### Teste consultando evento inexistente (deve retornar erro 404):

```bash
curl -X GET http://localhost:8000/api/eventos/999 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 12345"
```

## Respostas Esperadas

### Sucesso (201 - Criado):
```json
{
  "success": true,
  "message": "Evento criado com sucesso",
  "data": {
    "id": 1,
    "descricao": "Workshop de Laravel API",
    "data_inicio": "2024-12-15T10:00:00.000000Z",
    "data_final": "2024-12-15T18:00:00.000000Z",
    "cancelado": false,
    "created_at": "2025-11-05T23:44:41.000000Z",
    "updated_at": "2025-11-05T23:44:41.000000Z"
  }
}
```

### Erro de Validação (422):
```json
{
  "success": false,
  "message": "Erro de validação",
  "errors": {
    "descricao": ["O campo descricao é obrigatório."],
    "data_final": ["O campo data final deve ser uma data posterior a data inicio."]
  }
}
```

### Evento Não Encontrado (404):
```json
{
  "success": false,
  "message": "Evento não encontrado"
}
```

### Sucesso (200 - Removido):
```json
{
  "success": true,
  "message": "Evento removido com sucesso!",
  "data": null
}
```

### Erro de Autenticação (401):
```json
{
  "success": false,
  "message": "Token de autenticação inválido ou não fornecido.",
  "data": null
}
```

**Quando ocorre:** Quando você não envia o header `Authorization` ou envia um token inválido.

**Como testar:**
```bash
# Teste sem token (deve retornar 401)
curl -X GET http://localhost:8000/api/eventos \
  -H "Accept: application/json"

# Teste com token inválido (deve retornar 401)
curl -X GET http://localhost:8000/api/eventos \
  -H "Accept: application/json" \
  -H "Authorization: Bearer token_invalido"
```

