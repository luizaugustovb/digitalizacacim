# Etapa 11: Sistema de Configurações ⚙️

## ✅ Concluída em 27/01/2026

Sistema completo de gerenciamento de configurações com interface web, helper para acesso programático e cache.

---

## 📋 Arquitetura

### Controller
- **ConfiguracaoController** (218 linhas)
  - `index()`: Carrega todas as configurações organizadas por categoria
  - `update()`: Valida e salva configurações com validações específicas
  - Métodos privados: `getTipo()`, `getDescricao()`, `getCategoria()`

### Helper
- **ConfigHelper** (63 linhas)
  - `get($chave, $default)`: Obtém valor com cache (1 hora)
  - `set($chave, $valor, $tipo, $descricao, $categoria)`: Define configuração
  - `forget($chave)`: Remove configuração
  - `clearCache()`: Limpa cache de todas as configurações
  - Conversão automática de tipos (integer, boolean, array)

### View
- **configuracoes/index.blade.php** (5 abas)
  - Alpine.js para troca de tabs
  - Validações client-side e server-side
  - Help text explicativo para cada campo

---

## 🎯 Funcionalidades

### 1. Categorias de Configurações

#### **Geral**
- Nome do Sistema
- E-mail para Notificações
- Fuso Horário

#### **Documentos**
- Dias de Retenção (365-3650 dias, padrão 1825 = 5 anos)
- Tamanho Máximo de Arquivo (1-50 MB, padrão 10 MB)
- Formatos Permitidos (padrão: pdf,jpg,jpeg,png)
- Qualidade de Compressão (50-100%, padrão 85%)

#### **Produtividade**
- Meta Diária - Atendentes (padrão 50 pedidos)
- Meta Diária - Gestores (padrão 100 conferências)
- Alerta de Pedidos Antigos (1-30 dias, padrão 7 dias)

#### **Importação**
- Importação Automática (habilitada/desabilitada)
- Horário da Importação (padrão 02:00)
- Conexão SQL Server (host, database, username, password)

#### **Notificações**
- Notificar Pedido Devolvido (padrão: sim)
- Notificar Pedido Aprovado (padrão: não)
- Notificar Meta Atingida (padrão: sim)

---

## 🔐 Autorização

- **Acesso**: Restrito a Admins (`role:ADMIN`)
- Middleware: `auth` + `role:ADMIN`
- Menu visível apenas para Admins

---

## 📍 Rotas

| Método | URI              | Nome                     | Action              |
|--------|------------------|--------------------------|---------------------|
| GET    | /configuracoes   | configuracoes.index      | index()             |
| PUT    | /configuracoes   | configuracoes.update     | update()            |

---

## 🗄️ Banco de Dados

### Tabela: `configuracoes`

```sql
CREATE TABLE configuracoes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    chave VARCHAR(255) UNIQUE,
    valor TEXT,
    tipo VARCHAR(255) DEFAULT 'string',
    descricao TEXT,
    categoria VARCHAR(255) DEFAULT 'geral',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Campos:
- **chave**: Identificador único (ex: `dias_retencao_documentos`)
- **valor**: Valor armazenado como texto
- **tipo**: `string`, `integer`, `boolean`, `array`
- **descricao**: Texto explicativo
- **categoria**: `geral`, `documentos`, `produtividade`, `importacao`, `notificacoes`

---

## 📦 Seeder

**ConfiguracoesSeeder** cria 18 configurações padrão:

```php
php artisan db:seed --class=ConfiguracoesSeeder
```

---

## 💻 Uso do ConfigHelper

### Obter Configuração

```php
use App\Helpers\ConfigHelper;

// String
$nomeSistema = ConfigHelper::get('nome_sistema', 'Sistema CIM');

// Integer
$diasRetencao = ConfigHelper::get('dias_retencao_documentos', 1825);

