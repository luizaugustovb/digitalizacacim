<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportJob extends Model
{
    protected $fillable = [
        'tipo',
        'status',
        'iniciado_em',
        'finalizado_em',
        'total_registros',
        'importados',
        'ignorados',
        'erros',
        'detalhes_erros',
        'mensagem_erro',
    ];

    protected $casts = [
        'iniciado_em' => 'datetime',
        'finalizado_em' => 'datetime',
        'total_registros' => 'integer',
        'importados' => 'integer',
        'ignorados' => 'integer',
        'erros' => 'integer',
    ];
}
