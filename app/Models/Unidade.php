<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidade extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'codigo', 'endereco', 'telefone', 'ativo'];
    protected $casts = ['ativo' => 'boolean'];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
