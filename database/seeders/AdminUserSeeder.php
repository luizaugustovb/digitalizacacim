<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nome' => 'Administrador',
            'email' => 'contato@luizaugusto.me',
            'password' => Hash::make('123'),
            'role' => 'ADMIN',
            'ativo' => true,
            'forcar_troca_senha' => true,
        ]);
    }
}
