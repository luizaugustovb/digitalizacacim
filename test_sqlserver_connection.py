#!/usr/bin/env python3
"""
Script para testar conexão com SQL Server do Softlab
Uso: python test_sqlserver_connection.py

Requisitos: pip install pyodbc
"""

import sys
import socket
import pyodbc

# Configurações
HOST = '10.1.8.7'
PORT = 1433
DATABASE = 'BD_SOFTLAB_P00'
USERNAME = 'sa'  # ou outro usuário
PASSWORD = 'sua_senha'

def test_port(host, port, timeout=5):
    """Testa se a porta está acessível"""
    print(f"\n🔍 Testando conectividade com {host}:{port}...")
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    sock.settimeout(timeout)
    
    try:
        result = sock.connect_ex((host, port))
        if result == 0:
            print(f"✅ Porta {port} está ABERTA e acessível")
            return True
        else:
            print(f"❌ Porta {port} está FECHADA ou inacessível (código: {result})")
            return False
    except socket.timeout:
        print(f"⏱️ TIMEOUT: O host {host} não respondeu em {timeout} segundos")
        return False
    except socket.gaierror:
        print(f"❌ ERRO: Não foi possível resolver o hostname {host}")
        return False
    except Exception as e:
        print(f"❌ ERRO: {str(e)}")
        return False
    finally:
        sock.close()

def test_sqlserver_connection():
    """Testa conexão SQL Server completa"""
    print(f"\n🔌 Tentando conectar ao SQL Server...")
    print(f"   Host: {HOST}")
    print(f"   Port: {PORT}")
    print(f"   Database: {DATABASE}")
    print(f"   Username: {USERNAME}")
    print(f"   Password: {'*' * len(PASSWORD) if PASSWORD else '(vazia)'}")
    
    try:
        # String de conexão para SQL Server
        # Tenta com ODBC Driver 17 (mais novo)
        drivers = [
            '{ODBC Driver 17 for SQL Server}',
            '{ODBC Driver 13 for SQL Server}',
            '{ODBC Driver 11 for SQL Server}',
            '{SQL Server Native Client 11.0}',
            '{SQL Server}'
        ]
        
        connection_string = None
        for driver in drivers:
            try:
                conn_str = f'DRIVER={driver};SERVER={HOST},{PORT};DATABASE={DATABASE};UID={USERNAME};PWD={PASSWORD};TrustServerCertificate=yes'
                connection = pyodbc.connect(conn_str, timeout=10)
                connection_string = conn_str
                print(f"\n✅ Conectado usando: {driver}")
                break
            except pyodbc.Error:
                continue
        
        if not connection_string:
            print(f"\n❌ Nenhum driver ODBC encontrado!")
            print(f"\n💡 INSTALE OS DRIVERS:")
            print(f"   Download: https://docs.microsoft.com/en-us/sql/connect/odbc/download-odbc-driver-for-sql-server")
            print(f"   Ou: pip install pyodbc")
            return False
        
        cursor = connection.cursor()
        
        # Versão do SQL Server
        cursor.execute("SELECT @@VERSION as version")
        version = cursor.fetchone()[0]
        print(f"\n✅ CONEXÃO ESTABELECIDA COM SUCESSO!")
        print(f"   Versão SQL Server: {version.split('\\n')[0]}")
        
        # Database atual
        cursor.execute("SELECT DB_NAME() as db")
        db = cursor.fetchone()[0]
        print(f"   Database atual: {db}")
        
        # Testa se as tabelas existem
        cursor.execute("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'pedido'")
        has_pedido = cursor.fetchone()[0] > 0
        if has_pedido:
            print(f"   ✅ Tabela 'pedido' encontrada")
        else:
            print(f"   ⚠️ Tabela 'pedido' NÃO encontrada")
        
        cursor.execute("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'cliente'")
        has_cliente = cursor.fetchone()[0] > 0
        if has_cliente:
            print(f"   ✅ Tabela 'cliente' encontrada")
        else:
            print(f"   ⚠️ Tabela 'cliente' NÃO encontrada")
        
        # Testa query de pedidos de hoje
        if has_pedido:
            cursor.execute("""
                SELECT COUNT(*) as total 
                FROM pedido 
                WHERE CONVERT(DATE, datahora_atendimento) = CONVERT(DATE, GETDATE())
            """)
            count = cursor.fetchone()[0]
            print(f"   📊 Pedidos de hoje: {count}")
        
        cursor.close()
        connection.close()
        print(f"\n✅ Teste concluído com SUCESSO!\n")
        return True
            
    except pyodbc.Error as e:
        print(f"\n❌ ERRO DE CONEXÃO SQL Server:")
        print(f"   {str(e)}")
        
        error_str = str(e).lower()
        
        if 'login failed' in error_str or '18456' in error_str:
            print(f"\n💡 DICA: Credenciais inválidas (usuário/senha)")
            print(f"   - Verifique o usuário e senha")
            print(f"   - Confirme que o usuário tem permissão de login remoto")
        elif 'cannot open database' in error_str or '4060' in error_str:
            print(f"\n💡 DICA: Database '{DATABASE}' não existe ou sem permissão")
        elif 'timeout' in error_str:
            print(f"\n💡 DICA: Timeout de conexão")
            print(f"   - SQL Server pode não estar aceitando conexões remotas")
            print(f"   - Verifique configuração TCP/IP no SQL Server Configuration Manager")
        
        return False
    except Exception as e:
        print(f"\n❌ ERRO INESPERADO: {str(e)}")
        return False

