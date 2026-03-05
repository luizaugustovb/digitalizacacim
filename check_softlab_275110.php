<?php

use Illuminate\Support\Facades\DB;

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DADOS DO PEDIDO 275110 NO SOFTLAB ===\n\n";

try {
    $pedido = DB::connection('softlab')->select("
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
        WHERE p.codigo_pedido = '275110'
    ");

    if (empty($pedido)) {
        echo "Pedido não encontrado no Softlab!\n";
    } else {
        $p = $pedido[0];
        echo "Código Pedido: {$p->codigo_pedido}\n";
        echo "Código Cliente: {$p->cod_cliente}\n";
        echo "Nome Cliente: {$p->nome_cliente}\n";
        echo "Código Guia (Convênio): " . ($p->cod_guia ?? 'NULL') . "\n";
        echo "Usuário Pedido (Atendente): " . ($p->usu_pedido ?? 'NULL') . "\n";
        echo "Posto Cliente: {$p->posto_cliente}\n";
        echo "Código Origem: {$p->cod_origem}\n";
        echo "Data/Hora Atendimento: {$p->datahora_atendimento}\n";

        echo "\n=== MAPEAMENTOS NECESSÁRIOS ===\n";
        echo "1. Criar mapeamento de convênio: cod_guia = '{$p->cod_guia}'\n";
        echo "2. Criar mapeamento de usuário: usu_pedido = '{$p->usu_pedido}'\n";
        echo "3. Criar mapeamento de unidade: posto_cliente = '{$p->posto_cliente}' ou cod_origem = '{$p->cod_origem}'\n";
    }
} catch (\Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
