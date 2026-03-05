# 📡 Instalação da API Intermediária Softlab (SQL Server)

## Por que precisamos da API?

O servidor Softlab (`10.1.8.7`) usa **SQL Server na porta 1433**, mas essa porta não está acessível externamente. A solução é criar uma **API HTTP intermediária** no próprio servidor Softlab que fará as queries localmente e retornará os dados via HTTP.

---

## 🎯 Passo a Passo de Instalação

### 1️⃣ No Servidor Softlab (10.1.8.7)

#### A. Instalar Drivers SQL Server para PHP

1. Baixe os drivers: https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server
2. Extraia os arquivos `.dll` para a pasta de extensões do PHP:
   ```
   C:\xampp\php\ext\
   ```
3. Os arquivos necessários são:
   - `php_sqlsrv_XX_ts.dll`
   - `php_pdo_sqlsrv_XX_ts.dll`
   
   (Onde XX é a versão do PHP, ex: 82 para PHP 8.2)

4. Edite o `php.ini` (`C:\xampp\php\php.ini`):
   ```ini
   extension=php_sqlsrv_82_ts.dll
   extension=php_pdo_sqlsrv_82_ts.dll
   ```

5. Reinicie o Apache:
   ```
   XAMPP Control Panel > Apache > Stop > Start
   ```

6. Verifique se foi instalado:
   ```bash
   php -m | findstr sqlsrv
   ```
   Deve mostrar: `pdo_sqlsrv` e `sqlsrv`

#### B. Copiar o arquivo da API

1. No seu computador, copie o arquivo:
   ```
   softlab_api_para_instalar_no_servidor.php
   ```

2. Cole no servidor `10.1.8.7` em:
   ```
   C:\xampp\htdocs\softlab_api\index.php
   ```

3. Edite o arquivo e configure as credenciais SQL Server:
   ```php
   define('DB_HOST', 'localhost');     // SQL Server local
   define('DB_PORT', '1433');          
   define('DB_NAME', 'BD_SOFTLAB_P00');
   define('DB_USER', 'sa');            // seu usuário
   define('DB_PASS', 'sua_senha');     // sua senha
   define('API_TOKEN', 'meutoken123'); // crie um token seguro
   ```

#### C. Configurar SQL Server para aceitar conexões locais

No servidor, abra **SQL Server Management Studio (SSMS)** ou execute:

```sql
-- Habilitar login SQL Server (se necessário)
ALTER LOGIN sa ENABLE;
GO

-- Alterar senha (se necessário)
ALTER LOGIN sa WITH PASSWORD = 'nova_senha';
GO

-- Dar permissões no banco
USE BD_SOFTLAB_P00;
GO
GRANT SELECT ON SCHEMA::dbo TO sa;
GO
```

No **SQL Server Configuration Manager**:
1. SQL Server Network Configuration > Protocols for MSSQLSERVER
2. TCP/IP > **Enabled = Yes**
3. TCP/IP > IP Addresses > IPAll > **TCP Port = 1433**
4. Reinicie o serviço SQL Server

---

### 2️⃣ Testar a API

#### No navegador do servidor (ou seu computador):

```
http://10.1.8.7/softlab_api/?action=info
```

Deve retornar JSON com informações da API.

#### Com token:

```
http://10.1.8.7/softlab_api/?action=test&token=meutoken123
```

Deve retornar:
```json
{
  "success": true,
  "data": {
    "sqlserver_version": "Microsoft SQL Server 2019...",
    "database": "BD_SOFTLAB_P00",
    "tables": {
      "pedido": true,
      "cliente": true
    }
  },
  "message": "Conexão SQL Server OK"
}
```

#### Buscar pedidos de hoje:

```
http://10.1.8.7/softlab_api/?action=pedidos_hoje&token=meutoken123
```

---

### 3️⃣ Configurar no Laravel

No arquivo `.env` do seu sistema Laravel:

```env
SOFTLAB_USE_HTTP=true
SOFTLAB_API_URL=http://10.1.8.7/softlab_api
SOFTLAB_API_TOKEN=meutoken123
```

Limpe o cache:
```bash
php artisan config:clear
```

---

## 🧪 Testes de Diagnóstico

### Teste Python (SQL Server direto):
```bash
pip install pyodbc
python test_sqlserver_connection.py
```

### Teste rápido de porta:
```bash
.\teste_rapido.bat
```
(Deve testar porta 1433 agora, não 3306)

---

## ❓ Troubleshooting

### "Driver SQL Server não está instalado"
- Baixe e instale: https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server
- Adicione ao php.ini: `extension=php_pdo_sqlsrv_82_ts.dll`
- Reinicie Apache

### "Erro de conexão SQL Server"
- Verifique usuário e senha no arquivo da API
- No SSMS: `SELECT name, is_disabled FROM sys.sql_logins WHERE name='sa'`
- Habilite TCP/IP no SQL Server Configuration Manager

### "API não responde (404)"
- Confirme que o arquivo está em: `C:\xampp\htdocs\softlab_api\index.php`
- Teste: `http://10.1.8.7/softlab_api/` no navegador
- Verifique se Apache está rodando

### "Token inválido"
- Verifique se o token no `.env` é o mesmo do arquivo da API
- Token é case-sensitive

---

## 🔒 Segurança

1. **Mude o token padrão!** Nunca use `seu_token_secreto_aqui_123456`
2. Configure `.htaccess` para restringir IPs (opcional):
   ```apache
   Order Deny,Allow
   Deny from all
   Allow from 10.1.8.32
   ```
3. Use HTTPS em produção (configure certificado SSL)
4. Não exponha a API na internet, apenas rede local

---

## 📊 Endpoints Disponíveis

| Endpoint | Parâmetros | Descrição |
|----------|------------|-----------|
| `?action=info` | - | Informações da API (sem token) |
| `?action=test&token=XXX` | token | Testa conexão SQL Server |
| `?action=pedidos_hoje&token=XXX` | token | Pedidos do dia atual |
| `?action=pedido&cod_pedido=123&token=XXX` | cod_pedido, token | Busca pedido específico |
| `?action=cliente&cod_cliente=456&token=XXX` | cod_cliente, token | Busca cliente |

---

## ✅ Checklist de Instalação

- [ ] Drivers SQL Server para PHP instalados
- [ ] Extensions habilitadas no php.ini
- [ ] Apache reiniciado
- [ ] Arquivo API copiado para `C:\xampp\htdocs\softlab_api\index.php`
- [ ] Credenciais SQL Server configuradas no arquivo
- [ ] Token personalizado definido
- [ ] SQL Server TCP/IP habilitado
- [ ] Teste `?action=info` funcionou
- [ ] Teste `?action=test&token=XXX` funcionou
- [ ] Laravel configurado no `.env`
- [ ] Cache limpo: `php artisan config:clear`

---

Após seguir todos os passos, teste no Laravel acessando **Configurações > Integração > Gerenciar Mapeamentos Softlab > Testar Conexão**. 🚀
