@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tipos de Pendências</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gerencie os tipos de pendências disponíveis para devolução de documentos</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('configuracoes.index') }}"
                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar
            </a>
            <button onclick="showModal('create')"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nova Pendência
            </button>
        </div>
    </div>

    <!-- Mensagens -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <!-- Tabela de Pendências -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descrição</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Peso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($pendenciasTipos as $tipo)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $tipo->nome }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                        {{ $tipo->descricao ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $tipo->peso ?? 0 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($tipo->ativo)
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Ativo
                        </span>
                        @else
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            Inativo
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="editPendencia({{ $tipo->id }}, '{{ addslashes($tipo->nome) }}', '{{ addslashes($tipo->descricao ?? '') }}', {{ $tipo->peso ?? 0 }}, {{ $tipo->ativo ? 'true' : 'false' }})"
                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                            Editar
                        </button>
                        @if($tipo->ativo)
                        <form action="{{ route('configuracoes.pendencias.toggle', $tipo) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 mr-3">
                                Desativar
                            </button>
                        </form>
                        @else
                        <form action="{{ route('configuracoes.pendencias.toggle', $tipo) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3">
                                Ativar
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('configuracoes.pendencias.destroy', $tipo) }}" method="POST" class="inline"
                            onsubmit="return confirm('Tem certeza que deseja excluir esta pendência?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                Excluir
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-lg font-medium mb-2">Nenhuma pendência cadastrada</p>
                        <p class="text-sm mb-4">Clique no botão "Nova Pendência" para começar</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Criar/Editar -->
<div id="pendenciaModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full p-6">
        <h3 id="modalTitle" class="text-xl font-bold text-gray-900 dark:text-white mb-4">Nova Pendência</h3>

        <form id="pendenciaForm" method="POST" action="{{ route('configuracoes.pendencias.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" id="methodField" name="_method" value="POST">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nome <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nome" id="nome" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Ex: Documento ilegível">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Descrição
                </label>
                <textarea name="descricao" id="descricao" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Descreva quando usar esta pendência..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Peso (ordem de exibição)
                </label>
                <input type="number" name="peso" id="peso" value="0"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="ativo" id="ativo" value="1" checked
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="ativo" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                    Ativo
                </label>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    Salvar
                </button>
                <button type="button" onclick="hideModal()"
                    class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showModal(mode = 'create') {
        const modal = document.getElementById('pendenciaModal');
        const form = document.getElementById('pendenciaForm');
        const title = document.getElementById('modalTitle');

        if (mode === 'create') {
            title.textContent = 'Nova Pendência';
            form.action = '{{ route("configuracoes.pendencias.store") }}';
            document.getElementById('methodField').value = 'POST';
            form.reset();
            document.getElementById('ativo').checked = true;
        }

        modal.classList.remove('hidden');
    }

    function hideModal() {
        document.getElementById('pendenciaModal').classList.add('hidden');
    }

    function editPendencia(id, nome, descricao, peso, ativo) {
        const modal = document.getElementById('pendenciaModal');
        const form = document.getElementById('pendenciaForm');
        const title = document.getElementById('modalTitle');

        title.textContent = 'Editar Pendência';
        form.action = `/configuracoes/pendencias/${id}`;
        document.getElementById('methodField').value = 'PUT';

        document.getElementById('nome').value = nome;
        document.getElementById('descricao').value = descricao;
        document.getElementById('peso').value = peso;
        document.getElementById('ativo').checked = ativo;

        modal.classList.remove('hidden');
    }

    // Fechar modal ao clicar fora
    document.getElementById('pendenciaModal')?.addEventListener('click', function(e) {
        if (e.target === this) hideModal();
    });

    // Atalho ESC para fechar
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') hideModal();
    });
</script>
@endsection