// Boolean
$importacaoAutomatica = ConfigHelper::get('importacao_automatica', false);
```

### Definir Configuração

```php
ConfigHelper::set(
    'nova_config', 
    'valor', 
    'string', 
    'Descrição da configuração', 
    'geral'
);
```

### Limpar Cache

```php
ConfigHelper::clearCache(); // Limpa todas
ConfigHelper::forget('chave_especifica'); // Remove uma configuração
```

---

## 🎨 Interface

### 5 Abas com Alpine.js
- Navegação por tabs sem reload
- Formulário único com todas as configurações
- Validações em tempo real
- Help text abaixo de cada campo

### Campos Especiais
- **CPF**: Máscara automática (999.999.999-99)
- **Telefone**: Máscara automática ((99) 99999-9999)
- **Time**: Input type="time" HTML5
- **Checkbox**: Valores booleanos
- **Password SQL Server**: Opcional, mantém atual se vazio

---

## ✅ Validações

### Server-side (Laravel)
```php
'dias_retencao_documentos' => 'required|integer|min:365|max:3650'
'tamanho_maximo_arquivo' => 'required|integer|min:1|max:50'
'email_notificacoes' => 'nullable|email'
'importacao_automatica' => 'boolean'
```

### Client-side (Blade)
- Required para campos obrigatórios
- Min/max para ranges numéricos
- Help text explicativo

---

## 🚀 Testes Recomendados

### Como Admin:
1. **Acesso**: http://127.0.0.1:8000/configuracoes
2. **Testar todas as abas**: Geral, Documentos, Produtividade, Importação, Notificações
3. **Alterar valores**: Dias de retenção, metas, notificações
4. **Salvar**: Verificar mensagem de sucesso
5. **Cache**: Usar ConfigHelper::get() para verificar valor atualizado
6. **Validação**: Tentar salvar com valores inválidos (ex: dias < 365)

### Como Gestor/Atendente:
1. Verificar que menu "Configurações" **não aparece**
2. Tentar acessar /configuracoes diretamente
3. Deve retornar erro 403 ou redirecionar

---

## 📊 Configurações Padrão

| Chave                           | Valor Padrão                  | Tipo      |
|---------------------------------|-------------------------------|-----------|
| nome_sistema                    | Sistema de Digitalização CIM  | string    |
| email_notificacoes              | contato@luizaugusto.me        | string    |
| timezone                        | America/Sao_Paulo             | string    |
| dias_retencao_documentos        | 1825 (5 anos)                 | integer   |
| tamanho_maximo_arquivo          | 10 MB                         | integer   |
| formatos_permitidos             | pdf,jpg,jpeg,png              | string    |
| qualidade_compressao            | 85%                           | integer   |
| meta_diaria_atendente           | 50 pedidos                    | integer   |
| meta_diaria_gestor              | 100 conferências              | integer   |
| alerta_pedidos_antigos_dias     | 7 dias                        | integer   |
| importacao_automatica           | false                         | boolean   |
| horario_importacao              | 02:00                         | string    |
| sqlserver_host                  | (vazio)                       | string    |
| sqlserver_database              | (vazio)                       | string    |
| sqlserver_username              | (vazio)                       | string    |
| notificar_pedido_devolvido      | true                          | boolean   |
| notificar_pedido_aprovado       | false                         | boolean   |
| notificar_meta_atingida         | true                          | boolean   |

---

## 🔄 Próximos Passos

### Etapa 12: Importador SQL Server
- Criar `ImportPedidosCommand`
- Configurar conexão SQL Server
- Implementar lógica de importação
- Agendar execução diária
- Interface admin para histórico

---

## 📝 Notas Técnicas

### Cache
- **TTL**: 1 hora (3600 segundos)
- **Driver**: Cache padrão do Laravel (file/redis/memcached)
- **Chave**: `config.{chave}`
- Invalidado automaticamente ao salvar

### Segurança
- Password SQL Server não é retornado na view
- Placeholder "••••••••" indica senha atual
- Deixar vazio mantém senha anterior

### Performance
- Configurações são carregadas apenas na view de edição
- Cache reduz queries ao banco
- UpdateOrCreate evita duplicatas

---

## ✨ Melhorias Futuras

1. **Versionamento**: Histórico de alterações com timestamps
2. **Grupos de Permissões**: Permitir Gestores editarem algumas configs
3. **Validação Avançada**: Testar conexão SQL Server antes de salvar
4. **Export/Import**: Exportar configs como JSON
5. **Interface API**: Endpoint REST para configs
6. **Auditoria**: Log de quem alterou cada configuração
7. **Backup Automático**: Backup antes de alterar configurações críticas

---

**Sistema pronto para produção!** 🎉

Login Admin: contato@luizaugusto.me / 123
Acesso: http://127.0.0.1:8000/configuracoes
