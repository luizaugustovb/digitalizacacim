@extends('layouts.main')

@section('page-title', 'Convênios')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">Gerencie os convênios médicos do sistema</p>
        <div class="flex gap-3">
            <button onclick="importarConvenios()"
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Importar da Tabela tipo_g
            </button>
            <button onclick="showModal('create')"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Novo Convênio
            </button>
        </div>
    </div>

    @if(session('erros_importacao'))
    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200 rounded-lg">
        <p class="font-semibold mb-2">Erros durante a importação:</p>
        <ul class="list-disc list-inside text-sm">
            @foreach(session('erros_importacao') as $erro)
            <li>{{ $erro }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Tabela de Convênios -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pedidos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Módulos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($convenios as $convenio)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $convenio->nome }}</div>
                        @if($convenio->observacoes)
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($convenio->observacoes, 50) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $convenio->codigo }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $convenio->pedidos_count }} pedidos
                    </td>
                    <td class="px-6 py-4">
                        @if(!empty($convenio->modulos))
                        <div class="flex flex-wrap gap-1">
                            @foreach($convenio->modulos as $mod)
                            <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded">{{ $mod }}</span>
                            @endforeach
                        </div>
                        @else
                        <span class="text-xs text-gray-400">Padrão</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($convenio->ativo)
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
                        <button onclick="editConvenio({{ $convenio->id }}, '{{ addslashes($convenio->nome) }}', '{{ addslashes($convenio->codigo) }}', '{{ addslashes($convenio->observacoes ?? '') }}', {{ $convenio->ativo ? 'true' : 'false' }}, @json($convenio->modulos ?? []))"
                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                            Editar
                        </button>
                        @if($convenio->ativo)
                        <form action="{{ route('convenios.toggle', $convenio) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 mr-3">
                                Desativar
                            </button>
                        </form>
                        @else
                        <form action="{{ route('convenios.toggle', $convenio) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3">
                                Ativar
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('convenios.destroy', $convenio) }}" method="POST" class="inline"
                            onsubmit="return confirm('Tem certeza que deseja excluir este convênio?\n\nATENÇÃO: Todos os pedidos e documentos vinculados a ele também serão excluídos permanentemente!')">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <p class="text-lg font-medium mb-2">Nenhum convênio cadastrado</p>
                        <p class="text-sm mb-4">Clique em "Novo Convênio" ou "Importar da Tabela tipo_g" para começar</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Paginação -->
        @if($convenios->hasPages())
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700">
            {{ $convenios->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Criar/Editar -->
<div id="convenioModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full p-6">
        <h3 id="modalTitle" class="text-xl font-bold text-gray-900 dark:text-white mb-4">Novo Convênio</h3>

        <form id="convenioForm" method="POST" action="{{ route('convenios.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" id="methodField" name="_method" value="POST">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nome <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nome" id="nome" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Ex: Unimed">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Código <span class="text-red-500">*</span>
                </label>
                <input type="text" name="codigo" id="codigo" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Ex: UNIMED">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Observações
                </label>
                <textarea name="observacoes" id="observacoes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Informações adicionais sobre o convênio..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Módulos de Documentos
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Selecione os documentos obrigatórios para este convênio. Se nenhum for selecionado, usa o padrão do sistema.</p>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(\App\Models\Convenio::MODULOS_DISPONIVEIS as $modulo)
                    <label class="flex items-center gap-2 p-2 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                        <input type="checkbox" name="modulos[]" value="{{ $modulo }}"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 modulo-check">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $modulo }}</span>
                    </label>
                    @endforeach
                </div>
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

<!-- Form de Importação (oculto) -->
<form id="importForm" action="{{ route('convenios.importar') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
    function showModal(mode = 'create') {
        const modal = document.getElementById('convenioModal');
        const form = document.getElementById('convenioForm');
        const title = document.getElementById('modalTitle');

        if (mode === 'create') {
            title.textContent = 'Novo Convênio';
            form.action = '{{ route("convenios.store") }}';
            document.getElementById('methodField').value = 'POST';
            form.reset();
            document.getElementById('ativo').checked = true;
            // Limpar checkboxes de módulos
            document.querySelectorAll('.modulo-check').forEach(cb => cb.checked = false);
        }

        modal.classList.remove('hidden');
    }

    function hideModal() {
        document.getElementById('convenioModal').classList.add('hidden');
    }

    function editConvenio(id, nome, codigo, observacoes, ativo, modulos) {
        const modal = document.getElementById('convenioModal');
        const form = document.getElementById('convenioForm');
        const title = document.getElementById('modalTitle');

        title.textContent = 'Editar Convênio';
        form.action = `/convenios/${id}`;
        document.getElementById('methodField').value = 'PUT';

        document.getElementById('nome').value = nome;
        document.getElementById('codigo').value = codigo;
        document.getElementById('observacoes').value = observacoes;
        document.getElementById('ativo').checked = ativo;

        // Marcar checkboxes de módulos conforme configurado
        document.querySelectorAll('.modulo-check').forEach(cb => {
            cb.checked = Array.isArray(modulos) && modulos.includes(cb.value);
        });

        modal.classList.remove('hidden');
    }

    function importarConvenios() {
        if (confirm('Deseja importar convênios da tabela tipo_g (apenas tipo_guia = "G")?\n\nEsta ação pode criar vários novos convênios.')) {
            document.getElementById('importForm').submit();
        }
    }

    // Fechar modal ao clicar fora
    document.getElementById('convenioModal')?.addEventListener('click', function(e) {
        if (e.target === this) hideModal();
    });

    // Atalho ESC para fechar
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') hideModal();
    });
</script>
@endsection