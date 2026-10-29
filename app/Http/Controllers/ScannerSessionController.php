<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\ScannerSession;
use App\Services\DocumentoUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ScannerSessionController extends Controller
{
    private const TIPOS_DOCUMENTO = [
        'Guia Médica',
        'Autorização/SADT',
        'Documento Extra',
        'Formulário',
        'Controle Interno',
        'Requisição Médica',
        'Autorização',
        'Guia TISS',
        'Outros Documentos',
    ];

    public function store(Request $request, Pedido $pedido)
    {
        $this->authorize('update', $pedido);

        if (strtoupper($pedido->status) !== 'PENDENTE') {
            return response()->json([
                'message' => 'Apenas pedidos pendentes podem receber documentos.',
            ], 422);
        }

        $dados = $request->validate([
            'tipo_documento' => ['required', 'in:' . implode(',', self::TIPOS_DOCUMENTO)],
            'grupo' => ['required_if:tipo_documento,Outros Documentos', 'nullable', 'string', 'max:150'],
        ]);

        $token = Str::random(80);
        $session = ScannerSession::create([
            'id' => (string) Str::uuid(),
            'pedido_id' => $pedido->id,
            'tipo_documento' => $dados['tipo_documento'],
            'grupo' => $dados['grupo'] ?? null,
            'criado_por' => $request->user()->id,
            'token_hash' => hash('sha256', $token),
            'status' => 'PENDENTE',
            'expires_at' => now()->addMinutes(config('scanner.session_minutes')),
        ]);

        $params = http_build_query([
            'session' => $session->id,
            'token' => $token,
            'upload_url' => route('api.scanner-sessions.upload', $session),
        ], '', '&', PHP_QUERY_RFC3986);

        return response()->json([
            'id' => $session->id,
            'launch_url' => config('scanner.protocol') . '://scan?' . $params,
            'status_url' => route('scanner-sessions.show', $session),
            'expires_at' => $session->expires_at->toIso8601String(),
        ], 201);
    }

    public function show(Request $request, ScannerSession $scannerSession)
    {
        $this->authorize('update', $scannerSession->pedido);

        if ($scannerSession->status === 'PENDENTE' && $scannerSession->expirada()) {
            $scannerSession->update(['status' => 'EXPIRADA']);
        }

        return response()->json([
            'status' => $scannerSession->status,
            'erro' => $scannerSession->erro,
            'documento_id' => $scannerSession->documento_id,
        ]);
    }

    public function upload(Request $request, ScannerSession $scannerSession, DocumentoUploadService $documentoUploadService)
    {
        $token = $request->bearerToken();

        if (!$token || !hash_equals($scannerSession->token_hash, hash('sha256', $token))) {
            return response()->json(['message' => 'Token de digitalização inválido.'], 401);
        }

        $request->validate([
            'arquivo' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $documento = DB::transaction(function () use ($scannerSession, $request, $documentoUploadService) {
            $session = ScannerSession::query()
                ->whereKey($scannerSession->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($session->status !== 'PENDENTE') {
                throw ValidationException::withMessages([
                    'session' => 'Esta sessão de digitalização já foi utilizada ou encerrada.',
                ]);
            }

            if ($session->expirada()) {
                $session->update(['status' => 'EXPIRADA']);

                throw ValidationException::withMessages([
                    'session' => 'Esta sessão de digitalização expirou. Inicie uma nova digitalização.',
                ]);
            }

            $pedido = $session->pedido()->lockForUpdate()->firstOrFail();

            if (strtoupper($pedido->status) !== 'PENDENTE') {
                throw ValidationException::withMessages([
                    'pedido' => 'O pedido não aceita mais novos documentos.',
                ]);
            }

            $documento = $documentoUploadService->armazenar(
                $pedido,
                $request->file('arquivo'),
                $session->tipo_documento,
                $session->grupo,
                $session->criado_por,
            );

            $session->update([
                'documento_id' => $documento->id,
                'status' => 'CONCLUIDA',
                'concluido_em' => now(),
            ]);

            return $documento;
        });

        return response()->json([
            'message' => 'Documento digitalizado com sucesso.',
            'documento_id' => $documento->id,
        ], 201);
    }
}
