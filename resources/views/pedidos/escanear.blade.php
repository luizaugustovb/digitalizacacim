@extends('layouts.main')

@section('page-title', 'Escanear Documentos')

@section('page-content')
<div x-data="{ showConfirmEnviar: false }" class="max-w-5xl mx-auto space-y-6">
    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <div>
                <p class="text-sm font-medium text-red-800 dark:text-red-200 mb-1">Erro ao enviar documento:</p>
                <ul class="text-sm text-red-700 dark:text-red-300 list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Cabeçalho -->
    <div>
        <a href="{{ route('pedidos.show', $pedido) }}"
            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-2 mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Voltar para Detalhes
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Escanear Documentos</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Pedido #{{ $pedido->codigo_pedido }} - {{ $pedido->nome_paciente }}
        </p>
    </div>

    <!-- Informações do Pedido -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Paciente</span>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $pedido->nome_paciente }}</p>
            </div>
            <div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Convênio</span>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $pedido->cod_guia ?? '-' }}</p>
            </div>
            <div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data</span>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $pedido->data_atendimento ? $pedido->data_atendimento->format('d/m/Y') : '-' }}</p>
            </div>
            <div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo</span>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $pedido->tipo_atendimento ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @php
    $MODULOS_ICONES = [
    'Controle Interno' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'Requisição Médica' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
    'Autorização' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'Guia TISS' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'Guia Médica' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'Autorização/SADT' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'Documento Extra' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
    'Formulário' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
    ];

    $convenioModulos = $pedido->convenio?->modulos;
    if (!empty($convenioModulos)) {
    $tiposDocumento = collect($convenioModulos)->map(fn($mod) => [
    'tipo' => $mod,
    'obrigatorio' => true,
    'icon' => $MODULOS_ICONES[$mod] ?? $MODULOS_ICONES['Controle Interno'],
    ])->toArray();
    } else {
    $tiposDocumento = [
    ['tipo' => 'Guia Médica', 'obrigatorio' => true, 'icon' => $MODULOS_ICONES['Guia Médica']],
    ['tipo' => 'Autorização/SADT', 'obrigatorio' => true, 'icon' => $MODULOS_ICONES['Autorização/SADT']],
    ['tipo' => 'Documento Extra', 'obrigatorio' => false, 'icon' => $MODULOS_ICONES['Documento Extra']],
    ['tipo' => 'Formulário', 'obrigatorio' => false, 'icon' => $MODULOS_ICONES['Formulário']],
    ];
    }

    $modulosObrigatorios = collect($tiposDocumento)->where('obrigatorio', true)->pluck('tipo');
    @endphp

    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div class="text-sm">
                <p class="font-semibold text-blue-800 dark:text-blue-200">Documentos Obrigatórios</p>
                <ul class="mt-1 text-blue-700 dark:text-blue-300 list-disc list-inside">
                    @foreach($modulosObrigatorios as $modulo)
                    <li>{{ $modulo }}</li>
                    @endforeach
                </ul>
                <p class="mt-2 text-blue-600 dark:text-blue-400">Formato aceito: PDF, JPG, JPEG, PNG (máx. 10MB)</p>
            </div>
        </div>
    </div>

    <!-- Formulários de Upload por Tipo -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @php
        $documentosExistentes = $pedido->documentos->pluck('tipo_documento')->toArray();
        @endphp

        @foreach($tiposDocumento as $doc)
        @php
        $jaAnexado = in_array($doc['tipo'], $documentosExistentes);
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 {{ $jaAnexado ? 'ring-2 ring-green-500' : '' }}">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 {{ $jaAnexado ? 'bg-green-100 dark:bg-green-900' : 'bg-gray-100 dark:bg-gray-700' }} rounded-lg">
                        <svg class="w-6 h-6 {{ $jaAnexado ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $doc['icon'] }}" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $doc['tipo'] }}</h3>
                        @if($doc['obrigatorio'])
                        <span class="px-2 py-0.5 text-xs font-semibold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 rounded">OBRIGATÓRIO</span>
                        @else
                        <span class="text-xs text-gray-500 dark:text-gray-400">Opcional</span>
                        @endif
                    </div>
                </div>
                @if($jaAnexado)
                <div class="flex items-center gap-2 px-3 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-full text-xs font-medium">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Anexado
                </div>
                @endif
            </div>

            @if(!$jaAnexado)
            <form method="POST" action="{{ route('documentos.upload') }}" enctype="multipart/form-data"
                x-data="scannerForm()">
                @csrf
                <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
                <input type="hidden" name="tipo_documento" value="{{ $doc['tipo'] }}">

                <div class="space-y-3">
                    <!-- Botões de Opção -->
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="iniciarScanner()" :disabled="escaneando"
                            :class="escaneando ? 'bg-gray-400 cursor-wait' : 'bg-purple-600 hover:bg-purple-700'"
                            class="px-4 py-3 text-white rounded-lg transition font-medium text-sm flex items-center justify-center gap-2">
                            <svg x-show="!escaneando" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                            <svg x-show="escaneando" x-cloak class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="escaneando ? 'Escaneando...' : 'Escanear Agora'"></span>
                        </button>
                        <label class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium text-sm flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Selecionar Arquivo
                            <input type="file"
                                x-ref="fileInput"
                                name="arquivo"
                                @change="arquivo = $event.target.files[0]"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="hidden">
                        </label>
                    </div>

                    <div x-show="arquivo" class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded text-xs text-blue-700 dark:text-blue-300">
                        <span class="font-medium">Arquivo selecionado:</span>
                        <span x-text="arquivo ? arquivo.name : ''"></span>
                    </div>

                    <button type="submit"
                        class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Anexar {{ $doc['tipo'] }}
                    </button>
                </div>
            </form>
            @else
            <div class="text-center py-4">
                <p class="text-sm text-green-600 dark:text-green-400 font-medium">✓ Documento já anexado</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Outros Documentos -->
    <div id="outros-documentos" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6"
        x-data="outrosDocumentos()" x-init="init()">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Outros Documentos</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Documentos adicionais — cada item pode ter múltiplas páginas</p>
            </div>
            <button type="button" @click="abrirNovo()"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Novo Documento
            </button>
        </div>

        @php
        $outrosDocs = $pedido->documentos->where('tipo_documento', 'Outros Documentos');
        $gruposExistentes = $outrosDocs->groupBy('grupo');
        @endphp

        @if($gruposExistentes->count() > 0)
        <div class="space-y-3 mb-4">
            @foreach($gruposExistentes as $grupoNome => $grupoDocs)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-indigo-100 dark:bg-indigo-900 rounded">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $grupoNome }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">
                            {{ $grupoDocs->count() }} {{ $grupoDocs->count() == 1 ? 'página' : 'páginas' }}
                        </span>
                    </div>
                    <button type="button"
                        @click="adicionarPagina('{{ addslashes($grupoNome) }}')"
                        class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 flex items-center gap-1 border border-indigo-300 dark:border-indigo-700 px-2 py-1 rounded-lg transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Adicionar página
                    </button>
                </div>
                <div class="space-y-1.5">
                    @foreach($grupoDocs as $i => $doc)
                    <div class="flex items-center justify-between py-1.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded text-sm">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            <span class="text-xs font-medium text-gray-400 w-5">p{{ $i + 1 }}</span>
                            <span class="text-gray-600 dark:text-gray-300 truncate">{{ $doc->arquivo_nome }}</span>
                            <span class="text-xs text-gray-400">{{ number_format($doc->tamanho / 1024, 0) }}KB</span>
                        </div>
                        <div class="flex items-center gap-1 ml-2">
                            <a href="{{ route('documentos.preview', $doc) }}" target="_blank"
                                class="p-1 text-blue-500 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('documentos.destroy', $doc) }}" class="inline"
                                onsubmit="return confirm('Remover esta página?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Modal Upload -->
        <div x-show="modalAberto" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="modalAberto = false">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" x-text="titulo"></h3>

                <form method="POST" action="{{ route('documentos.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
                    <input type="hidden" name="tipo_documento" value="Outros Documentos">
                    <input type="hidden" name="mais_paginas_redirect" value="1">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nome do Documento <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="grupo" x-model="grupo" :readonly="grupoFixo"
                                :class="grupoFixo ? 'bg-gray-100 dark:bg-gray-600 cursor-not-allowed' : ''"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Ex: Laudo Médico, Receita, Prontuário..." required>
                            <p x-show="grupoFixo" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Adicionando página a: <strong x-text="grupo"></strong>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Arquivo</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" @click="iniciarScanner()" :disabled="escaneando"
                                    :class="escaneando ? 'bg-gray-400 cursor-wait' : 'bg-purple-600 hover:bg-purple-700'"
                                    class="px-4 py-3 text-white rounded-lg transition font-medium text-sm flex items-center justify-center gap-2">
                                    <svg x-show="!escaneando" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                    </svg>
                                    <svg x-show="escaneando" x-cloak class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="escaneando ? 'Escaneando...' : 'Escanear'"></span>
                                </button>
                                <label class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium text-sm flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    Selecionar
                                    <input type="file" x-ref="fileInput" name="arquivo"
                                        @change="arquivo = $event.target.files[0]"
                                        accept=".pdf,.jpg,.jpeg,.png" class="hidden" required>
                                </label>
                            </div>
                            <div x-show="arquivo" class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded text-xs text-blue-700 dark:text-blue-300">
                                <span class="font-medium">Selecionado:</span> <span x-text="arquivo ? arquivo.name : ''"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-5">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium text-sm">
                            Anexar
                        </button>
                        <button type="button" @click="modalAberto = false"
                            class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal "Mais Páginas?" -->
        <div x-show="modalMaisPaginas" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-sm w-full p-6 text-center">
                <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Mais páginas?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Deseja adicionar mais páginas ao documento <strong class="text-gray-900 dark:text-white" x-text="grupoMaisPaginas"></strong>?
                </p>
                <div class="flex gap-3">
                    <button type="button" @click="simMaisPaginas()"
                        class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition font-medium text-sm">
                        Sim, adicionar página
                    </button>
                    <button type="button" @click="modalMaisPaginas = false"
                        class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition text-sm">
                        Não, concluir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentos Já Anexados -->
    @if($pedido->documentos->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Documentos Anexados</h2>
        <div class="space-y-3">
            @foreach($pedido->documentos as $documento)
            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <div class="flex items-center gap-4 flex-1">
                    <!-- Ícone -->
                    @php
                    $iconeClass = [
                    'Guia Médica' => 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300',
                    'Autorização/SADT' => 'bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300',
                    'Documento Extra' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                    'Formulário' => 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300',
                    ];
                    @endphp
                    <div class="w-12 h-12 rounded-lg {{ $iconeClass[$documento->tipo_documento] ?? 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>

                    <!-- Informações -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $documento->tipo_documento }}
                            @if(in_array($documento->tipo_documento, $modulosObrigatorios->toArray()))
                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 rounded">OBRIGATÓRIO</span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $documento->arquivo_nome }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            {{ number_format($documento->tamanho / 1024, 2) }} KB •
                            {{ $documento->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                <!-- Ações -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('documentos.preview', $documento) }}"
                        target="_blank"
                        class="p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 rounded-lg transition"
                        title="Visualizar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('documentos.destroy', $documento) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('Tem certeza que deseja remover este documento?')"
                            class="p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 rounded-lg transition"
                            title="Remover">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Ações Rápidas -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ações</h2>
        <div class="space-y-3">
            <div class="flex gap-3">
                <!-- Botão Salvar (sempre disponível) -->
                <a href="{{ route('pedidos.show', $pedido) }}"
                    class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Salvar e Voltar
                </a>

                <!-- Botão Enviar (só se tiver documentos obrigatórios) -->
                @if($pedido->temDocumentosObrigatorios())
                <form method="POST" action="{{ route('pedidos.enviar', $pedido) }}" class="flex-1" id="enviarFormEscanear">
                    @csrf
                    @method('PUT')
                    <button type="button"
                        @click="showConfirmEnviar = true"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Enviar para Conferência
                    </button>
                </form>
                @endif
            </div>

            @if(!$pedido->temDocumentosObrigatorios())
            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    ⚠️ Anexe os documentos obrigatórios (Guia Médica e Autorização/SADT) para enviar o pedido
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div x-show="showConfirmEnviar"
        x-cloak
        @click.self="showConfirmEnviar = false"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirmar Envio</h3>
                    </div>
                </div>

                <p class="text-gray-600 dark:text-gray-300 mb-6">Tem certeza que deseja enviar este pedido para conferência?</p>

                <div class="flex gap-3 justify-end">
                    <button @click="showConfirmEnviar = false"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg transition">
                        Cancelar
                    </button>
                    <button @click="document.getElementById('enviarFormEscanear').submit()"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/scanner.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/scanner.js') }}" type="text/javascript"></script>
