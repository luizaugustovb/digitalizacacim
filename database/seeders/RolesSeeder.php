<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Criar roles
        $admin = Role::firstOrCreate(
            ['nome' => 'ADMIN'],
            ['descricao' => 'Administrador do Sistema']
        );

        $gestor = Role::firstOrCreate(
            ['nome' => 'GESTOR'],
            ['descricao' => 'Gestor - Conferência de Guias']
        );

        $atendente = Role::firstOrCreate(
            ['nome' => 'ATENDENTE'],
            ['descricao' => 'Atendente - Escaneamento e Envio']
        );

        // Atribuir todas as permissões ao Admin
        $todasPermissoes = Permission::all();
        $admin->permissions()->sync($todasPermissoes->pluck('id'));

        // Permissões do Gestor
        $permissoesGestor = Permission::whereIn('chave', [
            'ver_pendentes',
            'ver_enviados',
            'ver_aprovados',
            'ver_todos_pedidos',
            'devolver_guias',
            'aprovar_guias',
            'ver_relatorios',
            'baixar_arquivos',
            'filtrar_por_lote',
            'ver_dashboard',
            'ver_timeline',
        ])->get();
        $gestor->permissions()->sync($permissoesGestor->pluck('id'));

        // Permissões do Atendente
        $permissoesAtendente = Permission::whereIn('chave', [
            'ver_pendentes',
            'ver_enviados',
            'escanear_anexar',
            'cadastrar_pedido_manual',
            'baixar_arquivos',
            'ver_dashboard',
            'ver_timeline',
        ])->get();
        $atendente->permissions()->sync($permissoesAtendente->pluck('id'));

        $this->command->info('Roles e permissões configuradas!');
    }
}
