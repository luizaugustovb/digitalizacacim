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
        Schema::create('atendimentos', function (Blueprint $table) {
            $table->id();
            $table->string('protocolo')->unique();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->string('convenio')->nullable();
            $table->string('numero_carteirinha')->nullable();
            $table->string('status')->default('aguardando'); // aguardando, em_atendimento, concluido, cancelado
            $table->date('data_atendimento');
            $table->text('imagem_documento')->nullable(); // Base64 da CNH/RG
            $table->text('imagem_carteirinha')->nullable(); // Base64 da carteirinha
            $table->text('imagem_guias')->nullable(); // Base64 das guias médicas
            $table->text('dados_ocr_documento')->nullable(); // JSON com dados extraídos do documento
            $table->text('dados_ocr_carteirinha')->nullable(); // JSON com dados extraídos da carteirinha
            $table->timestamps();

            // Índices para melhorar performance
            $table->index('protocolo');
            $table->index('cliente_id');
            $table->index('data_atendimento');
            $table->index('status');

            // Chave estrangeira (se cliente existir)
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atendimentos');
    }
};
