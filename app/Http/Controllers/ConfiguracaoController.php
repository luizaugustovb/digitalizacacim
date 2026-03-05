<?php

namespace App\Http\Controllers;

use App\Helpers\ConfigHelper;
use App\Models\Configuracao;
use App\Models\SoftlabMapping;
use App\Models\Unidade;
use App\Models\User;
use App\Models\PendenciaTipo;
use App\Services\SoftlabService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ConfiguracaoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'role:ADMIN',
        ];
    }

    /**
     * Exibe o formulário de configurações
     */
    public function index()
    {
        $configuracoes = Configuracao::all()->keyBy('chave');

        // Organizar por categorias
        $config = [
            'geral' => [
                'nome_sistema' => $configuracoes->get('nome_sistema')->valor ?? 'Sistema de Digitalização CIM',
                'email_notificacoes' => $configuracoes->get('email_notificacoes')->valor ?? '',
                'timezone' => $configuracoes->get('timezone')->valor ?? 'America/Sao_Paulo',
            ],
            'documentos' => [
                'dias_retencao_documentos' => $configuracoes->get('dias_retencao_documentos')->valor ?? '1825',
                'tamanho_maximo_arquivo' => $configuracoes->get('tamanho_maximo_arquivo')->valor ?? '10',
                'formatos_permitidos' => $configuracoes->get('formatos_permitidos')->valor ?? 'pdf,jpg,jpeg,png',
                'qualidade_compressao' => $configuracoes->get('qualidade_compressao')->valor ?? '85',
            ],
            'produtividade' => [
                'meta_diaria_atendente' => $configuracoes->get('meta_diaria_atendente')->valor ?? '50',
                'meta_diaria_gestor' => $configuracoes->get('meta_diaria_gestor')->valor ?? '100',
                'alerta_pedidos_antigos_dias' => $configuracoes->get('alerta_pedidos_antigos_dias')->valor ?? '7',
            ],
            'importacao' => [
                'importacao_automatica' => $configuracoes->get('importacao_automatica')->valor ?? 'false',
                'horario_importacao' => $configuracoes->get('horario_importacao')->valor ?? '02:00',
                'importacao_timeout' => $configuracoes->get('importacao_timeout')->valor ?? '60',
                'importacao_convenios_permitidos' => $configuracoes->get('importacao_convenios_permitidos')->valor ?? '',
                'sqlserver_host' => $configuracoes->get('sqlserver_host')->valor ?? '',
                'sqlserver_database' => $configuracoes->get('sqlserver_database')->valor ?? '',
                'sqlserver_username' => $configuracoes->get('sqlserver_username')->valor ?? '',
            ],
            'notificacoes' => [
                'notificar_pedido_devolvido' => $configuracoes->get('notificar_pedido_devolvido')->valor ?? 'true',
                'notificar_pedido_aprovado' => $configuracoes->get('notificar_pedido_aprovado')->valor ?? 'false',
                'notificar_meta_atingida' => $configuracoes->get('notificar_meta_atingida')->valor ?? 'true',
            ],
        ];

        return view('configuracoes.index', compact('config'));
    }

    /**
     * Atualiza as configurações
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // Geral
            'nome_sistema' => 'required|string|max:255',
            'email_notificacoes' => 'nullable|email',
            'timezone' => 'required|string',

            // Documentos
            'dias_retencao_documentos' => 'required|integer|min:365|max:3650',
            'tamanho_maximo_arquivo' => 'required|integer|min:1|max:50',
            'formatos_permitidos' => 'required|string',
            'qualidade_compressao' => 'required|integer|min:50|max:100',

            // Produtividade
            'meta_diaria_atendente' => 'required|integer|min:1',
            'meta_diaria_gestor' => 'required|integer|min:1',
            'alerta_pedidos_antigos_dias' => 'required|integer|min:1|max:30',

            // Importação
            'importacao_automatica' => 'boolean',
            'horario_importacao' => 'required|string',
            'importacao_timeout' => 'required|integer|min:10|max:300',
            'sqlserver_host' => 'nullable|string',
            'sqlserver_database' => 'nullable|string',
            'importacao_convenios_permitidos' => 'nullable|string',
            'sqlserver_username' => 'nullable|string',
            'sqlserver_password' => 'nullable|string',

            // Softlab
            'softlab_host' => 'nullable|string',
            'softlab_port' => 'nullable|string',
            'softlab_database' => 'nullable|string',
            'softlab_username' => 'nullable|string',
            'softlab_password' => 'nullable|string',

            // Notificações
            'notificar_pedido_devolvido' => 'boolean',
            'notificar_pedido_aprovado' => 'boolean',
            'notificar_meta_atingida' => 'boolean',
        ]);

        // Salvar configurações do Softlab no .env
        if ($request->has('softlab_host')) {
            $this->updateEnvFile([
                'SOFTLAB_DB_HOST' => $request->input('softlab_host', '127.0.0.1'),
                'SOFTLAB_DB_PORT' => $request->input('softlab_port', '3306'),
                'SOFTLAB_DB_DATABASE' => $request->input('softlab_database', 'BD_SOFTLAB_P00'),
                'SOFTLAB_DB_USERNAME' => $request->input('softlab_username', 'root'),
                'SOFTLAB_DB_PASSWORD' => $request->input('softlab_password', ''),
            ]);
        }

        // Atualizar ou criar cada configuração
        foreach ($validated as $chave => $valor) {
            // Converter booleanos para string
            if (is_bool($valor)) {
                $valor = $valor ? 'true' : 'false';
            }

            Configuracao::updateOrCreate(
                ['chave' => $chave],
                [
                    'valor' => $valor,
                    'tipo' => $this->getTipo($chave),
                    'descricao' => $this->getDescricao($chave),
                    'categoria' => $this->getCategoria($chave),
                ]
            );
        }

        ConfigHelper::clearCache();

        return redirect()->route('configuracoes.index')
            ->with('success', 'Configurações atualizadas com sucesso!');
    }

    /**
     * Atualiza variáveis no arquivo .env
     */
    private function updateEnvFile(array $data): void
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            return;
        }

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            // Escapar valores com espaços ou caracteres especiais
            $escapedValue = $this->escapeEnvValue($value);

            // Verificar se a chave já existe
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                // Substituir valor existente
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$escapedValue}",
                    $envContent
                );
            } else {
                // Adicionar nova chave no final
                $envContent .= "\n{$key}={$escapedValue}";
            }
        }

        file_put_contents($envPath, $envContent);
    }

    /**
     * Escapa valores para o arquivo .env
     */
    private function escapeEnvValue(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        // Se contém espaços ou caracteres especiais, colocar entre aspas
        if (preg_match('/\s/', $value) || preg_match('/[#;]/', $value)) {
            return '"' . addslashes($value) . '"';
        }

        return $value;
    }

    /**
     * Determina o tipo da configuração
     */
    private function getTipo(string $chave): string
    {
        $inteiros = [
            'dias_retencao_documentos',
            'tamanho_maximo_arquivo',
            'qualidade_compressao',
            'meta_diaria_atendente',
            'meta_diaria_gestor',
            'alerta_pedidos_antigos_dias',
            'importacao_timeout'
        ];

        $booleanos = [
            'importacao_automatica',
            'notificar_pedido_devolvido',
            'notificar_pedido_aprovado',
            'notificar_meta_atingida'
        ];

        if (in_array($chave, $inteiros)) {
            return 'integer';
        }

        if (in_array($chave, $booleanos)) {
            return 'boolean';
        }

        return 'string';
    }

    /**
     * Retorna a descrição da configuração
     */
    private function getDescricao(string $chave): string
    {
        $descricoes = [
            'nome_sistema' => 'Nome do sistema exibido na interface',
            'email_notificacoes' => 'E-mail para receber notificações do sistema',
            'timezone' => 'Fuso horário do sistema',
            'dias_retencao_documentos' => 'Quantidade de dias para manter documentos arquivados',
            'tamanho_maximo_arquivo' => 'Tamanho máximo de arquivo em MB',
            'formatos_permitidos' => 'Formatos de arquivo permitidos (separados por vírgula)',
            'qualidade_compressao' => 'Qualidade de compressão de imagens (0-100)',
            'meta_diaria_atendente' => 'Meta diária de pedidos para atendentes',
            'meta_diaria_gestor' => 'Meta diária de conferências para gestores',
            'alerta_pedidos_antigos_dias' => 'Dias para alertar sobre pedidos antigos',
            'importacao_automatica' => 'Habilitar importação automática de pedidos',
            'horario_importacao' => 'Horário para executar a importação automática',
            'sqlserver_host' => 'Endereço do servidor SQL Server',
            'sqlserver_database' => 'Nome do banco de dados',
            'sqlserver_username' => 'Usuário do banco de dados',
            'importacao_convenios_permitidos' => 'Códigos de convênios permitidos para importação (separados por vírgula)',
            'sqlserver_password' => 'Senha do banco de dados',
            'notificar_pedido_devolvido' => 'Notificar atendente quando pedido for devolvido',
            'notificar_pedido_aprovado' => 'Notificar atendente quando pedido for aprovado',
            'notificar_meta_atingida' => 'Notificar quando meta diária for atingida',
        ];

        return $descricoes[$chave] ?? '';
    }

    /**
     * Retorna a categoria da configuração
     */
    private function getCategoria(string $chave): string
    {
        $categorias = [
            'nome_sistema' => 'geral',
            'email_notificacoes' => 'geral',
            'timezone' => 'geral',
            'dias_retencao_documentos' => 'documentos',
            'tamanho_maximo_arquivo' => 'documentos',
            'formatos_permitidos' => 'documentos',
            'qualidade_compressao' => 'documentos',
            'meta_diaria_atendente' => 'produtividade',
            'meta_diaria_gestor' => 'produtividade',
            'alerta_pedidos_antigos_dias' => 'produtividade',
            'importacao_automatica' => 'importacao',
            'horario_importacao' => 'importacao',
            'sqlserver_host' => 'importacao',
            'sqlserver_database' => 'importacao',
            'sqlserver_username' => 'importacao',
            'sqlserver_password' => 'importacao',
            'importacao_convenios_permitidos' => 'importacao',
            'notificar_pedido_devolvido' => 'notificacoes',
            'notificar_pedido_aprovado' => 'notificacoes',
            'notificar_meta_atingida' => 'notificacoes',
        ];

        return $categorias[$chave] ?? 'geral';
    }

    /**
     * Exibe a página de mapeamentos Softlab
     */
    public function softlabMappings()
    {
        $mappings = SoftlabMapping::with(['unidade', 'user'])->get()->groupBy('tipo');
        $unidades = Unidade::where('ativo', true)->orderBy('nome')->get();
        $usuarios = User::where('ativo', true)->whereIn('role', ['ATENDENTE', 'GESTOR'])->orderBy('nome')->get();

        return view('configuracoes.softlab-mappings', compact('mappings', 'unidades', 'usuarios'));
    }

    /**
     * Criar novo mapeamento Softlab
     */
    public function storeSoftlabMapping(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:unidade,usuario',
            'cod_softlab' => 'required|string',
            'nome_softlab' => 'nullable|string',
            'unidade_id' => 'nullable|exists:unidades,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        // Validar que unidade_id ou user_id esteja preenchido conforme o tipo
        if ($validated['tipo'] === 'unidade' && empty($validated['unidade_id'])) {
            return back()->with('error', 'Selecione uma unidade');
        }
        if ($validated['tipo'] === 'usuario' && empty($validated['user_id'])) {
            return back()->with('error', 'Selecione um usuário');
        }

        SoftlabMapping::create($validated);

        return back()->with('success', 'Mapeamento criado com sucesso');
    }

    /**
     * Excluir mapeamento Softlab
     */
    public function deleteSoftlabMapping(SoftlabMapping $mapping)
    {
        $mapping->delete();
        return back()->with('success', 'Mapeamento excluído com sucesso');
    }

    /**
     * Testar conexão com Softlab
     */
    public function testarConexaoSoftlab(SoftlabService $service)
    {
        $resultado = $service->testarConexao();

        if ($resultado['success']) {
            $details = $resultado['details'] ?? [];
            $msg = $resultado['message'];

            if (!empty($details['warnings'])) {
                $msg .= ' (Avisos: ' . implode(', ', $details['warnings']) . ')';
            }

            return back()->with('success', $msg);
        }

        // Erro de conexão - montar mensagem detalhada
        $errorMsg = $resultado['message'];
        if (isset($resultado['diagnostico'])) {
            $diag = $resultado['diagnostico'];
            $errorMsg .= "\n\n🔍 DIAGNÓSTICO:\n";
            $errorMsg .= "Problema: " . $diag['problema'] . "\n\n";
            $errorMsg .= "Possíveis causas:\n";
            foreach ($diag['causas'] as $causa) {
                $errorMsg .= "• $causa\n";
            }
            $errorMsg .= "\nSoluções sugeridas:\n";
            foreach ($diag['solucoes'] as $solucao) {
                $errorMsg .= "• $solucao\n";
            }
        }

        return back()
            ->with('error', $errorMsg)
            ->with('error_details', $resultado);
    }

    /**
     * Buscar pedidos do Softlab
     */
    public function buscarPedidosSoftlab(SoftlabService $service)
    {
        $resultado = $service->buscarPedidosDoDia();

        if ($resultado['success']) {
            return response()->json($resultado);
        }

        return response()->json($resultado, 500);
    }

    /**
     * Listar tipos de pendências
     */
    public function pendenciasIndex()
    {
        $pendenciasTipos = PendenciaTipo::orderBy('peso')->orderBy('nome')->get();
        return view('configuracoes.pendencias.index', compact('pendenciasTipos'));
    }

    /**
     * Criar novo tipo de pendência
     */
    public function pendenciasStore(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:pendencias_tipo,nome',
            'descricao' => 'nullable|string|max:500',
            'peso' => 'nullable|integer|min:0|max:999',
            'ativo' => 'boolean',
        ]);

        $validated['ativo'] = $request->has('ativo');
        $validated['peso'] = $validated['peso'] ?? 0;

        PendenciaTipo::create($validated);

        return redirect()
            ->route('configuracoes.pendencias.index')
            ->with('success', 'Tipo de pendência criado com sucesso!');
    }

    /**
     * Atualizar tipo de pendência
     */
    public function pendenciasUpdate(Request $request, PendenciaTipo $pendenciaTipo)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:pendencias_tipo,nome,' . $pendenciaTipo->id,
            'descricao' => 'nullable|string|max:500',
            'peso' => 'nullable|integer|min:0|max:999',
            'ativo' => 'boolean',
        ]);

        $validated['ativo'] = $request->has('ativo');
        $validated['peso'] = $validated['peso'] ?? 0;

        $pendenciaTipo->update($validated);

        return redirect()
            ->route('configuracoes.pendencias.index')
            ->with('success', 'Tipo de pendência atualizado com sucesso!');
    }

    /**
     * Ativar/Desativar tipo de pendência
     */
    public function pendenciasToggle(PendenciaTipo $pendenciaTipo)
    {
        $pendenciaTipo->update(['ativo' => !$pendenciaTipo->ativo]);

        $status = $pendenciaTipo->ativo ? 'ativado' : 'desativado';
        return redirect()
            ->route('configuracoes.pendencias.index')
            ->with('success', "Tipo de pendência {$status} com sucesso!");
    }

    /**
     * Excluir tipo de pendência
     */
    public function pendenciasDestroy(PendenciaTipo $pendenciaTipo)
    {
        // Verificar se há pendências vinculadas
        if ($pendenciaTipo->pedidoPendencias()->count() > 0) {
            return redirect()
                ->route('configuracoes.pendencias.index')
                ->with('error', 'Não é possível excluir este tipo de pendência pois existem registros vinculados a ele.');
        }

        $pendenciaTipo->delete();

        return redirect()
            ->route('configuracoes.pendencias.index')
            ->with('success', 'Tipo de pendência excluído com sucesso!');
    }
}
