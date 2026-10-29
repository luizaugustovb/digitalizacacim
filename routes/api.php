<?php

use App\Http\Controllers\TotemController;
use App\Http\Controllers\ScannerSessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('totem')->name('api.totem.')->group(function () {
    Route::post('/processar-documento', [TotemController::class, 'processarDocumento'])->name('processar-documento');
    Route::post('/processar-carteirinha', [TotemController::class, 'processarCarteirinha'])->name('processar-carteirinha');
    Route::post('/salvar-atendimento', [TotemController::class, 'salvarAtendimento'])->name('salvar-atendimento');
});

Route::post('/scanner-sessions/{scannerSession}/document', [ScannerSessionController::class, 'upload'])
    ->middleware('throttle:10,1')
    ->name('api.scanner-sessions.upload');
