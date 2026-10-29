<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScannerSession extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'pedido_id',
        'documento_id',
        'tipo_documento',
        'grupo',
        'criado_por',
        'token_hash',
        'status',
        'erro',
        'expires_at',
        'concluido_em',
    ];

    protected $hidden = [
        'token_hash',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'concluido_em' => 'datetime',
        ];
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function expirada(): bool
    {
        return $this->expires_at->isPast();
    }
}
