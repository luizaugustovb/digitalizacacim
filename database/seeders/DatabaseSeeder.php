<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionsSeeder::class,
            RolesSeeder::class,
            ConveniosUnidadesSeeder::class,
            AdminUserSeeder::class,
            GestorUserSeeder::class,
            PendenciasTipoSeeder::class,
            ConfiguracoesSeeder::class,
        ]);
    }
}
