# 📋 Como Configurar a Integração Softlab

## Passo a Passo

### 1️⃣ Acessar Configurações
1. Faça login como **ADMIN**
2. Navegue até **Configurações** no menu lateral
3. Clique na aba **"Integração"**

### 2️⃣ Preencher Dados do Banco Softlab
Preencha os campos com as informações do seu banco de dados Softlab:

```
Host: 127.0.0.1 (ou IP do servidor)
Porta: 3306
Database: BD_SOFTLAB_P00
Usuário: root (ou seu usuário)
Senha: (sua senha, deixe em branco se não tiver)
```

### 3️⃣ Salvar Configurações
- Clique no botão **"Salvar Configurações"** no final da página
- Os dados serão salvos automaticamente no arquivo `.env`
- Aguarde a mensagem de sucesso

### 4️⃣ Testar Conexão
1. Clique em **"Gerenciar Mapeamentos Softlab"**
2. Na nova página, clique em **"Testar Conexão"**
3. Você verá uma mensagem de sucesso se a conexão funcionar

### 5️⃣ Criar Mapeamentos

#### Mapeamento de Unidades
Os códigos do Softlab precisam ser mapeados para as unidades do sistema:

1. Clique em **"Adicionar Mapeamento"**
2. Selecione **Tipo: Unidade**
3. Preencha:
   - **Código Softlab**: `3` (exemplo: código da matriz no Softlab)
   - **Nome Referência**: `Matriz` (opcional, para sua referência)
   - **Unidade Sistema**: Selecione a unidade correspondente
4. Clique em **"Salvar"**

**Exemplos de códigos que podem vir do Softlab:**
- `posto_cliente` (ex: 01, 02, 03)
- `cod_origem` (ex: 3 para matriz)

#### Mapeamento de Usuários
Os códigos de usuários do Softlab precisam ser mapeados para os atendentes:

1. Clique em **"Adicionar Mapeamento"**
2. Selecione **Tipo: Usuário**
3. Preencha:
   - **Código Softlab**: `MARIA` (exemplo: username no Softlab)
   - **Nome Referência**: `Maria Silva` (opcional)
   - **Usuário Sistema**: Selecione o usuário correspondente
4. Clique em **"Salvar"**

### 6️⃣ Buscar Pedidos do Dia
1. Clique em **"Buscar Pedidos do Dia"**
2. O sistema buscará todos os pedidos do dia atual no Softlab
3. Uma tabela aparecerá com os resultados:
   - Código do Pedido
   - Nome do Paciente
   - Posto
   - Origem
   - Usuário
   - Data/Hora

### 7️⃣ Visualizar Mapeamentos
Na página de mapeamentos você verá duas tabelas:

- **Mapeamento de Unidades**: Lista todos os códigos mapeados para unidades
- **Mapeamento de Usuários**: Lista todos os códigos mapeados para usuários

Você pode **excluir** mapeamentos clicando no botão "Excluir" ao lado de cada item.

## 🔍 Dados Buscados do Softlab

O sistema busca os seguintes campos da tabela `pedido`:
- `cod_pedido` - Código do pedido
- `cod_cliente` - Código do cliente
- `posto_cliente` - Posto do cliente
- `usu_pedido` - Usuário que criou o pedido
- `cod_guia` - Código da guia
- `datahora_atendimento` - Data e hora do atendimento
- `cod_origem` - Código da origem

E faz JOIN com a tabela `cliente` para buscar:
- `nome_cliente` - Nome do cliente/paciente

## ⚠️ Importante

- **Apenas pedidos do dia atual são buscados** para não sobrecarregar o sistema
- **Crie os mapeamentos antes** de buscar pedidos, assim o sistema saberá vincular os códigos
- Se um código do Softlab não tiver mapeamento, o pedido será listado mas não poderá ser importado

## 🚀 Próximos Passos

Em versões futuras, você poderá:
- Importar pedidos selecionados diretamente para o sistema
- Configurar importação automática via Cron
- Sincronizar status bidirecional com o Softlab

## ❓ Problemas Comuns

### "Erro ao conectar com Softlab"
- Verifique se o servidor do banco está ligado
- Confirme host, porta, database, usuário e senha
- Teste a conexão usando HeidiSQL ou MySQL Workbench primeiro

### "Nenhum pedido encontrado"
- Pode não haver pedidos criados hoje no Softlab
- Verifique se a tabela `pedido` tem registros com data de hoje
- Confirme que o campo `datahora_atendimento` está preenchido

### "Configurações não salvam"
- Verifique se você é ADMIN
- Confirme que está clicando no botão "Salvar Configurações" no final da página
- Verifique se o arquivo `.env` tem permissão de escrita
