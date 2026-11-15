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
            'events' => config('morespaces.events', []),
            'faqs' => config('morespaces.faqs', []),
        ]);
    }
}
