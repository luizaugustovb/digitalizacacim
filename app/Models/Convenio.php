<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'codigo', 'observacoes', 'ativo'];
    protected $casts = ['ativo' => 'boolean'];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'user_convenios');
    }
}
