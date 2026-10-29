<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Atendimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TotemController extends Controller
{
    /**
     * Processa OCR da CNH/RG e busca cliente no banco
     */
    public function processarDocumento(Request $request)
    {
        try {
            $request->validate([
                'imagem' => 'required|string',
                'dados_ocr' => 'required|array'
            ]);

            $dadosOCR = $request->dados_ocr;
            $cpf = $this->extrairCPF($dadosOCR);

            if (!$cpf) {
                return response()->json([
                    'success' => false,
                    'message' => 'CPF não encontrado no documento'
                ], 400);
            }

            // Buscar cliente no banco de dados
            $cliente = Cliente::where('cpf', $cpf)->first();

            if ($cliente) {
                return response()->json([
                    'success' => true,
                    'cliente_encontrado' => true,
                    'dados_cliente' => [
                        'id' => $cliente->id,
                        'nome' => $cliente->nome,
                        'cpf' => $cliente->cpf,
                        'data_nascimento' => $cliente->data_nascimento?->format('d/m/Y'),
                        'telefone' => $cliente->telefoneFormatado,
                        'email' => $cliente->email,
                        'endereco' => $cliente->endereco,
                        'cidade' => $cliente->cidade,
                        'estado' => $cliente->estado,
                        'cep' => $cliente->cep
                    ],
                    'dados_ocr' => $dadosOCR,
                    'cpf' => $cpf
                ]);
            } else {
                // Cliente novo - retorna dados do OCR para preencher cadastro
                return response()->json([
                    'success' => true,
                    'cliente_encontrado' => false,
                    'dados_ocr' => $dadosOCR,
                    'cpf' => $cpf,
                    'message' => 'Cliente não encontrado. Novo cadastro será criado.'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar documento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Processa OCR da carteirinha do convênio
     */
    public function processarCarteirinha(Request $request)
    {
        try {
            $request->validate([
                'imagem' => 'required|string',
                'dados_ocr' => 'required|array'
            ]);

            $dadosOCR = $request->dados_ocr;

            // Extrair dados da carteirinha
            $dadosCarteirinha = [
                'numero_carteirinha' => $this->extrairNumeroCarteirinha($dadosOCR),
                'nome_titular' => $this->extrairNome($dadosOCR),
                'validade' => $this->extrairValidade($dadosOCR),
                'convenio' => $this->extrairConvenio($dadosOCR)
            ];

            return response()->json([
                'success' => true,
                'dados_carteirinha' => $dadosCarteirinha,
                'dados_ocr_completo' => $dadosOCR
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao processar carteirinha: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar carteirinha: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Salva atendimento completo no banco
     */
    public function salvarAtendimento(Request $request)
    {
        try {
            DB::beginTransaction();

            // Dados do cliente
            $dadosCliente = $request->dados_cliente;
            $clienteId = $request->cliente_id;

            // Se cliente não existe, criar novo
            if (!$clienteId) {
                $cliente = Cliente::create([
                    'nome' => $dadosCliente['nome'] ?? null,
                    'cpf' => preg_replace('/\D/', '', $dadosCliente['cpf']),
                    'data_nascimento' => $dadosCliente['data_nascimento'] ?? null,
                    'telefone' => $dadosCliente['telefone'] ?? null,
                    'email' => $dadosCliente['email'] ?? null,
                    'endereco' => $dadosCliente['endereco'] ?? null,
                    'cidade' => $dadosCliente['cidade'] ?? null,
                    'estado' => $dadosCliente['estado'] ?? null,
                    'cep' => $dadosCliente['cep'] ?? null,
                    'rg' => $dadosCliente['rg'] ?? null,
                    'nome_mae' => $dadosCliente['nome_mae'] ?? null
                ]);
                $clienteId = $cliente->id;
            }

            // Gerar protocolo
            $protocolo = $this->gerarProtocolo();

            // Salvar atendimento
            $atendimento = Atendimento::create([
                'protocolo' => $protocolo,
                'cliente_id' => $clienteId,
                'convenio' => $request->convenio,
                'numero_carteirinha' => $request->dados_carteirinha['numero_carteirinha'] ?? null,
                'status' => 'aguardando',
                'data_atendimento' => now(),
                'imagem_documento' => $request->imagem_documento,
                'imagem_carteirinha' => $request->imagem_carteirinha,
                'imagem_guias' => $request->imagem_guias,
                'dados_ocr_documento' => $dadosCliente,
                'dados_ocr_carteirinha' => $request->dados_carteirinha ?? null
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'protocolo' => $protocolo,
                'atendimento_id' => $atendimento->id,
                'cliente_id' => $clienteId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar atendimento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar atendimento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extrai CPF do texto OCR
     */
    private function extrairCPF($dadosOCR)
    {
        $texto = is_array($dadosOCR) ? implode(' ', $dadosOCR) : $dadosOCR;

        // Padrão de CPF: XXX.XXX.XXX-XX ou XXXXXXXXXXX
        preg_match('/(\d{3}\.?\d{3}\.?\d{3}-?\d{2})/', $texto, $matches);

        if (isset($matches[1])) {
            // Remover formatação
            return preg_replace('/[^0-9]/', '', $matches[1]);
        }

        return null;
    }

    /**
     * Extrai número da carteirinha
     */
    private function extrairNumeroCarteirinha($dadosOCR)
    {
        $texto = is_array($dadosOCR) ? implode(' ', $dadosOCR) : $dadosOCR;

        // Buscar padrões comuns de número de carteirinha
        if (preg_match('/(?:CARTEIRA|CARD|N[ºo°])[\s:]*([0-9]{8,20})/', $texto, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extrai nome do texto OCR
     */
    private function extrairNome($dadosOCR)
    {
        $texto = is_array($dadosOCR) ? implode(' ', $dadosOCR) : $dadosOCR;

        // Buscar padrão de nome após "NOME:"
        if (preg_match('/NOME[\s:]+([A-ZÀ-Ú\s]+)/', $texto, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Extrai validade da carteirinha
     */
    private function extrairValidade($dadosOCR)
    {
        $texto = is_array($dadosOCR) ? implode(' ', $dadosOCR) : $dadosOCR;

        // Buscar data de validade
        if (preg_match('/VALIDADE[\s:]*(\d{2}\/\d{2}\/\d{4})/', $texto, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extrai nome do convênio
     */
    private function extrairConvenio($dadosOCR)
    {
        $texto = is_array($dadosOCR) ? implode(' ', $dadosOCR) : $dadosOCR;

        $convenios = ['UNIMED', 'AMIL', 'BRADESCO', 'SULAMERICA', 'NOTREDAME'];

        foreach ($convenios as $convenio) {
            if (stripos($texto, $convenio) !== false) {
                return $convenio;
            }
        }

        return null;
    }

    /**
     * Gera protocolo único de atendimento
     */
    private function gerarProtocolo()
    {
        $date = now();
        $year = $date->format('y');
        $month = $date->format('m');
        $day = $date->format('d');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        return "AT{$year}{$month}{$day}{$random}";
    }
}
