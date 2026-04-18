@extends('layouts.main')

@section('page-title', 'Devolver Pedido')

@section('page-content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Cabeçalho -->
    <div>
        <a href="{{ route('pedidos.index') }}"
            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Voltar
        </a>

        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Devolver Pedido</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Selecione os motivos da devolução
        </p>
    </div>

    <!-- Informações do Pedido -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informações do Pedido</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Código:</span>
                <p class="font-medium text-gray-900 dark:text-white">{{ $pedido->codigo }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Data:</span>
                <p class="font-medium text-gray-900 dark:text-white">{{ $pedido->data->format('d/m/Y') }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Paciente:</span>
                <p class="font-medium text-gray-900 dark:text-white">{{ $pedido->nome_paciente }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Convênio:</span>
                <p class="font-medium text-gray-900 dark:text-white">{{ $pedido->convenio->nome }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Atendente:</span>
                <p class="font-medium text-gray-900 dark:text-white">{{ $pedido->atendente->nome }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Unidade:</span>
                <p class="font-medium text-gray-900 dark:text-white">{{ $pedido->unidade->nome }}</p>
            </div>
        </div>
    </div>

    <!-- Formulário de Devolução -->
    <form method="POST" action="{{ route('pedidos.devolver.submit', $pedido) }}">
        @csrf
        @method('PUT')

        <!-- Motivo da Devolução -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Motivo da Devolução</h2>
            <textarea name="motivo_devolucao"
                rows="4"
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="Descreva o motivo da devolução...">{{ old('motivo_devolucao') }}</textarea>
            @error('motivo_devolucao')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Pendências -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Selecione as Pendências</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Marque todas as pendências encontradas no pedido
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($pendenciasTipos as $tipo)
                <label class="flex items-start p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <input type="checkbox"
                        name="pendencias[]"
                        value="{{ $tipo->id }}"
                        {{ in_array($tipo->id, old('pendencias', [])) ? 'checked' : '' }}
                        class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <div class="ml-3">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $tipo->nome }}
                        </span>
                        @if($tipo->descricao)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $tipo->descricao }}
                        </p>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>

            @error('pendencias')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Botões -->
        <div class="flex gap-3">
            <button type="submit"
                class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium">
                Devolver Pedido
            </button>
            <a href="{{ route('pedidos.index') }}"
                class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition font-medium">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection