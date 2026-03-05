@extends('layouts.main')

@section('page-title', 'Novo Usuário')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Cabeçalho -->
    <div class="flex items-center gap-4">
        <a href="{{ route('usuarios.index') }}"
            class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Novo Usuário</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Preencha os dados do novo usuário</p>
        </div>
    </div>

    <!-- Formulário -->
    <form method="POST" action="{{ route('usuarios.store') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        @csrf

        <div class="space-y-6">
            <!-- Dados Pessoais -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Dados Pessoais</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nome Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nome" value="{{ old('nome') }}" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('nome') border-red-500 @enderror">
                        @error('nome')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Código Softlab <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="codigo" value="{{ old('codigo') }}" required
                            placeholder="Ex: DOR"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('codigo') border-red-500 @enderror">
                        @error('codigo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            E-mail <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('email') border-red-500 @enderror">
                        @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            CPF
                        </label>
                        <input type="text" name="cpf" value="{{ old('cpf') }}"
                            placeholder="000.000.000-00"
                            x-mask="999.999.999-99"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('cpf') border-red-500 @enderror">
                        @error('cpf')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Telefone
                        </label>
                        <input type="text" name="telefone" value="{{ old('telefone') }}"
                            placeholder="(00) 00000-0000"
                            x-mask="(99) 99999-9999"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('telefone') border-red-500 @enderror">
                        @error('telefone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Credenciais -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Credenciais de Acesso</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Senha <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('password') border-red-500 @enderror">
                        @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Confirmar Senha <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Perfil e Permissões -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Perfil e Permissões</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Perfil <span class="text-red-500">*</span>
                        </label>
                        <select name="role" required x-model="role"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('role') border-red-500 @enderror">
                            <option value="">Selecione...</option>
                            <option value="ATENDENTE" {{ old('role') === 'ATENDENTE' ? 'selected' : '' }}>Atendente</option>
                            <option value="GESTOR" {{ old('role') === 'GESTOR' ? 'selected' : '' }}>Gestor</option>
                            <option value="ADMIN" {{ old('role') === 'ADMIN' ? 'selected' : '' }}>Administrador</option>
                        </select>
                        @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ role: '{{ old('role') }}' }" x-show="role === 'GESTOR' || role === 'ATENDENTE'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Convênios Permitidos
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-60 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-lg p-3">
                            @foreach($convenios as $convenio)
                            <label class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer">
                                <input type="checkbox" name="convenios[]" value="{{ $convenio->id }}"
                                    {{ in_array($convenio->id, old('convenios', [])) ? 'checked' : '' }}
                                    class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-900 dark:text-white">{{ $convenio->nome }}</span>
                            </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Selecione os convênios que este usuário poderá acessar
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status e Opções</h3>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="ativo" value="1" checked
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Usuário ativo</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="forcar_troca_senha" value="1" checked
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Forçar troca de senha no primeiro login</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Botões -->
        <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <button type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                Criar Usuário
            </button>
            <a href="{{ route('usuarios.index') }}"
                class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection