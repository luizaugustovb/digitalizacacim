<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })->withSchedule(function ($schedule) {
        // Importação automática de pedidos (se habilitado nas configurações)
        $schedule->command('import:pedidos')
            ->dailyAt(\App\Helpers\ConfigHelper::get('horario_importacao', '02:00'))
            ->when(function () {
                return \App\Helpers\ConfigHelper::get('importacao_automatica', false);
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('Falha na importação automática de pedidos');
            });
    })->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
