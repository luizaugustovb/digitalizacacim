<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Convenio;
use Illuminate\Database\Seeder;

class GestorUserSeeder extends Seeder
{
    public function run(): void
    {
        // Criar usuário gestor de teste
        $gestor = User::firstOrCreate(
            ['email' => 'gestor@teste.com'],
            [
                'nome' => 'Gestor Teste',
                'password' => bcrypt('123'),
                'role' => 'GESTOR',
                'ativo' => true,
            ]
        );

        // Associar a todos os convênios
        $convenios = Convenio::all();
        if ($convenios->count() > 0) {
            $gestor->convenios()->sync($convenios->pluck('id'));
        }

        $this->command->info('Usuário Gestor criado: gestor@teste.com / 123');
    }
}
