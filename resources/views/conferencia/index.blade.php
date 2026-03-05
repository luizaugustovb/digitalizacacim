<x-layout page-title="Conferência de Pedidos">
    <div class="space-y-6" x-data="{ selecionados: [], selecionarTodos: false, showAprovarModal: false, showDevolverModal: false }">
        <!-- Cabeçalho -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Conferência de Pedidos</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Analise e aprove os pedidos enviados
                </p>
            </div>

            <!-- Estatísticas -->
            <div class="flex gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Total Aguardando</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['hoje'] }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Enviados Hoje</p>
                </div>
                @if($stats['pendentes_7dias'] > 0)
                <div class="text-center">
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['pendentes_7dias'] }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">+7 dias</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Ações em Lote -->
        <div x-show="selecionados.length > 0"
            x-transition
            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        <span x-text="selecionados.length"></span> pedido(s) selecionado(s)
                    </span>
                </div>
                <div class="flex gap-2">
                    <button @click="showAprovarModal = true"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Aprovar Selecionados
                    </button>
                    <button @click="showDevolverModal = true"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Devolver Selecionados
                    </button>
                    <button @click="selecionados = []; selecionarTodos = false"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg transition">
                        Limpar Seleção
                    </button>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <form method="GET" action="{{ route('conferencia.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Data Inicial -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Inicial</label>
                        <input type="date" name="data_inicio" value="{{ request('data_inicio') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Data Final -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Final</label>
                        <input type="date" name="data_fim" value="{{ request('data_fim') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Convênio -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Convênio</label>
                        <select name="convenio_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Todos</option>
                            @foreach($convenios as $convenio)
                            <option value="{{ $convenio->id }}" {{ request('convenio_id') == $convenio->id ? 'selected' : '' }}>
                                {{ $convenio->nome }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Unidade -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unidade</label>
                        <select name="unidade_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Todas</option>
                            @foreach($unidades as $unidade)
                            <option value="{{ $unidade->id }}" {{ request('unidade_id') == $unidade->id ? 'selected' : '' }}>
                                {{ $unidade->nome }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Atendente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Atendente</label>
                        <select name="atendente_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Todos</option>
                            @foreach($atendentes as $atendente)
                            <option value="{{ $atendente->id }}" {{ request('atendente_id') == $atendente->id ? 'selected' : '' }}>
                                {{ $atendente->nome }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Busca -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nº Pedido ou Paciente..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Filtrar
                    </button>
                    <a href="{{ route('conferencia.index') }}"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Lista de Pedidos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox"
                                    x-model="selecionarTodos"
                                    @change="selecionarTodos ? selecionados = {{ $pedidos->pluck('id') }} : selecionados = []"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Código
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Paciente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Convênio
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Atendente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Enviado em
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Docs
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($pedidos as $pedido)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4">
                                <input type="checkbox"
                                    :value="{{ $pedido->id }}"
                                    x-model="selecionados"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $pedido->codigo_pedido }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $pedido->tipo_atendimento ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $pedido->nome_paciente }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $pedido->data_atendimento ? $pedido->data_atendimento->format('d/m/Y') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $pedido->convenio?->nome ?? $pedido->cod_guia ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $pedido->atendente?->nome ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $pedido->data_envio ? $pedido->data_envio->format('d/m/Y') : '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $pedido->data_envio ? $pedido->data_envio->format('H:i') : '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $pedido->documentos->count() }} docs
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('conferencia.show', $pedido) }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    Conferir
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-lg">Nenhum pedido aguardando conferência</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($pedidos->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $pedidos->links() }}
            </div>
            @endif
        </div>

        <!-- Modal Aprovar Lote -->
        <div x-show="showAprovarModal"
            x-transition
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            @click.self="showAprovarModal = false">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Aprovar Pedidos em Lote</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Tem certeza que deseja aprovar <span class="font-bold" x-text="selecionados.length"></span> pedido(s) selecionado(s)?
                </p>
                <form method="POST" action="{{ route('conferencia.aprovar-lote') }}">
                    @csrf
                    <template x-for="id in selecionados">
                        <input type="hidden" name="pedidos[]" :value="id">
                    </template>
                    <div class="flex gap-3">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                            Confirmar Aprovação
                        </button>
                        <button type="button"
                            @click="showAprovarModal = false"
                            class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Devolver Lote -->
        <div x-show="showDevolverModal"
            x-transition
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 overflow-y-auto"
            @click.self="showDevolverModal = false">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full p-6 my-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Devolver Pedidos em Lote</h3>
                <form method="POST" action="{{ route('conferencia.devolver-lote') }}" class="space-y-4">
                    @csrf
                    <template x-for="id in selecionados">
                        <input type="hidden" name="pedidos[]" :value="id">
                    </template>

                    <!-- Motivo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Motivo da Devolução <span class="text-red-500">*</span>
                        </label>
                        <textarea name="motivo_devolucao"
                            rows="3"
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Descreva o motivo da devolução..."></textarea>
                    </div>

                    <!-- Pendências -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Pendências <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2 max-h-60 overflow-y-auto">
                            @foreach(\App\Models\PendenciaTipo::where('ativo', true)->orderBy('nome')->get() as $tipo)
                            <label class="flex items-center p-2 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                <input type="checkbox" name="pendencias[]" value="{{ $tipo->id }}" class="mr-2">
                                <span class="text-sm text-gray-900 dark:text-white">{{ $tipo->nome }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            Confirmar Devolução
                        </button>
                        <button type="button"
                            @click="showDevolverModal = false"
                            class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>