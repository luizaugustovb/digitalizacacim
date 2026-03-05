<x-layout page-title="Integração Softlab">
    <div class="space-y-6" x-data="softlabData()">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Integração Softlab</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Configure os mapeamentos entre Softlab e o sistema</p>
            </div>
            <button @click="showAddModal = true" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                Adicionar Mapeamento
            </button>
        </div>

        <!-- Teste de Conexão -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Teste de Conexão</h2>
            <div class="flex gap-4">
                <form method="POST" action="{{ route('configuracoes.softlab.testar') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Testar Conexão
                    </button>
                </form>

                <button @click="buscarPedidos()" :disabled="buscando" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg flex items-center gap-2 disabled:opacity-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span x-text="buscando ? 'Buscando...' : 'Buscar Pedidos do Dia'"></span>
                </button>
            </div>
        </div>

        <!-- Tabela Pedidos do Dia (aparece após busca) -->
        <div x-show="pedidosSoftlab.length > 0" x-transition class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Pedidos do Softlab - Hoje (<span x-text="pedidosSoftlab.length"></span>)
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Código</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Paciente</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Posto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Origem</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Usuário</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Data/Hora</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="pedido in pedidosSoftlab" :key="pedido.cod_pedido">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white" x-text="pedido.cod_pedido"></td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="pedido.nome_cliente"></div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400" x-text="'Cod: ' + pedido.cod_cliente"></div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white" x-text="pedido.posto_cliente"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white" x-text="pedido.cod_origem"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white" x-text="pedido.usu_pedido"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white" x-text="pedido.datahora_atendimento"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mapeamentos de Unidades -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Mapeamento de Unidades</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Vincule códigos do Softlab (posto_cliente e cod_origem) às unidades do sistema</p>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Código Softlab</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nome Referência</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Unidade Sistema</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($mappings->get('unidade', collect()) as $mapping)
                    <tr>
                        <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">{{ $mapping->cod_softlab }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $mapping->nome_softlab ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $mapping->unidade->nome ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('configuracoes.softlab.mappings.delete', $mapping) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Confirma exclusão?')" class="text-red-600 dark:text-red-400 hover:underline text-sm">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Nenhum mapeamento de unidade cadastrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mapeamentos de Usuários -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Mapeamento de Usuários</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Vincule códigos de usuários do Softlab (usu_pedido) aos atendentes do sistema</p>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Código Softlab</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nome Referência</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Usuário Sistema</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($mappings->get('usuario', collect()) as $mapping)
                    <tr>
                        <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">{{ $mapping->cod_softlab }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $mapping->nome_softlab ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $mapping->user->nome ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('configuracoes.softlab.mappings.delete', $mapping) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Confirma exclusão?')" class="text-red-600 dark:text-red-400 hover:underline text-sm">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Nenhum mapeamento de usuário cadastrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Adicionar Mapeamento -->
        <div x-show="showAddModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @click.self="showAddModal = false">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Adicionar Mapeamento</h3>
                <form method="POST" action="{{ route('configuracoes.softlab.mappings.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo</label>
                        <select name="tipo" x-model="tipo" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="unidade">Unidade</option>
                            <option value="usuario">Usuário</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Código Softlab *</label>
                        <input type="text" name="cod_softlab" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Ex: 3, 01, USR123">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nome Referência (opcional)</label>
                        <input type="text" name="nome_softlab" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Ex: Matriz, João Silva">
                    </div>
                    <div x-show="tipo === 'unidade'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unidade do Sistema *</label>
                        <select name="unidade_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Selecione...</option>
                            @foreach($unidades as $unidade)
                            <option value="{{ $unidade->id }}">{{ $unidade->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="tipo === 'usuario'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Usuário do Sistema *</label>
                        <select name="user_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Selecione...</option>
                            @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->nome }} ({{ $usuario->role }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Salvar</button>
                        <button type="button" @click="showAddModal = false" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function softlabData() {
            return {
                showAddModal: false,
                tipo: 'unidade',
                testando: false,
                buscando: false,
                pedidosSoftlab: [],

                buscarPedidos() {
                    this.buscando = true;

                    fetch('{{ route("configuracoes.softlab.buscar") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.pedidosSoftlab = data.data;
                                alert(data.message);
                            } else {
                                alert('Erro: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            alert('Erro ao buscar pedidos: ' + error);
                        })
                        .finally(() => {
                            this.buscando = false;
                        });
                }
            }
        }
    </script>
</x-layout>