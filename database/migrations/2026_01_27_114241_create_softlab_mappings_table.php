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
        Schema::create('softlab_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); // 'unidade' ou 'usuario'
            $table->string('cod_softlab'); // cod_origem ou usu_pedido do Softlab
            $table->string('nome_softlab')->nullable(); // Nome de referência
            $table->foreignId('unidade_id')->nullable()->constrained('unidades')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->unique(['tipo', 'cod_softlab']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('softlab_mappings');
    }
};
