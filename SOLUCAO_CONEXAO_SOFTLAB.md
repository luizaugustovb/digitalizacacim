# 🔧 DIAGNÓSTICO: Porta 3306 não está acessível no servidor 10.1.8.7

## ❌ Problema Identificado
O servidor MySQL no IP `10.1.8.7` **não está respondendo** na porta 3306.

**Código de erro:** `10035` (WSAEWOULDBLOCK - operação bloqueada)

---

## ✅ SOLUÇÕES (Execute no servidor 10.1.8.7)

### Solução 1: Verificar se MySQL está rodando

#### Windows (servidor com XAMPP/Wamp/MySQL):
```batch
REM Verificar serviço
sc query MySQL
REM ou
net start | findstr -i mysql

REM Se não estiver rodando, iniciar:
net start MySQL
REM ou inicie pelo XAMPP Control Panel
```

#### Linux:
```bash
# Verificar status
sudo systemctl status mysql
# ou
sudo service mysql status

# Se não estiver rodando, iniciar:
sudo systemctl start mysql
# ou
sudo service mysql start
```

---

### Solução 2: Configurar MySQL para aceitar conexões externas

#### Editar arquivo de configuração MySQL:

**Windows:** `C:\xampp\mysql\bin\my.ini` ou `C:\Program Files\MySQL\MySQL Server X.X\my.ini`

**Linux:** `/etc/mysql/mysql.conf.d/mysqld.cnf` ou `/etc/my.cnf`

**Alterar a linha:**
```ini
# ANTES (apenas localhost):
bind-address = 127.0.0.1

# DEPOIS (todas as interfaces):
bind-address = 0.0.0.0
```

**Reiniciar MySQL após alterar:**
```bash
# Windows
net stop MySQL
net start MySQL

# Linux
sudo systemctl restart mysql
```

---

### Solução 3: Configurar Firewall (Windows Server)

#### Abrir porta 3306 no Firewall:
```powershell
# PowerShell como Administrador
New-NetFirewallRule -DisplayName "MySQL Server" -Direction Inbound -LocalPort 3306 -Protocol TCP -Action Allow
```

#### Ou pela interface gráfica:
1. Abrir **Firewall do Windows com Segurança Avançada**
2. Clicar em **Regras de Entrada** → **Nova Regra**
3. Tipo: **Porta** → Avançar
4. Protocolo: **TCP**, Porta específica: **3306**
5. Ação: **Permitir conexão**
6. Aplicar a: **Domínio, Privado, Público**
7. Nome: **MySQL Server**

---

### Solução 4: Configurar Firewall (Linux)

```bash
# UFW (Ubuntu/Debian)
sudo ufw allow 3306/tcp
sudo ufw reload

# FirewallD (CentOS/RHEL)
sudo firewall-cmd --permanent --add-port=3306/tcp
sudo firewall-cmd --reload

# IPTables
sudo iptables -A INPUT -p tcp --dport 3306 -j ACCEPT
sudo iptables-save
```

---

### Solução 5: Dar permissões remotas ao usuário root

Conecte-se localmente ao MySQL no servidor `10.1.8.7`:

```sql
-- Conectar ao MySQL localmente
mysql -u root -p

-- Dar permissões remotas ao root
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'ndqualidade';

-- Ou criar usuário específico para acesso remoto
CREATE USER 'remote_user'@'%' IDENTIFIED BY 'senha_forte';
GRANT ALL PRIVILEGES ON BD_SOFTLAB_P00.* TO 'remote_user'@'%';

-- Aplicar mudanças
FLUSH PRIVILEGES;

-- Verificar usuários
SELECT user, host FROM mysql.user WHERE user='root';
```

---

## 🧪 TESTES DE VALIDAÇÃO

### Teste 1: Ping ao servidor
```bash
ping 10.1.8.7
```
✅ Se responder = servidor está na rede  
❌ Se não responder = problema de rede ou servidor offline

---

### Teste 2: Telnet para a porta 3306
```bash
# Windows/Linux
telnet 10.1.8.7 3306

# Se telnet não estiver instalado no Windows:
# PowerShell:
Test-NetConnection -ComputerName 10.1.8.7 -Port 3306
```
✅ Se conectar = porta está aberta  
❌ Se falhar = porta bloqueada ou MySQL não está escutando

---

### Teste 3: Usar este script
```bash
# No seu computador (onde está o Laravel)
cd C:\xampp\htdocs\digitalizacacim
python test_softlab_connection.py
```

---

### Teste 4: Testar com HeidiSQL/MySQL Workbench

1. Baixe **HeidiSQL** (Windows) ou **MySQL Workbench**
2. Crie nova conexão:
   - Host: `10.1.8.7`
   - Porta: `3306`
   - Usuário: `root`
   - Senha: `ndqualidade`
   - Database: `BD_SOFTLAB_P00`
3. Clicar em **Conectar**

✅ Se conectar = credenciais corretas, problema é no Laravel  
❌ Se falhar = problema de rede/permissões

---

## 🔄 ALTERNATIVA: Usar túnel SSH

Se não puder abrir a porta 3306 diretamente, pode usar túnel SSH:

```bash
# Criar túnel SSH (requer SSH habilitado no servidor)
ssh -L 3307:localhost:3306 usuario@10.1.8.7

# No .env do Laravel, usar:
SOFTLAB_DB_HOST=127.0.0.1
SOFTLAB_DB_PORT=3307
```

---

## 📝 CHECKLIST DE VERIFICAÇÃO

Execute no servidor `10.1.8.7`:

- [ ] MySQL está rodando? (`sc query MySQL` ou `systemctl status mysql`)
- [ ] MySQL está escutando em 0.0.0.0? (`bind-address = 0.0.0.0` no my.ini/my.cnf)
- [ ] Firewall permite porta 3306? (adicionar regra de entrada)
- [ ] Usuário root tem permissão remota? (`GRANT ALL ON *.* TO 'root'@'%'`)
- [ ] Senha está correta? (`ndqualidade`)
- [ ] Database BD_SOFTLAB_P00 existe? (`SHOW DATABASES;`)

Execute no seu computador:

- [ ] Ping funciona? (`ping 10.1.8.7`)
- [ ] Porta está aberta? (`telnet 10.1.8.7 3306`)
- [ ] Script Python conecta? (`python test_softlab_connection.py`)
- [ ] HeidiSQL conecta?

---

## 🎯 PRÓXIMOS PASSOS

1. **Execute o checklist acima no servidor 10.1.8.7**
2. **Teste a conexão com HeidiSQL primeiro** (para isolar o problema)
3. Se HeidiSQL conectar mas Laravel não:
   - Execute `php artisan config:clear`
   - Reinicie o servidor Laravel
4. Se nada funcionar, considere:
   - Criar VPN/túnel para acessar o servidor
   - Exportar dados do Softlab para arquivo e importar localmente
   - Usar API REST intermediária no servidor Softlab

---

## 📞 SUPORTE

Se após seguir todos os passos ainda houver erro, documente:
- [ ] Print do erro no Laravel
- [ ] Resultado do `python test_softlab_connection.py`
- [ ] Print da tentativa de conexão com HeidiSQL
- [ ] Output de `netstat -an | findstr 3306` (no servidor)
