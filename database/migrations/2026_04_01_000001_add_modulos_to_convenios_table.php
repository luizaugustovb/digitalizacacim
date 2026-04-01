<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('convenios', function (Blueprint $table) {
            $table->json('modulos')->nullable()->after('observacoes');
        });

        // Pré-popular módulos com base no nome do convênio
        $convenios = DB::table('convenios')->get();
        foreach ($convenios as $convenio) {
            $nome = mb_strtolower($convenio->nome);
            $modulos = null;

            // Cassi Periódico (mais específico antes de Cassi)
            if (str_contains($nome, 'cassi') && str_contains($nome, 'period')) {
                $modulos = ['Controle Interno', 'Requisição Médica'];
            } elseif (str_contains($nome, 'unimed') || str_contains($nome, 'cassi') || str_contains($nome, 'caurn')) {
                $modulos = ['Controle Interno', 'Requisição Médica', 'Autorização'];
            } elseif (str_contains($nome, 'hapvida') || str_contains($nome, 'cef') || str_contains($nome, 'amil')) {
                $modulos = ['Controle Interno', 'Requisição Médica'];
            } elseif (str_contains($nome, 'geap') || str_contains($nome, 'humana') || str_contains($nome, 'bradesco')) {
                $modulos = ['Requisição Médica', 'Guia TISS', 'Autorização'];
            } elseif (str_contains($nome, 'petrobras') || str_contains($nome, 'sulamerica') || str_contains($nome, 'sulamé') || str_contains($nome, 'camed')) {
                $modulos = ['Guia TISS', 'Requisição Médica'];
            }

            if ($modulos !== null) {
                DB::table('convenios')
                    ->where('id', $convenio->id)
                    ->update(['modulos' => json_encode($modulos)]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('convenios', function (Blueprint $table) {
            $table->dropColumn('modulos');
        });
    }
};
