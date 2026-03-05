# 📋 Gerenciamento de Convênios

## 🎯 O que foi implementado

### 1. **Interface de Gerenciamento de Convênios**
Nova tela acessível em: **Menu > Convênios**

Funcionalidades disponíveis:
- ✅ **Listar** todos os convênios cadastrados
- ✅ **Criar** novos convênios manualmente
- ✅ **Editar** convênios existentes
- ✅ **Ativar/Desativar** convênios
- ✅ **Excluir** convênios (se não tiverem pedidos vinculados)
- ✅ **Importar** convênios da tabela `tipo_g` (MySQL)

### 2. **Importação Automática da Tabela tipo_g**

O sistema pode importar convênios diretamente da tabela `tipo_g` do banco MySQL, com o seguinte filtro:
- ✅ Importa apenas registros onde `tipo_guia = 'G'`
- ❌ Ignora registros onde `tipo_guia = 'P'`

---

## 🚀 Como Usar

### Acessar a Tela de Convênios

1. Faça login como **Admin** ou **Gestor**
2. No menu lateral, clique em **"Convênios"**
3. Você verá a lista de todos os convênios cadastrados

### Criar Novo Convênio Manualmente

1. Clique no botão **"Novo Convênio"**
2. Preencha os campos:
   - **Nome**: Nome do convênio (ex: "Unimed")
   - **Código**: Código único (ex: "UNIMED")
   - **Observações**: Informações adicionais (opcional)
   - **Ativo**: Marque para ativar o convênio
3. Clique em **"Salvar"**

### Editar Convênio

1. Na lista de convênios, clique em **"Editar"** ao lado do convênio desejado
2. Modifique os campos necessários
3. Clique em **"Salvar"**

### Ativar/Desativar Convênio

- Na lista, clique em **"Desativar"** para desativar um convênio ativo
- Clique em **"Ativar"** para reativar um convênio desativado
- **Convênios inativos não aparecem** nas listas de seleção de pedidos

### Excluir Convênio

1. Na lista, clique em **"Excluir"** ao lado do convênio
2. Confirme a exclusão
3. ⚠️ **Não é possível excluir** convênios que têm pedidos associados

### Importar Convênios da Tabela tipo_g

1. Clique no botão **"Importar da Tabela tipo_g"**
2. Confirme a importação
3. O sistema irá:
   - Conectar ao banco MySQL
   - Buscar registros onde `tipo_guia = 'G'`
   - Criar ou atualizar convênios automaticamente
   - Exibir resumo da importação

**Exemplo de resultado:**
```
✅ Importação concluída! 15 novos convênios, 3 atualizados.
```

---

## 📊 Estrutura da Tabela tipo_g

Para que a importação funcione corretamente, a tabela `tipo_g` deve ter:

### Estrutura Esperada:
```sql
CREATE TABLE tipo_g (
    id INT PRIMARY KEY,
    nome VARCHAR(255),        -- Nome do convênio
    codigo VARCHAR(50),       -- Código do convênio
    descricao VARCHAR(500),   -- Descrição
    tipo_guia CHAR(1)         -- 'G' = Guia, 'P' = Outro tipo
);
```

### Campos Utilizados na Importação:

O controller tenta buscar os dados nesta ordem:
1. **Nome**: `descricao` → `nome` → `tipo_guia`
2. **Código**: `codigo` → `id` → UPPERCASE(nome)

### Exemplo de Dados:
```sql
INSERT INTO tipo_g (id, nome, codigo, descricao, tipo_guia) VALUES
(1, 'UNIMED', 'UNI001', 'Unimed São Paulo', 'G'),
(2, 'BRADESCO', 'BRA001', 'Bradesco Saúde', 'G'),
(3, 'PARTICULAR', 'PAR001', 'Atendimento Particular', 'P'); -- Este NÃO será importado
```

**Resultado da importação:**
- ✅ Unimed (tipo_guia = 'G')
- ✅ Bradesco (tipo_guia = 'G')
- ❌ Particular (tipo_guia = 'P') - **Não importado**

---

## 🔧 Configuração do Banco MySQL

Se a tabela `tipo_g` estiver em um banco diferente, configure em:

