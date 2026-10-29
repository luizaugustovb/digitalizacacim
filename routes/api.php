<?php

use App\Http\Controllers\TotemController;
use Illuminate\Support\Facades\Route;

Route::prefix('totem')->name('api.totem.')->group(function () {
    Route::post('/processar-documento', [TotemController::class, 'processarDocumento'])->name('processar-documento');
    Route::post('/processar-carteirinha', [TotemController::class, 'processarCarteirinha'])->name('processar-carteirinha');
    Route::post('/salvar-atendimento', [TotemController::class, 'salvarAtendimento'])->name('salvar-atendimento');
});
