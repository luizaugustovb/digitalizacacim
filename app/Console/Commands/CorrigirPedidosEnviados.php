<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\User;

class CorrigirPedidosEnviados extends Command
{
    protected $signature = 'pedidos:corrigir-enviados';
    protected $description = 'Corrige pedidos enviados sem atendente_id ou data_envio';

    public function handle()
    {
        $this->info('Buscando pedidos enviados sem atendente ou data de envio...');

        $pedidos = Pedido::where('status', 'ENVIADO')
            ->where(function ($query) {
                $query->whereNull('atendente_id')
                    ->orWhereNull('data_envio');
            })
            ->get();

        if ($pedidos->isEmpty()) {
            $this->info('Nenhum pedido para corrigir.');
            return 0;
        }

        $this->info("Encontrados {$pedidos->count()} pedido(s) para corrigir.");

        // Pegar primeiro atendente disponível
        $atendente = User::where('role', 'ATENDENTE')->where('ativo', true)->first();

        if (!$atendente) {
            $this->error('Nenhum atendente disponível no sistema!');
            return 1;
        }

        foreach ($pedidos as $pedido) {
            $updates = [];

            if (!$pedido->atendente_id) {
                $updates['atendente_id'] = $atendente->id;
                $this->line("  - Pedido #{$pedido->codigo_pedido}: atendente definido como {$atendente->nome}");
            }

            if (!$pedido->data_envio) {
                $updates['data_envio'] = $pedido->updated_at ?? now();
                $this->line("  - Pedido #{$pedido->codigo_pedido}: data_envio definida");
            }

            if (!empty($updates)) {
                $pedido->update($updates);
            }
        }

        $this->info("\n✓ Pedidos corrigidos com sucesso!");
        return 0;
    }
}
