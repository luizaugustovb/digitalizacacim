<div class="flow-root">
    <ul role="list" class="-mb-8">
        @forelse($timeline as $index => $log)
        <li>
            <div class="relative pb-8">
                @if(!$loop->last)
                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                @endif
                <div class="relative flex space-x-3">
                    <div>
                        @php
                        $icons = [
                        'PEDIDO_CRIADO' => '
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />',
                        'DOCUMENTO_ADICIONADO' => '
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
                        'DOCUMENTO_REMOVIDO' => '
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />',
                        'PEDIDO_ENVIADO' => '
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />',
                        'PEDIDO_APROVADO' => '
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
                        'PEDIDO_DEVOLVIDO' => '
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />',
                        'PENDENCIA_CRIADA' => '
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
                        'PENDENCIA_RESOLVIDA' => '
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
                        ];

                        $colors = [
                        'PEDIDO_CRIADO' => 'bg-blue-500',
                        'DOCUMENTO_ADICIONADO' => 'bg-purple-500',
                        'DOCUMENTO_REMOVIDO' => 'bg-red-500',
                        'PEDIDO_ENVIADO' => 'bg-green-500',
                        'PEDIDO_APROVADO' => 'bg-green-600',
                        'PEDIDO_DEVOLVIDO' => 'bg-red-600',
                        'PENDENCIA_CRIADA' => 'bg-yellow-500',
                        'PENDENCIA_RESOLVIDA' => 'bg-green-500',
                        ];
                        @endphp
                        <span class="{{ $colors[$log->acao] ?? 'bg-gray-500' }} h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $icons[$log->acao] ?? '
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />' !!}
                            </svg>
                        </span>
                    </div>
                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                        <div>
                            <p class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $log->descricao }}
                            </p>
                            @if($log->detalhes)
                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                {{ $log->detalhes }}
                            </p>
                            @endif
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $log->user?->nome ?? 'Sistema' }}
                            </p>
                        </div>
                        <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                            <time datetime="{{ $log->created_at->toIso8601String() }}">
                                {{ $log->created_at->format('d/m/Y') }}<br>
                                {{ $log->created_at->format('H:i') }}
                            </time>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        @empty
        <li class="text-center text-gray-500 dark:text-gray-400 py-8">
            Nenhum histórico disponível
        </li>
        @endforelse
    </ul>
</div>