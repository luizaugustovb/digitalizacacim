<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['nome' => 'Ver Pendentes', 'chave' => 'ver_pendentes', 'descricao' => 'Visualizar pedidos pendentes', 'grupo' => 'Pedidos'],
            ['nome' => 'Ver Enviados', 'chave' => 'ver_enviados', 'descricao' => 'Visualizar pedidos enviados', 'grupo' => 'Pedidos'],
            ['nome' => 'Ver Aprovados', 'chave' => 'ver_aprovados', 'descricao' => 'Visualizar pedidos aprovados', 'grupo' => 'Pedidos'],
            ['nome' => 'Escanear/Anexar', 'chave' => 'escanear_anexar', 'descricao' => 'Escanear ou anexar documentos', 'grupo' => 'Documentos'],
            ['nome' => 'Cadastrar Pedido Manual', 'chave' => 'cadastrar_pedido_manual', 'descricao' => 'Cadastrar pedido não cadastrado', 'grupo' => 'Pedidos'],
            ['nome' => 'Ver Relatórios', 'chave' => 'ver_relatorios', 'descricao' => 'Visualizar relatórios do sistema', 'grupo' => 'Relatórios'],
            ['nome' => 'Baixar Arquivos', 'chave' => 'baixar_arquivos', 'descricao' => 'Fazer download de documentos', 'grupo' => 'Documentos'],
            ['nome' => 'Remover Pedido', 'chave' => 'remover_pedido', 'descricao' => 'Remover pedidos do sistema', 'grupo' => 'Pedidos'],
            ['nome' => 'Devolver Guias', 'chave' => 'devolver_guias', 'descricao' => 'Devolver guias para correção', 'grupo' => 'Conferência'],
            ['nome' => 'Aprovar Guias', 'chave' => 'aprovar_guias', 'descricao' => 'Aprovar guias conferidas', 'grupo' => 'Conferência'],
            ['nome' => 'Filtrar por Lote', 'chave' => 'filtrar_por_lote', 'descricao' => 'Aplicar filtros por lote', 'grupo' => 'Filtros'],
            ['nome' => 'Ver Dashboard', 'chave' => 'ver_dashboard', 'descricao' => 'Visualizar dashboard de KPIs', 'grupo' => 'Dashboard'],
            ['nome' => 'Ver Configurações', 'chave' => 'ver_configuracoes', 'descricao' => 'Visualizar configurações do sistema', 'grupo' => 'Configurações'],
            ['nome' => 'Gerenciar Usuários', 'chave' => 'gerenciar_usuarios', 'descricao' => 'Criar e editar usuários', 'grupo' => 'Usuários'],
            ['nome' => 'Gerenciar Permissões', 'chave' => 'gerenciar_permissoes', 'descricao' => 'Gerenciar permissões e roles', 'grupo' => 'Permissões'],
            ['nome' => 'Gerenciar Convênios', 'chave' => 'gerenciar_convenios', 'descricao' => 'Gerenciar convênios', 'grupo' => 'Configurações'],
            ['nome' => 'Gerenciar Pendências', 'chave' => 'gerenciar_pendencias', 'descricao' => 'Gerenciar tipos de pendências', 'grupo' => 'Configurações'],
            ['nome' => 'Ver Timeline', 'chave' => 'ver_timeline', 'descricao' => 'Ver linha do tempo de pedidos', 'grupo' => 'Auditoria'],
            ['nome' => 'Ver Todos Pedidos', 'chave' => 'ver_todos_pedidos', 'descricao' => 'Ver pedidos de todos os usuários', 'grupo' => 'Pedidos'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
