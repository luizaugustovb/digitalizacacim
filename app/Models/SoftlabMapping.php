<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftlabMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'cod_softlab',
        'nome_softlab',
        'unidade_id',
        'user_id',
        'convenio_id',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function unidade()
    {
        return $this->belongsTo(Unidade::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function convenio()
    {
        return $this->belongsTo(Convenio::class);
    }

    // Buscar unidade pelo código do Softlab
    public static function getUnidadeByCodigo($codSoftlab)
    {
        $mapping = self::where('tipo', 'unidade')
            ->where('cod_softlab', $codSoftlab)
            ->where('ativo', true)
            ->first();

        return $mapping?->unidade;
    }

    // Buscar usuário pelo código do Softlab
    public static function getUserByCodigo($codSoftlab)
    {
        $codigo = $codSoftlab ? strtoupper(trim($codSoftlab)) : $codSoftlab;

        $mapping = self::where('tipo', 'usuario')
            ->where('cod_softlab', $codigo)
            ->where('ativo', true)
            ->first();

        if ($mapping?->user) {
            return $mapping->user;
        }

        return User::where('codigo', $codigo)->first();
    }

    // Buscar convênio pelo código do Softlab (cod_guia)
    public static function getConvenioByCodigo($codGuia)
    {
        $mapping = self::where('tipo', 'convenio')
            ->where('cod_softlab', $codGuia)
            ->where('ativo', true)
            ->first();

        return $mapping?->convenio;
    }
}
