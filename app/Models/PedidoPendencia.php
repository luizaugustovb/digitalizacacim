<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoPendencia extends Model
{
    use HasFactory;

    protected $table = 'pedido_pendencias';
    protected $fillable = [
        'pedido_id',
        'pendencia_tipo_id',
        'resolvida',
        'observacao',
        'criado_por',
        'resolvido_por',
        'resolvido_em',
    ];

    protected $casts = [
        'resolvida' => 'boolean',
        'resolvido_em' => 'datetime',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
    public function tipo()
    {
        return $this->belongsTo(PendenciaTipo::class, 'pendencia_tipo_id');
    }
    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }
    public function resolvidoPor()
    {
        return $this->belongsTo(User::class, 'resolvido_por');
    }
}
