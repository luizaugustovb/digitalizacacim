<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Alterar de ENUM para VARCHAR para suportar módulos configuráveis
        DB::statement("ALTER TABLE documentos MODIFY COLUMN tipo_documento VARCHAR(100) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE documentos MODIFY COLUMN tipo_documento ENUM('Guia Médica', 'Autorização/SADT', 'Documento Extra', 'Formulário') NOT NULL");
    }
};
