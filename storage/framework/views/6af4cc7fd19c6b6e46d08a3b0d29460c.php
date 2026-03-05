

<?php $__env->startSection('page-title', 'Conferir Pedido #' . $pedido->codigo_pedido); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Cabeçalho com Navegação -->
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="<?php echo e(route('conferencia.index')); ?>"
                class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pedido #<?php echo e($pedido->codigo_pedido); ?></h1>
                <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo e($pedido->nome_paciente); ?> - <?php echo e($pedido->convenio?->nome ?? $pedido->cod_guia ?? '-'); ?></p>
            </div>
        </div>

        <!-- Badge Status -->
        <span class="px-4 py-2 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-sm font-medium">
            Aguardando Conferência
        </span>
    </div>

    <!-- Layout Principal -->
    <div class="space-y-6">
        <!-- Informações do Pedido -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informações do Pedido</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <dt class="text-gray-600 dark:text-gray-400">Código</dt>
                    <dd class="font-medium text-gray-900 dark:text-white"><?php echo e($pedido->codigo); ?></dd>
                </div>
                <div>
                    <dt class="text-gray-600 dark:text-gray-400">Paciente</dt>
                    <dd class="font-medium text-gray-900 dark:text-white"><?php echo e($pedido->nome_paciente); ?></dd>
                </div>
                <div>
                    <dt class="text-gray-600 dark:text-gray-400">Código Paciente</dt>
                    <dd class="font-medium text-gray-900 dark:text-white"><?php echo e($pedido->codigo_paciente); ?></dd>
                </div>
                <div>
                    <dt class="text-gray-600 dark:text-gray-400">Data Atendimento</dt>
                    <dd class="font-medium text-gray-900 dark:text-white"><?php echo e($pedido->data_atendimento ? $pedido->data_atendimento->format('d/m/Y') : '-'); ?></dd>
                </div>
                <div>
                    <dt class="text-gray-600 dark:text-gray-400">Tipo</dt>
                    <dd class="font-medium text-gray-900 dark:text-white"><?php echo e($pedido->tipo_atendimento ?? '-'); ?></dd>
                </div>
                <div>
                    <dt class="text-gray-600 dark:text-gray-400">Convênio</dt>
                    <dd class="font-medium text-gray-900 dark:text-white"><?php echo e($pedido->convenio?->nome ?? $pedido->cod_guia ?? '-'); ?></dd>
                </div>
                <div>
                    <dt class="text-gray-600 dark:text-gray-400">Unidade</dt>
                    <dd class="font-medium text-gray-900 dark:text-white"><?php echo e($pedido->unidade?->nome ?? '-'); ?></dd>
                </div>
                <div>
                    <dt class="text-gray-600 dark:text-gray-400">Atendente</dt>
                    <dd class="font-medium text-gray-900 dark:text-white"><?php echo e($pedido->atendente?->nome ?? '-'); ?></dd>
                </div>
                <div>
                    <dt class="text-gray-600 dark:text-gray-400">Enviado em</dt>
                    <dd class="font-medium text-gray-900 dark:text-white"><?php echo e($pedido->data_envio ? $pedido->data_envio->format('d/m/Y H:i') : '-'); ?></dd>
                </div>
                <?php if($pedido->observacoes): ?>
                <div class="col-span-2 md:col-span-4">
                    <dt class="text-gray-600 dark:text-gray-400">Observações</dt>
                    <dd class="font-medium text-gray-900 dark:text-white"><?php echo e($pedido->observacoes); ?></dd>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Documentos Lado a Lado -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php $__currentLoopData = $pedido->documentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $documento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <!-- Header do Documento -->
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 flex items-center justify-between border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <?php
                        $colors = [
                        'Guia Médica' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                        'Autorização' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        'SADT' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                        'Documento Extra' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                        'Formulário' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200'
                        ];
                        ?>
                        <span class="px-2 py-1 rounded text-xs font-medium <?php echo e($colors[$documento->tipo_documento] ?? 'bg-gray-100 text-gray-800'); ?> flex-shrink-0">
                            <?php echo e($documento->tipo_documento); ?>

                        </span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white truncate"><?php echo e($documento->arquivo_nome); ?></span>
                    </div>
                    <a href="<?php echo e(route('documentos.download', $documento)); ?>"
                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>
                </div>

                <!-- Preview do Documento -->
                <div class="bg-gray-100 dark:bg-gray-900">
                    <?php if(str_ends_with($documento->arquivo_nome, '.pdf')): ?>
                    <iframe
                        src="<?php echo e(route('documentos.preview', $documento)); ?>"
                        class="w-full h-screen"
                        frameborder="0">
                    </iframe>
                    <?php else: ?>
                    <img src="<?php echo e(route('documentos.preview', $documento)); ?>"
                        alt="<?php echo e($documento->arquivo_nome); ?>"
                        class="w-full h-auto max-h-screen object-contain">
                    <?php endif; ?>
                </div>

                <!-- Informações do Documento -->
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-2 text-xs text-gray-600 dark:text-gray-400 border-t border-gray-200 dark:border-gray-600">
                    <span>Upload por <?php echo e($documento->uploadPor?->nome ?? $documento->criadoPor?->nome ?? 'Sistema'); ?> em <?php echo e($documento->created_at->format('d/m/Y H:i')); ?></span>
                    <span class="mx-2">•</span>
                    <span><?php echo e(number_format($documento->tamanho / 1024, 2)); ?> KB</span>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Pendências Anteriores (se houver) -->
        <?php if($pedido->pendencias->count() > 0): ?>
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-200 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Pendências Anteriores
            </h3>
            <ul class="space-y-2">
                <?php $__currentLoopData = $pedido->pendencias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pendencia): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex items-start gap-2 text-sm <?php echo e($pendencia->resolvida ? 'line-through text-gray-500' : 'text-yellow-800 dark:text-yellow-200'); ?>">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span><?php echo e($pendencia->tipo->nome); ?></span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Ações e Estatísticas -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Botões de Ação -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ações</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <!-- Aprovar -->
                        <form id="formAprovar" method="POST" action="<?php echo e(route('conferencia.aprovar-lote')); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="pedidos[]" value="<?php echo e($pedido->id); ?>">
                            <button type="button" onclick="showAprovarModal()"
                                class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Aprovar Pedido
                            </button>
                        </form>

                        <!-- Devolver -->
                        <button onclick="showDevolverModal()"
                            class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                            Devolver Pedido
                        </button>

                        <!-- Timeline -->
                        <button onclick="showTimeline(<?php echo e($pedido->id); ?>)"
                            class="w-full px-4 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Ver Cronologia
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Estatísticas</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Total Documentos</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($pedido->documentos->count()); ?></dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Dias Aguardando</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e(now()->diffInDays($pedido->enviado_em)); ?></dd>
                    </div>
                    <?php
                    $pendenciasAbertas = $pedido->pendencias->where('resolvida', false)->count();
                    ?>
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Pendências Anteriores</dt>
                        <dd class="text-sm font-medium <?php echo e($pendenciasAbertas > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400'); ?>">
                            <?php echo e($pendenciasAbertas); ?> abertas
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aprovar -->
<div id="aprovarModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Confirmar Aprovação</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
            Tem certeza que deseja aprovar este pedido? Esta ação não poderá ser desfeita.
        </p>
        <div class="flex gap-3">
            <button type="button" onclick="confirmarAprovar()"
                class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                Sim, Aprovar
            </button>
            <button type="button" onclick="hideAprovarModal()"
                class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                Cancelar
            </button>
        </div>
    </div>
