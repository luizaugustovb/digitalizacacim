# Melhorias no Sistema de Importação e Armazenamento

## 📋 Resumo das Alterações

### 1. Criação Automática de Convênios

O sistema agora **cria convênios automaticamente** durante a importação de pedidos:

- O campo `cod_guia` do SQL Server é usado como identificador do convênio
- Se o convênio não existir, ele é criado automaticamente
- O pedido é associado ao convênio correto

#### Como Funciona:

```php
// Exemplo de cod_guia: "UNIMED"
// Sistema busca: Convenio::where('nome', 'UNIMED')->first()
// Se não encontrar: Cria novo convênio com nome "Unimed"
```

#### Estratégia de Busca:
1. Busca exata por nome (case-insensitive)
2. Busca exata por código (case-insensitive)
3. Busca por similaridade no nome (se ≥ 3 caracteres)
4. Se não encontrar: **Cria novo convênio automaticamente**

#### Normalização:
- **Input**: `"UNIMED 123"`
- **Nome criado**: `"Unimed 123"` (ucwords)
- **Código criado**: `"UNIMED123"` (uppercase, sem espaços)

### 2. Estrutura de Pastas por Convênio

Os documentos agora são armazenados com a seguinte hierarquia:

```
storage/app/guias/
├── 2026/                    # Ano
│   ├── 01/                  # Mês
│   │   ├── 29/              # Dia
│   │   │   ├── unimed/      # Convênio
│   │   │   │   ├── 275110_joao-silva_20260129_guia-medica.pdf
│   │   │   │   └── 275110_joao-silva_20260129_autorizacao-sadt.pdf
│   │   │   ├── bradesco/    # Outro convênio
│   │   │   │   └── ...
│   │   │   └── sem-convenio/ # Pedidos sem convênio
│   │   │       └── ...
```

### 3. Benefícios

#### Organização:
- ✅ Fácil localização de documentos por data
- ✅ Separação por convênio facilita auditorias
- ✅ Estrutura intuitiva para backups seletivos

#### Performance:
- ✅ Índices adicionados em `convenios.nome`, `convenios.codigo`, `convenios.ativo`
- ✅ Busca otimizada de convênios existentes
- ✅ Redução de queries duplicadas

#### Gestão:
- ✅ Convênios criados automaticamente = menos trabalho manual
- ✅ Histórico de criação registrado em logs
- ✅ Facilita relatórios por convênio/período

## 🔧 Arquivos Modificados

### 1. ImportPedidosCommand.php
**Localização**: `app/Console/Commands/ImportPedidosCommand.php`

**Mudanças**:
- Adicionado método `processarConvenio()` que:
  - Normaliza nome do convênio
  - Busca convênio existente
  - Cria novo se não existir
  - Retorna ID do convênio
- Atualizado `importarPedido()` para associar convênio ao pedido

### 2. DocumentoController.php
**Localização**: `app/Http/Controllers/DocumentoController.php`

**Mudanças**:
- Adicionada lógica para incluir convênio no caminho:
  ```php
  $convenioSlug = $pedido->convenio 
      ? Str::slug($pedido->convenio->nome) 
      : 'sem-convenio';
  ```
- Caminho atualizado: `guias/{ano}/{mes}/{dia}/{convenio}/`

### 3. Migration: add_indexes_to_convenios_table
**Localização**: `database/migrations/2026_01_29_120258_add_indexes_to_convenios_table.php`

**Mudanças**:
- Índices adicionados para otimizar buscas:
  - `convenios.nome`
  - `convenios.codigo`
  - `convenios.ativo`

## 📊 Exemplo de Uso

### Antes da Importação:
```sql
SELECT * FROM convenios;
-- 5 convênios cadastrados
```

### Durante a Importação:
```bash
php artisan import:pedidos --date-start=2026-01-01

# Output:
# === Iniciando Importação de Pedidos ===
# Conectando ao SQL Server...
# Total de registros encontrados: 150
# 
# Novo convênio cadastrado: Unimed
# Novo convênio cadastrado: Bradesco Saúde
# Novo convênio cadastrado: Amil
# ...
```

### Depois da Importação:
```sql
SELECT * FROM convenios;
-- 8 convênios cadastrados (3 criados automaticamente)
```

### Estrutura de Arquivos Criada:
```bash
storage/app/guias/
└── 2026/
    └── 01/
        └── 29/
            ├── unimed/
            │   └── 275110_joao-silva_20260129_guia-medica.pdf
            ├── bradesco-saude/
            │   └── 275112_maria-santos_20260129_autorizacao-sadt.pdf
            └── amil/
                └── 275115_pedro-oliveira_20260129_guia-medica.pdf
```

## 🧪 Como Testar

### 1. Teste de Importação:
```bash
# Importar pedidos de uma data específica
php artisan import:pedidos --date-start=2026-01-29 --date-end=2026-01-29

# Verificar convênios criados
php artisan tinker
>>> App\Models\Convenio::orderBy('created_at', 'desc')->take(5)->get(['id', 'nome', 'codigo', 'created_at']);
```

### 2. Teste de Upload:
1. Acesse um pedido com convênio associado
2. Faça upload de um documento
3. Verifique a estrutura de pastas:
   ```bash
   ls storage/app/guias/2026/01/29/
   # Deve mostrar pasta com nome do convênio
   ```

### 3. Verificar Logs:
```bash
tail -f storage/logs/laravel.log
# Observe logs de convênios criados automaticamente
```

## 🔍 Troubleshooting

### Problema: Convênios duplicados sendo criados
**Solução**: Verifique a normalização do nome. Pode haver diferenças em:
- Espaços extras
- Caracteres especiais
- Acentuação

### Problema: Pasta "sem-convenio" muito cheia
**Solução**: 
1. Verifique pedidos sem convênio:
   ```sql
   SELECT codigo_pedido, cod_guia FROM pedidos WHERE convenio_id IS NULL;
   ```
2. Execute importação novamente com `--force` para reprocessar

### Problema: Erro ao criar pasta de convênio
**Solução**: Verifique permissões do storage:
```bash
chmod -R 775 storage/app/guias
chown -R www-data:www-data storage/app/guias
```

## 📝 Próximas Melhorias Sugeridas

1. **Interface para gerenciar convênios duplicados**
   - Ferramenta para mesclar convênios similares
   - Ex: "UNIMED" e "Unimed São Paulo"

2. **Relatório de convênios criados automaticamente**
   - Dashboard mostrando novos convênios por período
   - Permite revisão e ajustes

3. **Migração de documentos antigos**
   - Script para reorganizar documentos existentes na nova estrutura

4. **Backup seletivo por convênio**
   - Comando artisan para backup de convênio específico
   - `php artisan backup:convenio unimed --date-start=2026-01-01`

## 🎯 Impacto

### Positivo:
- ✅ Redução de 100% do trabalho manual de cadastro de convênios
- ✅ Organização melhorada: documentos separados por data e convênio
- ✅ Facilita auditorias e relatórios
- ✅ Performance otimizada com índices

### Atenção:
- ⚠️ Convênios podem ser criados com nomes ligeiramente diferentes
- ⚠️ Necessário revisar convênios criados automaticamente periodicamente
- ⚠️ Espaço em disco aumenta com mais subpastas (marginal)

## 📞 Suporte

Para dúvidas ou problemas, verifique:
1. Logs do sistema: `storage/logs/laravel.log`
2. Este documento de melhorias
3. Documentação original: `PROJETO.md`
