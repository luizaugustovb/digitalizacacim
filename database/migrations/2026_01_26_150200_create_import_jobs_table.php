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
        Schema::create('import_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('tipo')->default('pedidos');
            $table->enum('status', ['processando', 'concluido', 'erro', 'cancelado'])->default('processando');
            $table->timestamp('iniciado_em');
            $table->timestamp('finalizado_em')->nullable();
            $table->integer('total_registros')->default(0);
            $table->integer('importados')->default(0);
            $table->integer('ignorados')->default(0);
            $table->integer('erros')->default(0);
            $table->json('detalhes_erros')->nullable();
            $table->text('mensagem_erro')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_jobs');
    }
};
