<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scanner_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('pedido_id')->constrained()->cascadeOnDelete();
            $table->foreignId('documento_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tipo_documento', 100);
            $table->string('grupo', 150)->nullable();
            $table->foreignId('criado_por')->constrained('users')->cascadeOnDelete();
            $table->char('token_hash', 64);
            $table->string('status', 20)->default('PENDENTE');
            $table->text('erro')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('concluido_em')->nullable();
            $table->timestamps();

            $table->index(['status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scanner_sessions');
    }
};
