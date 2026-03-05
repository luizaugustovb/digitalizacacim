# CRUD de Usuários - Implementado ✅

## Resumo
Sistema completo de gerenciamento de usuários com autorização baseada em roles, filtros avançados e gestão de convênios.

## Arquivos Criados/Modificados

### Controllers
- **UserController.php** (206 linhas)
  - `index()`: Lista usuários com filtros e estatísticas
  - `create()`: Formulário de criação
  - `store()`: Salvar novo usuário com validações
  - `edit()`: Formulário de edição
  - `update()`: Atualizar usuário existente
  - `destroy()`: Soft delete com validações

### Policies
- **UserPolicy.php**: Autorização granular
  - `viewAny()`: Gestor e Admin podem listar
  - `view()`: Gestor e Admin podem visualizar
  - `create()`: Apenas Admin pode criar
  - `update()`: Apenas Admin pode editar
  - `delete()`: Apenas Admin pode excluir (não pode excluir a si mesmo)

### Views
- **usuarios/index.blade.php**: Lista com filtros e estatísticas
  - 6 cards de estatísticas: Total, Ativos, Inativos, Admins, Gestores, Atendentes
  - Filtros: busca (nome/email/CPF), role, status ativo
  - Avatar circular com iniciais coloridas por role
  - Badges de status: Ativo/Inativo, Forçar troca senha
  - Lista de convênios associados
  - Ações: Editar (Admin), Excluir (Admin)
  - Paginação 20 itens

- **usuarios/create.blade.php**: Formulário de criação
  - Seção Dados Pessoais: nome, email, CPF (máscara), telefone (máscara)
  - Seção Credenciais: senha, confirmar senha
  - Seção Perfil: role select, convênios multi-select (Alpine.js)
  - Seção Status: ativo checkbox, forçar_troca_senha checkbox
  - Validações client-side e server-side
  - Máscaras: CPF (999.999.999-99), Telefone ((99) 99999-9999)

- **usuarios/edit.blade.php**: Formulário de edição
  - Mesma estrutura do create
  - Senha opcional (deixar em branco mantém atual)
  - Valores pré-preenchidos
  - Convênios pré-selecionados

### Seeders
- **ConveniosUnidadesSeeder.php**: Dados de teste
  - 5 convênios: Unimed, Bradesco Saúde, Amil, SulAmérica, Particular
  - 3 unidades: Central, Norte, Sul

### Providers
- **AppServiceProvider.php**: Registra policies
  - UserPolicy
  - PedidoPolicy

## Funcionalidades

### Lista de Usuários (index)
1. **Estatísticas em Cards**
   - Total de usuários
   - Ativos (verde)
   - Inativos (vermelho)
   - Admins (roxo)
   - Gestores (azul)
   - Atendentes (laranja)

2. **Filtros**
   - Busca: nome, email ou CPF
   - Role: Todos / Admin / Gestor / Atendente
   - Status: Todos / Ativos / Inativos

3. **Tabela de Usuários**
   - Avatar circular colorido por role com iniciais
   - Nome, email, CPF (se cadastrado)
   - Badge de role colorido
   - Convênios associados (count + 2 primeiros nomes)
   - Status: Ativo/Inativo, Trocar senha
   - Ações: Editar ✏️, Excluir 🗑️ (com confirmação)

### Criação de Usuário (create/store)
1. **Validações**
   - Nome: obrigatório, max 255
   - Email: obrigatório, único, formato válido
   - CPF: opcional, único, formato 999.999.999-99
   - Telefone: opcional, formato (99) 99999-9999
   - Senha: obrigatória, min 3 chars, confirmação
   - Role: obrigatório (ATENDENTE/GESTOR/ADMIN)
   - Convênios: array de IDs válidos (exists)

2. **Lógica de Negócio**
   - Senha com bcrypt hash
   - Convênios apenas para Gestor/Atendente
   - Admin não tem convênios (acessa todos)
   - Ativo: true por padrão
   - Forçar troca senha: true por padrão no create

3. **UX com Alpine.js**
   - Seleção de convênios aparece apenas para Gestor/Atendente
   - Reativo ao mudar role