**Arquivo**: `config/database.php`

```php
'connections' => [
    'mysql' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'database' => env('DB_DATABASE', 'nome_do_banco'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        // ... outras configurações
    ],
]
```

**Arquivo**: `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco_com_tipo_g
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

---

## 🔍 Verificar Dados Antes de Importar

Antes de fazer a importação, você pode verificar quais convênios serão importados:

```bash
php artisan tinker
```

```php
// Ver todos os registros da tabela tipo_g
DB::connection('mysql')->table('tipo_g')->get();

// Ver apenas os que serão importados (tipo_guia = 'G')
DB::connection('mysql')->table('tipo_g')->where('tipo_guia', 'G')->get();

// Contar quantos serão importados
DB::connection('mysql')->table('tipo_g')->where('tipo_guia', 'G')->count();
```

---

## ⚠️ Troubleshooting

### Erro: "Base table or view not found: 1146 Table 'tipo_g' doesn't exist"

**Causa**: A tabela `tipo_g` não existe no banco configurado.

**Soluções**:
1. Verifique se o banco de dados está correto no `.env`
2. Confirme que a tabela existe: `SHOW TABLES LIKE 'tipo_g';`
3. Se a tabela tiver outro nome, atualize o controller

### Erro: "Nenhum convênio encontrado na tabela tipo_g com tipo_guia = 'G'"

**Causa**: Não há registros com `tipo_guia = 'G'` na tabela.

**Soluções**:
1. Verifique os dados: `SELECT * FROM tipo_g WHERE tipo_guia = 'G';`
2. Confirme se a coluna se chama `tipo_guia` (pode ser diferente)
3. Verifique se os valores são 'G' maiúsculo

### Erro: "Erro na importação: SQLSTATE[HY000]"

**Causa**: Problema de conexão com o banco MySQL.

**Soluções**:
1. Verifique as credenciais no `.env`
2. Teste a conexão: `php artisan tinker` → `DB::connection('mysql')->getPdo();`
3. Verifique se o servidor MySQL está rodando

### Convênios duplicados sendo criados

**Causa**: O campo `codigo` não é único ou varia entre importações.

**Solução**: O sistema usa `updateOrCreate` com o campo `codigo` como chave. Garanta que códigos sejam únicos na tabela `tipo_g`.

---

## 📝 Arquivos Criados/Modificados

### Novos Arquivos:
1. ✅ `app/Http/Controllers/ConvenioController.php` - Controller de convênios
2. ✅ `resources/views/convenios/index.blade.php` - Interface de gerenciamento
3. ✅ `CONVENIOS.md` - Esta documentação

### Arquivos Modificados:
1. ✅ `routes/web.php` - Rotas de convênios adicionadas
2. ✅ `resources/views/layouts/main.blade.php` - Menu atualizado

---

## 🎨 Capturas de Tela (Descrição)

### Tela Principal de Convênios:
- Lista de convênios com colunas: Nome, Código, Pedidos, Status
- Botões: "Importar da Tabela tipo_g" e "Novo Convênio"
- Ações por linha: Editar, Ativar/Desativar, Excluir

### Modal de Criar/Editar:
- Campos: Nome, Código, Observações, Ativo
- Botões: Salvar, Cancelar

---

## 🔗 Integração com Importação de Pedidos

Os convênios são **automaticamente vinculados** aos pedidos durante a importação:

1. O sistema lê o campo `cod_guia` do pedido
2. Busca um convênio com esse código
3. Se não existir, **cria automaticamente**
4. Associa o pedido ao convênio

**Ver documentação completa**: [MELHORIAS_IMPORTACAO.md](MELHORIAS_IMPORTACAO.md)

---

## ✅ Próximos Passos

1. Acesse a tela de convênios: `/convenios`
2. Se tiver a tabela `tipo_g`, clique em "Importar da Tabela tipo_g"
3. Revise os convênios importados
4. Ajuste nomes/códigos se necessário
5. Execute importação de pedidos para vincular convênios

---

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique os logs: `storage/logs/laravel.log`
2. Consulte esta documentação
3. Veja também: `MELHORIAS_IMPORTACAO.md`
