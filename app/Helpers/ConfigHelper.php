<?php

namespace App\Helpers;

use App\Models\Configuracao;
use Illuminate\Support\Facades\Cache;

class ConfigHelper
{
    /**
     * Obtém o valor de uma configuração
     */
    public static function get(string $chave, $default = null)
    {
        return Cache::remember("config.{$chave}", 3600, function () use ($chave, $default) {
            $config = Configuracao::where('chave', $chave)->first();

            if (!$config) {
                return $default;
            }

            // Converter string para tipo apropriado
            return self::convertValue($config->valor, $config->tipo);
        });
    }

    /**
     * Define o valor de uma configuração
     */
    public static function set(string $chave, $valor, string $tipo = 'string', string $descricao = '', string $categoria = 'geral'): void
    {
        // Converter valor para string
        if (is_bool($valor)) {
            $valor = $valor ? 'true' : 'false';
        }

        Configuracao::updateOrCreate(
            ['chave' => $chave],
            [
                'valor' => $valor,
                'tipo' => $tipo,
                'descricao' => $descricao,
                'categoria' => $categoria,
            ]
        );

        // Limpar cache
        Cache::forget("config.{$chave}");
    }

    /**
     * Remove uma configuração
     */
    public static function forget(string $chave): void
    {
        Configuracao::where('chave', $chave)->delete();
        Cache::forget("config.{$chave}");
    }

    /**
     * Limpa todo o cache de configurações
     */
    public static function clearCache(): void
    {
        $configs = Configuracao::all();
        foreach ($configs as $config) {
            Cache::forget("config.{$config->chave}");
        }
    }

    /**
     * Converte valor para o tipo apropriado
     */
    private static function convertValue($valor, string $tipo)
    {
        switch ($tipo) {
            case 'integer':
                return (int) $valor;
            case 'float':
                return (float) $valor;
            case 'boolean':
                return $valor === 'true' || $valor === '1' || $valor === 1;
            case 'array':
                return json_decode($valor, true) ?? [];
            default:
                return $valor;
        }
    }
}
