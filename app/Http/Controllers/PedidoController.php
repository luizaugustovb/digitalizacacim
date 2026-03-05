<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Convenio;
use App\Models\Unidade;
use App\Models\User;
use App\Models\TimelineLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Query base
        $query = Pedido::with(['convenio', 'unidade', 'atendente', 'gestor', 'documentos', 'pendencias.tipo'])
            ->orderBy('created_at', 'desc');

        // Filtro de acesso por role
        if ($user->isAtendente()) {
            $query->where('atendente_id', $user->id);
        } elseif ($user->isGestor()) {
            // Gestor vê tudo, mas pode filtrar por seus convênios
            if ($request->filled('meus_convenios') && $request->meus_convenios === '1') {
                $convenioIds = $user->convenios->pluck('id');
                $query->whereIn('convenio_id', $convenioIds);
            }
        }

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

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
            $query->whereDate('data_atendimento', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_atendimento', '<=', $request->data_fim);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('codigo_pedido', 'like', "%{$busca}%")
                    ->orWhere('codigo_paciente', 'like', "%{$busca}%")
                    ->orWhere('nome_paciente', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('lote')) {
            $query->where('lote', $request->lote);
        }

        // Paginação
        $pedidos = $query->paginate(20)->withQueryString();

        // Estatísticas para as abas
        $stats = $this->getStats($user);

        // Dados para filtros
        $convenios = Convenio::where('ativo', true)->orderBy('nome')->get();
        $unidades = Unidade::where('ativo', true)->orderBy('nome')->get();
        $atendentes = User::where('role', 'ATENDENTE')->where('ativo', true)->orderBy('nome')->get();

        return view('pedidos.index', compact('pedidos', 'stats', 'convenios', 'unidades', 'atendentes'));
    }

    private function getStats($user)
    {
        $query = Pedido::query();

        if ($user->isAtendente()) {
            $query->where('atendente_id', $user->id);
        }

        return [
            'TODOS' => (clone $query)->count(),
            'PENDENTE' => (clone $query)->where('status', 'PENDENTE')->count(),
            'ENVIADO' => (clone $query)->where('status', 'ENVIADO')->count(),
            'APROVADO' => (clone $query)->where('status', 'APROVADO')->count(),
            'DEVOLVIDO' => (clone $query)->where('status', 'DEVOLVIDO')->count(),
            'NAO_CADASTRADO' => (clone $query)->where('status', 'NAO_CADASTRADO')->count(),
        ];
    }

    public function show(Pedido $pedido)
    {
        Gate::authorize('view', $pedido);

        $pedido->load(['convenio', 'unidade', 'atendente', 'gestor', 'documentos.uploadPor', 'pendencias.tipo', 'pendencias.criadoPor']);

        // Log de visualização
        TimelineLog::create([
            'pedido_id' => $pedido->id,
            'user_id' => auth()->id(),
            'acao' => 'PEDIDO_VISUALIZADO',
            'descricao' => 'Pedido visualizado',
            'detalhes' => 'Visualização dos detalhes do pedido',
        ]);

        return view('pedidos.show', compact('pedido'));
    }

    public function escanear(Pedido $pedido)
    {
        Gate::authorize('update', $pedido);

        $pedido->load(['convenio', 'unidade', 'documentos', 'pendencias.tipo']);

        return view('pedidos.escanear', compact('pedido'));
    }

    public function enviar(Request $request, Pedido $pedido)
    {
        Gate::authorize('enviar', $pedido);

        // Validar se tem documentos obrigatórios
        if (!$pedido->temDocumentosObrigatorios()) {
            return back()->with('error', 'É necessário anexar Guia Médica e Autorização/SADT antes de enviar.');
        }

        DB::transaction(function () use ($pedido) {
            // Garantir que o atendente está definido
            $updateData = [
                'status' => 'ENVIADO',
                'data_envio' => now(),
            ];

            // Se o pedido não tem atendente, define o usuário logado
            if (!$pedido->atendente_id) {
                $updateData['atendente_id'] = auth()->id();
            }

            $pedido->update($updateData);

            // Log
            TimelineLog::create([
                'pedido_id' => $pedido->id,
                'user_id' => auth()->id(),
                'acao' => 'PEDIDO_ENVIADO',
                'descricao' => 'Pedido enviado para conferência',
                'detalhes' => 'Status alterado de PENDENTE para ENVIADO',
            ]);
        });

        return redirect()->route('pedidos.index')->with('success', 'Pedido enviado com sucesso!');
    }

    public function aprovar(Request $request, Pedido $pedido)
    {
        Gate::authorize('aprovar', $pedido);

        DB::transaction(function () use ($pedido) {
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
                'descricao' => 'Pedido aprovado pelo gestor',
                'detalhes' => 'Status alterado de ENVIADO para APROVADO',
            ]);
        });

        return redirect()->route('pedidos.index')->with('success', 'Pedido aprovado com sucesso!');
    }

    public function showDevolver(Pedido $pedido)
    {
        Gate::authorize('devolver', $pedido);

        $pedido->load(['convenio', 'unidade', 'atendente', 'documentos']);
        $pendenciasTipos = \App\Models\PendenciaTipo::where('ativo', true)->orderBy('nome')->get();

        return view('pedidos.devolver', compact('pedido', 'pendenciasTipos'));
    }

    public function devolver(Request $request, Pedido $pedido)
    {
        Gate::authorize('devolver', $pedido);

        $request->validate([
            'motivo_devolucao' => 'required|string',
            'pendencias' => 'required|array|min:1',
        ], [
            'motivo_devolucao.required' => 'O motivo da devolução é obrigatório.',
            'pendencias.required' => 'Selecione pelo menos uma pendência.',
            'pendencias.min' => 'Selecione pelo menos uma pendência.',
        ]);

        DB::transaction(function () use ($pedido, $request) {
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
                'descricao' => 'Pedido devolvido pelo gestor',
                'detalhes' => $request->motivo_devolucao,
            ]);
        });

        return redirect()->route('pedidos.index')->with('success', 'Pedido devolvido com sucesso!');
    }

    public function timeline(Pedido $pedido)
    {
        Gate::authorize('view', $pedido);

        $timeline = TimelineLog::where('pedido_id', $pedido->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pedidos.timeline', compact('timeline'));
    }

    public function destroy(Pedido $pedido)
    {
        Gate::authorize('delete', $pedido);

        DB::transaction(function () use ($pedido) {
            // Log antes de deletar
            TimelineLog::create([
                'pedido_id' => $pedido->id,
                'user_id' => auth()->id(),
                'acao' => 'PEDIDO_REMOVIDO',
                'descricao' => 'Pedido removido do sistema',
                'detalhes' => "Código: {$pedido->codigo}",
            ]);

            $pedido->delete();
        });

        return redirect()->route('pedidos.index')->with('success', 'Pedido removido com sucesso!');
    }
}
