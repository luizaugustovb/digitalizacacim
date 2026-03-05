<?php

namespace Database\Seeders;

use App\Models\Convenio;
use Illuminate\Database\Seeder;

class ConveniosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $convenios = [
            ['nome' => 'Unimed', 'codigo' => 'UNIMED', 'observacoes' => 'Convênio Unimed', 'ativo' => true],
            ['nome' => 'Bradesco Saúde', 'codigo' => 'BRADESCO', 'observacoes' => 'Convênio Bradesco Saúde', 'ativo' => true],
            ['nome' => 'Amil', 'codigo' => 'AMIL', 'observacoes' => 'Convênio Amil', 'ativo' => true],
            ['nome' => 'SulAmérica', 'codigo' => 'SULAMERICA', 'observacoes' => 'Convênio SulAmérica', 'ativo' => true],
            ['nome' => 'NotreDame Intermédica', 'codigo' => 'NOTREDAME', 'observacoes' => 'Convênio NotreDame Intermédica', 'ativo' => true],
            ['nome' => 'Particular', 'codigo' => 'PARTICULAR', 'observacoes' => 'Atendimento particular sem convênio', 'ativo' => true],
        ];

        foreach ($convenios as $convenio) {
            Convenio::firstOrCreate(
                ['codigo' => $convenio['codigo']], // Busca por código
                $convenio // Cria com todos os dados
            );
        }

        $this->command->info('Convênios criados com sucesso!');
    }
}
