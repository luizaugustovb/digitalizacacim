# Sistema de Digitalização de Guias Médicas - CIM

Sistema web moderno, robusto e seguro para escaneamento e conferência de guias médicas.

## 🚀 Status Atual

✅ **MVP Funcional - Pronto para Teste e Próximas Etapas**

### O que está pronto:
- ✅ Estrutura completa Laravel 11
- ✅ Banco de dados MySQL com 18 tabelas
- ✅ Sistema de autenticação completo
- ✅ RBAC com 3 níveis (ATENDENTE, GESTOR, ADMIN)
- ✅ 19 permissões granulares
- ✅ Layout responsivo com Tailwind CSS
- ✅ Tema claro/escuro com persistência
- ✅ Dashboard funcional
- ✅ Models com relacionamentos
- ✅ Middlewares de segurança
- ✅ Seeders com dados iniciais

## 🔐 Acesso ao Sistema

**URL:** http://127.0.0.1:8000

**Credenciais Admin:**
- Email: `contato@luizaugusto.me`
- Senha: `123`
- ⚠️ Sistema forçará troca de senha no primeiro login

## 📦 Instalação Rápida

```bash
# 1. Já está instalado em: c:\xampp\htdocs\digitalizacacim

# 2. Verificar se o banco existe
# Banco: digitalizacao_cim já foi criado

# 3. Servidor já está rodando
# URL: http://127.0.0.1:8000

# 4. Para reiniciar o servidor se necessário:
cd c:\xampp\htdocs\digitalizacacim
php artisan serve
```

## 🎨 Interface

### Dashboard
- 4 cards com estatísticas (Pendentes, Enviados, Aprovados, Devolvidos)
- Ações rápidas contextuais
- Sidebar recolhível
- Toggle de tema claro/escuro

### Navegação
- **Dashboard**: Visão geral e estatísticas
- **Pedidos**: Lista de pedidos (em desenvolvimento)
- **Conferência**: Para gestores (em desenvolvimento)
- **Usuários**: Apenas admin
- **Configurações**: Apenas admin

## 👥 Perfis e Permissões

### ATENDENTE
- Ver pendentes
- Ver enviados
- Escanear/anexar documentos
- Enviar pedidos

### GESTOR
- Todas do atendente +
- Ver aprovados
- Aprovar guias
- Devolver guias
- Ver relatórios
- Filtrar por lote

### ADMIN
- Acesso total
- Gerenciar usuários
- Gerenciar permissões
- Gerenciar configurações

## 📊 Estrutura do Banco

### Tabelas Implementadas (18):
1. users
2. roles
3. permissions
4. role_permissions
5. user_permissions
6. convenios
7. user_convenios
8. unidades
9. pedidos
10. documentos
11. pendencias_tipo
12. pedido_pendencias
13. timeline_logs
14. configuracoes
15. produtividade_regras
16. import_jobs
17. cache
18. jobs

### Dados Iniciais (Seeders):
- 1 usuário admin
- 19 permissões
- 8 tipos de pendências

## 🔄 Próximas Etapas

### Etapa 1: Lista de Pedidos
- [ ] Controller com filtros
- [ ] View com abas (Todos, Pendentes, Enviados, etc.)
- [ ] Paginação e busca
- [ ] Indicadores visuais de pendências

### Etapa 2: Escaneamento
- [ ] Interface de upload
- [ ] Padrão de nomenclatura de arquivos
- [ ] Storage por data (YYYY/MM/DD)
- [ ] Validação de arquivos

### Etapa 3: Pendências
- [ ] CRUD de tipos
- [ ] Devolver com seleção de pendências
- [ ] Reenvio após correção
- [ ] Ícones OK/Falha por campo

### Etapa 4: Conferência
- [ ] PDF viewer lado a lado
- [ ] Controles de aprovação/devolução
- [ ] Filtros por convênio do gestor

### Etapa 5: Importador
- [ ] Command para importar SQL Server
- [ ] Job agendável
- [ ] Logs de importação

## 🛠️ Tecnologias

- **Backend**: Laravel 11
- **Frontend**: Blade + Tailwind CSS + Alpine.js
- **Banco**: MySQL 8.0
- **Build**: Vite
- **Ambiente**: XAMPP

## 📝 Comandos Úteis

```bash
# Servidor
php artisan serve

# Migrations
php artisan migrate
php artisan migrate:fresh --seed

# Cache
php artisan config:clear
php artisan cache:clear

# Assets
npm run build
npm run dev

# Criar recursos
php artisan make:controller NomeController
php artisan make:model Nome
php artisan make:migration nome_migration
```

## 🎯 Arquitetura

### Models Criados:
- User (com RBAC)
- Role
- Permission
- Convenio
- Unidade
- Pedido
- Documento
- PendenciaTipo
- PedidoPendencia
- TimelineLog
- Configuracao

### Controllers Criados:
- AuthController (Login/Logout)
- DashboardController
- PedidoController (estrutura)
- DocumentoController (estrutura)
- ConferenciaController (estrutura)
- UserController (estrutura)
- ConfiguracaoController (estrutura)

### Middlewares:
- CheckRole (verifica papel do usuário)
- CheckPermission (verifica permissão específica)

## 🔒 Segurança

- ✅ Senhas com bcrypt
- ✅ CSRF protection
- ✅ Soft deletes com auditoria
- ✅ Logs de todas ações
- ✅ Middleware de autenticação
- ✅ Middleware de autorização

## 📞 Suporte

Sistema desenvolvido seguindo as melhores práticas Laravel e com foco em:
- Código limpo e organizado
- Segurança e auditoria
- Escalabilidade
- Manutenibilidade

---

**Desenvolvido por LAVB Tecnologias**

Para continuar o desenvolvimento, digite: **"vamos para a próxima etapa"**
