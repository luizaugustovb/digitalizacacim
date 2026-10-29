<?php

namespace App\Http\Controllers;

use App\Models\Convenio;
use App\Models\SoftlabMapping;
use App\Helpers\SoftlabConvenioImportFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
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
     * Importar convênios da tabela tipo_g do Softlab.
     */
    public function importar()
    {
        try {
            $tiposGuia = DB::connection('softlab')
                ->table('tipo_g')
                ->select('cod_guia', 'nome_conv')
                ->where('tipo_guia', 'G')
                ->orderBy('nome_conv')
                ->get();
        } catch (QueryException $e) {
            Log::error('Erro ao consultar convênios no Softlab', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->route('convenios.index')
                ->with('error', 'Não foi possível consultar os convênios no Softlab. Verifique a conexão configurada e tente novamente.');
        }

        if ($tiposGuia->isEmpty()) {
            return redirect()
                ->route('convenios.index')
                ->with('error', 'Nenhum convênio encontrado na tabela tipo_g com tipo_guia = "G".');
        }

        $importados = 0;
        $atualizados = 0;
        $erros = [];

        foreach ($tiposGuia as $tipo) {
            $nome = trim((string) $tipo->nome_conv);
            $codigo = SoftlabConvenioImportFilter::normalizar($tipo->cod_guia);

            if ($nome === '') {
                $erros[] = 'Registro ignorado por não possuir código ou nome de convênio.';
                continue;
            }

            if (SoftlabConvenioImportFilter::bloqueado($codigo)) {
                continue;
            }

            try {
                $convenio = Convenio::where('codigo', $codigo)->first();

                if (!$convenio) {
                    $convenio = Convenio::where('nome', $nome)->first();
                }

                if ($convenio) {
                    $atualizados++;
                } else {
                    $convenio = Convenio::create([
                        'codigo' => $codigo,
                        'nome' => $nome,
                        'observacoes' => 'Importado da tabela tipo_g do Softlab',
                        'ativo' => true,
                    ]);
                    $importados++;
                }

                SoftlabMapping::updateOrCreate(
                    [
                        'tipo' => 'convenio',
                        'cod_softlab' => $codigo,
                    ],
                    [
                        'nome_softlab' => $nome,
                        'convenio_id' => $convenio->id,
                        'ativo' => true,
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Erro ao importar convênio do Softlab', [
                    'codigo' => $codigo,
                    'message' => $e->getMessage(),
                ]);
                $erros[] = "Não foi possível importar o convênio {$codigo}.";
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
    }
}
