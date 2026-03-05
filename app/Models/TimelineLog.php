<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimelineLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'user_id',
        'acao',
        'detalhes',
        'ip',
        'user_agent',
    ];

    protected $casts = ['detalhes' => 'array'];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
