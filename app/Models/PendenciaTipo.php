<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendenciaTipo extends Model
{
    use HasFactory;

    protected $table = 'pendencias_tipo';
    protected $fillable = ['nome', 'descricao', 'cor', 'peso', 'ativo'];
    protected $casts = ['ativo' => 'boolean', 'peso' => 'integer'];

    public function pedidoPendencias()
    {
        return $this->hasMany(PedidoPendencia::class);
    }
}
