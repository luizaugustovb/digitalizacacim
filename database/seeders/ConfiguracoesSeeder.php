<?php

namespace Database\Seeders;

use App\Models\Configuracao;
use Illuminate\Database\Seeder;

class ConfiguracoesSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            // Geral
            ['chave' => 'nome_sistema', 'valor' => 'Sistema de Digitalização CIM', 'tipo' => 'string', 'descricao' => 'Nome do sistema exibido na interface', 'categoria' => 'geral'],
            ['chave' => 'email_notificacoes', 'valor' => 'contato@luizaugusto.me', 'tipo' => 'string', 'descricao' => 'E-mail para receber notificações do sistema', 'categoria' => 'geral'],
            ['chave' => 'timezone', 'valor' => 'America/Sao_Paulo', 'tipo' => 'string', 'descricao' => 'Fuso horário do sistema', 'categoria' => 'geral'],

            // Documentos
            ['chave' => 'dias_retencao_documentos', 'valor' => '1825', 'tipo' => 'integer', 'descricao' => 'Quantidade de dias para manter documentos (5 anos)', 'categoria' => 'documentos'],
            ['chave' => 'tamanho_maximo_arquivo', 'valor' => '10', 'tipo' => 'integer', 'descricao' => 'Tamanho máximo de arquivo em MB', 'categoria' => 'documentos'],
            ['chave' => 'formatos_permitidos', 'valor' => 'pdf,jpg,jpeg,png', 'tipo' => 'string', 'descricao' => 'Formatos de arquivo permitidos', 'categoria' => 'documentos'],
            ['chave' => 'qualidade_compressao', 'valor' => '85', 'tipo' => 'integer', 'descricao' => 'Qualidade de compressão de imagens', 'categoria' => 'documentos'],

            // Produtividade
            ['chave' => 'meta_diaria_atendente', 'valor' => '50', 'tipo' => 'integer', 'descricao' => 'Meta diária de pedidos para atendentes', 'categoria' => 'produtividade'],
            ['chave' => 'meta_diaria_gestor', 'valor' => '100', 'tipo' => 'integer', 'descricao' => 'Meta diária de conferências para gestores', 'categoria' => 'produtividade'],
            ['chave' => 'alerta_pedidos_antigos_dias', 'valor' => '7', 'tipo' => 'integer', 'descricao' => 'Dias para alertar sobre pedidos antigos', 'categoria' => 'produtividade'],

            // Importação
            ['chave' => 'importacao_automatica', 'valor' => 'false', 'tipo' => 'boolean', 'descricao' => 'Habilitar importação automática de pedidos', 'categoria' => 'importacao'],
            ['chave' => 'horario_importacao', 'valor' => '02:00', 'tipo' => 'string', 'descricao' => 'Horário para executar a importação automática', 'categoria' => 'importacao'],
            ['chave' => 'importacao_timeout', 'valor' => '60', 'tipo' => 'integer', 'descricao' => 'Timeout de conexão SQL Server em segundos', 'categoria' => 'importacao'],
            ['chave' => 'sqlserver_host', 'valor' => '', 'tipo' => 'string', 'descricao' => 'Endereço do servidor SQL Server', 'categoria' => 'importacao'],
            ['chave' => 'sqlserver_database', 'valor' => '', 'tipo' => 'string', 'descricao' => 'Nome do banco de dados', 'categoria' => 'importacao'],
            ['chave' => 'sqlserver_username', 'valor' => '', 'tipo' => 'string', 'descricao' => 'Usuário do banco de dados', 'categoria' => 'importacao'],

            // Notificações
            ['chave' => 'notificar_pedido_devolvido', 'valor' => 'true', 'tipo' => 'boolean', 'descricao' => 'Notificar atendente quando pedido for devolvido', 'categoria' => 'notificacoes'],
            ['chave' => 'notificar_pedido_aprovado', 'valor' => 'false', 'tipo' => 'boolean', 'descricao' => 'Notificar atendente quando pedido for aprovado', 'categoria' => 'notificacoes'],
            ['chave' => 'notificar_meta_atingida', 'valor' => 'true', 'tipo' => 'boolean', 'descricao' => 'Notificar quando meta diária for atingida', 'categoria' => 'notificacoes'],
        ];

        foreach ($configs as $config) {
            Configuracao::firstOrCreate(
                ['chave' => $config['chave']],
                $config
            );
        }

        $this->command->info('Configurações padrão criadas com sucesso!');
    }
}
