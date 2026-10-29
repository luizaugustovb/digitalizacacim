<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atendimento extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocolo',
        'cliente_id',
        'convenio',
        'numero_carteirinha',
        'status',
        'data_atendimento',
        'imagem_documento',
        'imagem_carteirinha',
        'imagem_guias',
        'dados_ocr_documento',
        'dados_ocr_carteirinha'
    ];

    protected $casts = [
        'data_atendimento' => 'date',
        'dados_ocr_documento' => 'array',
        'dados_ocr_carteirinha' => 'array',
    ];

    /**
     * Relacionamento com cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Escopo para atendimentos aguardando
     */
    public function scopeAguardando($query)
    {
        return $query->where('status', 'aguardando');
    }

    /**
     * Escopo para atendimentos em andamento
     */
    public function scopeEmAtendimento($query)
    {
        return $query->where('status', 'em_atendimento');
    }

    /**
     * Escopo para atendimentos concluídos
     */
    public function scopeConcluido($query)
    {
        return $query->where('status', 'concluido');
    }

    /**
     * Escopo para buscar por protocolo
     */
    public function scopeByProtocolo($query, $protocolo)
    {
        return $query->where('protocolo', $protocolo);
    }

    /**
     * Escopo para buscar por período
     */
    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_atendimento', [$dataInicio, $dataFim]);
    }

    /**
     * Obtém o nome do cliente (relacionamento ou dados OCR)
     */
    public function getNomeClienteAttribute()
    {
        if ($this->cliente) {
            return $this->cliente->nome;
        }

        if ($this->dados_ocr_documento && isset($this->dados_ocr_documento['nome'])) {
            return $this->dados_ocr_documento['nome'];
        }

        return 'Não informado';
    }

    /**
     * Obtém o CPF do cliente (relacionamento ou dados OCR)
     */
    public function getCpfClienteAttribute()
    {
        if ($this->cliente) {
            return $this->cliente->cpf;
        }

        if ($this->dados_ocr_documento && isset($this->dados_ocr_documento['cpf'])) {
            return $this->dados_ocr_documento['cpf'];
        }

        return 'Não informado';
    }
}
