<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Convenio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'role:ADMIN,GESTOR',
        ];
    }

    /**
     * Lista todos os usuários
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $query = User::query()->with('convenios');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%")
                    ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === '1');
        }

        // Ordenação
        $query->orderBy('nome');

        $usuarios = $query->paginate(20);

        // Estatísticas
        $stats = [
            'total' => User::count(),
            'ativos' => User::where('ativo', true)->count(),
            'inativos' => User::where('ativo', false)->count(),
            'admins' => User::where('role', 'ADMIN')->count(),
            'gestores' => User::where('role', 'GESTOR')->count(),
            'atendentes' => User::where('role', 'ATENDENTE')->count(),
        ];

        return view('usuarios.index', compact('usuarios', 'stats'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        Gate::authorize('create', User::class);

        $convenios = Convenio::where('ativo', true)->orderBy('nome')->get();

        return view('usuarios.create', compact('convenios'));
    }

    /**
     * Salvar novo usuário
     */
    public function store(Request $request)
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo' => 'required|string|max:50|unique:users,codigo',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'nullable|string|size:14|unique:users,cpf',
            'telefone' => 'nullable|string|max:20',
            'password' => 'required|string|min:3|confirmed',
            'role' => 'required|in:ATENDENTE,GESTOR,ADMIN',
            'ativo' => 'boolean',
            'forcar_troca_senha' => 'boolean',
            'convenios' => 'nullable|array',
            'convenios.*' => 'exists:convenios,id',
        ]);

        $user = User::create([
            'nome' => $validated['nome'],
            'codigo' => $validated['codigo'],
            'email' => $validated['email'],
            'cpf' => $validated['cpf'] ?? null,
            'telefone' => $validated['telefone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'ativo' => $request->boolean('ativo', true),
            'forcar_troca_senha' => $request->boolean('forcar_troca_senha', false),
        ]);

        // Associar convênios (apenas para Gestores e Atendentes)
        if (in_array($validated['role'], ['GESTOR', 'ATENDENTE']) && $request->has('convenios')) {
            $user->convenios()->sync($request->convenios);
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Formulário de edição
     */
    public function edit(User $usuario)
    {
        Gate::authorize('update', $usuario);

        $convenios = Convenio::where('ativo', true)->orderBy('nome')->get();

        return view('usuarios.edit', compact('usuario', 'convenios'));
    }

    /**
     * Atualizar usuário
     */
    public function update(Request $request, User $usuario)
    {
        Gate::authorize('update', $usuario);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo' => ['required', 'string', 'max:50', Rule::unique('users', 'codigo')->ignore($usuario->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($usuario->id)],
            'cpf' => ['nullable', 'string', 'size:14', Rule::unique('users')->ignore($usuario->id)],
            'telefone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:3|confirmed',
            'role' => 'required|in:ATENDENTE,GESTOR,ADMIN',
            'ativo' => 'boolean',
            'forcar_troca_senha' => 'boolean',
            'convenios' => 'nullable|array',
            'convenios.*' => 'exists:convenios,id',
        ]);

        $usuario->update([
            'nome' => $validated['nome'],
            'codigo' => $validated['codigo'],
            'email' => $validated['email'],
            'cpf' => $validated['cpf'] ?? null,
            'telefone' => $validated['telefone'] ?? null,
            'role' => $validated['role'],
            'ativo' => $request->boolean('ativo', true),
            'forcar_troca_senha' => $request->boolean('forcar_troca_senha', false),
        ]);

        // Atualizar senha se fornecida
        if ($request->filled('password')) {
            $usuario->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Associar convênios
        if (in_array($validated['role'], ['GESTOR', 'ATENDENTE'])) {
            $usuario->convenios()->sync($request->convenios ?? []);
        } else {
            $usuario->convenios()->detach();
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Excluir usuário (soft delete)
     */
    public function destroy(User $usuario)
    {
        Gate::authorize('delete', $usuario);

        // Não permite excluir o próprio usuário
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'Você não pode excluir seu próprio usuário!');
        }

        // Não permite excluir se tiver pedidos
        if ($usuario->pedidosComoAtendente()->count() > 0 || $usuario->pedidosComoGestor()->count() > 0) {
            return back()->with('error', 'Não é possível excluir este usuário pois ele possui pedidos associados.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }
}