### Edição de Usuário (edit/update)
1. **Diferenças do Create**
   - Senha opcional (deixar em branco mantém atual)
   - Email único ignorando o próprio usuário
   - CPF único ignorando o próprio usuário
   - Valores pré-preenchidos
   - Convênios pré-selecionados

2. **Atualização de Convênios**
   - Sync convênios se Gestor/Atendente
   - Detach todos se virar Admin

### Exclusão de Usuário (destroy)
1. **Validações**
   - Não pode excluir a si mesmo
   - Não pode excluir se tiver pedidos como atendente
   - Não pode excluir se tiver pedidos como gestor
   - Soft delete (deleted_at)

2. **Mensagens**
   - Sucesso: "Usuário excluído com sucesso!"
   - Erro próprio: "Você não pode excluir seu próprio usuário!"
   - Erro pedidos: "Não é possível excluir este usuário pois ele possui pedidos associados."

## Autorização

### Admin
- ✅ Ver lista de usuários
- ✅ Ver detalhes de usuário
- ✅ Criar novo usuário
- ✅ Editar qualquer usuário
- ✅ Excluir qualquer usuário (exceto si mesmo)

### Gestor
- ✅ Ver lista de usuários
- ✅ Ver detalhes de usuário
- ❌ Criar usuário
- ❌ Editar usuário
- ❌ Excluir usuário

### Atendente
- ❌ Sem acesso à gestão de usuários

## Rotas

```php
GET    /usuarios              → index    (lista)
GET    /usuarios/create       → create   (formulário criação)
POST   /usuarios              → store    (salvar novo)
GET    /usuarios/{id}/edit    → edit     (formulário edição)
PUT    /usuarios/{id}         → update   (atualizar)
DELETE /usuarios/{id}         → destroy  (excluir)
```

Middleware: `auth`, `role:ADMIN,GESTOR`

## Dados de Teste

### Usuários
- **Admin**: contato@luizaugusto.me / 123
- **Gestor**: gestor@teste.com / 123

### Convênios
- Unimed (UNMD)
- Bradesco Saúde (BRAD)
- Amil (AMIL)
- SulAmérica (SULA)
- Particular (PART)

### Unidades
- Unidade Central (UC01)
- Unidade Norte (UN01)
- Unidade Sul (US01)

## Testes Recomendados

1. **Login como Admin**
   - Acessar menu Usuários
   - Ver estatísticas e lista
   - Criar novo atendente com 2 convênios
   - Editar atendente, adicionar mais convênios
   - Criar novo gestor com todos convênios
   - Tentar excluir gestor (deve mostrar erro se tiver pedidos)
   - Criar admin sem convênios
   - Tentar excluir a si mesmo (deve mostrar erro)

2. **Login como Gestor**
   - Acessar menu Usuários (deve ter acesso)
   - Ver lista de usuários
   - Botão "Novo Usuário" não aparece
   - Ações de editar/excluir não aparecem
   - Tentar acessar /usuarios/create diretamente (deve dar 403)

3. **Filtros**
   - Buscar por nome parcial
   - Buscar por email parcial
   - Buscar por CPF completo
   - Filtrar por role
   - Filtrar por status ativo/inativo
   - Combinar filtros

4. **Validações**
   - Email duplicado (deve mostrar erro)
   - CPF duplicado (deve mostrar erro)
   - Senha sem confirmação (deve mostrar erro)
   - Senha curta <3 chars (deve mostrar erro)
   - Role vazio (deve mostrar erro)

## Melhorias Futuras (Opcional)
- [ ] Exportar lista para Excel
- [ ] Importar usuários via CSV
- [ ] Foto de perfil com upload
- [ ] Histórico de alterações (audit log)
- [ ] Desativar em massa
- [ ] Resetar senha e enviar por email
- [ ] 2FA (autenticação de dois fatores)

## Próximos Passos
- ✅ **Etapa 10 concluída**: CRUD de Usuários
- ⏭️ **Etapa 11**: Sistema de Configurações
- ⏭️ **Etapa 12**: Importador SQL Server
