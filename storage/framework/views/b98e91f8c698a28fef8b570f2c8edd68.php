<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => ['pageTitle' => 'Configurações']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['page-title' => 'Configurações']); ?>
    <div class="max-w-6xl mx-auto space-y-6" x-data="{ tab: 'geral' }">
        <!-- Cabeçalho -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Configurações do Sistema</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Gerencie as configurações gerais do sistema
            </p>
        </div>

        <!-- Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button @click="tab = 'geral'"
                        :class="tab === 'geral' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Geral
                    </button>
                    <button @click="tab = 'documentos'"
                        :class="tab === 'documentos' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Documentos
                    </button>
                    <button @click="tab = 'produtividade'"
                        :class="tab === 'produtividade' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Produtividade
                    </button>
                    <button @click="tab = 'importacao'"
                        :class="tab === 'importacao' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Importação
                    </button>
                    <button @click="tab = 'integracao'"
                        :class="tab === 'integracao' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        Integração
                    </button>
                    <button @click="tab = 'notificacoes'"
                        :class="tab === 'notificacoes' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Notificações
                    </button>
                </nav>
            </div>

            <!-- Formulário -->
            <form method="POST" action="<?php echo e(route('configuracoes.update')); ?>" class="p-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <!-- Tab: Geral -->
                <div x-show="tab === 'geral'" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nome do Sistema
                        </label>
                        <input type="text" name="nome_sistema" value="<?php echo e(old('nome_sistema', $config['geral']['nome_sistema'])); ?>"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Nome exibido no cabeçalho e título das páginas</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            E-mail para Notificações
                        </label>
                        <input type="email" name="email_notificacoes" value="<?php echo e(old('email_notificacoes', $config['geral']['email_notificacoes'])); ?>"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">E-mail principal para receber notificações do sistema</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fuso Horário
                        </label>
                        <select name="timezone"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="America/Sao_Paulo" <?php echo e($config['geral']['timezone'] === 'America/Sao_Paulo' ? 'selected' : ''); ?>>Brasília (GMT-3)</option>
                            <option value="America/Manaus" <?php echo e($config['geral']['timezone'] === 'America/Manaus' ? 'selected' : ''); ?>>Manaus (GMT-4)</option>
                            <option value="America/Rio_Branco" <?php echo e($config['geral']['timezone'] === 'America/Rio_Branco' ? 'selected' : ''); ?>>Rio Branco (GMT-5)</option>
                        </select>
                    </div>
                </div>

                <!-- Tab: Documentos -->
                <div x-show="tab === 'documentos'" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Dias de Retenção de Documentos
                        </label>
                        <input type="number" name="dias_retencao_documentos" min="365" max="3650"
                            value="<?php echo e(old('dias_retencao_documentos', $config['documentos']['dias_retencao_documentos'])); ?>"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Quantidade de dias para manter documentos arquivados (1825 dias = 5 anos)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tamanho Máximo de Arquivo (MB)
                        </label>
                        <input type="number" name="tamanho_maximo_arquivo" min="1" max="50"
                            value="<?php echo e(old('tamanho_maximo_arquivo', $config['documentos']['tamanho_maximo_arquivo'])); ?>"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tamanho máximo permitido para upload de documentos</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Formatos Permitidos
                        </label>
                        <input type="text" name="formatos_permitidos"
                            value="<?php echo e(old('formatos_permitidos', $config['documentos']['formatos_permitidos'])); ?>"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Extensões permitidas separadas por vírgula (ex: pdf,jpg,jpeg,png)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Qualidade de Compressão (%)
                        </label>
                        <input type="number" name="qualidade_compressao" min="50" max="100"
                            value="<?php echo e(old('qualidade_compressao', $config['documentos']['qualidade_compressao'])); ?>"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Qualidade para compressão de imagens (85% recomendado)</p>
                    </div>

                    <!-- Link para Gerenciar Pendências -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <a href="<?php echo e(route('configuracoes.pendencias.index')); ?>"
                            class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white">Gerenciar Tipos de Pendências</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Cadastre e gerencie os tipos de pendências disponíveis para devolução</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Tab: Produtividade -->
                <div x-show="tab === 'produtividade'" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Meta Diária - Atendentes
                        </label>
                        <input type="number" name="meta_diaria_atendente" min="1"
                            value="<?php echo e(old('meta_diaria_atendente', $config['produtividade']['meta_diaria_atendente'])); ?>"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Quantidade de pedidos que cada atendente deve processar por dia</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Meta Diária - Gestores
                        </label>
                        <input type="number" name="meta_diaria_gestor" min="1"
                            value="<?php echo e(old('meta_diaria_gestor', $config['produtividade']['meta_diaria_gestor'])); ?>"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Quantidade de pedidos que cada gestor deve conferir por dia</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Alerta de Pedidos Antigos (dias)
                        </label>
                        <input type="number" name="alerta_pedidos_antigos_dias" min="1" max="30"
                            value="<?php echo e(old('alerta_pedidos_antigos_dias', $config['produtividade']['alerta_pedidos_antigos_dias'])); ?>"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Após quantos dias um pedido é considerado antigo e recebe alerta</p>
                    </div>
                </div>

                <!-- Tab: Importação -->
                <div x-show="tab === 'importacao'" class="space-y-6">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="importacao_automatica" value="1"
                                <?php echo e(old('importacao_automatica', $config['importacao']['importacao_automatica']) === 'true' ? 'checked' : ''); ?>

                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Habilitar importação automática de pedidos</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Horário da Importação
                        </label>
                        <input type="time" name="horario_importacao"
                            value="<?php echo e(old('horario_importacao', $config['importacao']['horario_importacao'])); ?>"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Horário para executar a importação diária automaticamente</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Timeout de Conexão (segundos)
                        </label>
                        <input type="number" name="importacao_timeout" min="10" max="300"
                            value="<?php echo e(old('importacao_timeout', $config['importacao']['importacao_timeout'])); ?>"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tempo máximo de espera para conectar ao SQL Server (30-60 segundos recomendado)</p>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Conexão SQL Server</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Host
                                </label>
                                <input type="text" name="sqlserver_host"
                                    value="<?php echo e(old('sqlserver_host', $config['importacao']['sqlserver_host'])); ?>"
                                    placeholder="localhost ou 192.168.1.100"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Database
                                </label>
                                <input type="text" name="sqlserver_database"
                                    value="<?php echo e(old('sqlserver_database', $config['importacao']['sqlserver_database'])); ?>"
                                    placeholder="nome_banco"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Username
                                </label>
                                <input type="text" name="sqlserver_username"
                                    value="<?php echo e(old('sqlserver_username', $config['importacao']['sqlserver_username'])); ?>"
                                    placeholder="usuario"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Password
                                </label>
                                <input type="password" name="sqlserver_password"
                                    placeholder="••••••••"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Deixe em branco para manter a senha atual</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Integração -->
                <div x-show="tab === 'integracao'" class="space-y-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-blue-900 dark:text-blue-300">Integração com Softlab</h3>
                                <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">Configure a integração com o banco de dados do Softlab para importar pedidos automaticamente.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Host</label>
                            <input type="text" name="softlab_host" value="<?php echo e(env('SOFTLAB_DB_HOST', '127.0.0.1')); ?>"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="127.0.0.1">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Porta</label>
                            <input type="text" name="softlab_port" value="<?php echo e(env('SOFTLAB_DB_PORT', '3306')); ?>"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="3306">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Database</label>
                            <input type="text" name="softlab_database" value="<?php echo e(env('SOFTLAB_DB_DATABASE', 'BD_SOFTLAB_P00')); ?>"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="BD_SOFTLAB_P00">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Usuário</label>
                            <input type="text" name="softlab_username" value="<?php echo e(env('SOFTLAB_DB_USERNAME', 'root')); ?>"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="root">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Senha</label>
                            <input type="password" name="softlab_password" value="<?php echo e(env('SOFTLAB_DB_PASSWORD', '')); ?>"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="••••••••">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Deixe em branco se não houver senha</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <a href="<?php echo e(route('configuracoes.softlab.mappings')); ?>"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            Gerenciar Mapeamentos Softlab
                        </a>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            Configure os mapeamentos entre códigos do Softlab (unidades e usuários) e o sistema.
                        </p>
                    </div>
                </div>

                <!-- Tab: Notificações -->
                <div x-show="tab === 'notificacoes'" class="space-y-4">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="notificar_pedido_devolvido" value="1"
                                <?php echo e(old('notificar_pedido_devolvido', $config['notificacoes']['notificar_pedido_devolvido']) === 'true' ? 'checked' : ''); ?>

                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Notificar atendente quando pedido for devolvido</span>
                        </label>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="notificar_pedido_aprovado" value="1"
                                <?php echo e(old('notificar_pedido_aprovado', $config['notificacoes']['notificar_pedido_aprovado']) === 'true' ? 'checked' : ''); ?>

                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Notificar atendente quando pedido for aprovado</span>
                        </label>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="notificar_meta_atingida" value="1"
                                <?php echo e(old('notificar_meta_atingida', $config['notificacoes']['notificar_meta_atingida']) === 'true' ? 'checked' : ''); ?>

                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Notificar quando meta diária for atingida</span>
                        </label>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Salvar Configurações
                    </button>
                </div>
            </form>
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
<?php endif; ?><?php /**PATH C:\xampp\htdocs\digitalizacacim\resources\views/configuracoes/index.blade.php ENDPATH**/ ?>