@extends('layouts.main')

@section('page-title', 'Detalhes da Importação')

@section('page-content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Cabeçalho -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detalhes da Importação</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Job #{{ $job->id }} • {{ $job->iniciado_em->format('d/m/Y H:i:s') }}
            </p>
        </div>
        <a href="{{ route('import.index') }}"
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
            Voltar
        </a>
    </div>

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total de Registros</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ number_format($job->total_registros, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Importados</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                        {{ number_format($job->importados, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Ignorados</p>
                    <p class="text-2xl font-bold text-gray-600 dark:text-gray-400 mt-1">
                        {{ number_format($job->ignorados, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Erros</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">
                        {{ number_format($job->erros, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações do Job -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informações do Job</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Tipo</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ strtoupper($job->tipo) }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                <p class="font-medium">
                    @if($job->status === 'concluido')
                    <span class="text-green-600 dark:text-green-400">✓ Concluído</span>
                    @elseif($job->status === 'processando')
                    <span class="text-yellow-600 dark:text-yellow-400">⟳ Processando</span>
                    @else
                    <span class="text-red-600 dark:text-red-400">✗ Erro</span>
                    @endif
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Iniciado em</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $job->iniciado_em->format('d/m/Y H:i:s') }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Finalizado em</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    @if($job->finalizado_em)
                    {{ $job->finalizado_em->format('d/m/Y H:i:s') }}
                    @else
                    Em andamento...
                    @endif
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Duração</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    @if($job->finalizado_em)
                    {{ $job->iniciado_em->diffForHumans($job->finalizado_em, true) }}
                    @else
                    {{ $job->iniciado_em->diffForHumans(null, true) }}
                    @endif
                </p>
            </div>

            @if($job->mensagem_erro)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Mensagem de Erro</p>
                <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-3">
                    <p class="text-sm text-red-800 dark:text-red-200">{{ $job->mensagem_erro }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Lista de Erros -->
    @if(count($erros) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Detalhes dos Erros ({{ count($erros) }})</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Código
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Erro
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($erros as $erro)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                            {{ $erro['codigo'] ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-red-600 dark:text-red-400">
                            {{ $erro['erro'] ?? 'Erro desconhecido' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection