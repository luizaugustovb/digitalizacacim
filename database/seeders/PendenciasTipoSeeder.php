<?php

namespace Database\Seeders;

use App\Models\PendenciaTipo;
use Illuminate\Database\Seeder;

class PendenciasTipoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pendencias = [
            ['nome' => 'Senha de Autorização', 'descricao' => 'Senha de autorização ausente ou incorreta', 'cor' => 'vermelho', 'peso' => 5],
            ['nome' => 'CRM do Médico', 'descricao' => 'CRM do médico ausente ou inválido', 'cor' => 'vermelho', 'peso' => 5],
            ['nome' => 'Número da Guia', 'descricao' => 'Número da guia ausente ou incorreto', 'cor' => 'vermelho', 'peso' => 3],
            ['nome' => 'Validade da Senha', 'descricao' => 'Senha de autorização vencida', 'cor' => 'vermelho', 'peso' => 5],
            ['nome' => 'Número da Carteirinha', 'descricao' => 'Número da carteirinha ausente ou incorreto', 'cor' => 'amarelo', 'peso' => 2],
            ['nome' => 'Data Incorreta', 'descricao' => 'Data de atendimento incorreta', 'cor' => 'amarelo', 'peso' => 2],
            ['nome' => 'Documento Ilegível', 'descricao' => 'Documento escaneado com má qualidade', 'cor' => 'amarelo', 'peso' => 3],
            ['nome' => 'Documento Incompleto', 'descricao' => 'Documento anexado está incompleto', 'cor' => 'vermelho', 'peso' => 4],
        ];

        foreach ($pendencias as $pendencia) {
            PendenciaTipo::create($pendencia);
        }
    }
}
