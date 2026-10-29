<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_job_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_job_id')->constrained('import_jobs')->cascadeOnDelete();
            $table->enum('resultado', ['importado', 'ignorado', 'erro']);
            $table->string('codigo_pedido')->nullable();
            $table->string('codigo_paciente')->nullable();
            $table->string('nome_paciente')->nullable();
            $table->string('convenio')->nullable();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();
            $table->text('motivo')->nullable();
            $table->timestamps();

            $table->index(['import_job_id', 'resultado']);
            $table->index('codigo_pedido');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_job_items');
    }
};