</div>

<!-- Modal Devolver -->
<div id="devolverModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full p-6 my-8">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Devolver Pedido</h3>
        <form method="POST" action="<?php echo e(route('conferencia.devolver-lote')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="pedidos[]" value="<?php echo e($pedido->id); ?>">

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
                <div id="errorPendencias" class="hidden mb-3 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 rounded-lg text-sm">
                    Por favor, selecione ao menos uma pendência.
                </div>
                <div class="grid grid-cols-2 gap-2 max-h-60 overflow-y-auto">
                    <?php if($pendenciasTipos->count() > 0): ?>
                    <?php $__currentLoopData = $pendenciasTipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center p-2 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                        <input type="checkbox" name="pendencias[]" value="<?php echo e($tipo->id); ?>" class="mr-2">
                        <span class="text-sm text-gray-900 dark:text-white"><?php echo e($tipo->nome); ?></span>
                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                    <div class="col-span-2 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg text-center">
                        <p class="text-sm text-yellow-800 dark:text-yellow-200 mb-2">Nenhuma pendência cadastrada.</p>
                        <a href="<?php echo e(route('configuracoes.pendencias.index')); ?>" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            Clique aqui para cadastrar tipos de pendências
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit"
                    onclick="return validarDevolucao()"
                    class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    Confirmar Devolução
                </button>
                <button type="button"
                    onclick="hideDevolverModal()"
                    class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Timeline -->
