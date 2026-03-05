<x-layout page-title="Gerenciamento de Pedidos">
    <div class="space-y-6" x-data="{ showFilters: false }">
        <!-- Cabeçalho com estatísticas -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pedidos</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gerencie todos os pedidos do sistema</p>
                </div>

                @if(auth()->user()->isAtendente())
                <a href="{{ route('pedidos.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Novo Pedido
                </a>
                @endif
            </div>

            <!-- Abas de Status -->
            <div class="flex flex-wrap gap-2 mb-4">
                <a href="{{ route('pedidos.index') }}" class="px-4 py-2 rounded-lg {{ !request('status') ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Todos ({{ $stats['TODOS'] }})
                </a>
                <a href="{{ route('pedidos.index', ['status' => 'PENDENTE']) }}" class="px-4 py-2 rounded-lg {{ request('status') === 'PENDENTE' ? 'bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Pendentes ({{ $stats['PENDENTE'] }})
                </a>
                <a href="{{ route('pedidos.index', ['status' => 'ENVIADO']) }}" class="px-4 py-2 rounded-lg {{ request('status') === 'ENVIADO' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Enviados ({{ $stats['ENVIADO'] }})
                </a>
                <a href="{{ route('pedidos.index', ['status' => 'APROVADO']) }}" class="px-4 py-2 rounded-lg {{ request('status') === 'APROVADO' ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Aprovados ({{ $stats['APROVADO'] }})
                </a>
                <a href="{{ route('pedidos.index', ['status' => 'DEVOLVIDO']) }}" class="px-4 py-2 rounded-lg {{ request('status') === 'DEVOLVIDO' ? 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Devolvidos ({{ $stats['DEVOLVIDO'] }})
                </a>
                <a href="{{ route('pedidos.index', ['status' => 'NAO_CADASTRADO']) }}" class="px-4 py-2 rounded-lg {{ request('status') === 'NAO_CADASTRADO' ? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Não Cadastrados ({{ $stats['NAO_CADASTRADO'] }})
                </a>
            </div>

            <!-- Toggle Filtros -->
            <button @click="showFilters = !showFilters" class="text-sm text-green-600 dark:text-green-400 hover:underline mb-4 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span x-text="showFilters ? 'Ocultar Filtros' : 'Mostrar Filtros'"></span>
            </button>

            <!-- Formulário de Filtros -->
            <form method="GET" x-show="showFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Convênio</label>
                    <select name="convenio_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Todos</option>
                        @foreach($convenios as $convenio)
                        <option value="{{ $convenio->id }}" {{ request('convenio_id') == $convenio->id ? 'selected' : '' }}>{{ $convenio->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unidade</label>
                    <select name="unidade_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Todas</option>
                        @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}" {{ request('unidade_id') == $unidade->id ? 'selected' : '' }}>{{ $unidade->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Atendente</label>
                    <select name="atendente_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Todos</option>
                        @foreach($atendentes as $atendente)
                        <option value="{{ $atendente->id }}" {{ request('atendente_id') == $atendente->id ? 'selected' : '' }}>{{ $atendente->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Início</label>
                    <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Fim</label>
                    <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Busca</label>
                    <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Código pedido, paciente..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div class="md:col-span-3 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Filtrar</button>
                    <a href="{{ route('pedidos.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">Limpar</a>
                </div>
            </form>
        </div>

        <!-- Lista de Pedidos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">CodSoftlab</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Paciente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Atendente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Convênio</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Data</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Documentos</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pedidos as $pedido)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">{{ $pedido->codigo_pedido }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $pedido->nome_paciente }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $pedido->atendente->nome ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $pedido->cod_guia ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $pedido->data_atendimento ? $pedido->data_atendimento->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3">
                            @php
                            $statusColors = [
                            'PENDENTE' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                            'Pendente' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                            'ENVIADO' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                            'Enviado' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                            'APROVADO' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'Aprovado' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'DEVOLVIDO' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            'Devolvido' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            'NAO_CADASTRADO' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                            ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded {{ $statusColors[$pedido->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $pedido->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                            <span class="font-medium">{{ $pedido->documentos->count() }}</span> arquivos
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('pedidos.escanear', $pedido) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Escanear</a>
                                <span class="text-gray-300 dark:text-gray-600">|</span>
                                <a href="{{ route('pedidos.show', $pedido) }}" class="text-green-600 dark:text-green-400 hover:underline text-sm">Ver Detalhes</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            Nenhum pedido encontrado
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        @if($pedidos->hasPages())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            {{ $pedidos->links() }}
        </div>
        @endif
    </div>
</x-layout>