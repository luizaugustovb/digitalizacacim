<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Pedido;
use App\Models\TimelineLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DocumentoController extends Controller
{
    use AuthorizesRequests;

    public function upload(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'tipo_documento' => 'required|in:Guia Médica,Autorização/SADT,Documento Extra,Formulário',
            'arquivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB
        ], [
            'arquivo.required' => 'Selecione um arquivo para upload.',
            'arquivo.mimes' => 'O arquivo deve ser PDF, JPG, JPEG ou PNG.',
            'arquivo.max' => 'O arquivo não pode ser maior que 10MB.',
        ]);

        $pedido = Pedido::findOrFail($request->pedido_id);

        // Verificar permissão
        $this->authorize('update', $pedido);

        // Verificar se o pedido está em status que permite upload
        if (strtoupper($pedido->status) !== 'PENDENTE') {
            return back()->with('error', 'Apenas pedidos com status PENDENTE podem receber novos documentos.');
        }

        DB::beginTransaction();
        try {
            $arquivo = $request->file('arquivo');
            $extensao = $arquivo->getClientOriginalExtension();

            // Gerar nome do arquivo: numeroPedido_nomePaciente_dataDoDia_tipoArquivo.extensao
            $data = now()->format('Ymd');
            $nomePaciente = Str::slug($pedido->nome_paciente);
            $tipoDoc = Str::slug($request->tipo_documento);
            $codigoPedido = $pedido->codigo_pedido ?? $pedido->id;
            $nomeArquivo = "{$codigoPedido}_{$nomePaciente}_{$data}_{$tipoDoc}.{$extensao}";

            // Caminho: storage/app/guias/YYYY/MM/DD/CONVENIO/
            $ano = now()->format('Y');
            $mes = now()->format('m');
            $dia = now()->format('d');

            // Obter nome do convênio para a pasta
            $convenioSlug = 'sem-convenio';
            if ($pedido->convenio) {
                $convenioSlug = Str::slug($pedido->convenio->nome);
            }

            $caminhoRelativo = "guias/{$ano}/{$mes}/{$dia}/{$convenioSlug}";

            // Salvar arquivo no storage padrão do Laravel
            $caminhoCompleto = Storage::putFileAs(
                $caminhoRelativo,
                $arquivo,
                $nomeArquivo
            );

            // Gerar hash do arquivo
            $hashArquivo = hash_file('sha256', $arquivo->getRealPath());

            // Criar registro no banco
            $documento = Documento::create([
                'pedido_id' => $pedido->id,
                'tipo_documento' => $request->tipo_documento,
                'arquivo_nome' => $nomeArquivo,
                'arquivo_path' => $caminhoCompleto,
                'tamanho' => $arquivo->getSize(),
                'mime_type' => $arquivo->getMimeType(),
                'hash' => $hashArquivo,
                'criado_por' => auth()->id(),
            ]);

            // Log na timeline
            TimelineLog::create([
                'pedido_id' => $pedido->id,
                'user_id' => auth()->id(),
                'acao' => 'DOCUMENTO_ADICIONADO',
                'descricao' => "Documento adicionado: {$request->tipo_documento}",
                'detalhes' => $nomeArquivo,
            ]);

            DB::commit();

            return back()->with('success', 'Documento enviado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao fazer upload do documento: ' . $e->getMessage());
        }
    }

    public function destroy(Documento $documento)
    {
        $pedido = $documento->pedido;

        // Verificar permissão
        $this->authorize('update', $pedido);

        // Verificar se o pedido está em status que permite remoção
        if (strtoupper($pedido->status) !== 'PENDENTE') {
            return back()->with('error', 'Apenas documentos de pedidos com status PENDENTE podem ser removidos.');
        }

        DB::beginTransaction();
        try {
            // Remover arquivo físico
            if (Storage::exists($documento->arquivo_path)) {
                Storage::delete($documento->arquivo_path);
            }

            // Log na timeline
            TimelineLog::create([
                'pedido_id' => $pedido->id,
                'user_id' => auth()->id(),
                'acao' => 'DOCUMENTO_REMOVIDO',
                'descricao' => "Documento removido: {$documento->tipo_documento}",
                'detalhes' => $documento->arquivo_nome,
            ]);

            // Remover registro do banco
            $documento->delete();

            DB::commit();

            return back()->with('success', 'Documento removido com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao remover documento: ' . $e->getMessage());
        }
    }

    public function download(Documento $documento)
    {
        $pedido = $documento->pedido;

        // Verificar permissão
        $this->authorize('view', $pedido);

        if (!Storage::exists($documento->arquivo_path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::download(
            $documento->arquivo_path,
            $documento->arquivo_nome
        );
    }

    public function preview(Documento $documento)
    {
        $pedido = $documento->pedido;

        // Verificar permissão
        $this->authorize('view', $pedido);

        if (!Storage::exists($documento->arquivo_path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $conteudo = Storage::get($documento->arquivo_path);

        return response($conteudo)
            ->header('Content-Type', $documento->mime_type)
            ->header('Content-Disposition', 'inline; filename="' . $documento->arquivo_nome . '"');
    }
}
