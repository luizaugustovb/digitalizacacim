<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
    use HasFactory;

    const MODULOS_DISPONIVEIS = [
        'Controle Interno',
        'Requisição Médica',
        'Autorização',
        'Guia TISS',
    ];

    protected $fillable = ['nome', 'codigo', 'observacoes', 'ativo', 'modulos'];
    protected $casts = [
        'ativo' => 'boolean',
        'modulos' => 'array',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'user_convenios');
    }
}