def main():
    print("="*70)
    print("🔍 DIAGNÓSTICO DE CONEXÃO SQL SERVER SOFTLAB")
    print("="*70)
    
    # Passo 1: Teste de ping/porta
    port_ok = test_port(HOST, PORT)
    
    if not port_ok:
        print("\n" + "="*70)
        print("❌ DIAGNÓSTICO: A porta 1433 não está acessível")
        print("="*70)
        print("\n💡 SOLUÇÕES POSSÍVEIS:")
        print("   1. SQL Server não está rodando ou configurado para aceitar conexões TCP/IP")
        print("   2. No SQL Server Configuration Manager:")
        print("      - Protocols for MSSQLSERVER > TCP/IP > Enabled = Yes")
        print("      - TCP/IP Port = 1433")
        print("   3. Firewall bloqueando porta 1433:")
        print("      - Windows: Libere porta 1433 no Firewall")
        print("      - PowerShell: New-NetFirewallRule -DisplayName 'SQL Server' -Direction Inbound -LocalPort 1433 -Protocol TCP -Action Allow")
        print("   4. SQL Server Browser deve estar rodando (para instâncias nomeadas)")
        print("\n")
        sys.exit(1)
    
    # Passo 2: Teste de conexão SQL Server
    sqlserver_ok = test_sqlserver_connection()
    
    if not sqlserver_ok:
        print("\n" + "="*70)
        print("❌ DIAGNÓSTICO: Porta acessível mas SQL Server não conecta")
        print("="*70)
        print("\n💡 PRÓXIMOS PASSOS:")
        print("   1. Verifique credenciais (usuário/senha)")
        print("   2. No SQL Server Management Studio (SSMS):")
        print("      - Security > Logins > sa > Properties")
        print("      - Certifique-se que 'Enforce password policy' não está bloqueando")
        print("   3. Habilite autenticação SQL Server:")
        print("      - Server Properties > Security > SQL Server and Windows Authentication mode")
        print("   4. Reinicie o serviço SQL Server após mudanças")
        print("\n")
        sys.exit(1)
    
    sys.exit(0)

if __name__ == "__main__":
    # Verifica se pyodbc está instalado
    try:
        import pyodbc
    except ImportError:
        print("❌ Módulo 'pyodbc' não está instalado!")
        print("\n💡 Instale com:")
        print("   pip install pyodbc")
        print("\nDepois execute novamente este script.")
        sys.exit(1)
    
    main()
