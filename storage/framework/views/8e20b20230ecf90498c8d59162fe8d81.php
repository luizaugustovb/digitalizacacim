

<?php $__env->startSection('page-title', 'Detalhes do Pedido'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="{ showConfirmModal: false, confirmAction: null }" class="max-w-7xl mx-auto space-y-6">
    <!-- Cabeçalho -->
    <div class="flex items-center justify-between">
        <div>
            <a href="<?php echo e(route('pedidos.index')); ?>"
                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-2 mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar para Lista
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pedido #<?php echo e($pedido->codigo_pedido); ?></h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                <?php echo e($pedido->nome_paciente); ?> • <?php echo e($pedido->data_atendimento ? $pedido->data_atendimento->format('d/m/Y') : 'Sem data'); ?>

            </p>
        </div>

        <!-- Status Badge -->
        <div>
            <?php
            $statusColors = [
            'PENDENTE' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            'Pendente' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            'ENVIADO' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'Enviado' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'APROVADO' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'Aprovado' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'DEVOLVIDO' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'Devolvido' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'NAO_CADASTRADO' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            ];
            ?>
            <span class="px-4 py-2 text-base font-semibold rounded-full <?php echo e($statusColors[$pedido->status] ?? ''); ?>">
                <?php echo e(str_replace('_', ' ', $pedido->status)); ?>

            </span>
        </div>
    </div>

    <!-- Alertas de Pendências -->
    <?php if($pedido->pendencias->where('resolvida', false)->count() > 0): ?>
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div>
                <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">
                    Este pedido possui <?php echo e($pedido->pendencias->where('resolvida', false)->count()); ?> pendência(s) ativa(s)
                </h3>
                <ul class="mt-2 text-sm text-yellow-700 dark:text-yellow-300 list-disc list-inside">
                    <?php $__currentLoopData = $pedido->pendencias->where('resolvida', false); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pendencia): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($pendencia->tipo->nome); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informações do Pedido -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informações do Pedido</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">CodSoftlab</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($pedido->codigo_pedido); ?></p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Código do Paciente</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($pedido->codigo_paciente ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nome do Paciente</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($pedido->nome_paciente); ?></p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data do Atendimento</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($pedido->data_atendimento ? $pedido->data_atendimento->format('d/m/Y') : '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo de Atendimento</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($pedido->tipo_atendimento ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Convênio</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($pedido->cod_guia ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Unidade</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($pedido->unidade->nome ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Atendente</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($pedido->atendente->nome ?? '-'); ?></p>
                    </div>
                    <?php if($pedido->gestor_id): ?>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Gestor</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($pedido->gestor->nome); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if($pedido->lote): ?>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Lote</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($pedido->lote); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if($pedido->observacoes): ?>
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Observações</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo e($pedido->observacoes); ?></p>
                </div>
                <?php endif; ?>

                <?php if($pedido->motivo_devolucao): ?>
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Motivo da Devolução</label>
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($pedido->motivo_devolucao); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Documentos -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Documentos</h2>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $pedido)): ?>
                    <?php if($pedido->status === 'PENDENTE'): ?>
                    <a href="<?php echo e(route('pedidos.escanear', $pedido)); ?>"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Adicionar Documento
                    </a>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php if($pedido->documentos->count() > 0): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $pedido->documentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $documento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="flex items-center gap-4 flex-1">
                            <!-- Ícone do Tipo -->
                            <div class="flex-shrink-0">
                                <?php
                                $iconeClass = [
                                'Guia Médica' => 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300',
                                'Autorização/SADT' => 'bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300',
                                'Documento Extra' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                'Formulário' => 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300',
                                ];
                                ?>
                                <div class="w-12 h-12 rounded-lg <?php echo e($iconeClass[$documento->tipo_documento] ?? 'bg-gray-100 text-gray-600'); ?> flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Informações -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    <?php echo e($documento->tipo_documento); ?>

                                    <?php if($documento->tipo_documento === 'Guia Médica' || $documento->tipo_documento === 'Autorização/SADT'): ?>
                                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 rounded">OBRIGATÓRIO</span>
                                    <?php endif; ?>
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate"><?php echo e($documento->arquivo_nome); ?></p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    Upload por <?php echo e($documento->criadoPor->nome); ?> • <?php echo e($documento->created_at->format('d/m/Y H:i')); ?>

                                </p>
                            </div>
                        </div>

                        <!-- Ações -->
                        <div class="flex items-center gap-2">
                            <a href="<?php echo e(route('documentos.preview', $documento)); ?>"
                                target="_blank"
                                class="p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 rounded-lg transition"
                                title="Visualizar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <a href="<?php echo e(route('documentos.download', $documento)); ?>"
                                class="p-2 text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-700/50 rounded-lg transition"
                                title="Download">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $pedido)): ?>
                            <?php if($pedido->status === 'PENDENTE'): ?>
                            <form method="POST" action="<?php echo e(route('documentos.destroy', $documento)); ?>" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit"
                                    onclick="return confirm('Tem certeza que deseja remover este documento?')"
                                    class="p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 rounded-lg transition"
                                    title="Remover">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php else: ?>
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Nenhum documento anexado</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Adicione documentos para enviar o pedido.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Pendências -->
            <?php if($pedido->pendencias->count() > 0): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pendências</h2>
                <div class="space-y-3">
                    <?php $__currentLoopData = $pedido->pendencias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pendencia): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-start gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg <?php echo e($pendencia->resolvida ? 'bg-gray-50 dark:bg-gray-700/30' : 'bg-yellow-50 dark:bg-yellow-900/10'); ?>">
                        <div class="flex-shrink-0 mt-1">
                            <?php if($pendencia->resolvida): ?>
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <?php else: ?>
                            <div class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium <?php echo e($pendencia->resolvida ? 'text-gray-700 dark:text-gray-300 line-through' : 'text-gray-900 dark:text-white'); ?>">
                                <?php echo e($pendencia->tipo->nome); ?>

                            </h3>
                            <?php if($pendencia->observacao): ?>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400"><?php echo e($pendencia->observacao); ?></p>
                            <?php endif; ?>
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <?php if($pendencia->resolvida): ?>
                                <span>Resolvida por <?php echo e($pendencia->resolvidoPor->nome); ?> em <?php echo e($pendencia->resolvido_em->format('d/m/Y H:i')); ?></span>
                                <?php else: ?>
                                <span>Criada por <?php echo e($pendencia->criadoPor->nome); ?> em <?php echo e($pendencia->created_at->format('d/m/Y H:i')); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Coluna Lateral -->
        <div class="space-y-6">
            <!-- Ações -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ações</h2>
                <div class="space-y-3">
                    <?php if($pedido->status === 'PENDENTE'): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $pedido)): ?>
                    <a href="<?php echo e(route('pedidos.escanear', $pedido)); ?>"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Escanear Documentos
                    </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('enviar', $pedido)): ?>
                    <form method="POST" action="<?php echo e(route('pedidos.enviar', $pedido)); ?>" id="enviarForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <button type="button"
                            @click="confirmAction = 'enviar'; showConfirmModal = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Enviar para Conferência
                        </button>
                    </form>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php if($pedido->status === 'ENVIADO'): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('aprovar', $pedido)): ?>
                    <form method="POST" action="<?php echo e(route('pedidos.aprovar', $pedido)); ?>" id="aprovarForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <button type="button"
                            @click="confirmAction = 'aprovar'; showConfirmModal = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Aprovar Pedido
                        </button>
                    </form>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('devolver', $pedido)): ?>
                    <a href="<?php echo e(route('pedidos.devolver', $pedido)); ?>"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Devolver Pedido
                    </a>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $pedido)): ?>
                    <?php if($pedido->status === 'PENDENTE' || strtoupper($pedido->status) === 'PENDENTE'): ?>
                    <a href="<?php echo e(route('pedidos.escanear', $pedido)); ?>"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Escanear
                    </a>
                    <?php endif; ?>
                    <?php endif; ?>

                    <button type="button"
                        onclick="showTimeline(<?php echo e($pedido->id); ?>)"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Ver Histórico
                    </button>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $pedido)): ?>
                    <form method="POST" action="<?php echo e(route('pedidos.destroy', $pedido)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit"
                            onclick="return confirm('Tem certeza que deseja remover este pedido? Esta ação não pode ser desfeita.')"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Remover Pedido
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cronologia -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cronologia</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-1.5"></div>
                        <div>
                            <p class="text-gray-900 dark:text-white font-medium">Criado</p>
                            <p class="text-gray-500 dark:text-gray-400"><?php echo e($pedido->created_at->format('d/m/Y H:i')); ?></p>
                        </div>
                    </div>
                    <?php if($pedido->enviado_em): ?>
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-1.5"></div>
                        <div>
                            <p class="text-gray-900 dark:text-white font-medium">Enviado</p>
                            <p class="text-gray-500 dark:text-gray-400"><?php echo e($pedido->enviado_em->format('d/m/Y H:i')); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($pedido->aprovado_em): ?>
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-green-600 rounded-full mt-1.5"></div>
                        <div>
                            <p class="text-gray-900 dark:text-white font-medium">Aprovado</p>
                            <p class="text-gray-500 dark:text-gray-400"><?php echo e($pedido->aprovado_em->format('d/m/Y H:i')); ?></p>
                            <p class="text-gray-500 dark:text-gray-400">por <?php echo e($pedido->gestor->nome); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($pedido->devolvido_em): ?>
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-red-500 rounded-full mt-1.5"></div>
                        <div>
                            <p class="text-gray-900 dark:text-white font-medium">Devolvido</p>
                            <p class="text-gray-500 dark:text-gray-400"><?php echo e($pedido->devolvido_em->format('d/m/Y H:i')); ?></p>
                            <p class="text-gray-500 dark:text-gray-400">por <?php echo e($pedido->gestor->nome); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Estatísticas</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total de Documentos</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($pedido->documentos->count()); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Pendências Ativas</span>
                        <span class="text-sm font-semibold <?php echo e($pedido->pendencias->where('resolvida', false)->count() > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-900 dark:text-white'); ?>">
                            <?php echo e($pedido->pendencias->where('resolvida', false)->count()); ?>

                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Pendências Resolvidas</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($pedido->pendencias->where('resolvida', true)->count()); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div x-show="showConfirmModal"
        x-cloak
        @click.self="showConfirmModal = false"
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
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirmar Ação</h3>
                    </div>
                </div>

                <p class="text-gray-600 dark:text-gray-300 mb-6" x-text="confirmAction === 'enviar' ? 'Tem certeza que deseja enviar este pedido para conferência?' : 'Tem certeza que deseja aprovar este pedido?'"></p>

                <div class="flex gap-3 justify-end">
                    <button @click="showConfirmModal = false"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg transition">
                        Cancelar
                    </button>
                    <button @click="document.getElementById(confirmAction + 'Form').submit()"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Timeline -->
<div id="timelineModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
    onclick="if(event.target === this) closeTimeline()">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] overflow-hidden"
        onclick="event.stopPropagation()">
        <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Histórico do Pedido</h3>
            <button onclick="closeTimeline()"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="timelineContent" class="p-6 overflow-y-auto max-h-[60vh]">
            <!-- Conteúdo carregado via AJAX -->
        </div>
    </div>
</div>

<script>
    function showTimeline(pedidoId) {
        const modal = document.getElementById('timelineModal');
        const content = document.getElementById('timelineContent');

        content.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div></div>';
        modal.classList.remove('hidden');

        fetch(`/pedidos/${pedidoId}/timeline`)
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;
            })
            .catch(error => {
                content.innerHTML = '<div class="text-center text-red-500">Erro ao carregar histórico</div>';
            });
    }

    function closeTimeline() {
        document.getElementById('timelineModal').classList.add('hidden');
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\digitalizacacim\resources\views/pedidos/show.blade.php ENDPATH**/ ?>