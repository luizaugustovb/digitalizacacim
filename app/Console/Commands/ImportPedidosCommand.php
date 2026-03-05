<?php

namespace App\Console\Commands;

use App\Helpers\ConfigHelper;
use App\Models\Convenio;
use App\Models\ImportJob;
use App\Models\Pedido;
use App\Models\Unidade;
use App\Models\SoftlabMapping;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ImportPedidosCommand extends Command
{
    protected $signature = 'import:pedidos 
                            {--date-start= : Data inicial para importação (Y-m-d)}
                            {--date-end= : Data final para importação (Y-m-d)}
                            {--force : Forçar reimportação de pedidos existentes}';

    protected $description = 'Importa pedidos do sistema legado SQL Server';

    private $importJob;
    private $errors = [];
    private $imported = 0;
    private $skipped = 0;
    private ?array $conveniosPermitidos = null;

    public function handle()
    {
        $this->info('=== Iniciando Importação de Pedidos ===');

        // Verificar configurações
        if (!$this->validateConfig()) {
            return 1;
        }

        // Criar registro de job
        $this->importJob = ImportJob::create([
            'tipo' => 'pedidos',
            'status' => 'processando',
            'iniciado_em' => now(),
            'total_registros' => 0,
            'importados' => 0,
            'erros' => 0,
            'detalhes_erros' => json_encode([]),
        ]);

        try {
            // Configurar timeout da conexão
            $timeout = ConfigHelper::get('importacao_timeout', 60);
            $this->info("Timeout configurado: {$timeout} segundos");

            // Conectar ao SQL Server
            $this->info('Conectando ao SQL Server...');
            $connection = $this->getSqlServerConnection($timeout);

            // Buscar pedidos
            $pedidos = $this->fetchPedidos($connection);

            $this->importJob->update(['total_registros' => count($pedidos)]);
            $this->info("Total de registros encontrados: " . count($pedidos));

            // Processar pedidos
            $this->processarPedidos($pedidos);

            // Finalizar job
            $this->finalizarJob('concluido');

            $this->info("\n=== Importação Concluída ===");
            $this->info("Importados: {$this->imported}");
            $this->info("Ignorados: {$this->skipped}");
            $this->info("Erros: " . count($this->errors));

            return 0;
        } catch (\Exception $e) {
            $this->error("Erro na importação: " . $e->getMessage());
            Log::error('Erro na importação de pedidos', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->finalizarJob('erro', $e->getMessage());
            return 1;
        }
    }

    private function validateConfig(): bool
    {
        // Verifica se a conexão 'softlab' está configurada
        $config = config('database.connections.softlab');

        if (!$config || !$config['host'] || !$config['database']) {
            $this->error('Configurações de Softlab (SQL Server) incompletas!');
            $this->error('Configure em: Configurações > Integração > Softlab');
            return false;
        }

        return true;
    }

    private function getSqlServerConnection(int $timeout)
    {
        // Usa a conexão 'softlab' já configurada
        return DB::connection('softlab');
    }

    private function fetchPedidos($connection): array
    {
        $query = "
            SELECT 
                p.codigo_pedido as codigo,
                p.cod_cliente as codigo_paciente,
                c.nome_cliente as nome_paciente,
                p.datahora_atendimento as data_atendimento,
                p.posto_cliente as unidade_codigo,
                p.cod_origem,
                p.usu_pedido,
                p.cod_guia as observacoes
            FROM pedido p
            JOIN cliente c ON p.cod_cliente = c.cod_cliente
            WHERE 1=1
        ";

        // Filtros opcionais
        if ($dateStart = $this->option('date-start')) {
            $query .= " AND CONVERT(DATE, p.datahora_atendimento) >= '{$dateStart}'";
        }

        if ($dateEnd = $this->option('date-end')) {
            $query .= " AND CONVERT(DATE, p.datahora_atendimento) <= '{$dateEnd}'";
        }

        $query .= " ORDER BY p.datahora_atendimento DESC";

        $this->info("Executando query no SQL Server Softlab...");

        return $connection->select($query);
    }

    private function processarPedidos(array $pedidos): void
    {
        $bar = $this->output->createProgressBar(count($pedidos));
        $bar->start();

        foreach ($pedidos as $pedidoLegado) {
            try {
                $this->importarPedido($pedidoLegado);
                $bar->advance();
            } catch (\Exception $e) {
                $this->errors[] = [
                    'codigo' => $pedidoLegado->codigo,
                    'erro' => $e->getMessage()
                ];
            }
        }

        $bar->finish();
        $this->newLine();
    }

    private function importarPedido($pedidoLegado): void
    {
        // Verificar duplicata
        if (!$this->option('force')) {
            $existe = Pedido::where('codigo_pedido', $pedidoLegado->codigo)->exists();
            if ($existe) {
                $this->skipped++;
                return;
            }
        }

        // Verificar convênio permitido
        $codGuia = $pedidoLegado->observacoes ?? null;
        if (!$this->convenioPermitido($codGuia)) {
            $this->skipped++;
            return;
        }

        // Mapear unidade via posto_cliente ou cod_origem
        $unidade = SoftlabMapping::getUnidadeByCodigo($pedidoLegado->unidade_codigo);
        if (!$unidade && isset($pedidoLegado->cod_origem)) {
            $unidade = SoftlabMapping::getUnidadeByCodigo($pedidoLegado->cod_origem);
        }

        // Mapear atendente via usu_pedido
        $atendente = null;
        if (isset($pedidoLegado->usu_pedido)) {
            $atendente = SoftlabMapping::getUserByCodigo($pedidoLegado->usu_pedido);
        }

        // Processar convênio a partir de cod_guia (observacoes)
        $convenioId = null;
        if (!empty($pedidoLegado->observacoes)) {
            $convenioId = $this->processarConvenio($pedidoLegado->observacoes);
        }

        // Criar ou atualizar pedido
        Pedido::updateOrCreate(
            ['codigo_pedido' => $pedidoLegado->codigo],
            [
                'codigo_paciente' => $pedidoLegado->codigo_paciente,
                'nome_paciente' => $pedidoLegado->nome_paciente,
                'data_atendimento' => Carbon::parse($pedidoLegado->data_atendimento)->format('Y-m-d'),
                'tipo_atendimento' => null,
                'convenio_id' => $convenioId,
                'unidade_id' => $unidade?->id,
                'atendente_id' => $atendente?->id,
                'status' => 'PENDENTE',
                'cod_guia' => $pedidoLegado->observacoes ?? null,
                'observacoes' => "Importado Softlab - Posto: {$pedidoLegado->unidade_codigo}" .
                    (isset($pedidoLegado->cod_origem) ? ", Origem: {$pedidoLegado->cod_origem}" : ""),
            ]
        );

        $this->imported++;
    }

    /**
     * Processa o convênio a partir do cod_guia
     * Cria automaticamente se não existir
     */
    private function processarConvenio(string $codGuia): ?int
    {
        // Limpar e normalizar o nome do convênio
        $nomeConvenio = trim(strtoupper($codGuia));

        // Remover caracteres especiais e números para melhor match
        $nomeConvenioLimpo = preg_replace('/[0-9\s\-\.]+/', '', $nomeConvenio);

        // Buscar convênio existente (case-insensitive)
        $convenio = Convenio::whereRaw('UPPER(nome) = ?', [$nomeConvenio])
            ->orWhereRaw('UPPER(codigo) = ?', [$nomeConvenio])
            ->first();

        // Se não encontrar exatamente, tentar buscar por similaridade
        if (!$convenio && strlen($nomeConvenioLimpo) >= 3) {
            $convenio = Convenio::whereRaw('UPPER(nome) LIKE ?', ["%{$nomeConvenioLimpo}%"])
                ->first();
        }

        // Se não existe, criar novo convênio
        if (!$convenio) {
            try {
                $convenio = Convenio::create([
                    'nome' => ucwords(strtolower($codGuia)),
                    'codigo' => $nomeConvenio,
                    'observacoes' => 'Convênio criado automaticamente durante importação',
                    'ativo' => true,
                ]);

                $this->info("\nNovo convênio cadastrado: {$convenio->nome}");
            } catch (\Exception $e) {
                Log::error('Erro ao criar convênio automaticamente', [
                    'cod_guia' => $codGuia,
                    'erro' => $e->getMessage()
                ]);
                return null;
            }
        }

        return $convenio->id;
    }

    private function convenioPermitido(?string $codGuia): bool
    {
        $permitidos = $this->getConveniosPermitidos();
        if (empty($permitidos)) {
            return true;
        }

        $codigo = $codGuia ? strtoupper(trim($codGuia)) : '';
        if ($codigo === '') {
            return false;
        }

        return in_array($codigo, $permitidos, true);
    }

    private function getConveniosPermitidos(): array
    {
        if ($this->conveniosPermitidos !== null) {
            return $this->conveniosPermitidos;
        }

        $raw = (string) ConfigHelper::get('importacao_convenios_permitidos', '');
        $parts = preg_split('/[\s,;]+/', $raw) ?: [];

        $this->conveniosPermitidos = array_values(array_filter(array_map(function ($item) {
            $item = strtoupper(trim($item));
            return $item === '' ? null : $item;
        }, $parts)));

        return $this->conveniosPermitidos;
    }

    private function finalizarJob(string $status, ?string $mensagemErro = null): void
    {
        $this->importJob->update([
            'status' => $status,
            'finalizado_em' => now(),
            'importados' => $this->imported,
            'ignorados' => $this->skipped,
            'erros' => count($this->errors),
            'detalhes_erros' => json_encode($this->errors),
            'mensagem_erro' => $mensagemErro,
        ]);
    }
}
