<?php

/**
 * API INTERMEDIÁRIA PARA SOFTLAB - SQL SERVER
 * 
 * REQUISITOS:
 * - PHP com extensão pdo_sqlsrv habilitada
 * - Drivers Microsoft SQL Server para PHP
 * - Download: https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server
 * 
 * INSTALAÇÃO NO SERVIDOR 10.1.8.7:
 * 1. Instale drivers SQL Server para PHP (SQLSRV)
 * 2. Copie este arquivo para: C:\xampp\htdocs\softlab_api\index.php
 * 3. Acesse: http://10.1.8.7/softlab_api/?action=test
 * 4. Configure no Laravel para usar esta API
 */

// Configurações do banco SQL Server Softlab (local)
define('DB_HOST', 'localhost');  // ou 127.0.0.1 ou nome da instância
define('DB_PORT', '1433');       // Porta padrão SQL Server
define('DB_NAME', 'BD_SOFTLAB_P00');
define('DB_USER', 'sa');         // ou outro usuário com permissão
define('DB_PASS', 'sua_senha');  // senha do SQL Server

// Segurança: Token de acesso (altere para algo seguro)
define('API_TOKEN', 'seu_token_secreto_aqui_123456');

// Headers CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Função para conectar ao SQL Server
function getConnection()
{
    try {
        // DSN para SQL Server usando PDO SQLSRV
        $dsn = "sqlsrv:Server=" . DB_HOST . "," . DB_PORT . ";Database=" . DB_NAME;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        // Verificar se driver está instalado
        $drivers = PDO::getAvailableDrivers();
        if (!in_array('sqlsrv', $drivers)) {
            respondError('Driver SQL Server (sqlsrv) não está instalado. Drivers disponíveis: ' . implode(', ', $drivers));
        }
        respondError('Erro de conexão SQL Server: ' . $e->getMessage());
    }
}

// Função para validar token
function validateToken()
{
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? $_GET['token'] ?? null;

    if ($token !== API_TOKEN) {
        respondError('Token inválido', 401);
    }
}

// Função para responder com erro
function respondError($message, $code = 500)
{
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Função para responder com sucesso
function respondSuccess($data, $message = 'OK')
{
    echo json_encode([
        'success' => true,
        'data' => $data,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Validar token (exceto para action=info)
$action = $_GET['action'] ?? 'info';
if ($action !== 'info') {
    validateToken();
}

// Roteamento
switch ($action) {
    case 'info':
        // Informações da API (sem token necessário)
        respondSuccess([
            'version' => '1.0',
            'database' => DB_NAME,
            'available_actions' => [
                'test' => 'Testa conexão com o banco',
                'pedidos_hoje' => 'Busca pedidos do dia atual',
                'pedido' => 'Busca pedido específico (requer cod_pedido)',
                'cliente' => 'Busca cliente (requer cod_cliente)'
            ],
            'auth' => 'Adicione ?token=SEU_TOKEN ou header Authorization: SEU_TOKEN'
        ], 'Softlab API v1.0');
        break;

    case 'test':
        // Testa conexão SQL Server
        $pdo = getConnection();

        // Versão do SQL Server
        $stmt = $pdo->query("SELECT @@VERSION as version, DB_NAME() as database");
        $result = $stmt->fetch();

        // Verifica tabelas (sintaxe SQL Server)
        $stmt = $pdo->query("SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'pedido'");
        $hasPedido = $stmt->fetch()['existe'] > 0;

        $stmt = $pdo->query("SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'cliente'");
        $hasCliente = $stmt->fetch()['existe'] > 0;

        respondSuccess([
            'sqlserver_version' => $result['version'],
            'database' => $result['database'],
            'tables' => [
                'pedido' => $hasPedido,
                'cliente' => $hasCliente
            ]
        ], 'Conexão SQL Server OK');
        break;

    case 'pedidos_hoje':
        // Busca pedidos do dia atual
        $pdo = getConnection();

        $sql = "SELECT 
                    p.cod_pedido,
                    p.cod_cliente,
                    p.posto_cliente,
                    p.usu_pedido,
                    p.cod_guia,
                    p.datahora_atendimento,
                    p.cod_origem,
                    c.nome_cliente
                FROM pedido p
                JOIN cliente c ON p.cod_cliente = c.cod_cliente
                WHERE CONVERT(DATE, p.datahora_atendimento) = CONVERT(DATE, GETDATE())
                ORDER BY p.datahora_atendimento DESC";

        $stmt = $pdo->query($sql);
        $pedidos = $stmt->fetchAll();

        respondSuccess($pedidos, "Encontrados " . count($pedidos) . " pedidos");
        break;

    case 'pedido':
        // Busca pedido específico
        $codPedido = $_GET['cod_pedido'] ?? null;
        if (!$codPedido) {
            respondError('Parâmetro cod_pedido é obrigatório', 400);
        }

        $pdo = getConnection();

        $sql = "SELECT 
                    p.*,
                    c.nome_cliente,
                    c.endereco_cliente,
                    c.telefone_cliente
                FROM pedido p
                LEFT JOIN cliente c ON p.cod_cliente = c.cod_cliente
                WHERE p.cod_pedido = :cod_pedido";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cod_pedido' => $codPedido]);
        $pedido = $stmt->fetch();

        if (!$pedido) {
            respondError('Pedido não encontrado', 404);
        }

        respondSuccess($pedido);
        break;

    case 'cliente':
        // Busca cliente
        $codCliente = $_GET['cod_cliente'] ?? null;
        if (!$codCliente) {
            respondError('Parâmetro cod_cliente é obrigatório', 400);
        }

        $pdo = getConnection();

        $stmt = $pdo->prepare("SELECT * FROM cliente WHERE cod_cliente = :cod_cliente");
        $stmt->execute(['cod_cliente' => $codCliente]);
        $cliente = $stmt->fetch();

        if (!$cliente) {
            respondError('Cliente não encontrado', 404);
        }

        respondSuccess($cliente);
        break;

    default:
        respondError('Ação inválida', 400);
}
