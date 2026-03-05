<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => ['pageTitle' => 'Usuários']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['page-title' => 'Usuários']); ?>
    <div class="space-y-6">
        <!-- Cabeçalho -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Usuários</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Gerenciar usuários do sistema
                </p>
            </div>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\User::class)): ?>
            <a href="<?php echo e(route('usuarios.create')); ?>"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Novo Usuário
            </a>
            <?php endif; ?>
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['total']); ?></p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Ativos</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400"><?php echo e($stats['ativos']); ?></p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Inativos</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400"><?php echo e($stats['inativos']); ?></p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Admins</p>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400"><?php echo e($stats['admins']); ?></p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Gestores</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400"><?php echo e($stats['gestores']); ?></p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Atendentes</p>
                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400"><?php echo e($stats['atendentes']); ?></p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <form method="GET" action="<?php echo e(route('usuarios.index')); ?>" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Busca -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                            placeholder="Nome, e-mail ou CPF..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Perfil</label>
                        <select name="role"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Todos</option>
                            <option value="ADMIN" <?php echo e(request('role') === 'ADMIN' ? 'selected' : ''); ?>>Admin</option>
                            <option value="GESTOR" <?php echo e(request('role') === 'GESTOR' ? 'selected' : ''); ?>>Gestor</option>
                            <option value="ATENDENTE" <?php echo e(request('role') === 'ATENDENTE' ? 'selected' : ''); ?>>Atendente</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="ativo"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Todos</option>
                            <option value="1" <?php echo e(request('ativo') === '1' ? 'selected' : ''); ?>>Ativos</option>
                            <option value="0" <?php echo e(request('ativo') === '0' ? 'selected' : ''); ?>>Inativos</option>
                        </select>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Filtrar
                    </button>
                    <a href="<?php echo e(route('usuarios.index')); ?>"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Lista de Usuários -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Usuário
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Perfil
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Convênios
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold
                                        <?php echo e($usuario->role === 'ADMIN' ? 'bg-purple-600' : ($usuario->role === 'GESTOR' ? 'bg-blue-600' : 'bg-orange-600')); ?>">
                                        <?php echo e(strtoupper(substr($usuario->nome, 0, 2))); ?>

                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($usuario->nome); ?></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($usuario->email); ?></div>
                                        <?php if($usuario->cpf): ?>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">CPF: <?php echo e($usuario->cpf); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $roleColors = [
                                'ADMIN' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                'GESTOR' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'ATENDENTE' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200'
                                ];
                                ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo e($roleColors[$usuario->role]); ?>">
                                    <?php echo e($usuario->role); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($usuario->convenios->count() > 0): ?>
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <?php echo e($usuario->convenios->count()); ?> convênio(s)
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <?php echo e($usuario->convenios->pluck('nome')->take(2)->join(', ')); ?>

                                    <?php if($usuario->convenios->count() > 2): ?>
                                    <span>+<?php echo e($usuario->convenios->count() - 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php else: ?>
                                <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($usuario->ativo): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Ativo
                                </span>
                                <?php else: ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Inativo
                                </span>
                                <?php endif; ?>
                                <?php if($usuario->forcar_troca_senha): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 ml-1">
                                    Trocar senha
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $usuario)): ?>
                                    <a href="<?php echo e(route('usuarios.edit', $usuario)); ?>"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $usuario)): ?>
                                    <form method="POST" action="<?php echo e(route('usuarios.destroy', $usuario)); ?>"
                                        onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <p class="text-lg">Nenhum usuário encontrado</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <?php if($usuarios->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <?php echo e($usuarios->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $attributes = $__attributesOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__attributesOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $component = $__componentOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__componentOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\digitalizacacim\resources\views/usuarios/index.blade.php ENDPATH**/ ?>