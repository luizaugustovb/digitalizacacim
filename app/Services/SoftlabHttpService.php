<?php

namespace App\Services;

use App\Helpers\ConfigHelper;
use Illuminate\Support\Facades\Http;
use App\Models\Pedido;
use App\Models\SoftlabMapping;
use Carbon\Carbon;

class SoftlabHttpService
{
    private string $apiUrl;
    private string $apiToken;

    public function __construct()
    {
        $this->apiUrl = config('services.softlab.api_url', 'http://10.1.8.7/softlab_api');
        $this->apiToken = config('services.softlab.api_token', 'seu_token_secreto_aqui_123456');
    }

    /**
     * Busca pedidos do dia atual via API HTTP
     */
    public function buscarPedidosDoDia()
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => $this->apiToken])
                ->get($this->apiUrl, [
                    'action' => 'pedidos_hoje'
                ]);

            if ($response->successful()) {
                $result = $response->json();

                if ($result['success']) {
                    return [
                        'success' => true,
                        'data' => $result['data'],
                        'total' => count($result['data']),
                        'message' => $result['message']
                    ];
                }

                return [
                    'success' => false,
                    'error' => $result['error'] ?? 'Erro desconhecido',
                    'message' => 'API Softlab retornou erro'
                ];
            }

            return [
                'success' => false,
                'error' => 'Status HTTP: ' . $response->status(),
                'message' => 'Erro ao comunicar com API Softlab'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erro ao conectar com API Softlab'
            ];
        }
    }

    /**
     * Testa conexão com a API
     */
    public function testarConexao()
    {
        try {
            // Primeiro testa sem token (endpoint info)
            $response = Http::timeout(5)->get($this->apiUrl, ['action' => 'info']);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'API não respondeu',
                    'error' => 'Status HTTP: ' . $response->status(),
                    'diagnostico' => $this->diagnosticarErroHttp($response)
                ];
            }

            // Depois testa com token (endpoint test)
            $response = Http::timeout(5)
                ->withHeaders(['Authorization' => $this->apiToken])
                ->get($this->apiUrl, ['action' => 'test']);

            if ($response->successful()) {
                $result = $response->json();

                if ($result['success']) {
                    $warnings = [];
                    if (!($result['data']['tables']['pedido'] ?? false)) {
                        $warnings[] = 'Tabela "pedido" não encontrada';
                    }
                    if (!($result['data']['tables']['cliente'] ?? false)) {
                        $warnings[] = 'Tabela "cliente" não encontrada';
                    }

                    return [
                        'success' => true,
                        'message' => 'Conexão com API Softlab estabelecida com sucesso',
                        'details' => [
                            'api_url' => $this->apiUrl,
                            'mysql_version' => $result['data']['mysql_version'] ?? 'Desconhecida',
                            'database' => $result['data']['database'] ?? 'Desconhecido',
                            'has_pedido_table' => $result['data']['tables']['pedido'] ?? false,
                            'has_cliente_table' => $result['data']['tables']['cliente'] ?? false,
                            'warnings' => $warnings
                        ]
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'API retornou erro',
                    'error' => $result['error'] ?? 'Erro desconhecido'
                ];
            }

            return [
                'success' => false,
                'message' => 'Erro ao testar API',
                'error' => 'Status HTTP: ' . $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erro ao conectar com API Softlab',
                'diagnostico' => [
                    'problema' => 'API não está acessível via HTTP',
                    'causas' => [
                        'API não foi instalada no servidor 10.1.8.7',
                        'Apache/servidor web não está rodando',
                        'URL da API está incorreta',
                        'Firewall bloqueando porta 80/443'
                    ],
                    'solucoes' => [
                        '1. Instale o arquivo softlab_api_para_instalar_no_servidor.php no servidor 10.1.8.7',
                        '2. Coloque em: C:\\xampp\\htdocs\\softlab_api\\index.php',
                        '3. Teste no navegador: http://10.1.8.7/softlab_api/?action=info',
                        '4. Configure o token no .env: SOFTLAB_API_TOKEN=seu_token',
                        '5. Se não funcionar, use telnet para testar porta 80: telnet 10.1.8.7 80'
                    ]
                ]
            ];
        }
    }

    /**
     * Busca pedido específico
     */
    public function buscarPedido($codPedido)
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['Authorization' => $this->apiToken])
                ->get($this->apiUrl, [
                    'action' => 'pedido',
                    'cod_pedido' => $codPedido
                ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['success'] ? $result['data'] : null;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Importa pedido do Softlab para o sistema
     */
    public function importarPedido($codPedido)
    {
        try {
            $pedidoSoftlab = $this->buscarPedido($codPedido);

            if (!$pedidoSoftlab) {
                return [
                    'success' => false,
                    'message' => 'Pedido não encontrado no Softlab'
                ];
            }

            if (!$this->convenioPermitido($pedidoSoftlab['cod_guia'] ?? null)) {
                return [
                    'success' => false,
                    'message' => 'Convênio não permitido para importação'
                ];
            }

            // Mapear unidade
            $unidadeId = null;
            if (!empty($pedidoSoftlab['posto_cliente'])) {
                $unidade = SoftlabMapping::getUnidadeByCodigo($pedidoSoftlab['posto_cliente']);
                $unidadeId = $unidade?->id;
            }
            if (!$unidadeId && !empty($pedidoSoftlab['cod_origem'])) {
                $unidade = SoftlabMapping::getUnidadeByCodigo($pedidoSoftlab['cod_origem']);
                $unidadeId = $unidade?->id;
            }

            // Mapear usuário
            $userId = null;
            if (!empty($pedidoSoftlab['usu_pedido'])) {
                $user = SoftlabMapping::getUserByCodigo($pedidoSoftlab['usu_pedido']);
                $userId = $user?->id;
            }

            // Criar pedido
            $pedido = Pedido::create([
                'codigo_pedido' => $pedidoSoftlab['cod_pedido'],
                'codigo_paciente' => $pedidoSoftlab['cod_cliente'] ?? null,
                'nome_paciente' => $pedidoSoftlab['nome_cliente'] ?? 'Sem nome',
                'data_atendimento' => Carbon::parse($pedidoSoftlab['datahora_atendimento'] ?? now())->format('Y-m-d'),
                'unidade_id' => $unidadeId,
                'atendente_id' => $userId,
                'status' => 'PENDENTE',
                'cod_guia' => $pedidoSoftlab['cod_guia'] ?? null,
                'observacoes' => "Importado do Softlab - Posto: {$pedidoSoftlab['posto_cliente']}, Origem: {$pedidoSoftlab['cod_origem']}"
            ]);

            return [
                'success' => true,
                'message' => 'Pedido importado com sucesso',
                'pedido_id' => $pedido->id
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

    /**
     * Diagnostica erro HTTP
     */
    private function diagnosticarErroHttp($response)
    {
        $status = $response->status();

        $diagnosticos = [
            404 => [
                'problema' => 'API não encontrada (404)',
                'causas' => ['Arquivo index.php não existe no servidor', 'URL incorreta'],
                'solucoes' => ['Verifique se instalou em: C:\\xampp\\htdocs\\softlab_api\\index.php']
            ],
            401 => [
                'problema' => 'Token inválido (401)',
                'causas' => ['Token no .env diferente do definido na API'],
                'solucoes' => ['Configure SOFTLAB_API_TOKEN no .env com o mesmo valor da API']
            ],
            500 => [
                'problema' => 'Erro interno da API (500)',
                'causas' => ['Erro de conexão com MySQL na API', 'Erro de sintaxe no código PHP'],
                'solucoes' => ['Verifique logs de erro do PHP no servidor', 'Teste a API diretamente no navegador']
            ],
            0 => [
                'problema' => 'Não foi possível conectar',
                'causas' => ['Servidor web não está rodando', 'IP/URL incorreto', 'Firewall'],
                'solucoes' => ['Verifique se Apache está rodando no servidor', 'Teste: ping 10.1.8.7', 'Teste no navegador: http://10.1.8.7']
            ]
        ];

        return $diagnosticos[$status] ?? $diagnosticos[0];
    }
}
