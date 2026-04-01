<?php

namespace App\Http\Controllers;

use App\Models\Convenio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ConvenioController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'role:ADMIN,GESTOR',
        ];
    }

    /**
     * Listar todos os convênios
     */
    public function index()
    {
        $convenios = Convenio::withCount('pedidos')
            ->orderBy('ativo', 'desc')
            ->orderBy('nome')
            ->paginate(20);

        return view('convenios.index', compact('convenios'));
    }

    /**
     * Criar novo convênio
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo' => 'required|string|max:50|unique:convenios,codigo',
            'observacoes' => 'nullable|string|max:500',
            'ativo' => 'boolean',
            'modulos' => 'nullable|array',
            'modulos.*' => 'in:Controle Interno,Requisição Médica,Autorização,Guia TISS',
        ]);

        $validated['ativo'] = $request->has('ativo');
        $validated['modulos'] = $request->input('modulos', []);

        Convenio::create($validated);

        return redirect()
            ->route('convenios.index')
            ->with('success', 'Convênio criado com sucesso!');
    }

    /**
     * Atualizar convênio
     */
    public function update(Request $request, Convenio $convenio)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo' => 'required|string|max:50|unique:convenios,codigo,' . $convenio->id,
            'observacoes' => 'nullable|string|max:500',
            'ativo' => 'boolean',
            'modulos' => 'nullable|array',
            'modulos.*' => 'in:Controle Interno,Requisição Médica,Autorização,Guia TISS',
        ]);

        $validated['ativo'] = $request->has('ativo');
        $validated['modulos'] = $request->input('modulos', []);

        $convenio->update($validated);

        return redirect()
            ->route('convenios.index')
            ->with('success', 'Convênio atualizado com sucesso!');
    }

    /**
     * Ativar/Desativar convênio
     */
    public function toggle(Convenio $convenio)
    {
        $convenio->update(['ativo' => !$convenio->ativo]);

        $status = $convenio->ativo ? 'ativado' : 'desativado';
        return redirect()
            ->route('convenios.index')
            ->with('success', "Convênio {$status} com sucesso!");
    }

    /**
     * Excluir convênio e todos os pedidos vinculados
     */
    public function destroy(Convenio $convenio)
    {
        DB::transaction(function () use ($convenio) {
            foreach ($convenio->pedidos()->withTrashed()->get() as $pedido) {
                // Apagar arquivos físicos e registros de documentos
                foreach ($pedido->documentos()->withTrashed()->get() as $doc) {
                    if ($doc->arquivo_path) {
                        Storage::delete($doc->arquivo_path);
                    }
                    $doc->forceDelete();
                }

                $pedido->pendencias()->delete();
                $pedido->timelineLogs()->delete();
                $pedido->forceDelete();
            }

            $convenio->delete();
        });

        return redirect()
            ->route('convenios.index')
            ->with('success', 'Convênio e todos os pedidos vinculados foram excluídos com sucesso!');
    }

    /**
     * Importar convênios da tabela tipo_g do MySQL
     */
    public function importar()
    {
        try {
            // Conectar ao banco MySQL legado
            $tiposGuia = DB::connection('mysql')
                ->table('tipo_g')
                ->where('tipo_guia', 'G') // Apenas tipo G
                ->get();

            if ($tiposGuia->isEmpty()) {
                return redirect()
                    ->route('convenios.index')
                    ->with('error', 'Nenhum convênio encontrado na tabela tipo_g com tipo_guia = "G".');
            }

            $importados = 0;
            $atualizados = 0;
            $erros = [];

            foreach ($tiposGuia as $tipo) {
                try {
                    // Verificar qual campo usar como nome (ajustar conforme estrutura da tabela)
                    $nome = $tipo->descricao ?? $tipo->nome ?? $tipo->tipo_guia ?? 'Sem Nome';
                    $codigo = $tipo->codigo ?? $tipo->id ?? strtoupper($nome);

                    // Criar ou atualizar
                    $convenio = Convenio::updateOrCreate(
                        ['codigo' => $codigo],
                        [
                            'nome' => $nome,
                            'observacoes' => 'Importado da tabela tipo_g',
                            'ativo' => true,
                        ]
                    );

                    if ($convenio->wasRecentlyCreated) {
                        $importados++;
                    } else {
                        $atualizados++;
                    }
                } catch (\Exception $e) {
                    $erros[] = "Erro ao importar: {$e->getMessage()}";
                }
            }

            $mensagem = "✅ Importação concluída! {$importados} novos convênios, {$atualizados} atualizados.";
            if (count($erros) > 0) {
                $mensagem .= " ⚠️ " . count($erros) . " erros encontrados.";
            }

            return redirect()
                ->route('convenios.index')
                ->with('success', $mensagem)
                ->with('erros_importacao', $erros);
        } catch (\Exception $e) {
            return redirect()
                ->route('convenios.index')
                ->with('error', 'Erro na importação: ' . $e->getMessage());
        }
    }
}
