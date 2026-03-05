<?php

namespace App\Http\Controllers;

use App\Models\ImportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ImportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'role:ADMIN',
        ];
    }

    /**
     * Exibe o histórico de importações
     */
    public function index()
    {
        $jobs = ImportJob::orderBy('iniciado_em', 'desc')
            ->paginate(20);

        return view('import.index', compact('jobs'));
    }

    /**
     * Exibe detalhes de uma importação
     */
    public function show(ImportJob $job)
    {
        $erros = json_decode($job->detalhes_erros, true) ?? [];

        return view('import.show', compact('job', 'erros'));
    }

    /**
     * Executa importação manual
     */
    public function executar(Request $request)
    {
        $validated = $request->validate([
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
            'force' => 'boolean',
        ]);

        try {
            $params = [];

            if ($validated['date_start'] ?? false) {
                $params['--date-start'] = $validated['date_start'];
            }

            if ($validated['date_end'] ?? false) {
                $params['--date-end'] = $validated['date_end'];
            }

            if ($validated['force'] ?? false) {
                $params['--force'] = true;
            }

            // Executar comando em background
            Artisan::call('import:pedidos', $params);

            $output = Artisan::output();

            return redirect()->route('import.index')
                ->with('success', 'Importação iniciada com sucesso!')
                ->with('output', $output);
        } catch (\Exception $e) {
            return redirect()->route('import.index')
                ->with('error', 'Erro ao iniciar importação: ' . $e->getMessage());
        }
    }
}
