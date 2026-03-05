#!/usr/bin/env python3
"""
Script para testar conexão com banco MySQL/MariaDB do Softlab
Uso: python test_softlab_connection.py
"""

import sys
import socket
import mysql.connector
from mysql.connector import Error

# Configurações (edite conforme necessário)
HOST = '10.1.8.7'
PORT = 3306
DATABASE = 'BD_SOFTLAB_P00'
USERNAME = 'root'
PASSWORD = 'ndqualidade'

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

def test_mysql_connection():
    """Testa conexão MySQL completa"""
    print(f"\n🔌 Tentando conectar ao MySQL...")
    print(f"   Host: {HOST}")
    print(f"   Port: {PORT}")
    print(f"   Database: {DATABASE}")
    print(f"   Username: {USERNAME}")
    print(f"   Password: {'*' * len(PASSWORD) if PASSWORD else '(vazia)'}")
    
    try:
        connection = mysql.connector.connect(
            host=HOST,
            port=PORT,
            database=DATABASE,
            user=USERNAME,
            password=PASSWORD,
            connect_timeout=10,
            connection_timeout=10
        )
        
        if connection.is_connected():
            db_info = connection.get_server_info()
            print(f"\n✅ CONEXÃO ESTABELECIDA COM SUCESSO!")
            print(f"   Versão MySQL: {db_info}")
            
            cursor = connection.cursor()
            cursor.execute("SELECT DATABASE();")
            record = cursor.fetchone()
            print(f"   Database atual: {record[0]}")
            
            # Testa se as tabelas existem
            cursor.execute("SHOW TABLES LIKE 'pedido';")
            if cursor.fetchone():
                print(f"   ✅ Tabela 'pedido' encontrada")
            else:
                print(f"   ⚠️ Tabela 'pedido' NÃO encontrada")
            
            cursor.execute("SHOW TABLES LIKE 'cliente';")
            if cursor.fetchone():
                print(f"   ✅ Tabela 'cliente' encontrada")
            else:
                print(f"   ⚠️ Tabela 'cliente' NÃO encontrada")
            
            # Testa query
            cursor.execute("SELECT COUNT(*) FROM pedido WHERE DATE(datahora_atendimento) = CURDATE();")
            count = cursor.fetchone()[0]
            print(f"   📊 Pedidos de hoje: {count}")
            
            cursor.close()
            connection.close()
            print(f"\n✅ Teste concluído com SUCESSO!\n")
            return True
            
    except Error as e:
        print(f"\n❌ ERRO DE CONEXÃO MySQL:")
        print(f"   Código: {e.errno}")
        print(f"   Mensagem: {e.msg}")
        
        if e.errno == 2003:
            print(f"\n💡 DICA: O servidor MySQL não está acessível.")
            print(f"   Possíveis causas:")
            print(f"   - Firewall bloqueando a porta 3306")
            print(f"   - MySQL não está rodando no servidor {HOST}")
            print(f"   - IP ou porta incorretos")
        elif e.errno == 1045:
            print(f"\n💡 DICA: Credenciais inválidas (usuário/senha)")
        elif e.errno == 1044 or e.errno == 1049:
            print(f"\n💡 DICA: Database '{DATABASE}' não existe ou sem permissão")
        
        return False
    except Exception as e:
        print(f"\n❌ ERRO INESPERADO: {str(e)}")
        return False

def main():
    print("="*70)
    print("🔍 DIAGNÓSTICO DE CONEXÃO SOFTLAB")
    print("="*70)
    
    # Passo 1: Teste de ping/porta
    port_ok = test_port(HOST, PORT)
    
    if not port_ok:
        print("\n" + "="*70)
        print("❌ DIAGNÓSTICO: A porta não está acessível")
        print("="*70)
        print("\n💡 SOLUÇÕES POSSÍVEIS:")
        print("   1. Verifique se o servidor MySQL está rodando:")
        print("      - No servidor: sudo systemctl status mysql")
        print("   2. Verifique o firewall:")
        print("      - Windows: Libere porta 3306 no Firewall")
        print("      - Linux: sudo ufw allow 3306")
        print("   3. Verifique se MySQL está escutando em todas interfaces:")
        print("      - my.cnf: bind-address = 0.0.0.0")
        print("   4. Teste ping ao servidor:")
        print(f"      - ping {HOST}")
        print("\n")
        sys.exit(1)
    
    # Passo 2: Teste de conexão MySQL
    mysql_ok = test_mysql_connection()
    
    if not mysql_ok:
        print("\n" + "="*70)
        print("❌ DIAGNÓSTICO: Porta acessível mas MySQL não conecta")
        print("="*70)
        print("\n💡 PRÓXIMOS PASSOS:")
        print("   1. Verifique credenciais (usuário/senha)")
        print("   2. Verifique permissões do usuário:")
        print("      - GRANT ALL ON BD_SOFTLAB_P00.* TO 'root'@'%';")
        print("   3. Teste conexão local no servidor primeiro")
        print("\n")
        sys.exit(1)
    
    sys.exit(0)

if __name__ == "__main__":
    main()
