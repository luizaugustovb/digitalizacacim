<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportJobItem extends Model
{
    protected $fillable = [
        'import_job_id',
        'resultado',
        'codigo_pedido',
        'codigo_paciente',
        'nome_paciente',
        'convenio',
        'pedido_id',
        'motivo',
    ];

    public function job()
    {
        return $this->belongsTo(ImportJob::class, 'import_job_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