<script>
    // Configurar Asprise Scanner
    if (typeof scanner !== 'undefined') {
        scanner.setLicenseKey(""); // Versão gratuita/trial
        console.log('Asprise Scanner carregado com sucesso!');
    } else {
        console.error('Asprise Scanner não foi carregado. Verifique se o arquivo scanner.js está acessível.');
    }

    // Função Alpine.js para o formulário de scanner (módulos obrigatórios)
    function scannerForm() {
        return {
            arquivo: null,
            escaneando: false,

            iniciarScanner() {
                console.log('Iniciando scanner...');

                if (typeof scanner === 'undefined') {
                    alert('Biblioteca do scanner não está carregada. Use a opção Selecionar Arquivo.');
                    return;
                }

                this.escaneando = true;

                scanner.scan((successful, mesg, response) => {
                    this.escaneando = false;

                    if (!successful) {
                        console.error('Erro ao escanear:', mesg);
                        alert('Erro ao escanear: ' + mesg);
                        return;
                    }

                    const images = (typeof scanner !== 'undefined' && scanner.getScannedImages) ?
                        scanner.getScannedImages(response, true, false) : [];

                    if (images && images.length > 0) {
                        const base64Data = images[0].src.replace(/^data:image\/(png|jpg|jpeg);base64,/, '');
                        const blob = this.base64ToBlob(base64Data, images[0].mimeType || 'image/jpeg');
                        const file = new File([blob], 'scan_' + Date.now() + '.jpg', {
                            type: images[0].mimeType || 'image/jpeg'
                        });

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        this.$refs.fileInput.files = dataTransfer.files;
                        this.arquivo = file;

                        alert('Documento escaneado com sucesso!');
                    } else {
                        alert('Nenhuma imagem foi escaneada.');
                    }
                }, {
                    output_settings: [{
                        type: 'return-base64',
                        format: 'jpg',
                        quality: 92
                    }],
                    use_asprise_dialog: true,
                    show_scanner_ui: true,
                    resolution: 300,
                    page_size: 'auto',
                    pixel_type: 'color'
                });
            },

            base64ToBlob(base64, contentType) {
                const byteCharacters = atob(base64);
                const byteArrays = [];

                for (let offset = 0; offset < byteCharacters.length; offset += 512) {
                    const slice = byteCharacters.slice(offset, offset + 512);
                    const byteNumbers = new Array(slice.length);

                    for (let i = 0; i < slice.length; i++) {
                        byteNumbers[i] = slice.charCodeAt(i);
                    }

                    byteArrays.push(new Uint8Array(byteNumbers));
                }

                return new Blob(byteArrays, {
                    type: contentType
                });
            }
        };
    }

    // Função Alpine.js para "Outros Documentos" (multi-página)
    function outrosDocumentos() {
        return {
            modalAberto: false,
            modalMaisPaginas: false,
            titulo: 'Novo Documento',
            grupo: '',
            grupoFixo: false,
            grupoMaisPaginas: '',
            arquivo: null,
            escaneando: false,

            init() {
                const params = new URLSearchParams(window.location.search);
                if (params.get('mais_paginas') === '1') {
                    const g = params.get('grupo');
                    if (g) {
                        this.grupoMaisPaginas = decodeURIComponent(g);
                        window.history.replaceState({}, '', window.location.pathname + '#outros-documentos');
                        this.$nextTick(() => {
                            this.modalMaisPaginas = true;
                        });
                    }
                }
            },

            abrirNovo() {
                this.titulo = 'Novo Documento';
                this.grupo = '';
                this.grupoFixo = false;
                this.arquivo = null;
                this.modalAberto = true;
            },

            adicionarPagina(g) {
                this.titulo = 'Adicionar Página — ' + g;
                this.grupo = g;
                this.grupoFixo = true;
                this.arquivo = null;
                this.modalAberto = true;
            },

            simMaisPaginas() {
                this.modalMaisPaginas = false;
                this.adicionarPagina(this.grupoMaisPaginas);
            },

            iniciarScanner() {
                if (typeof scanner === 'undefined') {
                    alert('Biblioteca do scanner não está carregada. Use "Selecionar".');
                    return;
                }

                this.escaneando = true;

                scanner.scan((successful, mesg, response) => {
                    this.escaneando = false;

                    if (!successful) {
                        alert('Erro ao escanear: ' + mesg);
                        return;
                    }

                    const images = scanner.getScannedImages ? scanner.getScannedImages(response, true, false) : [];

                    if (images && images.length > 0) {
                        const base64Data = images[0].src.replace(/^data:image\/(png|jpg|jpeg);base64,/, '');
                        const byteCharacters = atob(base64Data);
                        const byteArrays = [];
                        for (let offset = 0; offset < byteCharacters.length; offset += 512) {
                            const slice = byteCharacters.slice(offset, offset + 512);
                            const byteNumbers = new Array(slice.length);
                            for (let i = 0; i < slice.length; i++) byteNumbers[i] = slice.charCodeAt(i);
                            byteArrays.push(new Uint8Array(byteNumbers));
                        }
                        const blob = new Blob(byteArrays, {
                            type: images[0].mimeType || 'image/jpeg'
                        });
                        const file = new File([blob], 'scan_' + Date.now() + '.jpg', {
                            type: blob.type
                        });

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        this.$refs.fileInput.files = dataTransfer.files;
                        this.arquivo = file;
                    } else {
                        alert('Nenhuma imagem foi escaneada.');
                    }
                }, {
                    output_settings: [{
                        type: 'return-base64',
                        format: 'jpg',
                        quality: 92
                    }],
                    use_asprise_dialog: true,
                    show_scanner_ui: true,
                    resolution: 300,
                    page_size: 'auto',
                    pixel_type: 'color'
                });
            }
        };
    }
</script>
@endpush