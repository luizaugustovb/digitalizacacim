<?php

namespace Database\Seeders;

use App\Models\Convenio;
use App\Models\Unidade;
use Illuminate\Database\Seeder;

class ConveniosUnidadesSeeder extends Seeder
{
    public function run(): void
    {
        // Criar Convênios
        $convenios = [
            ['nome' => 'Unimed', 'codigo' => 'UNMD', 'ativo' => true],
            ['nome' => 'Bradesco Saúde', 'codigo' => 'BRAD', 'ativo' => true],
            ['nome' => 'Amil', 'codigo' => 'AMIL', 'ativo' => true],
            ['nome' => 'SulAmérica', 'codigo' => 'SULA', 'ativo' => true],
            ['nome' => 'Particular', 'codigo' => 'PART', 'ativo' => true],
        ];

        foreach ($convenios as $convenio) {
            Convenio::firstOrCreate(
                ['codigo' => $convenio['codigo']],
                $convenio
            );
        }

        // Criar Unidades
        $unidades = [
            ['nome' => 'Unidade Central', 'codigo' => 'UC01', 'endereco' => 'Rua Principal, 100', 'ativo' => true],
            ['nome' => 'Unidade Norte', 'codigo' => 'UN01', 'endereco' => 'Av. Norte, 200', 'ativo' => true],
            ['nome' => 'Unidade Sul', 'codigo' => 'US01', 'endereco' => 'Rua Sul, 300', 'ativo' => true],
        ];

        foreach ($unidades as $unidade) {
            Unidade::firstOrCreate(
                ['codigo' => $unidade['codigo']],
                $unidade
            );
        }

        $this->command->info('Convênios e Unidades criados com sucesso!');
    }
}
