<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Ver lista de usuários
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isGestor();
    }

    /**
     * Ver usuário específico
     */
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->isGestor();
    }

    /**
     * Criar usuário
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Editar usuário
     */
    public function update(User $user, User $model): bool
    {
        // Apenas Admin pode editar
        return $user->isAdmin();
    }

    /**
     * Excluir usuário
     */
    public function delete(User $user, User $model): bool
    {
        // Apenas Admin pode excluir
        // Não pode excluir a si mesmo
        return $user->isAdmin() && $user->id !== $model->id;
    }
}
