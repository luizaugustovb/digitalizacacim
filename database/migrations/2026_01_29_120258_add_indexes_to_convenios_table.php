<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('convenios', function (Blueprint $table) {
            // Adicionar índices para melhorar performance de buscas
            $table->index('nome');
            $table->index('codigo');
            $table->index('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('convenios', function (Blueprint $table) {
            // Remover índices
            $table->dropIndex(['nome']);
            $table->dropIndex(['codigo']);
            $table->dropIndex(['ativo']);
        });
    }
};
