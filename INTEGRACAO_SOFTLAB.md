# Integração com Softlab

Este documento descreve como configurar a integração entre o sistema de Digitalização CIM e o banco de dados do Softlab.

## Configuração

### 1. Variáveis de Ambiente

Edite o arquivo `.env` e configure as credenciais do banco Softlab:

```env
SOFTLAB_DB_HOST=127.0.0.1
SOFTLAB_DB_PORT=3306
SOFTLAB_DB_DATABASE=BD_SOFTLAB_P00
SOFTLAB_DB_USERNAME=root
SOFTLAB_DB_PASSWORD=sua_senha_aqui
```

### 2. Criar Mapeamentos

Acesse o sistema como **ADMIN** e vá em:
```
Configurações > Integração > Gerenciar Mapeamentos Softlab
```

#### 2.1 Mapeamento de Unidades

Vincule os códigos do Softlab às unidades do sistema:

| Código Softlab | Nome Referência | Unidade Sistema |
|----------------|-----------------|-----------------|
| 3              | Matriz          | Unidade Matriz  |
| 01             | Posto 1         | Unidade 01      |
| 02             | Posto 2         | Unidade 02      |

**Campos do Softlab usados:**
- `posto_cliente` - Código do posto onde foi atendido
- `cod_origem` - Código da origem (3 = Matriz, outras unidades)

#### 2.2 Mapeamento de Usuários

Vincule os códigos de usuários do Softlab aos atendentes do sistema:

| Código Softlab | Nome Referência | Usuário Sistema |
|----------------|-----------------|-----------------|
| MARIA          | Maria Silva     | Maria (ATENDENTE) |
| JOAO           | João Santos     | João (ATENDENTE)  |

**Campo do Softlab usado:**
- `usu_pedido` - Código do usuário que criou o pedido

### 3. Testar Conexão

Na página de Mapeamentos Softlab, clique no botão **"Testar Conexão"** para verificar se o sistema consegue se conectar ao banco do Softlab.

### 4. Buscar Pedidos do Dia

Clique no botão **"Buscar Pedidos do Dia"** para:
- Buscar todos os pedidos criados hoje no Softlab
- Visualizar os dados antes de importar
- Verificar se os mapeamentos estão corretos

## Estrutura do Banco Softlab

### Tabela: `pedido`
```sql
- cod_pedido (PK) - Código único do pedido
- cod_cliente - Código do paciente
- posto_cliente - Código do posto (para mapear unidade)
- usu_pedido - Código do usuário (para mapear atendente)
- cod_guia - Número da guia
- datahora_atendimento - Data e hora do atendimento
- cod_origem - Código da origem (3=Matriz, outras)
```

### Tabela: `cliente`
```sql
- cod_cliente (PK) - Código do paciente
- nome_cliente - Nome completo do paciente
```

## Query Executada

O sistema executa a seguinte query para buscar pedidos do dia:

```sql
SELECT 
    p.cod_pedido,
    p.cod_cliente,
    p.posto_cliente,
    p.usu_pedido,
    p.cod_guia,
    p.datahora_atendimento,
    p.cod_origem,
    c.nome_cliente
FROM pedido p
JOIN cliente c ON p.cod_cliente = c.cod_cliente
WHERE DATE(p.datahora_atendimento) = CURDATE()
ORDER BY p.datahora_atendimento DESC
```

**Nota:** A query usa `CURDATE()` para buscar apenas pedidos do dia atual, evitando sobrecarga.

## Importação de Pedidos

### Fluxo de Importação

1. Sistema busca pedidos do Softlab do dia atual
2. Para cada pedido:
   - Busca o mapeamento de unidade (usando `posto_cliente` ou `cod_origem`)
   - Busca o mapeamento de usuário (usando `usu_pedido`)
   - Cria um novo pedido no sistema interno
3. Pedidos importados ficam com status **PENDENTE**

### Campos Mapeados

| Campo Softlab | Campo Sistema | Observação |
|---------------|---------------|------------|
| cod_pedido | numero_pedido | Número único |
| cod_guia | numero_guia | Número da guia |
| datahora_atendimento | data_envio | Data de criação |
| posto_cliente → mapeamento | unidade_id | Via tabela softlab_mappings |
| usu_pedido → mapeamento | atendente_id | Via tabela softlab_mappings |
| nome_cliente | - | Para referência visual |

## Solução de Problemas

### Erro: "Não foi possível conectar ao banco Softlab"
- Verifique se o banco Softlab está rodando
- Confirme as credenciais no `.env`
- Verifique se o firewall permite a conexão

### Erro: "Unidade não mapeada"
- Crie mapeamento para o código de posto/origem
- Verifique se o código está correto (com zeros à esquerda?)

### Erro: "Usuário não mapeado"
- Crie mapeamento para o código de usuário
- Verifique se o código está em maiúsculas/minúsculas corretas

### Pedidos não aparecem
- Confirme que existem pedidos criados hoje no Softlab
- Verifique a query no banco Softlab diretamente:
```sql
SELECT COUNT(*) FROM pedido WHERE DATE(datahora_atendimento) = CURDATE();
```

## Segurança

- O sistema tem acesso **somente leitura** ao banco Softlab
- Apenas administradores podem configurar mapeamentos
- As credenciais ficam no `.env` (nunca versione este arquivo)
- A conexão usa a porta padrão MySQL (3306)

## Próximos Passos (Futuro)

- [ ] Importação automática via Cron/Schedule
- [ ] Sincronização bidirecional (atualizar status no Softlab)
- [ ] Mapeamento de convênios
- [ ] Validação de pedidos duplicados
- [ ] Log detalhado de importações
- [ ] Dashboard de estatísticas de importação
