<?php

namespace App\Services;

use App\Helpers\ConfigHelper;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\SoftlabMapping;
use Carbon\Carbon;

class SoftlabService
{
    /**
     * Busca pedidos do dia atual no Softlab (SQL Server)
     */
    public function buscarPedidosDoDia()
    {
        try {
            // Para SQL Server, usar CONVERT(DATE, ...) em vez de DATE()
            $pedidos = DB::connection('softlab')
                ->select("
                    SELECT 
                        p.codigo_pedido,
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
                    ORDER BY p.datahora_atendimento DESC
                ");

            return [
                'success' => true,
                'data' => $pedidos,
                'total' => count($pedidos),
                'message' => "Encontrados " . count($pedidos) . " pedidos do dia"
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erro ao conectar com o banco Softlab: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Testa conexão com o Softlab (SQL Server)
     */
    public function testarConexao()
    {
        try {
            $pdo = DB::connection('softlab')->getPdo();

            // Versão do SQL Server
            $result = DB::connection('softlab')->select("SELECT @@VERSION as version, DB_NAME() as db_name");
            $version = $result[0]->version ?? 'Desconhecida';
            $database = $result[0]->db_name ?? 'Desconhecido';

            // Testa se as tabelas existem (sintaxe SQL Server)
            $resultPedido = DB::connection('softlab')->select("
                SELECT COUNT(*) as existe 
                FROM INFORMATION_SCHEMA.TABLES 
                WHERE TABLE_NAME = 'pedido'
            ");
            $hasPedido = ($resultPedido[0]->existe ?? 0) > 0;

            $resultCliente = DB::connection('softlab')->select("
                SELECT COUNT(*) as existe 
                FROM INFORMATION_SCHEMA.TABLES 
                WHERE TABLE_NAME = 'cliente'
            ");
            $hasCliente = ($resultCliente[0]->existe ?? 0) > 0;

            $warnings = [];
            if (!$hasPedido) $warnings[] = 'Tabela "pedido" não encontrada';
            if (!$hasCliente) $warnings[] = 'Tabela "cliente" não encontrada';

            return [
                'success' => true,
                'message' => 'Conexão com Softlab (SQL Server) estabelecida com sucesso',
                'details' => [
                    'version' => explode("\n", $version)[0], // Primeira linha da versão
                    'database' => $database,
                    'host' => config('database.connections.softlab.host'),
                    'port' => config('database.connections.softlab.port'),
                    'has_pedido_table' => $hasPedido,
                    'has_cliente_table' => $hasCliente,
                    'warnings' => $warnings
                ]
            ];
        } catch (\PDOException $e) {
            $errorCode = $e->getCode();
            $errorMsg = $e->getMessage();

            // Diagnóstico detalhado baseado no erro
            $diagnostico = $this->diagnosticarErroConexao($errorCode, $errorMsg);

            return [
                'success' => false,
                'error' => $errorMsg,
                'error_code' => $errorCode,
                'message' => 'Erro ao conectar com Softlab (SQL Server)',
                'diagnostico' => $diagnostico
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erro ao conectar com Softlab: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Diagnostica erro de conexão SQL Server e sugere soluções
     */
    private function diagnosticarErroConexao($code, $message)
    {
        $diagnosticos = [
            'SQLSTATE[08001]' => [
                'problema' => 'Não foi possível conectar ao SQL Server',
                'causas' => [
                    'Driver pdo_sqlsrv não está instalado',
                    'SQL Server não está rodando',
                    'Porta 1433 bloqueada por firewall',
                    'SQL Server não configurado para aceitar conexões TCP/IP'
                ],
                'solucoes' => [
                    'Verifique se driver está instalado: php -m | findstr sqlsrv',
                    'Instale driver: https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server',
                    'Verifique SQL Server Configuration Manager: TCP/IP deve estar habilitado',
                    'Teste porta: Test-NetConnection -ComputerName ' . config('database.connections.softlab.host') . ' -Port 1433'
                ]
            ],
            'SQLSTATE[28000]' => [
                'problema' => 'Falha de autenticação (usuário/senha)',
                'causas' => [
                    'Usuário ou senha incorretos',
                    'Autenticação SQL Server não habilitada',
                    'Login desabilitado'
                ],
                'solucoes' => [
                    'Verifique credenciais no arquivo .env',
                    'Habilite Mixed Mode Authentication no SQL Server',
                    'No SSMS: ALTER LOGIN sa ENABLE;'
                ]
            ],
            'could not find driver' => [
                'problema' => 'Driver PDO SQLSRV não encontrado',
                'causas' => [
                    'Extensão pdo_sqlsrv não instalada',
                    'Driver não habilitado no php.ini'
                ],
                'solucoes' => [
                    '1. Baixe drivers: https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server',
                    '2. Copie php_pdo_sqlsrv_*.dll para C:\\xampp\\php\\ext\\',
                    '3. Adicione no php.ini: extension=php_pdo_sqlsrv_82_ts.dll',
                    '4. Reinicie Apache',
                    '5. Verifique: php -m | findstr sqlsrv'
                ]
            ]
        ];

        // Busca por padrão na mensagem
        foreach ($diagnosticos as $pattern => $info) {
            if (stripos($message, $pattern) !== false || stripos($code, $pattern) !== false) {
                return $info;
            }
        }

        // Diagnóstico genérico
        return [
            'problema' => 'Erro de conexão SQL Server',
            'causas' => ['Erro não catalogado: ' . substr($message, 0, 200)],
            'solucoes' => [
                '1. Verifique se driver pdo_sqlsrv está instalado: php -m | findstr sqlsrv',
                '2. Confirme credenciais no .env (host, porta, usuário, senha)',
                '3. Teste conexão com SQL Server Management Studio primeiro',
                '4. Consulte o erro completo nos logs'
            ]
        ];
    }

    /**
     * Importa pedido do Softlab para o sistema
     */
    public function importarPedido($codPedido)
    {
        try {
            // Busca pedido no Softlab (SQL Server)
            $pedidosSoftlab = DB::connection('softlab')->select("
                SELECT 
                    p.codigo_pedido,
                    p.cod_cliente,
                    p.posto_cliente,
                    p.usu_pedido,
                    p.cod_guia,
                    p.datahora_atendimento,
                    p.cod_origem,
                    c.nome_cliente
                FROM pedido p
                INNER JOIN cliente c ON p.cod_cliente = c.cod_cliente
                WHERE p.codigo_pedido = ?
            ", [$codPedido]);

            if (empty($pedidosSoftlab)) {
                return [
                    'success' => false,
                    'message' => 'Pedido não encontrado no Softlab'
                ];
            }

            $pedidoSoftlab = $pedidosSoftlab[0];

            if (!$this->convenioPermitido($pedidoSoftlab->cod_guia ?? null)) {
                return [
                    'success' => false,
                    'message' => 'Convênio não permitido para importação'
                ];
            }

            // Buscar mapeamentos
            $unidadePosto = SoftlabMapping::getUnidadeByCodigo($pedidoSoftlab->posto_cliente);
            $unidadeOrigem = SoftlabMapping::getUnidadeByCodigo($pedidoSoftlab->cod_origem);
            $atendente = SoftlabMapping::getUserByCodigo($pedidoSoftlab->usu_pedido);
            $convenio = SoftlabMapping::getConvenioByCodigo($pedidoSoftlab->cod_guia);

            // Verifica se já existe
            $pedidoExistente = Pedido::where('codigo_pedido', $pedidoSoftlab->codigo_pedido)->first();
            if ($pedidoExistente) {
                return [
                    'success' => false,
                    'message' => 'Pedido já existe no sistema'
                ];
            }

            // Cria pedido no sistema
            $pedido = Pedido::create([
                'codigo_pedido' => $pedidoSoftlab->codigo_pedido,
                'codigo_paciente' => $pedidoSoftlab->cod_cliente,
                'nome_paciente' => $pedidoSoftlab->nome_cliente,
                'convenio_id' => $convenio?->id,
                'unidade_id' => $unidadePosto?->id ?? $unidadeOrigem?->id,
                'tipo_atendimento' => null,
                'data_atendimento' => Carbon::parse($pedidoSoftlab->datahora_atendimento)->format('Y-m-d'),
                'status' => 'PENDENTE',
                'atendente_id' => $atendente?->id,
                'cod_guia' => $pedidoSoftlab->cod_guia,
                'observacoes' => "Importado do Softlab - Cod Origem: {$pedidoSoftlab->cod_origem}, Posto: {$pedidoSoftlab->posto_cliente}"
            ]);

            return [
                'success' => true,
                'pedido' => $pedido,
                'message' => 'Pedido importado com sucesso'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erro ao importar pedido'
            ];
        }
    }
    private function convenioPermitido(?string $codGuia): bool
    {
        $raw = (string) ConfigHelper::get('importacao_convenios_permitidos', '');
        $parts = preg_split('/[\s,;]+/', $raw) ?: [];
        $permitidos = array_values(array_filter(array_map(function ($item) {
            $item = strtoupper(trim($item));
            return $item === '' ? null : $item;
        }, $parts)));

        if (empty($permitidos)) {
            return true;
        }

        $codigo = $codGuia ? strtoupper(trim($codGuia)) : '';
        if ($codigo === '') {
            return false;
        }

        return in_array($codigo, $permitidos, true);
    }
}
