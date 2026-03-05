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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_pedido')->unique();
            $table->string('codigo_paciente')->nullable();
            $table->string('nome_paciente');
            $table->foreignId('convenio_id')->nullable()->constrained('convenios')->nullOnDelete();
            $table->foreignId('unidade_id')->nullable()->constrained('unidades')->nullOnDelete();
            $table->string('tipo_atendimento')->nullable();
            $table->date('data_atendimento');
            $table->enum('status', ['Pendente', 'Enviado', 'Aprovado', 'Devolvido', 'Não Cadastrado'])->default('Pendente');
            $table->foreignId('atendente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('gestor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('data_envio')->nullable();
            $table->timestamp('data_aprovacao')->nullable();
            $table->timestamp('data_devolucao')->nullable();
            $table->text('motivo_devolucao')->nullable();
            $table->text('observacoes')->nullable();
            $table->string('lote')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'data_atendimento']);
            $table->index('atendente_id');
            $table->index('gestor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
