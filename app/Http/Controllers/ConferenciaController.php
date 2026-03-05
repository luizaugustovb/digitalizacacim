<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Convenio;
use App\Models\Unidade;
use App\Models\User;
use App\Models\TimelineLog;
use App\Models\PendenciaTipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ConferenciaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'role:GESTOR,ADMIN',
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Query base - apenas pedidos enviados
        $query = Pedido::with(['convenio', 'unidade', 'atendente', 'documentos', 'pendencias.tipo'])
            ->where('status', 'ENVIADO')
            ->orderBy('data_envio', 'asc'); // Mais antigos primeiro

        // Filtros
        if ($request->filled('convenio_id')) {
            $query->where('convenio_id', $request->convenio_id);
        }

        if ($request->filled('unidade_id')) {
            $query->where('unidade_id', $request->unidade_id);
        }

        if ($request->filled('atendente_id')) {
            $query->where('atendente_id', $request->atendente_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_envio', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_envio', '<=', $request->data_fim);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_pedido', 'like', "%{$search}%")
                    ->orWhere('codigo_paciente', 'like', "%{$search}%")
                    ->orWhere('nome_paciente', 'like', "%{$search}%");
            });
        }

        // Paginação
        $pedidos = $query->paginate(15)->withQueryString();

        // Dados para filtros
        $convenios = Convenio::where('ativo', true)->orderBy('nome')->get();
        $unidades = Unidade::where('ativo', true)->orderBy('nome')->get();
        $atendentes = User::where('role', 'ATENDENTE')->where('ativo', true)->orderBy('nome')->get();

        // Estatísticas
        $stats = [
            'total' => Pedido::where('status', 'ENVIADO')->count(),
            'hoje' => Pedido::where('status', 'ENVIADO')->whereDate('data_envio', today())->count(),
            'pendentes_7dias' => Pedido::where('status', 'ENVIADO')
                ->where('data_envio', '<=', now()->subDays(7))
                ->count(),
        ];

        return view('conferencia.index', compact('pedidos', 'convenios', 'unidades', 'atendentes', 'stats'));
    }

    public function show(Pedido $pedido)
    {
        // Verificar se é pedido enviado (case-insensitive)
        if (strtoupper($pedido->status) !== 'ENVIADO') {
            return redirect()->route('conferencia.index')
                ->with('error', 'Apenas pedidos com status ENVIADO podem ser conferidos.');
        }

        $pedido->load([
            'convenio',
            'unidade',
            'atendente',
            'documentos.uploadPor',
            'pendencias.tipo',
            'timelineLogs.user'
        ]);

        // Log de visualização
        TimelineLog::create([
            'pedido_id' => $pedido->id,
            'user_id' => auth()->id(),
            'acao' => 'CONFERENCIA_VISUALIZADA',
            'descricao' => 'Pedido visualizado na conferência',
            'detalhes' => 'Gestor: ' . auth()->user()->nome,
        ]);

        $pendenciasTipos = PendenciaTipo::where('ativo', true)->orderBy('nome')->get();

        return view('conferencia.show', compact('pedido', 'pendenciasTipos'));
    }

    public function aprovarLote(Request $request)
    {
        $request->validate([
            'pedidos' => 'required|array|min:1',
            'pedidos.*' => 'exists:pedidos,id',
        ], [
            'pedidos.required' => 'Selecione pelo menos um pedido para aprovar.',
            'pedidos.min' => 'Selecione pelo menos um pedido para aprovar.',
        ]);

        DB::beginTransaction();
        try {
            $aprovados = 0;
            $erros = [];

            foreach ($request->pedidos as $pedidoId) {
                $pedido = Pedido::find($pedidoId);

                if (!$pedido) {
                    $erros[] = "Pedido #{$pedidoId} não encontrado.";
                    continue;
                }

                if (strtoupper($pedido->status) !== 'ENVIADO') {
                    $erros[] = "Pedido #{$pedido->codigo_pedido} não está com status ENVIADO (atual: {$pedido->status}).";
                    continue;
                }

                $pedido->update([
                    'status' => 'APROVADO',
                    'data_aprovacao' => now(),
                    'gestor_id' => auth()->id(),
                ]);

                // Resolver pendências abertas
                $pedido->pendencias()->where('resolvida', false)->update([
                    'resolvida' => true,
                    'resolvido_por' => auth()->id(),
                    'resolvido_em' => now(),
                ]);

                // Log
                TimelineLog::create([
                    'pedido_id' => $pedido->id,
                    'user_id' => auth()->id(),
                    'acao' => 'PEDIDO_APROVADO',
                    'descricao' => 'Pedido aprovado em lote',
                    'detalhes' => 'Aprovação em lote pelo gestor',
                ]);

                $aprovados++;
            }

            DB::commit();

            $mensagem = "✅ {$aprovados} pedido(s) aprovado(s) com sucesso!";
            if (count($erros) > 0) {
                $mensagem .= " ⚠️ " . implode(' ', $erros);
            }

            return redirect()->route('conferencia.index')->with('success', $mensagem);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao aprovar pedidos: ' . $e->getMessage());
        }
    }

    public function devolverLote(Request $request)
    {
        $request->validate([
            'pedidos' => 'required|array|min:1',
            'pedidos.*' => 'exists:pedidos,id',
            'motivo_devolucao' => 'required|string',
            'pendencias' => 'required|array|min:1',
        ], [
            'pedidos.required' => 'Selecione pelo menos um pedido para devolver.',
            'pedidos.min' => 'Selecione pelo menos um pedido para devolver.',
            'motivo_devolucao.required' => 'O motivo da devolução é obrigatório.',
            'pendencias.required' => 'Selecione pelo menos uma pendência.',
            'pendencias.min' => 'Selecione pelo menos uma pendência.',
        ]);

        DB::beginTransaction();
        try {
            $devolvidos = 0;
            $erros = [];

            foreach ($request->pedidos as $pedidoId) {
                $pedido = Pedido::find($pedidoId);

                if (!$pedido) {
                    $erros[] = "Pedido #{$pedidoId} não encontrado.";
                    continue;
                }

                if (strtoupper($pedido->status) !== 'ENVIADO') {
                    $erros[] = "Pedido #{$pedido->codigo_pedido} não está com status ENVIADO (atual: {$pedido->status}).";
                    continue;
                }

                $pedido->update([
                    'status' => 'DEVOLVIDO',
                    'data_devolucao' => now(),
                    'motivo_devolucao' => $request->motivo_devolucao,
                    'gestor_id' => auth()->id(),
                ]);

                // Criar pendências
                foreach ($request->pendencias as $pendenciaId) {
                    $pedido->pendencias()->create([
                        'pendencia_tipo_id' => $pendenciaId,
                        'resolvida' => false,
                        'observacao' => $request->motivo_devolucao,
                        'criado_por' => auth()->id(),
                    ]);
                }

                // Log
                TimelineLog::create([
                    'pedido_id' => $pedido->id,
                    'user_id' => auth()->id(),
                    'acao' => 'PEDIDO_DEVOLVIDO',
                    'descricao' => 'Pedido devolvido em lote',
                    'detalhes' => $request->motivo_devolucao,
                ]);

                $devolvidos++;
            }

            DB::commit();

            $mensagem = "✅ {$devolvidos} pedido(s) devolvido(s) com sucesso!";
            if (count($erros) > 0) {
                $mensagem .= " ⚠️ " . implode(' ', $erros);
            }

            return redirect()->route('conferencia.index')->with('success', $mensagem);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao devolver pedidos: ' . $e->getMessage());
        }
    }
}
