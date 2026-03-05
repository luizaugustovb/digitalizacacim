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
        Schema::create('produtividade_regras', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->decimal('valor_base_comissao', 10, 2)->default(0);
            $table->decimal('desconto_por_erro', 10, 2)->default(0);
            $table->integer('limite_erros')->default(0);
            $table->json('formula')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtividade_regras');
    }
};
