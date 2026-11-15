<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Controller da landing page pública da aplicação MoreSpaces.
 */
class HomeController extends Controller
{
    /**
     * Página inicial (Home).
     *
     * Monta e envia para a view 'home' os dados dinâmicos da landing page.
     */
    public function index(): View
    {
        return view('home', [
            'isAuthenticated' => $this->isAuthenticated(),
            'isSpacesUser' => $this->isSpacesUser(),
            'events' => config('morespaces.events', []),
            'faqs' => config('morespaces.faqs', []),
        ]);
    }

    /**
     * Verifica se o usuário logado é um "Spaces User".
     *
     * Regra simplificada: o username salvo em sessão precisa conter a
     * substring "spaces" (case-insensitive). É essa flag que libera o
     * link "Arquivos" no menu e o acesso à área de níveis.
     */
    private function isSpacesUser(): bool
    {
        return str_contains(strtolower((string) session('username')), 'spaces');
    }

    /**
     * Verifica se existe uma sessão de usuário ativa.
     *
     * Não usa o sistema de Auth do Laravel (Auth::check()) porque não
     * há model/tabela de usuários — a "autenticação" é apenas a
     * presença da chave 'username' na sessão.
     */
    private function isAuthenticated(): bool
    {
        return (string) session('username') !== '';
    }
}
