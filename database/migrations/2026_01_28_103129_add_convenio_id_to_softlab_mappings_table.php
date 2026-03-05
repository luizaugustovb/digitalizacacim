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
        Schema::table('softlab_mappings', function (Blueprint $table) {
            $table->foreignId('convenio_id')->nullable()->after('user_id')->constrained('convenios')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('softlab_mappings', function (Blueprint $table) {
            $table->dropForeign(['convenio_id']);
            $table->dropColumn('convenio_id');
        });
    }
};
