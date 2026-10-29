<?php

namespace App\Helpers;

class SoftlabConvenioImportFilter
{
    public static function bloqueado(?string $codigo): bool
    {
        $codigoNormalizado = self::normalizar($codigo);

        if ($codigoNormalizado === '' || str_starts_with($codigoNormalizado, 'NC')) {
            return true;
        }

        return in_array($codigoNormalizado, self::codigosBloqueados(), true);
    }

    public static function normalizar(?string $codigo): string
    {
        return strtoupper(trim((string) $codigo));
    }

    private static function codigosBloqueados(): array
    {
        $raw = (string) ConfigHelper::get('importacao_convenios_bloqueados', '');
        $codigos = preg_split('/[,\r\n;]+/', $raw) ?: [];

        return array_values(array_filter(array_map(
            fn (string $codigo) => self::normalizar($codigo),
            $codigos
        )));
    }
}
