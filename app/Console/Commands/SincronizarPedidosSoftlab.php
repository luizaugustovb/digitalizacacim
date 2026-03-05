<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\SoftlabMapping;
use Illuminate\Support\Facades\DB;

class SincronizarPedidosSoftlab extends Command
{
    protected $signature = 'softlab:sincronizar-pedidos {codigo_pedido?}';
    protected $description = 'Sincroniza dados de pedidos com informações do Softlab (convênio e atendente)';

    public function handle()
    {
        $codigoPedido = $this->argument('codigo_pedido');

        if ($codigoPedido) {
            $pedidos = Pedido::where('codigo_pedido', $codigoPedido)->get();
            if ($pedidos->isEmpty()) {
                $this->error("Pedido {$codigoPedido} não encontrado!");
                return 1;
            }
        } else {
            $pedidos = Pedido::whereNull('convenio_id')
                ->orWhereNull('atendente_id')
                ->get();
        }

        if ($pedidos->isEmpty()) {
            $this->info('Nenhum pedido para sincronizar.');
            return 0;
        }

        $this->info("Encontrados {$pedidos->count()} pedido(s) para sincronizar.");

        $bar = $this->output->createProgressBar($pedidos->count());
        $bar->start();

        $atualizado = 0;
        $erros = 0;

        foreach ($pedidos as $pedido) {
            try {
                // Buscar pedido no Softlab
                $pedidoSoftlab = DB::connection('softlab')->select("
                    SELECT 
                        p.codigo_pedido,
                        p.cod_guia,
                        p.usu_pedido
                    FROM pedido p
                    WHERE p.codigo_pedido = ?
                ", [$pedido->codigo_pedido]);

                if (empty($pedidoSoftlab)) {
                    $this->newLine();
                    $this->warn("Pedido {$pedido->codigo_pedido} não encontrado no Softlab");
                    $erros++;
                    $bar->advance();
                    continue;
                }

                $dados = $pedidoSoftlab[0];
                $updates = [];

                // Mapear convênio se não existir
                if (!$pedido->convenio_id && $dados->cod_guia) {
                    $convenio = SoftlabMapping::getConvenioByCodigo($dados->cod_guia);
                    if ($convenio) {
                        $updates['convenio_id'] = $convenio->id;
                    }
                }

                // Mapear atendente se não existir
                if (!$pedido->atendente_id && $dados->usu_pedido) {
                    $atendente = SoftlabMapping::getUserByCodigo($dados->usu_pedido);
                    if ($atendente) {
                        $updates['atendente_id'] = $atendente->id;
                    }
                }

                // Salvar cod_guia se não existir
                if (!$pedido->cod_guia && $dados->cod_guia) {
                    $updates['cod_guia'] = $dados->cod_guia;
                }

                if (!empty($updates)) {
                    $pedido->update($updates);
                    $atualizado++;
                }
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Erro no pedido {$pedido->codigo_pedido}: " . $e->getMessage());
                $erros++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✓ Sincronização concluída!");
        $this->info("  - {$atualizado} pedido(s) atualizado(s)");
        if ($erros > 0) {
            $this->warn("  - {$erros} erro(s)");
        }

        return 0;
    }
}
