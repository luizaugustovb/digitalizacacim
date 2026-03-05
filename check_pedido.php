<?php

use App\Models\Pedido;

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$pedido = Pedido::where('codigo_pedido', '275110')->first();

if ($pedido) {
    echo "=== PEDIDO 275110 ===\n";
    echo "ID: {$pedido->id}\n";
    echo "Código: {$pedido->codigo_pedido}\n";
    echo "Status: {$pedido->status}\n";
    echo "Convênio ID: " . ($pedido->convenio_id ?? 'NULL') . "\n";
    echo "Atendente ID: " . ($pedido->atendente_id ?? 'NULL') . "\n";
    echo "Data Envio: " . ($pedido->data_envio ? $pedido->data_envio->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "\n=== RELACIONAMENTOS ===\n";
    echo "Convênio: " . ($pedido->convenio?->nome ?? 'NULL') . "\n";
    echo "Atendente: " . ($pedido->atendente?->nome ?? 'NULL') . "\n";
} else {
    echo "Pedido não encontrado!\n";
}
