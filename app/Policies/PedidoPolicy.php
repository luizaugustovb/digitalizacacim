<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Pedido;
use App\Models\User;

class PedidoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pedido $pedido): bool
    {
        // Admin e Gestor podem ver tudo
        if ($user->isAdmin() || $user->isGestor()) {
            return true;
        }

        // Atendente só vê seus próprios pedidos
        return $pedido->atendente_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Todos podem criar pedidos
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pedido $pedido): bool
    {
        // Admin pode atualizar tudo
        if ($user->isAdmin()) {
            return true;
        }

        // Gestor pode atualizar tudo
        if ($user->isGestor()) {
            return true;
        }

        // Atendente não pode editar pedidos aprovados
        if (strtoupper($pedido->status) === 'APROVADO') {
            return false;
        }

        // Atendente só pode atualizar seus próprios pedidos
        return $pedido->atendente_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pedido $pedido): bool
    {
        // Apenas admin pode deletar
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pedido $pedido): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pedido $pedido): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine se o usuário pode enviar o pedido
     */
    public function enviar(User $user, Pedido $pedido): bool
    {
        // Admin e Gestor podem enviar sempre
        if ($user->isAdmin() || $user->isGestor()) {
            return true;
        }

        // Atendente não pode reenviar pedidos aprovados
        if (strtoupper($pedido->status) === 'APROVADO') {
            return false;
        }

        return $this->update($user, $pedido);
    }

    /**
     * Determine se o usuário pode aprovar o pedido
     */
    public function aprovar(User $user, Pedido $pedido): bool
    {
        // Apenas Gestor e Admin podem aprovar
        return $user->isGestor() || $user->isAdmin();
    }

    /**
     * Determine se o usuário pode devolver o pedido
     */
    public function devolver(User $user, Pedido $pedido): bool
    {
        // Apenas Gestor e Admin podem devolver
        return $user->isGestor() || $user->isAdmin();
    }
}
