<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo_pedido',
        'codigo_paciente',
        'nome_paciente',
        'convenio_id',
        'unidade_id',
        'tipo_atendimento',
        'data_atendimento',
        'status',
        'atendente_id',
        'gestor_id',
        'data_envio',
        'data_aprovacao',
        'data_devolucao',
        'motivo_devolucao',
        'observacoes',
        'lote',
        'cod_guia',
    ];

    protected $casts = [
        'data_atendimento' => 'date',
        'data_envio' => 'datetime',
        'data_aprovacao' => 'datetime',
        'data_devolucao' => 'datetime',
    ];

    public function convenio()
    {
        return $this->belongsTo(Convenio::class);
    }
    public function unidade()
    {
        return $this->belongsTo(Unidade::class);
    }
    public function atendente()
    {
        return $this->belongsTo(User::class, 'atendente_id');
    }
    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }
    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }
    public function pendencias()
    {
        return $this->hasMany(PedidoPendencia::class);
    }
    public function timelineLogs()
    {
        return $this->hasMany(TimelineLog::class)->orderBy('created_at', 'desc');
    }

    public function temDocumentosObrigatorios()
    {
        $temGuia = $this->documentos()
            ->where('tipo_documento', 'Guia Médica')
            ->exists();

        $temAutorizacao = $this->documentos()
            ->where('tipo_documento', 'Autorização/SADT')
            ->exists();

        return $temGuia && $temAutorizacao;
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'PENDENTE');
    }

    public function scopeEnviados($query)
    {
        return $query->where('status', 'ENVIADO');
    }

    public function scopeAprovados($query)
    {
        return $query->where('status', 'APROVADO');
    }

    public function scopeDevolvidos($query)
    {
        return $query->where('status', 'DEVOLVIDO');
    }
}
