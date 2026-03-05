<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documento extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pedido_id',
        'tipo_documento',
        'arquivo_nome',
        'arquivo_path',
        'mime_type',
        'tamanho',
        'hash',
        'criado_por',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function uploadPor()
    {
        return $this->belongsTo(User::class, 'upload_por');
    }
}
