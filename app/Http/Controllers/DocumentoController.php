<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Pedido;
use App\Models\TimelineLog;
use App\Services\DocumentoUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DocumentoController extends Controller
{
    use AuthorizesRequests;

    public function upload(Request $request, DocumentoUploadService $documentoUploadService)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'tipo_documento' => 'required|in:Guia Médica,Autorização/SADT,Documento Extra,Formulário,Controle Interno,Requisição Médica,Autorização,Guia TISS,Outros Documentos',
            'grupo' => 'required_if:tipo_documento,Outros Documentos|nullable|string|max:150',
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
            $documentoUploadService->armazenar(
                $pedido,
                $request->file('arquivo'),
                $request->tipo_documento,
                $request->grupo,
                auth()->id(),
            );

            DB::commit();

            // Para Outros Documentos, redirecionar com parâmetro para perguntar sobre mais páginas
            if ($request->tipo_documento === 'Outros Documentos' && $request->mais_paginas_redirect) {
                $grupo = urlencode($request->grupo);
                return redirect(route('pedidos.escanear', $pedido) . '?mais_paginas=1&grupo=' . $grupo)
                    ->with('success', 'Documento enviado com sucesso!');
            }

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