<div id="timelineModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full p-6 my-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Cronologia do Pedido</h3>
            <button onclick="hideTimeline()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="timelineContent" class="text-center py-8">
            <svg class="animate-spin h-8 w-8 mx-auto text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Carregando...</p>
        </div>
    </div>
</div>

<script>
    function showAprovarModal() {
        document.getElementById('aprovarModal').classList.remove('hidden');
    }

    function hideAprovarModal() {
        document.getElementById('aprovarModal').classList.add('hidden');
    }

    function confirmarAprovar() {
        document.getElementById('formAprovar').submit();
    }

    function validarDevolucao() {
        const checkboxes = document.querySelectorAll('input[name="pendencias[]"]:checked');
        const errorDiv = document.getElementById('errorPendencias');

        if (checkboxes.length === 0) {
            errorDiv.classList.remove('hidden');
            errorDiv.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
            return false;
        }

        errorDiv.classList.add('hidden');
        return true;
    }

    function showDevolverModal() {
        document.getElementById('devolverModal').classList.remove('hidden');
    }

    function hideDevolverModal() {
        document.getElementById('devolverModal').classList.add('hidden');
    }

    function showTimeline(pedidoId) {
        const modal = document.getElementById('timelineModal');
        const content = document.getElementById('timelineContent');

        modal.classList.remove('hidden');

        fetch(`/pedidos/${pedidoId}/timeline`)
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;
            })
            .catch(error => {
                content.innerHTML = '<p class="text-red-500">Erro ao carregar cronologia</p>';
            });
    }

    function hideTimeline() {
        document.getElementById('timelineModal').classList.add('hidden');
    }

    // Fechar modals ao clicar fora
    document.getElementById('aprovarModal')?.addEventListener('click', function(e) {
        if (e.target === this) hideAprovarModal();
    });

    document.getElementById('devolverModal')?.addEventListener('click', function(e) {
        if (e.target === this) hideDevolverModal();
    });

    document.getElementById('timelineModal')?.addEventListener('click', function(e) {
        if (e.target === this) hideTimeline();
    });

    // Atalhos de teclado
    document.addEventListener('keydown', function(e) {
        if (e.altKey && e.key === 'a') {
            e.preventDefault();
            document.querySelector('form[action*="aprovar"]')?.submit();
        }
        if (e.altKey && e.key === 'r') {
            e.preventDefault();
            showDevolverModal();
        }
        if (e.key === 'Escape') {
            hideAprovarModal();
            hideDevolverModal();
            hideTimeline();
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\digitalizacacim\resources\views/conferencia/show.blade.php ENDPATH**/ ?>