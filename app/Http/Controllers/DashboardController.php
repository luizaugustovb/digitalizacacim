<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Estatísticas baseadas no role do usuário
        if ($user->isAtendente()) {
            $stats = [
                'pendentes' => Pedido::where('atendente_id', $user->id)->pendentes()->count(),
                'enviados' => Pedido::where('atendente_id', $user->id)->enviados()->count(),
                'aprovados' => Pedido::where('atendente_id', $user->id)->aprovados()->count(),
                'devolvidos' => Pedido::where('atendente_id', $user->id)->devolvidos()->count(),
            ];
        } else {
            $stats = [
                'pendentes' => Pedido::pendentes()->count(),
                'enviados' => Pedido::enviados()->count(),
                'aprovados' => Pedido::aprovados()->count(),
                'devolvidos' => Pedido::devolvidos()->count(),
            ];
        }

        return view('dashboard.index-component', compact('stats'));
    }
}
