<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\ConferenciaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConfiguracaoController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/teste-simples-extends', function () {
        return view('teste-simples');
    });

    // Teste de layout
    Route::get('/test-layout', function () {
        return view('test-layout');
    });

    Route::get('/test-direct', function () {
        return view('layouts.main')->with('stats', ['pendentes' => 0, 'enviados' => 0, 'aprovados' => 0, 'devolvidos' => 0]);
    });

    // Pedidos
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/create', [PedidoController::class, 'create'])->name('pedidos.create');
    Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::get('/pedidos/{pedido}/escanear', [PedidoController::class, 'escanear'])->name('pedidos.escanear');
    Route::put('/pedidos/{pedido}/enviar', [PedidoController::class, 'enviar'])->name('pedidos.enviar');
    Route::put('/pedidos/{pedido}/aprovar', [PedidoController::class, 'aprovar'])->name('pedidos.aprovar');
    Route::get('/pedidos/{pedido}/devolver', [PedidoController::class, 'showDevolver'])->name('pedidos.devolver');
    Route::put('/pedidos/{pedido}/devolver', [PedidoController::class, 'devolver'])->name('pedidos.devolver.submit');
    Route::get('/pedidos/{pedido}/timeline', [PedidoController::class, 'timeline'])->name('pedidos.timeline');
    Route::delete('/pedidos/{pedido}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');

    // Documentos
    Route::post('/documentos/upload', [DocumentoController::class, 'upload'])->name('documentos.upload');
    Route::delete('/documentos/{documento}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');
    Route::get('/documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
    Route::get('/documentos/{documento}/preview', [DocumentoController::class, 'preview'])->name('documentos.preview');

    // Conferência
    Route::get('/conferencia', [ConferenciaController::class, 'index'])->name('conferencia.index');
    Route::get('/conferencia/{pedido}', [ConferenciaController::class, 'show'])->name('conferencia.show');
    Route::post('/conferencia/aprovar-lote', [ConferenciaController::class, 'aprovarLote'])->name('conferencia.aprovar-lote');
    Route::post('/conferencia/devolver-lote', [ConferenciaController::class, 'devolverLote'])->name('conferencia.devolver-lote');

    // Usuários (Admin/Gestor)
    Route::middleware(['role:ADMIN,GESTOR'])->group(function () {
        Route::resource('usuarios', UserController::class);

        // Convênios
        Route::prefix('convenios')->name('convenios.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ConvenioController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\ConvenioController::class, 'store'])->name('store');
            Route::put('/{convenio}', [\App\Http\Controllers\ConvenioController::class, 'update'])->name('update');
            Route::patch('/{convenio}/toggle', [\App\Http\Controllers\ConvenioController::class, 'toggle'])->name('toggle');
            Route::delete('/{convenio}', [\App\Http\Controllers\ConvenioController::class, 'destroy'])->name('destroy');
            Route::post('/importar', [\App\Http\Controllers\ConvenioController::class, 'importar'])->name('importar');
        });
    });

    // Configurações (Admin)
    Route::middleware(['role:ADMIN'])->group(function () {
        Route::get('/configuracoes', [ConfiguracaoController::class, 'index'])->name('configuracoes.index');
        Route::put('/configuracoes', [ConfiguracaoController::class, 'update'])->name('configuracoes.update');

        // Pendências
        Route::prefix('configuracoes/pendencias')->name('configuracoes.pendencias.')->group(function () {
            Route::get('/', [ConfiguracaoController::class, 'pendenciasIndex'])->name('index');
            Route::post('/', [ConfiguracaoController::class, 'pendenciasStore'])->name('store');
            Route::put('/{pendenciaTipo}', [ConfiguracaoController::class, 'pendenciasUpdate'])->name('update');
            Route::patch('/{pendenciaTipo}/toggle', [ConfiguracaoController::class, 'pendenciasToggle'])->name('toggle');
            Route::delete('/{pendenciaTipo}', [ConfiguracaoController::class, 'pendenciasDestroy'])->name('destroy');
        });

        // Softlab - Mapeamentos
        Route::prefix('configuracoes/softlab')->name('configuracoes.softlab.')->group(function () {
            Route::get('/mappings', [ConfiguracaoController::class, 'softlabMappings'])->name('mappings');
            Route::post('/mappings', [ConfiguracaoController::class, 'storeSoftlabMapping'])->name('mappings.store');
            Route::delete('/mappings/{mapping}', [ConfiguracaoController::class, 'deleteSoftlabMapping'])->name('mappings.delete');
            Route::post('/testar-conexao', [ConfiguracaoController::class, 'testarConexaoSoftlab'])->name('testar');
            Route::post('/buscar-pedidos', [ConfiguracaoController::class, 'buscarPedidosSoftlab'])->name('buscar');
        });

        // Importações
        Route::prefix('import')->name('import.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ImportController::class, 'index'])->name('index');
            Route::get('/{job}', [\App\Http\Controllers\ImportController::class, 'show'])->name('show');
            Route::post('/executar', [\App\Http\Controllers\ImportController::class, 'executar'])->name('executar');
        });
    });
});
