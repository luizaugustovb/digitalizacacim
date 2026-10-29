<?php

namespace App\Services;

use App\Models\Documento;
use App\Models\Pedido;
use App\Models\TimelineLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentoUploadService
{
    public function armazenar(
        Pedido $pedido,
        UploadedFile $arquivo,
        string $tipoDocumento,
        ?string $grupo,
        int $criadoPor,
    ): Documento {
        $extensao = $arquivo->getClientOriginalExtension();
        $data = now()->format('Ymd');
        $nomePaciente = Str::slug($pedido->nome_paciente);
        $tipoDoc = Str::slug($tipoDocumento);
        $codigoPedido = $pedido->codigo_pedido ?? $pedido->id;
        $nomeArquivo = "{$codigoPedido}_{$nomePaciente}_{$data}_{$tipoDoc}.{$extensao}";

        $caminhoRelativo = sprintf(
            'guias/%s/%s/%s/%s',
            now()->format('Y'),
            now()->format('m'),
            now()->format('d'),
            Str::slug($pedido->convenio?->nome ?? 'sem-convenio')
        );

        $caminhoCompleto = Storage::putFileAs($caminhoRelativo, $arquivo, $nomeArquivo);

        $documento = Documento::create([
            'pedido_id' => $pedido->id,
            'tipo_documento' => $tipoDocumento,
            'grupo' => $tipoDocumento === 'Outros Documentos' ? $grupo : null,
            'arquivo_nome' => $nomeArquivo,
            'arquivo_path' => $caminhoCompleto,
            'tamanho' => $arquivo->getSize(),
            'mime_type' => $arquivo->getMimeType(),
            'hash' => hash_file('sha256', $arquivo->getRealPath()),
            'criado_por' => $criadoPor,
        ]);

        TimelineLog::create([
            'pedido_id' => $pedido->id,
            'user_id' => $criadoPor,
            'acao' => 'DOCUMENTO_ADICIONADO',
            'descricao' => "Documento adicionado: {$tipoDocumento}",
            'detalhes' => $nomeArquivo,
        ]);

        return $documento;
    }
}
